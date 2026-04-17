<?php

namespace App\Controller;

use App\Entity\Diplome;
use App\Entity\Etudiant;
use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger,
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userToPersist = $this->createSimpleUserFromType($form->get('type')->getData());

            /** @var UploadedFile|null $avatarFile */
            $avatarFile = $form->get('avatarFile')->getData();
            if ($avatarFile instanceof UploadedFile) {
                $targetDir = $this->getParameter('kernel.project_dir') . '/public/uploads/avatars';
                (new Filesystem())->mkdir($targetDir);

                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename)->lower();
                $newFilename = $safeFilename . '-' . uniqid('', true) . '.' . ($avatarFile->guessExtension() ?: 'bin');

                $avatarFile->move($targetDir, $newFilename);
                $userToPersist->setAvatarPath('uploads/avatars/' . $newFilename);
            }

            $userToPersist
                ->setNom((string) $user->getNom())
                ->setPrenom((string) $user->getPrenom())
                ->setEmail((string) $user->getEmail())
                ->setPassword($passwordHasher->hashPassword($userToPersist, (string) $user->getPassword()))
                ->setIsActive(true)
                ->setAdminRole(null)
                ->setLocalDateTime(new \DateTimeImmutable());

            $entityManager->persist($userToPersist);
            $entityManager->flush();

            $this->addFlash('success', 'Compte créé avec succès. Vous pouvez vous connecter.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'formAjout' => $form->createView(),
        ]);
    }

    #[Route('/ajoutuser', name: 'app_ajout_user', methods: ['GET'])]
    public function legacyAjoutUserRedirect(): Response
    {
        return $this->redirectToRoute('app_register');
    }

    private function createSimpleUserFromType(?string $type): User
    {
        return match ($type) {
            User::TYPE_DIPLOME => new Diplome(),
            User::TYPE_ETUDIANT => new Etudiant(),
            default => new Etudiant(),
        };
    }
}
