<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListePreselectionne extends Model
{
    use HasFactory;

    protected $table = 'liste_preselectionnes';

    /**
     * Champs autorisés en mass-assignment.
     * La table contient principalement un snapshot JSON et une référence projet_id.
     */
    protected $fillable = [
        'projet_id',
        'snapshot',
        'video_demonstration',
    ];

    /**
     * Relation vers le projet lié.
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

}
