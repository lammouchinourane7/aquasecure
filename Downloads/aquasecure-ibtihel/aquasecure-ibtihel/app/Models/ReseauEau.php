<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReseauEau extends Model
{
    protected $fillable = ['zone_id', 'type', 'etat'];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'reseau_id');
    }

    public function capteurs(): HasMany
    {
        return $this->hasMany(Capteur::class, 'reseau_id');
    }

    public function projetRenovations(): HasMany
    {
        return $this->hasMany(ProjetRenovation::class, 'reseau_id');
    }

    public function pointPrelevements(): HasMany
    {
        return $this->hasMany(PointPrelevement::class, 'reseau_id');
    }
}
