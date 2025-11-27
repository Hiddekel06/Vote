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
    public function index(): View
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
        $classementGeneral = Projet::whereIn('id', $preselectedProjectIds)
            // ->where('validation_admin', 1)   // tu peux laisser commenté si tu ne l’utilises plus
            ->withCount('votes')
            ->with('secteur', 'submission')
            ->orderBy('votes_count', 'desc')
            ->orderBy('nom_projet', 'asc')
            ->get();

        // 2. Classements par catégorie (toujours sur les présélectionnés)
        $classementsParCategorie = $categories->mapWithKeys(function ($categorie) use ($preselectedProjectIds) {
            $projets = Projet::whereIn('id', $preselectedProjectIds)
                // ->where('validation_admin', 1)
                ->whereHas('submission', fn ($q) => $q->where('profile_type', $categorie->slug))
                ->withCount('votes')
                ->with('secteur')
                ->orderBy('votes_count', 'desc')
                ->orderBy('nom_projet', 'asc')
                ->get();

            return [$categorie->slug => $projets];
        });

        return view('classement', compact('categories', 'classementGeneral', 'classementsParCategorie'));
    }
}
