<?php

namespace App\Service;

final class CvAiAssistant
{
    public function improveAndAdvise(string $originalText): CvAiResult
    {
        $clean = trim($originalText);
        if ($clean === '') {
            return new CvAiResult('', '');
        }

        $clean = str_replace(["\r\n", "\r"], "\n", $clean);
        $clean = preg_replace("/\n{3,}/", "\n\n", $clean) ?? $clean;

        $lines = array_values(array_filter(array_map('trim', explode("\n", $clean)), static fn (string $l): bool => $l !== ''));

        // Very simple structure (beginner-friendly). We keep the CV content clean.
        $summaryBullets = [
            'Profil sérieux, motivé et orienté résultats',
            'Bonne capacité d’apprentissage et esprit d’équipe',
        ];

        $skillsBullets = $this->guessSkills($lines);
        $contentBullets = $this->toBullets($lines);

        $improved = trim(
            "RÉSUMÉ\n".
            $this->renderBullets($summaryBullets)."\n\n".
            "COMPÉTENCES\n".
            $this->renderBullets($skillsBullets)."\n\n".
            "EXPÉRIENCES / PROJETS\n".
            $this->renderBullets($contentBullets)."\n\n".
            "FORMATION\n".
            $this->renderBullets(['À compléter (diplôme, établissement, dates)'])."\n"
        );

        $advice = [
            'Ajoutez des chiffres concrets (ex: +20%, 3 projets, 12 semaines).',
            'Gardez une seule police et des titres courts.',
            'Mettez vos expériences/projets les plus récents en premier.',
            'Ajoutez les mots-clés de l’offre (ATS).',
        ];

        if (!$this->containsEmail($clean)) {
            $advice[] = 'Ajoutez votre email et (optionnel) un lien LinkedIn/Portfolio.';
        }

        return new CvAiResult($improved, implode("\n", array_map(static fn (string $a): string => '• '.$a, $advice)));
    }

    /**
     * @param string[] $lines
     * @return string[]
     */
    private function toBullets(array $lines): array
    {
        $bullets = [];
        foreach ($lines as $line) {
            $line = ltrim($line, "•-* \t");
            if ($line === '') {
                continue;
            }
            $bullets[] = $line;
        }

        return array_slice($bullets, 0, 18);
    }

    /**
     * @param string[] $items
     */
    private function renderBullets(array $items): string
    {
        $items = array_values(array_filter(array_map('trim', $items), static fn (string $i): bool => $i !== ''));
        if ($items === []) {
            return "• À compléter";
        }

        return implode("\n", array_map(static fn (string $i): string => '• '.$i, $items));
    }

    /**
     * @param string[] $lines
     * @return string[]
     */
    private function guessSkills(array $lines): array
    {
        $text = mb_strtolower(implode(' ', $lines));
        $skills = [];

        foreach (['php', 'symfony', 'mysql', 'html', 'css', 'javascript', 'git', 'docker'] as $k) {
            if (str_contains($text, $k)) {
                $skills[] = strtoupper($k);
            }
        }

        if ($skills === []) {
            $skills = ['Communication', 'Organisation', 'Travail en équipe'];
        }

        return array_slice($skills, 0, 10);
    }

    private function containsEmail(string $text): bool
    {
        return (bool) preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $text);
    }
}

