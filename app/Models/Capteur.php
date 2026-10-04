<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Capteur extends Model
{
    protected $fillable = ['reseau_id', 'type_mesure'];

    public function reseauEau(): BelongsTo
    {
        return $this->belongsTo(ReseauEau::class, 'reseau_id');
    }

    public function releves(): HasMany
    {
        return $this->hasMany(Releve::class);
    }
}
