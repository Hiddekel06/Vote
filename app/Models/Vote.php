<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vote extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     * Cette table stocke les votes du JOUR J (finale)
     *
     * @var string
     */
    protected $table = 'votes';

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = ['projet_id', 'telephone', 'ip_address', 'user_agent', 'vote_event_id',
    'latitude_user',
    'longitude_user',
    'distance_metres',
    'validation_status'];

    /**
     * Relation avec l'audit VoteJourJ
     */
    public function voteJourJ(): HasOne
    {
        return $this->hasOne(VoteJourJ::class);
    }

    /**
     * Relation avec le projet voté
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }
    public function voteEvent()
{
    return $this->belongsTo(VoteEvent::class, 'vote_event_id');
}
}


