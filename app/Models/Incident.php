<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    protected $fillable = ['reseau_id', 'type', 'gravite', 'date_signalement'];

    protected $casts = [
        'date_signalement' => 'date',
    ];

    public function reseauEau(): BelongsTo
    {
        return $this->belongsTo(ReseauEau::class, 'reseau_id');
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }
}
