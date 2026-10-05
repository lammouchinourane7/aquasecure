<?php

namespace Database\Seeders;

use App\Models\ReseauEau;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class InfrastructureSeeder extends Seeder
{
    /**
     * Seed Zones and ReseauEau with realistic sample data so the
     * front/back infrastructure pages aren't empty.
     */
    public function run(): void
    {
        $zones = [
            [
                'nom' => 'Centre-Ville',
                'commune' => 'Tunis',
                'reseaux' => [
                    ['type' => 'Canalisation principale Avenue Habib Bourguiba', 'etat' => 'bon'],
                    ['type' => 'Réservoir Centre-Ville', 'etat' => 'bon'],
                    ['type' => 'Station de traitement Tunis Centre', 'etat' => 'degrade'],
                ],
            ],
            [
                'nom' => 'La Marsa',
                'commune' => 'Tunis',
                'reseaux' => [
                    ['type' => 'Canalisation secondaire La Marsa', 'etat' => 'bon'],
                    ['type' => 'Réservoir La Marsa Nord', 'etat' => 'degrade'],
                ],
            ],
            [
                'nom' => 'Ariana Centre',
                'commune' => 'Ariana',
                'reseaux' => [
                    ['type' => 'Canalisation Ariana Centre', 'etat' => 'bon'],
                    ['type' => 'Station de pompage Ariana', 'etat' => 'bon'],
                ],
            ],
            [
                'nom' => 'Sfax Ville',
                'commune' => 'Sfax',
                'reseaux' => [
                    ['type' => 'Canalisation principale Sfax Ville', 'etat' => 'hors_service'],
                    ['type' => 'Réservoir Sfax Ville', 'etat' => 'degrade'],
                    ['type' => 'Station de traitement Sfax', 'etat' => 'bon'],
                ],
            ],
            [
                'nom' => 'Sousse Médina',
                'commune' => 'Sousse',
                'reseaux' => [
                    ['type' => 'Canalisation historique Médina de Sousse', 'etat' => 'degrade'],
                    ['type' => 'Réservoir Sousse Médina', 'etat' => 'bon'],
                ],
            ],
            [
                'nom' => 'Bizerte Nord',
                'commune' => 'Bizerte',
                'reseaux' => [
                    ['type' => 'Canalisation Zone Industrielle Bizerte', 'etat' => 'degrade'],
                    ['type' => 'Réservoir Bizerte Nord', 'etat' => 'hors_service'],
                    ['type' => 'Station de traitement Bizerte', 'etat' => 'bon'],
                ],
            ],
        ];

        foreach ($zones as $zoneData) {
            $zone = Zone::updateOrCreate(
                ['nom' => $zoneData['nom'], 'commune' => $zoneData['commune']],
            );

            foreach ($zoneData['reseaux'] as $reseauData) {
                ReseauEau::updateOrCreate(
                    ['zone_id' => $zone->id, 'type' => $reseauData['type']],
                    ['etat' => $reseauData['etat']]
                );
            }
        }
    }
}
