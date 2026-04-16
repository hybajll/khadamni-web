<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Society;
use App\Form\OfferType;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/society')]
#[IsGranted('ROLE_SOCIETY')]
class SocietyDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'society_dashboard')]
    public function dashboard(OfferRepository $offerRepository): Response
    {
        /** @var Society $society */
        $society = $this->getUser();
        
        $offers = $offerRepository->findActiveBySociety($society);
        $totalOffers = count($offers);
        $activeOffers = count(array_filter($offers, fn($o) => $o->isActive()));

        return $this->render('society/dashboard/index.html.twig', [
            'society' => $society,
            'offers' => $offers,
            'totalOffers' => $totalOffers,
            'activeOffers' => $activeOffers,
        ]);
    }

    #[Route('/home', name: 'society_home')]
    public function home(OfferRepository $offerRepository): Response
    {
        /** @var Society $society */
        $society = $this->getUser();
        
        $offers = $offerRepository->findActiveBySociety($society);

        return $this->render('society/home.html.twig', [
            'society' => $society,
            'offers' => $offers,
        ]);
    }

    #[Route('/profile', name: 'society_profile', methods: ['GET'])]
    public function profile(): Response
    {
        /** @var Society $society */
        $society = $this->getUser();
        
        return $this->render('society/profile/show.html.twig', [
            'society' => $society,
        ]);
    }

    #[Route('/profile/edit', name: 'society_profile_edit', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Society $society */
        $society = $this->getUser();
        
        $form = $this->createFormBuilder($society)
            ->add('name')
            ->add('email')
            ->add('phone')
            ->add('address')
            ->add('domain')
            ->add('description')
            ->add('website')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profil mis a jour avec succes.');

            return $this->redirectToRoute('society_profile');
        }

        return $this->render('society/profile/edit.html.twig', [
            'form' => $form->createView(),
            'society' => $society,
        ]);
    }

    #[Route('/offers/new', name: 'society_offer_new', methods: ['GET', 'POST'])]
    public function newOffer(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Society $society */
        $society = $this->getUser();
        
        $offer = new Offer();
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offer
                ->setSociety($society)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsActive(true);

            $entityManager->persist($offer);
            $entityManager->flush();

            $this->addFlash('success', 'Offre d\'emploi creee avec succes.');

            return $this->redirectToRoute('society_dashboard');
        }

        return $this->render('society/offer/new.html.twig', [
            'form' => $form->createView(),
            'society' => $society,
        ]);
    }

    #[Route('/offers/{id}/edit', name: 'society_offer_edit', methods: ['GET', 'POST'])]
    public function editOffer(Offer $offer, Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Society $society */
        $society = $this->getUser();
        
        if ($offer->getSociety() !== $society) {
            throw $this->createAccessDeniedException('Vous n\'avez pas acces a cette offre.');
        }

        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Offre mise a jour avec succes.');

            return $this->redirectToRoute('society_dashboard');
        }

        return $this->render('society/offer/edit.html.twig', [
            'form' => $form->createView(),
            'offer' => $offer,
        ]);
    }

    #[Route('/offers/{id}', name: 'society_offer_show', methods: ['GET'])]
    public function showOffer(Offer $offer): Response
    {
        /** @var Society $society */
        $society = $this->getUser();
        
        if ($offer->getSociety() !== $society) {
            throw $this->createAccessDeniedException('Vous n\'avez pas acces a cette offre.');
        }

        return $this->render('society/offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }

    #[Route('/offers/{id}/delete', name: 'society_offer_delete', methods: ['POST'])]
    public function deleteOffer(Offer $offer, EntityManagerInterface $entityManager): Response
    {
        /** @var Society $society */
        $society = $this->getUser();
        
        if ($offer->getSociety() !== $society) {
            throw $this->createAccessDeniedException('Vous n\'avez pas acces a cette offre.');
        }

        $entityManager->remove($offer);
        $entityManager->flush();

        $this->addFlash('success', 'Offre supprimee avec succes.');

        return $this->redirectToRoute('society_dashboard');
    }
}
