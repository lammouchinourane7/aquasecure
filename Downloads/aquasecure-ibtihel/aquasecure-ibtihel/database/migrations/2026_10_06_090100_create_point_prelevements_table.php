<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Binôme 5 — Qualité de l'eau : points physiques du réseau où sont
 * prélevés les échantillons d'eau destinés à l'analyse.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_prelevements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseau_id')->constrained('reseau_eaus')->cascadeOnDelete();
            $table->string('code', 50)->unique();
            $table->string('nom', 150);
            $table->string('adresse');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('type_point', 30);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_prelevements');
    }
};
