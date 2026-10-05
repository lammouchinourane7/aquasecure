<?php

namespace App\Models;

use Database\Factories\CapteurFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Capteur extends Model
{
    /** @use HasFactory<CapteurFactory> */
    use HasFactory;

    protected $table = 'capteurs';

    protected $fillable = [
        'reseau_id',
        'code',
        'modele',
        'type_mesure',
        'unite',
        'seuil_min',
        'seuil_max',
        'date_installation',
        'statut',
    ];

    /**
     * Types de mesure gérés et l'unité attendue pour chacun.
     * Sert à la fois aux listes déroulantes et à la validation.
     */
    public const TYPES = [
        'pression' => ['label' => 'Pression', 'unite' => 'bar'],
        'debit' => ['label' => 'Débit', 'unite' => 'm3/h'],
        'niveau' => ['label' => 'Niveau', 'unite' => 'm'],
        'temperature' => ['label' => 'Température', 'unite' => '°C'],
    ];

    public const STATUTS = [
        'actif' => 'Actif',
        'en_panne' => 'En panne',
        'hors_service' => 'Hors service',
    ];

    protected function casts(): array
    {
        return [
            'seuil_min' => 'float',
            'seuil_max' => 'float',
            'date_installation' => 'date',
        ];
    }

    public function reseauEau(): BelongsTo
    {
        return $this->belongsTo(ReseauEau::class, 'reseau_id');
    }

    public function releves(): HasMany
    {
        return $this->hasMany(Releve::class, 'capteur_id');
    }

    public function dernierReleve(): HasOne
    {
        return $this->hasOne(Releve::class, 'capteur_id')->latestOfMany('date_releve');
    }

    /**
     * Indique si une valeur sort de la plage normale [seuil_min, seuil_max].
     */
    public function estHorsSeuil(float $valeur): bool
    {
        return ($this->seuil_min !== null && $valeur < $this->seuil_min)
            || ($this->seuil_max !== null && $valeur > $this->seuil_max);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type_mesure]['label'] ?? ucfirst((string) $this->type_mesure);
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? ucfirst((string) $this->statut);
    }
}
