<?php

namespace App\Controller;

use App\Service\MockJobNewsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class UserHomeController extends AbstractController
{
    #[Route('/user/home', name: 'app_user_home', methods: ['GET'])]
    public function index(MockJobNewsService $mockJobNewsService): Response
    {
        $videoEmbedUrl = (string) $this->getParameter('app.motivational_video_embed_url');
        $videoEmbedUrl = trim($videoEmbedUrl) !== '' ? trim($videoEmbedUrl) : null;

        $videoPath = (string) $this->getParameter('app.motivational_video_path');
        $videoPath = trim($videoPath) !== '' ? trim($videoPath) : null;
        if ($videoPath !== null) {
            $publicVideoAbsolutePath = rtrim((string) $this->getParameter('kernel.project_dir'), '\\/') . '/public/' . ltrim($videoPath, '\\/');
            if (!is_file($publicVideoAbsolutePath)) {
                $videoPath = null;
            }
        }

        return $this->render('user/home.html.twig', [
            'user' => $this->getUser(),
            'news' => $mockJobNewsService->getLatest(),
            'video_embed_url' => $videoEmbedUrl,
            'video_path' => $videoPath,
        ]);
    }
}
