<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    /**
     * Relation vers l'entrée de la table `liste_preselectionnes` associée au projet.
     */
    public function listePreselectionne()
    {
        return $this->hasOne(\App\Models\ListePreselectionne::class, 'projet_id');
    }

    /**
     * Définit la relation "un projet appartient à une soumission".
     * La liaison se fait via la colonne 'submission_token'.
     *
     * @return BelongsTo
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_token', 'submission_token');
    }
}
