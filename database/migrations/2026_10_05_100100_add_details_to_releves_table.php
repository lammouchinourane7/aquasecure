<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Surveillance / Capteurs (binôme 4) : un relevé sait s'il sort
 * de la plage normale de son capteur (calculé automatiquement par le
 * modèle Releve) et peut porter une remarque du technicien.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('releves', function (Blueprint $table) {
            $table->boolean('hors_seuil')->default(false)->after('date_releve');
            $table->string('remarque', 255)->nullable()->after('hors_seuil');
        });
    }

    public function down(): void
    {
        Schema::table('releves', function (Blueprint $table) {
            $table->dropColumn(['hors_seuil', 'remarque']);
        });
    }
};
