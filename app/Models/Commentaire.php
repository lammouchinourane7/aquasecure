<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commentaire extends Model
{
    protected $fillable = ['signalement_id', 'texte', 'date_commentaire'];

    public function signalement(): BelongsTo
    {
        return $this->belongsTo(Signalement::class);
    }
}
