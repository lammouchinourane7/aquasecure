<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Creates one account per role so the app is testable right after a
     * fresh install — there is no UI path to create the first admin,
     * since public registration always assigns "citoyen". There is only
     * ever one admin account, hence the plain "Admin" name.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'citoyen@example.com'],
            ['name' => 'Citoyen Test', 'password' => Hash::make('password'), 'role' => 'citoyen', 'email_verified_at' => now()]
        );

        User::updateOrCreate(
            ['email' => 'gestionnaire@example.com'],
            ['name' => 'Gestionnaire Test', 'password' => Hash::make('password'), 'role' => 'gestionnaire', 'email_verified_at' => now()]
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin', 'email_verified_at' => now()]
        );

        $this->call(InfrastructureSeeder::class);
    }
}
