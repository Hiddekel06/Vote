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
    protected $fillable = ['projet_id', 'telephone', 'ip_address', 'user_agent'];

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
}