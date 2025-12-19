<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteJourJ extends Model
{
    protected $table = 'vote_jour_j';

    protected $fillable = [
        'vote_id',
        'vote_event_id',
        'latitude_user',
        'longitude_user',
        'distance_metres',
        'qr_token_used',
        'qr_token_expires_at',
        'validation_status',
    ];

    protected $casts = [
        'latitude_user' => 'decimal:8',
        'longitude_user' => 'decimal:8',
        'distance_metres' => 'decimal:2',
        'qr_token_expires_at' => 'datetime',
    ];

    /**
     * Relation : Un VoteJourJ appartient à un Vote
     */
    public function vote()
    {
        return $this->belongsTo(Vote::class);
    }

    /**
     * Relation : Un VoteJourJ appartient à un VoteEvent
     */
    public function voteEvent()
    {
        return $this->belongsTo(VoteEvent::class);
    }

    /**
     * Vérifie si la validation a réussi
     */
    public function isSuccessful(): bool
    {
        return $this->validation_status === 'success';
    }

    /**
     * Vérifie si c'est une erreur GPS
     */
    public function isGpsFailed(): bool
    {
        return $this->validation_status === 'gps_failed';
    }

    /**
     * Vérifie si l'utilisateur était en dehors de la zone
     */
    public function isOutsideZone(): bool
    {
        return $this->validation_status === 'outside_zone';
    }

    /**
     * Vérifie si le token a expiré
     */
    public function isTokenExpired(): bool
    {
        return $this->validation_status === 'token_expired';
    }
}
