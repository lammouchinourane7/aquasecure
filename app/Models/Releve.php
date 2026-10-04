<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Releve extends Model
{
    protected $fillable = ['capteur_id', 'valeur', 'date_releve'];

    public function capteur(): BelongsTo
    {
        return $this->belongsTo(Capteur::class);
    }
}
