<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Financement extends Model
{
    protected $fillable = ['projet_id', 'source', 'montant'];

    public function projetRenovation(): BelongsTo
    {
        return $this->belongsTo(ProjetRenovation::class, 'projet_id');
    }
}
