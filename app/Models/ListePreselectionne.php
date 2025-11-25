<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListePreselectionne extends Model
{
    protected $table = 'liste_preselectionnes';

    protected $fillable = [
        'projet_id', 'nom_projet', 'nom_equipe', 'resume', 'description', 'lien_prototype', 'validation_admin'
    ];

    // Relation avec Projet (si nécessaire)
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    // Getter magique pour simuler le modèle Projet
    public function __get($key)
    {
        if ($this->attributes[$key] ?? false) {
            return $this->attributes[$key];
        }

        // fallback vers relation projet si existe
        return $this->projet->$key ?? null;
    }
}
