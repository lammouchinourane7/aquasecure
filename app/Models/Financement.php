<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Financement extends Model
{
    use HasFactory;

    protected $fillable = ['projet_id', 'source', 'montant'];

    protected $casts = [
        'montant' => 'decimal:2',
    ];

    public const SOURCES = [
        'Etat' => 'État',
        'municipalite' => 'Municipalité',
        'bailleur' => 'Bailleur',
        'don' => 'Don',
    ];

    public function projetRenovation(): BelongsTo
    {
        return $this->belongsTo(ProjetRenovation::class, 'projet_id');
    }

    public function getSourceLabelAttribute(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }
}
