<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Releve extends Model
{
    public function capteur(): BelongsTo
    {
        return $this->belongsTo(Capteur::class);
    }
}
