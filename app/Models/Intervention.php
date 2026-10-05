<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Intervention extends Model
{
    protected $fillable = ['incident_id', 'statut', 'date_intervention'];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}
