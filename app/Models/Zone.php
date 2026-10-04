<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['nom', 'commune'];

    public function reseauEaus(): HasMany
    {
        return $this->hasMany(ReseauEau::class);
    }
}
