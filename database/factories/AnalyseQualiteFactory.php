<?php

namespace Database\Factories;

use App\Models\AnalyseQualite;
use App\Models\PointPrelevement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnalyseQualite>
 */
class AnalyseQualiteFactory extends Factory
{
    public function definition(): array
    {
        $valeurs = [
            'ph' => fake()->randomFloat(2, 6.8, 8.2),
            'chlore_residuel' => fake()->randomFloat(2, 0.25, 0.9),
            'turbidite' => fake()->randomFloat(2, 0.1, 3.5),
            'nitrates' => fake()->randomFloat(2, 2, 40),
            'bacteries_coliformes' => 0,
        ];

        // ~20 % des analyses présentent un problème de qualité réaliste
        if (fake()->boolean(20)) {
            $probleme = fake()->randomElement(['ph', 'chlore_bas', 'turbidite', 'nitrates', 'coliformes']);
            match ($probleme) {
                'ph' => $valeurs['ph'] = fake()->randomElement([fake()->randomFloat(2, 5.5, 6.4), fake()->randomFloat(2, 8.6, 9.3)]),
                'chlore_bas' => $valeurs['chlore_residuel'] = fake()->randomFloat(2, 0, 0.15),
                'turbidite' => $valeurs['turbidite'] = fake()->randomFloat(2, 5.5, 25),
                'nitrates' => $valeurs['nitrates'] = fake()->randomFloat(2, 52, 90),
                'coliformes' => $valeurs['bacteries_coliformes'] = fake()->numberBetween(1, 40),
            };
        }

        return array_merge($valeurs, [
            // Relation : chaque analyse appartient à un point de prélèvement
            'point_id' => PointPrelevement::factory(),
            'date_prelevement' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            // Calculé ici aussi car les événements de modèle sont désactivés
            // pendant le seeding (WithoutModelEvents dans DatabaseSeeder).
            'conforme' => AnalyseQualite::estConforme($valeurs),
            'laboratoire' => fake()->randomElement([
                'Laboratoire central SONEDE',
                'Institut Pasteur de Tunis',
                'Laboratoire régional d\'hygiène',
                'Laboratoire ONAS',
            ]),
        ]);
    }
}
