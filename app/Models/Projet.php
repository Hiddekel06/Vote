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
     * Définit la relation "un projet a plusieurs votes du Jour J".
     * Ces votes sont enregistrés lors de l'événement final.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Définit la relation "un projet a plusieurs votes publics".
     * Ces votes sont enregistrés pendant la période de votation publique normale.
     */
    public function votePublics(): HasMany
    {
        return $this->hasMany(VotePublic::class);
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
