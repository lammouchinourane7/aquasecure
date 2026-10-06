<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjetRenovation extends Model
{
    use HasFactory;

    protected $fillable = ['reseau_id', 'titre', 'statut'];

    public const STATUTS = [
        'planifie' => 'Planifié',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
    ];

    public function reseauEau(): BelongsTo
    {
        return $this->belongsTo(ReseauEau::class, 'reseau_id');
    }

    public function financements(): HasMany
    {
        return $this->hasMany(Financement::class, 'projet_id');
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function getStatutBadgeColorAttribute(): string
    {
        return match ($this->statut) {
            'termine' => 'success',
            'en_cours' => 'primary',
            'planifie' => 'warning',
            default => 'neutral',
        };
    }

    public function getTotalFinancementsAttribute(): float
    {
        return (float) ($this->financements_sum_montant ?? $this->financements()->sum('montant'));
    }
}
