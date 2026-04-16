<?php

namespace App\Controller;

use App\Entity\Society;
use App\Form\SocietyRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/society')]
class SocietyRegistrationController extends AbstractController
{
    #[Route('/register', name: 'society_register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $society = new Society();
        $form = $this->createForm(SocietyRegistrationType::class, $society);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $society
                ->setPassword($passwordHasher->hashPassword($society, $society->getPassword()))
                ->setIsActive(true)
                ->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($society);
            $entityManager->flush();

            $this->addFlash('success', 'Compte societe cree avec succes. Vous pouvez vous connecter.');

            return $this->redirectToRoute('society_login');
        }

        return $this->render('society/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
