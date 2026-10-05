<?php

namespace Database\Factories;

use App\Models\Capteur;
use App\Models\ReseauEau;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Capteur>
 */
class CapteurFactory extends Factory
{
    /**
     * Plage normale réaliste par type de mesure : [seuil_min, seuil_max].
     */
    public const PLAGES = [
        'pression' => [2.0, 6.0],
        'debit' => [50.0, 400.0],
        'niveau' => [1.0, 8.0],
        'temperature' => [8.0, 25.0],
    ];

    public function definition(): array
    {
        $type = fake()->randomElement(array_keys(Capteur::TYPES));
        [$min, $max] = self::PLAGES[$type];

        return [
            // Relation : un capteur est rattaché à un réseau existant.
            // Dans le seeder on le fixe explicitement avec ->for($reseau, 'reseauEau').
            'reseau_id' => fn () => ReseauEau::query()->inRandomOrder()->value('id'),
            'code' => 'CAP-'.strtoupper(substr($type, 0, 3)).'-'.fake()->unique()->numerify('###'),
            'modele' => fake()->randomElement(['Endress+Hauser PMC21', 'Siemens SITRANS P', 'ABB AquaMaster', 'Krohne Optiflux', 'Vega VEGAPULS 21', 'WIKA TR10']),
            'type_mesure' => $type,
            'unite' => Capteur::TYPES[$type]['unite'],
            'seuil_min' => $min,
            'seuil_max' => $max,
            'date_installation' => fake()->dateTimeBetween('-5 years', '-1 month')->format('Y-m-d'),
            'statut' => fake()->randomElement(['actif', 'actif', 'actif', 'actif', 'en_panne', 'hors_service']),
        ];
    }

    public function actif(): static
    {
        return $this->state(fn () => ['statut' => 'actif']);
    }
}
