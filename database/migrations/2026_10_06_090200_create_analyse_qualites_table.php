<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Binôme 5 — Qualité de l'eau : analyses de laboratoire
 * (physico-chimiques et bactériologiques) réalisées sur un point.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyse_qualites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('point_id')->constrained('point_prelevements')->cascadeOnDelete();
            $table->date('date_prelevement');
            $table->decimal('ph', 4, 2);
            $table->decimal('chlore_residuel', 5, 2);
            $table->decimal('turbidite', 6, 2);
            $table->decimal('nitrates', 6, 2);
            $table->unsignedInteger('bacteries_coliformes');
            $table->boolean('conforme')->default(true);
            $table->string('laboratoire', 150);
            $table->timestamps();

            // Une seule analyse par point et par jour
            $table->unique(['point_id', 'date_prelevement']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyse_qualites');
    }
};
