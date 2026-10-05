<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Binôme 5 : le module « Participation citoyenne » (signalements +
 * commentaires) est remplacé par le module « Qualité de l'eau »
 * (points de prélèvement + analyses qualité), conformément au modèle
 * de données validé par l'équipe.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('commentaires');
        Schema::dropIfExists('signalements');
    }

    public function down(): void
    {
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reseau_id')->nullable()->constrained('reseau_eaus')->nullOnDelete();
            $table->enum('type', ['fuite', 'contamination', 'coupure'])->default('fuite');
            $table->text('description');
            $table->string('statut');
            $table->timestamps();
        });

        Schema::create('commentaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signalement_id')->constrained('signalements')->cascadeOnDelete();
            $table->text('texte');
            $table->date('date_commentaire');
            $table->timestamps();
        });
    }
};
