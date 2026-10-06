<?php

namespace Database\Factories;

use App\Models\ProjetRenovation;
use App\Models\ReseauEau;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjetRenovation>
 */
class ProjetRenovationFactory extends Factory
{
    public function definition(): array
    {
        $exemples = [
            'Remplacement de la conduite principale et sécurisation des vannes',
            'Décontamination biologique et réhabilitation du réservoir',
            'Modernisation de la station de pompage et automatisation',
            'Renouvellement du réseau de distribution vétuste',
            'Travaux d\'étanchéité et renforcement structurel du réservoir',
            'Remplacement des canalisations en amiante-ciment',
            'Mise en place d\'un système de désinfection et chloration avancée',
        ];

        return [
            'reseau_id' => fn () => ReseauEau::query()->inRandomOrder()->value('id') ?? ReseauEau::factory(),
            'titre' => fake()->randomElement($exemples),
            'statut' => fake()->randomElement(['planifie', 'en_cours', 'termine']),
        ];
    }

    public function planifie(): static
    {
        return $this->state(fn () => ['statut' => 'planifie']);
    }

    public function enCours(): static
    {
        return $this->state(fn () => ['statut' => 'en_cours']);
    }

    public function termine(): static
    {
        return $this->state(fn () => ['statut' => 'termine']);
    }
}
