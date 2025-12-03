<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListePreselectionne extends Model
{
    use HasFactory;

    protected $table = 'liste_preselectionnes';

    protected $fillable = [
        'projet_id',
        'snapshot',
        'encadrant_id',
    ];

    // Relation avec la table projets (clé étrangère projet_id)
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

  


}
