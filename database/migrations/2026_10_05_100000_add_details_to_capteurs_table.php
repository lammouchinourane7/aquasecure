<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Surveillance / Capteurs (binôme 4) : complète la table capteurs
 * avec les informations nécessaires à la surveillance (référence, seuils
 * d'alerte, statut...). Migration additive : ne casse pas les bases
 * déjà migrées par les autres membres de l'équipe.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capteurs', function (Blueprint $table) {
            $table->string('code', 50)->nullable()->unique()->after('reseau_id');
            $table->string('modele', 100)->nullable()->after('code');
            $table->string('unite', 10)->nullable()->after('type_mesure');
            $table->float('seuil_min')->nullable()->after('unite');
            $table->float('seuil_max')->nullable()->after('seuil_min');
            $table->date('date_installation')->nullable()->after('seuil_max');
            $table->string('statut', 20)->default('actif')->after('date_installation');
        });
    }

    public function down(): void
    {
        Schema::table('capteurs', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'modele', 'unite', 'seuil_min', 'seuil_max', 'date_installation', 'statut']);
        });
    }
};
