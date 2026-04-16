<?php

namespace App\Controller;

use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/society')]
class SocietyHomeController extends AbstractController
{
    #[Route('/', name: 'society_index')]
    public function index(OfferRepository $offerRepository): Response
    {
        $recentOffers = $offerRepository->findAllActive();
        $recentOffers = array_slice($recentOffers, 0, 6);

        return $this->render('society/index.html.twig', [
            'offers' => $recentOffers,
        ]);
    }
}
