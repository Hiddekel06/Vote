<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Categorie;
use Illuminate\View\View;
use App\Models\Projet;

class ProjetController extends Controller
{
    /**
     * Affiche la page de classement des projets, triés par nombre de votes.
     *
     * @return View
     */
    public function index(string $profileType): View
    {
        // On déduit le nom de la catégorie à partir du profile_type pour l'affichage
        $categorieNom = match ($profileType) {
            'student' => 'Étudiant',
            'startup' => 'Startup',
            'other' => 'Autre',
        };
        $categorie = (object)['nom' => $categorieNom, 'slug' => $profileType];

        // On récupère les projets validés, on compte leurs votes
        // et on les trie par ordre décroissant de votes.
        // La méthode withCount('votes') ajoutera une colonne 'votes_count'
        // à chaque projet.
        $projets = Projet::where('validation_admin', 1)
                         ->whereHas('submission', fn($q) => $q->where('profile_type', $profileType))
                         ->withCount('votes')
                         
                         ->with('secteur') // On charge la relation 'secteur' pour l'afficher dans la vue
                         ->orderBy('votes_count', 'desc')
                         ->orderBy('nom_projet', 'asc') // Tri secondaire pour la cohérence
                         ->paginate(20); // 20 projets par page

        // On passe les projets à la vue
        return view('classement', compact('projets', 'categorie'));
    }
}
