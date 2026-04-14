<?php

namespace App\Controller;

use App\Entity\Diplome;
use App\Entity\Etudiant;
use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userToPersist = $this->createSimpleUserFromType($form->get('type')->getData());

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
