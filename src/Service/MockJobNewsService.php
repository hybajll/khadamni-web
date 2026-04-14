<?php

namespace App\Service;

final class MockJobNewsService
{
    /**
     * @return array<int, array{title: string, date: \DateTimeImmutable, summary: string}>
     */
    public function getLatest(): array
    {
        return [
            [
                'title' => 'Tendances recrutement: profils juniors recherchés',
                'date' => new \DateTimeImmutable('-1 day'),
                'summary' => 'De plus en plus d’entreprises recrutent des juniors avec de bonnes bases et une forte motivation.',
            ],
            [
                'title' => 'CV: l’impact des mots-clés (ATS)',
                'date' => new \DateTimeImmutable('-3 days'),
                'summary' => 'Les recruteurs utilisent des systèmes de tri. Pensez à adapter vos mots-clés au poste visé.',
            ],
            [
                'title' => 'Stage: comment se démarquer en 5 points',
                'date' => new \DateTimeImmutable('-6 days'),
                'summary' => 'Projets concrets, chiffres, clarté du CV, petite lettre ciblée et profil LinkedIn à jour.',
            ],
        ];
    }
}

