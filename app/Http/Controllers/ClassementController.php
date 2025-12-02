<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClassementController extends Controller
{
    /**
     * Affiche la page de classement général et par catégories.
     *
     * @return View
     */
    public function index(Request $request)
    {
        // Catégories pour les onglets
        $categories = collect([
            (object) ['nom' => 'Étudiant', 'slug' => 'student'],
            (object) ['nom' => 'Startup',  'slug' => 'startup'],
            (object) ['nom' => 'Citoyens', 'slug' => 'other'],
        ]);

        // 🔹 IDs des projets présélectionnés (liste_preselectionnes)
        $preselectedProjectIds = DB::table('liste_preselectionnes')
            ->select('projet_id');

        // 1. Classement général = uniquement les projets présélectionnés
        // Pagination serveur : 6 éléments par page (nom de paramètre 'page_general')
        $classementGeneral = Projet::whereIn('id', $preselectedProjectIds)
            ->withCount('votes')
            ->with('secteur', 'submission')
            ->orderBy('votes_count', 'desc')
            ->orderBy('nom_projet', 'asc')
            ->paginate(6, ['*'], 'page_general');

        // 2. Classements par catégorie (toujours sur les présélectionnés)
        $classementsParCategorie = $categories->mapWithKeys(function ($categorie) use ($preselectedProjectIds) {
            // Chaque catégorie aura son propre nom de page pour la pagination (ex: page_student)
            $pageName = 'page_' . $categorie->slug;
            $projets = Projet::whereIn('id', $preselectedProjectIds)
                ->whereHas('submission', fn ($q) => $q->where('profile_type', $categorie->slug))
                ->withCount('votes')
                ->with('secteur')
                ->orderBy('votes_count', 'desc')
                ->orderBy('nom_projet', 'asc')
                ->paginate(6, ['*'], $pageName);

            return [$categorie->slug => $projets];
        });

        // Déterminer l'onglet actif en fonction des paramètres de page présents
        $activeTab = 'general';
        foreach ($categories as $categorie) {
            if ($request->query('page_' . $categorie->slug) !== null) {
                $activeTab = $categorie->slug;
                break;
            }
        }
        if ($request->query('page_general') !== null) {
            $activeTab = 'general';
        }

        // Si requête AJAX, renvoyer uniquement le partial (HTML) pour injection côté client
        if ($request->ajax()) {
            return view('partials.classement-list', compact('categories', 'classementGeneral', 'classementsParCategorie', 'activeTab'))->render();
        }

        return view('classement', compact('categories', 'classementGeneral', 'classementsParCategorie', 'activeTab'));
    }
}
