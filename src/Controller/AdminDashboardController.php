<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\User;
use App\Form\AdminType;
use App\Repository\AdminRepository;
use App\Repository\CvRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
final class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/users', name: 'app_admin_users', methods: ['GET'])]
    public function users(Request $request, UserRepository $userRepository): Response
    {
        $query = (string) $request->query->get('q', '');

        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findNonAdmins($query),
            'q' => $query,
        ]);
    }

    #[Route('/users/{id}/toggle-active', name: 'app_admin_user_toggle_active', methods: ['POST'])]
    public function toggleUserActive(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('toggle_active_'.$user->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_users');
        }

        $user->setIsActive(!$user->isActive());
        $entityManager->flush();

        $this->addFlash('success', 'Statut mis à jour.');
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/users/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_user_'.$user->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_users');
        }

        if ($this->getUser() instanceof User && $this->getUser()->getId() === $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('app_admin_users');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé.');
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/cvs', name: 'app_admin_cvs', methods: ['GET'])]
    public function cvs(Request $request, CvRepository $cvRepository): Response
    {
        $query = (string) $request->query->get('q', '');

        return $this->render('admin/cvs.html.twig', [
            'cvs' => $cvRepository->findWithUserSearch($query),
            'q' => $query,
        ]);
    }

    #[Route('/admins', name: 'app_admin_admins', methods: ['GET'])]
    public function admins(Request $request, AdminRepository $adminRepository): Response
    {
        $query = (string) $request->query->get('q', '');

        return $this->render('admin/admins.html.twig', [
            'admins' => $adminRepository->findSearch($query),
            'q' => $query,
        ]);
    }

    #[Route('/admins/new', name: 'app_admin_admins_new', methods: ['GET', 'POST'])]
    public function newAdmin(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $admin = new Admin();
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = (string) $form->get('plainPassword')->getData();

            $admin
                ->setPassword($passwordHasher->hashPassword($admin, $plainPassword))
                ->setIsActive(true)
                ->setLocalDateTime(new \DateTimeImmutable());

            try {
                $entityManager->persist($admin);
                $entityManager->flush();

                $this->addFlash('success', 'Administrateur créé.');
                return $this->redirectToRoute('app_admin_admins');
            } catch (UniqueConstraintViolationException) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
            }
        }

        return $this->render('admin/admin_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
