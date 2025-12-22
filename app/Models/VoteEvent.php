<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteEvent extends Model
{
    protected $fillable = [
        'nom',
        'latitude',
        'longitude',
        'rayon_metres',
        'qr_secret',
        'is_active',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    /**
     * Relation : Un VoteEvent a plusieurs VoteJourJ
     */
    public function voteJourJ()
    {
        return $this->hasMany(VoteJourJ::class);
    }

    /**
     * Relation : Un VoteEvent a plusieurs Vote (via VoteJourJ)
     */
    public function votes()
    {
        return $this->hasManyThrough(Vote::class, VoteJourJ::class, 'vote_event_id', 'id', 'id', 'vote_id');
    }

    /**
     * Vérifie si l'événement est actuellement actif
     */
    public function isLive(): bool
    {
        return $this->is_active && now()->between($this->date_debut, $this->date_fin);
    }

}
