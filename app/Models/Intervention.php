<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Intervention extends Model
{
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}
