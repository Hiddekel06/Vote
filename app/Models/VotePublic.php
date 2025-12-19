<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotePublic extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     * Cette table stocke les votes NORMAUX (votation publique)
     * À utiliser quand vote_publics est actif (période de votation classique)
     *
     * @var string
     */
    protected $table = 'vote_publics';

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'projet_id',
        'telephone',
        'email',
        'token',
        'est_verifie',
        'ip_address',
        'user_agent',
        'geo_country',
        'geo_city',
    ];

    /**
     * Relation avec le projet voté
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }
}
