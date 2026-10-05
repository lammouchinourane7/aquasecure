<?php

namespace Database\Seeders;

use App\Models\Capteur;
use App\Models\Releve;
use App\Models\ReseauEau;
use Illuminate\Database\Seeder;

/**
 * Module Surveillance / Capteurs (binôme 4).
 * Équipe chaque réseau existant de 1 à 3 capteurs, puis génère un
 * historique de relevés pour chaque capteur grâce aux factories
 * (relation ReseauEau 1-N Capteur 1-N Releve).
 */
class SurveillanceSeeder extends Seeder
{
    public function run(): void
    {
        // Évite les doublons si le seeder est relancé sans migrate:fresh
        if (Capteur::exists()) {
            return;
        }

        ReseauEau::all()->each(function (ReseauEau $reseau) {
            Capteur::factory()
                ->count(fake()->numberBetween(1, 3))
                ->for($reseau, 'reseauEau')
                ->has(Releve::factory()->count(15), 'releves')
                ->create();
        });
    }
}
