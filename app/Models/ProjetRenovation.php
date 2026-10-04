<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjetRenovation extends Model
{
    protected $fillable = ['reseau_id', 'titre', 'statut'];

    public function reseauEau(): BelongsTo
    {
        return $this->belongsTo(ReseauEau::class, 'reseau_id');
    }

    public function financements(): HasMany
    {
        return $this->hasMany(Financement::class, 'projet_id');
    }
}
