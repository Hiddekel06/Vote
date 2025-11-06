<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    use HasFactory;

    /**
     * Définit la relation "une soumission a un projet".
     * La liaison se fait via la colonne 'submission_token'.
     *
     * @return HasOne
     */
    public function projet(): HasOne
    {
        return $this->hasOne(Projet::class, 'submission_token', 'submission_token');
    }
}
