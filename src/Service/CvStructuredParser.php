<?php

namespace App\Service;

final class CvStructuredParser
{
    /**
     * Turns plain CV text into simple sections for rendering in HTML/PDF.
     *
     * @return array<int, array{title: string, items: string[]}>
     */
    public function parse(string $text): array
    {
        $clean = trim($text);
        if ($clean === '') {
            return [];
        }

        $clean = str_replace(["\r\n", "\r"], "\n", $clean);
        $lines = array_map('trim', explode("\n", $clean));

        $sections = [];
        $currentTitle = 'Contenu';
        $currentItems = [];

        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            if ($this->isHeading($line)) {
                if ($currentItems !== []) {
                    $sections[] = [
                        'title' => $currentTitle,
                        'items' => $currentItems,
                    ];
                }

                $currentTitle = $this->normalizeHeading($line);
                $currentItems = [];
                continue;
            }

            $currentItems[] = $this->normalizeItem($line);
        }

        if ($currentItems !== []) {
            $sections[] = [
                'title' => $currentTitle,
                'items' => $currentItems,
            ];
        }

        return $sections;
    }

    private function isHeading(string $line): bool
    {
        $line = trim($line);
        $known = [
            'résumé', 'resume',
            'compétences', 'competences', 'skills',
            'expériences', 'experiences', 'projets', 'projets / expériences', 'expériences / projets',
            'formation', 'éducation', 'education',
            'langues', 'centres d’intérêt', 'centres d\'intérêt', 'contact',
        ];

        $lower = mb_strtolower(rtrim($line, ':'));
        if (in_array($lower, $known, true)) {
            return true;
        }

        // All caps headings often appear in improved text.
        $letters = preg_replace('/[^\\p{L}]+/u', '', $line) ?? '';
        if ($letters !== '' && mb_strtoupper($letters) === $letters && mb_strlen($letters) >= 4) {
            return true;
        }

        return str_ends_with($line, ':') && mb_strlen($line) <= 40;
    }

    private function normalizeHeading(string $line): string
    {
        $line = trim(rtrim($line, ':'));
        return mb_strtoupper(mb_substr($line, 0, 1)).mb_substr($line, 1);
    }

    private function normalizeItem(string $line): string
    {
        $line = trim($line);
        $line = ltrim($line, "•-* \t");
        return $line === '' ? '—' : $line;
    }
}

