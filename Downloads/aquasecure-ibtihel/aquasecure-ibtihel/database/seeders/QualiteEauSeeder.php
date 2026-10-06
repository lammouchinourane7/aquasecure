<?php

namespace Database\Seeders;

use App\Models\AnalyseQualite;
use App\Models\PointPrelevement;
use App\Models\ReseauEau;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

/**
 * Binôme 5 — Qualité de l'eau.
 * Crée 1 à 2 points de prélèvement par réseau, chacun avec un
 * historique mensuel d'analyses, via les factories
 * (relation ReseauEau 1-N PointPrelevement 1-N AnalyseQualite).
 */
class QualiteEauSeeder extends Seeder
{
    /**
     * Coordonnées approximatives des communes du jeu de données,
     * pour placer les points au bon endroit sur la carte.
     */
    private const COORDONNEES = [
        'Tunis' => [36.8065, 10.1815],
        'Ariana' => [36.8665, 10.1647],
        'Sfax' => [34.7406, 10.7603],
        'Sousse' => [35.8256, 10.6084],
        'Bizerte' => [37.2744, 9.8739],
    ];

    public function run(): void
    {
        // Évite les doublons si le seeder est relancé sans migrate:fresh
        if (PointPrelevement::exists()) {
            return;
        }

        ReseauEau::with('zone')->get()->each(function (ReseauEau $reseau) {
            [$lat, $lng] = self::COORDONNEES[$reseau->zone?->commune] ?? [36.8065, 10.1815];

            PointPrelevement::factory()
                ->count(fake()->numberBetween(1, 2))
                ->for($reseau, 'reseauEau')
                ->state(fn () => [
                    'latitude' => round($lat + fake()->randomFloat(4, -0.03, 0.03), 7),
                    'longitude' => round($lng + fake()->randomFloat(4, -0.03, 0.03), 7),
                ])
                ->has(
                    // Une analyse par mois sur les 6 derniers mois (dates uniques par point)
                    AnalyseQualite::factory()
                        ->count(6)
                        ->state(new Sequence(fn (Sequence $s) => [
                            'date_prelevement' => now()->subMonths($s->index % 6)->subDays(fake()->numberBetween(0, 20))->format('Y-m-d'),
                        ])),
                    'analyses'
                )
                ->create();
        });
    }
}
