<?php

namespace App\Controller;

use App\Entity\Cv;
use App\Form\CvUserType;
use App\Repository\CvRepository;
use App\Service\CvAiImprover;
use App\Service\PdfTextExtractor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/cv')]
final class CvController extends AbstractController
{
    #[Route('/', name: 'app_cv_manage', methods: ['GET'])]
    public function manage(CvRepository $cvRepository): Response
    {
        $user = $this->getUser();
        $cv = $cvRepository->findOneBy(['user' => $user]);

        return $this->render('cv/manage.html.twig', [
            'cv' => $cv,
        ]);
    }

    #[Route('/new', name: 'app_cv_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CvRepository $cvRepository,
        PdfTextExtractor $pdfTextExtractor,
    ): Response {
        $user = $this->getUser();
        $existingCv = $cvRepository->findOneBy(['user' => $user]);

        if ($existingCv) {
            $this->addFlash('info', 'Vous avez déjà un CV. Vous pouvez le modifier.');
            return $this->redirectToRoute('app_cv_manage');
        }

        $cv = new Cv();
        $cv->setUser($user);
        $cv->setNombreAmeliorations(0);
        $cv->setEstPublic(false);
        $cv->setDateUpload(new \DateTime());

        $form = $this->createForm(CvUserType::class, $cv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $uploadedPdf */
            $uploadedPdf = $form->get('cvPdf')->getData();

            $hasText = trim((string) $cv->getContenuOriginal()) !== '';
            $hasPdf = $uploadedPdf instanceof UploadedFile;

            if (!$hasText && !$hasPdf) {
                $this->addFlash('error', 'Veuillez importer un PDF ou coller votre CV en texte.');
                return $this->redirectToRoute('app_cv_new');
            }

            if ($hasPdf) {
                $relativePath = $this->saveCvPdf($uploadedPdf);
                $cv->setPdfPath($relativePath);

                if (!$hasText) {
                    $absolutePath = $this->getParameter('kernel.project_dir').'/public/'.$relativePath;
                    $extracted = $pdfTextExtractor->extractText($absolutePath);

                    if ($extracted !== '') {
                        $cv->setContenuOriginal($extracted);
                    } else {
                        $this->addFlash('info', 'PDF importé, mais extraction du texte impossible. Vous pouvez coller le texte manuellement.');
                    }
                }
            }

            $entityManager->persist($cv);
            $entityManager->flush();

            $this->addFlash('success', 'CV créé avec succès.');
            return $this->redirectToRoute('app_cv_manage');
        }

        return $this->render('cv/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit', name: 'app_cv_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        CvRepository $cvRepository,
        PdfTextExtractor $pdfTextExtractor,
    ): Response {
        $user = $this->getUser();
        $cv = $cvRepository->findOneBy(['user' => $user]);

        if (!$cv) {
            $this->addFlash('error', 'Aucun CV trouvé.');
            return $this->redirectToRoute('app_cv_new');
        }

        $form = $this->createForm(CvUserType::class, $cv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $uploadedPdf */
            $uploadedPdf = $form->get('cvPdf')->getData();

            if ($uploadedPdf instanceof UploadedFile) {
                $relativePath = $this->saveCvPdf($uploadedPdf);
                $cv->setPdfPath($relativePath);

                $absolutePath = $this->getParameter('kernel.project_dir').'/public/'.$relativePath;
                $extracted = $pdfTextExtractor->extractText($absolutePath);

                if ($extracted !== '') {
                    $cv->setContenuOriginal($extracted);
                    $this->addFlash('success', 'PDF importé et texte extrait.');
                } else {
                    $this->addFlash('info', 'PDF importé, mais extraction du texte impossible. Vous pouvez coller le texte manuellement.');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'CV modifié avec succès.');
            return $this->redirectToRoute('app_cv_manage');
        }

        return $this->render('cv/edit.html.twig', [
            'form' => $form->createView(),
            'cv' => $cv,
        ]);
    }

    #[Route('/ameliorer', name: 'app_cv_improve', methods: ['POST'])]
    public function improve(
        Request $request,
        EntityManagerInterface $entityManager,
        CvRepository $cvRepository,
        CvAiImprover $cvAiImprover,
    ): Response {
        if (!$this->isCsrfTokenValid('improve_cv', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_cv_manage');
        }

        $user = $this->getUser();
        $cv = $cvRepository->findOneBy(['user' => $user]);

        if (!$cv) {
            $this->addFlash('error', 'Aucun CV trouvé.');
            return $this->redirectToRoute('app_cv_new');
        }

        $improved = $cvAiImprover->improve((string) $cv->getContenuOriginal());
        if ($improved === '') {
            $this->addFlash('error', 'Veuillez ajouter du texte à votre CV avant de l’améliorer.');
            return $this->redirectToRoute('app_cv_edit');
        }

        $cv->setContenuAmeliore($improved);
        $cv->setNombreAmeliorations(($cv->getNombreAmeliorations() ?? 0) + 1);

        $entityManager->flush();
        $this->addFlash('success', 'Votre CV a été amélioré (simulation IA).');

        return $this->redirectToRoute('app_cv_view_improved');
    }

    #[Route('/view-improved', name: 'app_cv_view_improved', methods: ['GET'])]
    public function viewImproved(CvRepository $cvRepository): Response
    {
        $user = $this->getUser();
        $cv = $cvRepository->findOneBy(['user' => $user]);

        if (!$cv || !$cv->getContenuAmeliore()) {
            $this->addFlash('error', 'Améliorez votre CV d\'abord.');
            return $this->redirectToRoute('app_cv_manage');
        }

        return $this->render('cv/view_improved.html.twig', [
            'cv' => $cv,
        ]);
    }

    private function saveCvPdf(UploadedFile $uploadedPdf): string
    {
        $uploadsDir = $this->getParameter('kernel.project_dir').'/public/uploads/cv';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0775, true);
        }

        $extension = $uploadedPdf->guessExtension() ?: 'pdf';
        $safeName = bin2hex(random_bytes(8)).'.'.$extension;
        $uploadedPdf->move($uploadsDir, $safeName);

        return 'uploads/cv/'.$safeName;
    }
}
