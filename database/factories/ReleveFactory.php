<?php

namespace Database\Factories;

use App\Models\Capteur;
use App\Models\Releve;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Releve>
 */
class ReleveFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Relation : chaque relevé appartient à un capteur (créé via sa factory si besoin)
            'capteur_id' => Capteur::factory(),
            // La valeur dépend des seuils du capteur : ~85 % dans la plage normale,
            // ~15 % en dehors pour simuler des anomalies (fuite, surpression...).
            'valeur' => function (array $attributes) {
                $capteur = Capteur::find($attributes['capteur_id']);
                $min = $capteur?->seuil_min ?? 0;
                $max = $capteur?->seuil_max ?? 100;
                $ecart = $max - $min;

                if (fake()->boolean(15)) {
                    return fake()->boolean()
                        ? round($min - fake()->randomFloat(2, 0.05, 0.4) * $ecart, 2)
                        : round($max + fake()->randomFloat(2, 0.05, 0.4) * $ecart, 2);
                }

                return round(fake()->randomFloat(2, $min, $max), 2);
            },
            // Calculé ici aussi car les événements de modèle sont désactivés
            // pendant le seeding (WithoutModelEvents dans DatabaseSeeder).
            'hors_seuil' => fn (array $attributes) => (bool) Capteur::find($attributes['capteur_id'])?->estHorsSeuil((float) $attributes['valeur']),
            'date_releve' => fake()->dateTimeBetween('-30 days', 'now'),
            'remarque' => fake()->optional(0.2)->randomElement([
                'Contrôle de routine',
                'Valeur vérifiée sur site',
                'Capteur nettoyé avant mesure',
                'Mesure après intervention',
            ]),
        ];
    }
}
