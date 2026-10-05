<?php

namespace Database\Seeders;

use App\Models\Financement;
use App\Models\ProjetRenovation;
use App\Models\ReseauEau;
use Illuminate\Database\Seeder;

class RenovationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reseaux = ReseauEau::all();
        if ($reseaux->isEmpty()) {
            return;
        }

        $projetsData = [
            [
                'reseau_index' => 0,
                'titre' => 'Remplacement de la conduite principale et sécurisation des vannes',
                'statut' => 'en_cours',
                'financements' => [
                    ['source' => 'Etat', 'montant' => 120000.00],
                    ['source' => 'municipalite', 'montant' => 45000.00],
                    ['source' => 'bailleur', 'montant' => 60000.00],
                ],
            ],
            [
                'reseau_index' => 1,
                'titre' => 'Décontamination biologique et étanchéité du réservoir',
                'statut' => 'termine',
                'financements' => [
                    ['source' => 'Etat', 'montant' => 85000.00],
                    ['source' => 'don', 'montant' => 15000.00],
                ],
            ],
            [
                'reseau_index' => 2,
                'titre' => 'Modernisation intégrale des filtres et pompes de traitement',
                'statut' => 'en_cours',
                'financements' => [
                    ['source' => 'bailleur', 'montant' => 250000.00],
                    ['source' => 'Etat', 'montant' => 100000.00],
                ],
            ],
            [
                'reseau_index' => 3,
                'titre' => 'Renouvellement des canalisations vétustes et élimination des fuites',
                'statut' => 'planifie',
                'financements' => [
                    ['source' => 'municipalite', 'montant' => 75000.00],
                ],
            ],
            [
                'reseau_index' => 4,
                'titre' => 'Réhabilitation structurelle et curage du réservoir Nord',
                'statut' => 'en_cours',
                'financements' => [
                    ['source' => 'Etat', 'montant' => 95000.00],
                    ['source' => 'municipalite', 'montant' => 30000.00],
                ],
            ],
            [
                'reseau_index' => 5,
                'titre' => 'Extension et raccordement au nouveau réseau d\'adduction',
                'statut' => 'planifie',
                'financements' => [
                    ['source' => 'Etat', 'montant' => 180000.00],
                    ['source' => 'bailleur', 'montant' => 120000.00],
                ],
            ],
        ];

        foreach ($projetsData as $pData) {
            $reseau = $reseaux->get($pData['reseau_index']) ?? $reseaux->first();

            $projet = ProjetRenovation::updateOrCreate(
                ['titre' => $pData['titre']],
                [
                    'reseau_id' => $reseau->id,
                    'statut' => $pData['statut'],
                ]
            );

            foreach ($pData['financements'] as $fData) {
                Financement::firstOrCreate(
                    [
                        'projet_id' => $projet->id,
                        'source' => $fData['source'],
                        'montant' => $fData['montant'],
                    ]
                );
            }
        }
    }
}
