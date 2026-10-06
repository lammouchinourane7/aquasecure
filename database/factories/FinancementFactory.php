<?php

namespace Database\Factories;

use App\Models\Financement;
use App\Models\ProjetRenovation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Financement>
 */
class FinancementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'projet_id' => fn () => ProjetRenovation::query()->inRandomOrder()->value('id') ?? ProjetRenovation::factory(),
            'source' => fake()->randomElement(array_keys(Financement::SOURCES)),
            'montant' => fake()->randomFloat(2, 5000, 250000),
        ];
    }
}
