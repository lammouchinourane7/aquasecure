<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commentaire extends Model
{
    public function signalement(): BelongsTo
    {
        return $this->belongsTo(Signalement::class);
    }
}
