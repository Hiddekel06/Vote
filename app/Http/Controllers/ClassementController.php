<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassementController extends Controller
{
    /**
     * Affiche la page de classement général et par catégories.
     *
     * @return View
     */
    public function index(): View
    {
        // On définit statiquement les catégories pour les onglets
        $categories = collect([
            (object) ['nom' => 'Étudiant', 'slug' => 'student'],
            (object) ['nom' => 'Startup', 'slug' => 'startup'],
            (object) ['nom' => 'Citoyens', 'slug' => 'other'],
        ]);

        // 1. Classement Général
        // On récupère tous les projets validés, on compte leurs votes et on les trie.
        $classementGeneral = Projet::where('validation_admin', 1)
            ->withCount('votes') // Ajoute une colonne 'votes_count'
            ->with('secteur', 'submission') // Eager loading pour la performance
            ->orderBy('votes_count', 'desc')
            ->orderBy('nom_projet', 'asc')
            ->get();

        // 2. Classements par Catégorie
        // On crée une collection où chaque catégorie contient ses projets classés.
        $classementsParCategorie = $categories->mapWithKeys(function ($categorie) {
            $projets = Projet::where('validation_admin', 1)
                ->whereHas('submission', fn($q) => $q->where('profile_type', $categorie->slug))
                ->withCount('votes')
                ->with('secteur')
                ->orderBy('votes_count', 'desc')
                ->orderBy('nom_projet', 'asc')
                ->get();

            return [$categorie->slug => $projets];
        });

        // On passe toutes les données à la vue
        return view('classement', compact('categories', 'classementGeneral', 'classementsParCategorie'));
    }
}
