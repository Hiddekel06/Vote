<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Projet extends Model
{
    use HasFactory;

    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    /**
     * Définit la relation "un projet a plusieurs votes".
     */
    public function votes(): HasMany
    {
        // On ne compte que les votes qui ont été vérifiés par OTP
        return $this->hasMany(Vote::class);
    }
}
