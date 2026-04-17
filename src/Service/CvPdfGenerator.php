<?php

namespace App\Service;

use App\Entity\Cv;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class CvPdfGenerator
{
    public function __construct(
        private readonly Environment $twig,
        private readonly CvStructuredParser $structuredParser,
    ) {
    }

    public function downloadResponse(Cv $cv, bool $useImproved = true): Response
    {
        $content = $useImproved ? (string) $cv->getContenuAmeliore() : (string) $cv->getContenuOriginal();
        $sections = $this->structuredParser->parse($content);

        $html = $this->twig->render('cv/pdf.html.twig', [
            'cv' => $cv,
            'user' => $cv->getUser(),
            'sections' => $sections,
            'generated_at' => new \DateTimeImmutable(),
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        $filename = $useImproved ? 'cv-ameliore.pdf' : 'cv.pdf';
        if ($cv->getUser()) {
            $name = trim((string) $cv->getUser()->getPrenom().' '.(string) $cv->getUser()->getNom());
            if ($name !== '') {
                $filename = ($useImproved ? 'CV-'.$name.'-ameliore.pdf' : 'CV-'.$name.'.pdf');
                $filename = preg_replace('/[^A-Za-z0-9._\\- ]+/', '', $filename) ?? $filename;
                $filename = str_replace(' ', '_', $filename);
            }
        }

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]
        );
    }
}

