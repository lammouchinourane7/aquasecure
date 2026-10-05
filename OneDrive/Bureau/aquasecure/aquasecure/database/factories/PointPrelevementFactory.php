<?php

namespace Database\Factories;

use App\Models\PointPrelevement;
use App\Models\ReseauEau;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PointPrelevement>
 */
class PointPrelevementFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(array_keys(PointPrelevement::TYPES));

        return [
            // Relation : un point appartient à un réseau existant.
            // Dans le seeder on le fixe avec ->for($reseau, 'reseauEau').
            'reseau_id' => fn () => ReseauEau::query()->inRandomOrder()->value('id'),
            'code' => 'PP-'.fake()->unique()->numerify('###'),
            'nom' => match ($type) {
                'robinet_public' => 'Fontaine '.fake()->randomElement(['de la Place', 'du Marché', 'de l\'École', 'de la Mosquée', 'du Parc']),
                'reservoir' => 'Sortie réservoir '.fake()->randomElement(['Nord', 'Sud', 'Est', 'Ouest']),
                default => 'Sortie station '.fake()->randomElement(['principale', 'secondaire']),
            },
            'adresse' => fake()->randomElement(['Rue', 'Avenue', 'Boulevard']).' '.fake()->randomElement(['Habib Bourguiba', 'de la République', 'Ibn Khaldoun', 'Farhat Hached', 'de la Liberté', 'Hédi Chaker']),
            // Coordonnées aléatoires en Tunisie (affinées par le seeder selon la commune)
            'latitude' => fake()->randomFloat(7, 33.5, 37.2),
            'longitude' => fake()->randomFloat(7, 8.5, 11.0),
            'type_point' => $type,
            'actif' => fake()->boolean(90),
        ];
    }
}
