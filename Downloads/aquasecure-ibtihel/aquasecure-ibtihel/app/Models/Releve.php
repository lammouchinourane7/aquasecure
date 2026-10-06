<?php

namespace App\Models;

use Database\Factories\ReleveFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Releve extends Model
{
    /** @use HasFactory<ReleveFactory> */
    use HasFactory;

    protected $table = 'releves';

    protected $fillable = ['capteur_id', 'valeur', 'date_releve', 'remarque'];

    protected function casts(): array
    {
        return [
            'valeur' => 'float',
            'date_releve' => 'datetime',
            'hors_seuil' => 'boolean',
        ];
    }

    /**
     * hors_seuil n'est jamais saisi à la main : il est recalculé à chaque
     * enregistrement à partir des seuils du capteur associé.
     */
    protected static function booted(): void
    {
        static::saving(function (Releve $releve) {
            $capteur = $releve->capteur()->first();
            $releve->hors_seuil = $capteur ? $capteur->estHorsSeuil((float) $releve->valeur) : false;
        });
    }

    public function capteur(): BelongsTo
    {
        return $this->belongsTo(Capteur::class, 'capteur_id');
    }
}
