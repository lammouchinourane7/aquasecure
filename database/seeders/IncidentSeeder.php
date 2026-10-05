<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\Intervention;
use App\Models\ReseauEau;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    /**
     * Seed Incidents and Interventions with realistic sample data for Binôme 2.
     */
    public function run(): void
    {
        $reseaux = ReseauEau::all();

        if ($reseaux->isEmpty()) {
            return;
        }

        $sampleIncidents = [
            [
                'reseau_index' => 0,
                'type' => 'fuite',
                'gravite' => 'moyenne',
                'date_signalement' => now()->subDays(10)->toDateString(),
                'interventions' => [
                    ['statut' => 'terminee', 'date_intervention' => now()->subDays(8)->toDateString()],
                ],
            ],
            [
                'reseau_index' => 2,
                'type' => 'contamination',
                'gravite' => 'critique',
                'date_signalement' => now()->subDays(5)->toDateString(),
                'interventions' => [
                    ['statut' => 'en_cours', 'date_intervention' => now()->subDays(3)->toDateString()],
                ],
            ],
            [
                'reseau_index' => 5,
                'type' => 'coupure',
                'gravite' => 'faible',
                'date_signalement' => now()->subDays(2)->toDateString(),
                'interventions' => [
                    ['statut' => 'planifiee', 'date_intervention' => now()->addDays(2)->toDateString()],
                ],
            ],
        ];

        foreach ($sampleIncidents as $data) {
            $reseau = $reseaux[$data['reseau_index'] % $reseaux->count()];

            $incident = Incident::create([
                'reseau_id' => $reseau->id,
                'type' => $data['type'],
                'gravite' => $data['gravite'],
                'date_signalement' => $data['date_signalement'],
            ]);

            foreach ($data['interventions'] as $interData) {
                Intervention::create([
                    'incident_id' => $incident->id,
                    'statut' => $interData['statut'],
                    'date_intervention' => $interData['date_intervention'],
                ]);
            }
        }
    }
}
