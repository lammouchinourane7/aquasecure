<?php

namespace App\Models;

use Database\Factories\PointPrelevementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PointPrelevement extends Model
{
    /** @use HasFactory<PointPrelevementFactory> */
    use HasFactory;

    protected $table = 'point_prelevements';

    protected $fillable = [
        'reseau_id',
        'code',
        'nom',
        'adresse',
        'latitude',
        'longitude',
        'type_point',
        'actif',
    ];

    public const TYPES = [
        'robinet_public' => 'Robinet public',
        'reservoir' => 'Réservoir',
        'sortie_station' => 'Sortie de station',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'actif' => 'boolean',
        ];
    }

    public function reseauEau(): BelongsTo
    {
        return $this->belongsTo(ReseauEau::class, 'reseau_id');
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(AnalyseQualite::class, 'point_id');
    }

    public function derniereAnalyse(): HasOne
    {
        return $this->hasOne(AnalyseQualite::class, 'point_id')->latestOfMany('date_prelevement');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type_point] ?? ucfirst((string) $this->type_point);
    }

    public function aDesCoordonnees(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }
}
