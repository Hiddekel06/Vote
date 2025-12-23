<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ClassementLiveController extends Controller
{
    /**
     * Retourne le classement en JSON (polling AJAX côté front).
     */
    public function index(): JsonResponse
    {
        $limit = (int) config('classement.limit', 10);
        if ($limit <= 0) {
            $limit = 10; // garde-fou
        }

        // Cache de 2 secondes pour éviter de surcharger la DB (clé liée au limit)
        $data = Cache::remember('classement_live_' . $limit, 2, function () use ($limit) {
            $projets = Projet::withCount('votes')
                ->orderByDesc('votes_count')
                ->orderBy('nom_projet')
                ->take($limit)
                ->get(['id', 'nom_projet', 'nom_equipe']);

            return $projets->values()->map(function ($projet, int $index) {
                return [
                    'id' => $projet->id,
                    'nom_projet' => $projet->nom_projet,
                    'nom_equipe' => $projet->nom_equipe,
                    'votes' => $projet->votes_count,
                    'rank' => $index + 1,
                ];
            });
        });

        return response()->json([
            'data' => $data,
            'fetched_at' => now()->toIso8601String(),
            'limit' => $limit,
        ]);
    }
}
