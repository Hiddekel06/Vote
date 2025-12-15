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

        // 🔹 IDs des projets finalistes uniquement (is_finaliste = 1)
        $preselectedProjectIds = DB::table('liste_preselectionnes')
            ->where('is_finaliste', 1)
            ->select('projet_id');

        // Déterminer le nombre d'éléments par page (paramètre 'per_page')
        $allowedPerPage = [5, 10, 15];
        $defaultPerPage = 5;
        $perPage = (int) $request->query('per_page', $defaultPerPage);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = $defaultPerPage;
        }

        // 1. Classement général = uniquement les projets présélectionnés
        // Pagination serveur : paramétrable via 'per_page' (nom de paramètre 'page_general')
        $classementGeneral = Projet::whereIn('id', $preselectedProjectIds)
            ->withCount('votes')
            ->with('secteur', 'submission', 'listePreselectionne')
            ->orderBy('votes_count', 'desc')
            ->orderBy('nom_projet', 'asc')
            ->paginate($perPage, ['*'], 'page_general');

        // 2. Classements par catégorie (toujours sur les présélectionnés)
        $classementsParCategorie = $categories->mapWithKeys(function ($categorie) use ($preselectedProjectIds, $perPage) {
            // Chaque catégorie aura son propre nom de page pour la pagination (ex: page_student)
            $pageName = 'page_' . $categorie->slug;
            $projets = Projet::whereIn('id', $preselectedProjectIds)
                ->whereHas('submission', fn ($q) => $q->where('profile_type', $categorie->slug))
                ->withCount('votes')
                ->with('secteur', 'submission', 'listePreselectionne')
                ->orderBy('votes_count', 'desc')
                ->orderBy('nom_projet', 'asc')
                ->paginate($perPage, ['*'], $pageName);

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
            return view('partials.classement-list', compact('categories', 'classementGeneral', 'classementsParCategorie', 'activeTab', 'perPage'))->render();
        }

        return view('classement', compact('categories', 'classementGeneral', 'classementsParCategorie', 'activeTab', 'perPage'));
    }
}
