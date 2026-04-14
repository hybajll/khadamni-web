<?php

namespace App\Service;

final class CvAiImprover
{
    public function improve(string $originalText): string
    {
        $clean = trim($originalText);
        if ($clean === '') {
            return '';
        }

        $clean = str_replace(["\r\n", "\r"], "\n", $clean);
        $clean = preg_replace("/\n{3,}/", "\n\n", $clean) ?? $clean;

        $lines = array_values(array_filter(array_map('trim', explode("\n", $clean)), static fn (string $l): bool => $l !== ''));
        $bullets = array_map(static fn (string $l): string => '• '.$l, $lines);

        return trim(
            "=== CV (Version améliorée - simulation IA) ===\n\n".
            "Résumé\n".
            "• Profil sérieux, motivé et orienté résultats\n".
            "• Bonne capacité d’apprentissage et esprit d’équipe\n\n".
            "Contenu structuré\n".
            implode("\n", $bullets)."\n\n".
            "Conseil\n".
            "• Ajoutez des chiffres (durée, outils, résultats) pour rendre votre CV plus convaincant.\n"
        );
    }
}

