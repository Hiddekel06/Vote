<?php

// C'est bien ce fichier, le contrôleur du tableau de bord administrateur.
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Secteur;
use App\Models\Projet;
use App\Models\Submission;
use App\Models\Vote;
use App\Models\VotePublic;
use App\Models\VoteEvent;
use App\Models\VoteJourJ;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Import Carbon for date manipulation
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord principal de l'administration.
     */
public function index(): View
{
     $preselectedProjectIds = DB::table('liste_preselectionnes')
         ->where('is_finaliste', 1)
         ->select('projet_id');
     $projets = Projet::whereIn('id', $preselectedProjectIds)

        ->with('secteur')
        ->withCount('votes')
        ->orderBy('votes_count', 'desc')
        ->get();

    // Statut du vote
    $voteStatus = Configuration::where('cle', 'vote_status')->first();
    $currentStatus = $voteStatus ? $voteStatus->valeur : 'inactive';

    // Statut du vote Jour J - Vérifier s'il y a des événements actifs
    $voteJourJEnabled = VoteEvent::where('is_active', true)->exists();

    // Statistiques générales VOTE PUBLIC
   $totalProjets = Projet::whereIn('id', $preselectedProjectIds)->count();
    $totalVotes = VotePublic::count();
    $totalVotants = VotePublic::distinct('telephone')->count('telephone');
    $projetEnTete = $projets->first();

    // ✅ Votes PUBLIC par Projet (Top 20)
    $projetsLesPlusVotes = Projet::whereIn('id', $preselectedProjectIds)
        ->withCount('votePublics')
        ->orderBy('vote_publics_count', 'desc')
        ->take(20)
        ->get();

    $projetLabels = $projetsLesPlusVotes->pluck('nom_projet');
    $projetData = $projetsLesPlusVotes->pluck('vote_publics_count');

    // ✅ Votes PUBLIC par type de profil (Étudiant, Startup, Citoyens)
    $votesParProfileType = Projet::whereIn('id', $preselectedProjectIds)
        ->with('submission')
        ->withCount('votePublics')
        ->get()
        ->groupBy(function ($projet) {
            return $projet->submission->profile_type ?? 'unknown';
        })
        ->map(function ($projectsGroup) {
            return $projectsGroup->sum('vote_publics_count');
        });

    $profileTypeLabels = $votesParProfileType->keys()->map(function ($type) {
        return [
            'student' => 'Étudiant',
            'startup' => 'Startup',
            'other' => 'Citoyens',
        ][$type] ?? 'Inconnu';
    });
    $profileTypeData = $votesParProfileType->values();

    // ✅ Votes PUBLIC par Catégorie (ou Secteur)
   $votesParCategorie = Secteur::with(['projets' => function ($query) use ($preselectedProjectIds) {
    $query->whereIn('id', $preselectedProjectIds)->withCount('votePublics');
    }])
    ->get()
    ->map(function ($secteur) {
        $secteur->total_votes = $secteur->projets->sum('vote_publics_count');
        return $secteur;
    });

    $categorieLabels = $votesParCategorie->pluck('nom');
    $categorieData = $votesParCategorie->pluck('total_votes');

    // --- NOUVEAU : Répartition VOTE PUBLIC par secteur pour chaque profile_type (Étudiant, Startup, Citoyens)
    // On récupère tous les secteurs avec leurs projets validés et la soumission pour déterminer le profile_type
    $secteurs = Secteur::with(['projets' => function ($query) use ($preselectedProjectIds) {
    $query->whereIn('id', $preselectedProjectIds)->with('submission')->withCount('votePublics');
    }])->get();

    $secteurLabels = $secteurs->pluck('nom')->toArray();

    $studentData = $secteurs->map(function ($s) {
        return $s->projets->filter(function ($p) {
            return (($p->submission->profile_type ?? 'other') === 'student');
        })->sum('vote_publics_count');
    })->toArray();

    $startupData = $secteurs->map(function ($s) {
        return $s->projets->filter(function ($p) {
            return (($p->submission->profile_type ?? 'other') === 'startup');
        })->sum('vote_publics_count');
    })->toArray();

    $otherData = $secteurs->map(function ($s) {
        return $s->projets->filter(function ($p) {
            $type = $p->submission->profile_type ?? 'other';
            return ($type !== 'student' && $type !== 'startup');
        })->sum('vote_publics_count');
    })->toArray();

    // --- NOUVEAU : Données VOTE PUBLIC pour l'évolution des votes par jour ---
    // Récupérer toutes les dates où il y a eu des votes publics
    $allVoteDates = VotePublic::select(DB::raw('DATE(created_at) as vote_date'))
        ->distinct()
        ->orderBy('vote_date', 'asc')
        ->pluck('vote_date');

    $dailyVoteLabels = $allVoteDates->map(fn($date) => Carbon::parse($date)->format('d/m'))->toArray();

    // Calculer le total des votes publics par jour
    $totalDailyVotesCollection = VotePublic::select(DB::raw('DATE(created_at) as vote_date'), DB::raw('count(*) as total_votes_count'))
        ->groupBy('vote_date')
        ->orderBy('vote_date', 'asc')
        ->get()
        ->keyBy('vote_date');

    $dailyVoteData = [];
    foreach ($allVoteDates as $date) {
        $dailyVoteData[] = $totalDailyVotesCollection->has($date) ? $totalDailyVotesCollection[$date]->total_votes_count : 0;
    }

    // Heatmap horaire VOTE PUBLIC (jour/heure)
    $heatmapBuckets = VotePublic::selectRaw('DAYOFWEEK(created_at) as weekday, HOUR(created_at) as hour, COUNT(*) as total')
        ->groupBy('weekday', 'hour')
        ->get();

    $heatmapDays = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam']; // DAYOFWEEK: 1=dimanche
    $heatmapHours = array_map(fn ($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . 'h', range(0, 23));
    $heatmapData = [];
    foreach ($heatmapBuckets as $row) {
        $dayIndex = max(0, (int) $row->weekday - 1); // 0-6
        $hourIndex = (int) $row->hour; // 0-23
        $heatmapData[] = [$hourIndex, $dayIndex, (int) $row->total];
    }
    $heatmapMax = $heatmapBuckets->max('total') ?? 0;

    // Récupérer les 3 projets les plus votés (VOTE PUBLIC)
    $top3Projects = Projet::whereIn('id', $preselectedProjectIds)
        ->withCount('votePublics')
        ->orderBy('vote_publics_count', 'desc')
        ->take(3)
        ->get();

    $top3ProjectsDailyData = [];
    $top3ProjectNames = [];
    $colors = ['#5470C6', '#91CC75', '#EE6666']; // Couleurs pour les séries

    foreach ($top3Projects as $index => $project) {
        $projectDailyVotes = VotePublic::where('projet_id', $project->id)
            ->select(DB::raw('DATE(created_at) as vote_date'), DB::raw('count(*) as project_daily_votes'))
            ->groupBy('vote_date')
            ->orderBy('vote_date', 'asc')
            ->get()
            ->keyBy('vote_date');

        $dailyDataForProject = [];
        foreach ($allVoteDates as $date) {
            $dailyDataForProject[] = $projectDailyVotes->has($date) ? $projectDailyVotes[$date]->project_daily_votes : 0;
        }
        $top3ProjectsDailyData[] = [
            'name' => $project->nom_projet,
            'type' => 'line',
            'stack' => 'Total', // Pour un graphique en aires empilées
            'areaStyle' => [],
            'emphasis' => ['focus' => 'series'],
            'data' => $dailyDataForProject,
            'color' => $colors[$index] ?? '#ccc' // Assigner une couleur
        ];
        $top3ProjectNames[] = $project->nom_projet;
    }

    // Préparer les données pour ECharts
    $allSeriesData = [
        [
            'name' => 'Total Votes',
            'type' => 'line',
            'stack' => 'Total',
            'areaStyle' => [],
            'emphasis' => ['focus' => 'series'],
            'data' => $dailyVoteData,
            'color' => '#FAC858' // Couleur distincte pour le total
        ]
    ];

    // Fusionner les données des 3 meilleurs projets
    $allSeriesData = array_merge($allSeriesData, $top3ProjectsDailyData);

    // Fusionner tous les noms pour la légende
    $allLegendNames = array_merge(['Total Votes'], $top3ProjectNames);
    // --- FIN NOUVEAU ---

    // --- Préparer 3 jeux de données séparés PAR PROFIL (étudiants, startup, citoyens)
    
    // 1. ÉTUDIANTS : Filtrer uniquement les projets étudiants (VOTE PUBLIC)
    $studentProjects = Projet::whereIn('id', $preselectedProjectIds)
        ->with('submission', 'listePreselectionne')
        ->withCount('votePublics')
        ->whereHas('submission', function ($query) {
            $query->where('profile_type', 'student');
        })
        ->orderBy('vote_publics_count', 'desc')
        ->take(12)
        ->get();

    $studentLabelsProjects = $studentProjects->map(function ($p) {
        $team = $p->nom_equipe ?? 'Équipe inconnue';
        $proj = $p->nom_projet ?? 'Projet inconnu';
        $label = $team . ' — ' . $proj;
        
        // Ajouter le nom de l'école
        if ($p->listePreselectionne?->snapshot) {
            $snapshot = json_decode($p->listePreselectionne->snapshot, true);
            if (isset($snapshot['champs_personnalises'])) {
                $champsPerso = is_string($snapshot['champs_personnalises'])
                    ? json_decode($snapshot['champs_personnalises'], true)
                    : $snapshot['champs_personnalises'];
                
                $schoolValue = $champsPerso['student_school'] ?? null;
                if ($schoolValue === 'OTHER') {
                    $school = $champsPerso['student_school_other'] ?? null;
                } else {
                    $school = $schoolValue;
                }
                
                if ($school) {
                    $label .= ' — ' . $school;
                }
            }
        }
        
        return $label;
    })->toArray();

    $studentDataProjects = $studentProjects->pluck('vote_publics_count')->toArray();

    // 2. STARTUPS : Filtrer uniquement les projets startup (VOTE PUBLIC)
    $startupProjects = Projet::whereIn('id', $preselectedProjectIds)
        ->with('submission', 'listePreselectionne')
        ->withCount('votePublics')
        ->whereHas('submission', function ($query) {
            $query->where('profile_type', 'startup');
        })
        ->orderBy('vote_publics_count', 'desc')
        ->take(7)
        ->get();

    $startupLabelsProjects = $startupProjects->map(function ($p) {
        $team = $p->nom_equipe ?? 'Équipe inconnue';
        $proj = $p->nom_projet ?? 'Projet inconnu';
        return $team . ' — ' . $proj;
    })->toArray();

    $startupDataProjects = $startupProjects->pluck('vote_publics_count')->toArray();

    // 3. CITOYENS (autres) : Filtrer les projets qui ne sont ni student ni startup (VOTE PUBLIC)
    $otherProjects = Projet::whereIn('id', $preselectedProjectIds)
        ->with('submission', 'listePreselectionne')
        ->withCount('votePublics')
        ->whereHas('submission', function ($query) {
            $query->whereNotIn('profile_type', ['student', 'startup']);
        })
        ->orWhereDoesntHave('submission')
        ->orderBy('vote_publics_count', 'desc')
        ->take(2)
        ->get();

    $otherLabelsProjects = $otherProjects->map(function ($p) {
        $team = $p->nom_equipe ?? 'Équipe inconnue';
        $proj = $p->nom_projet ?? 'Projet inconnu';
        return $team . ' — ' . $proj;
    })->toArray();

    $otherDataProjects = $otherProjects->pluck('vote_publics_count')->toArray();

    // Variable utilisée dans la vue (on garde le nom générique)
    $secteurLabels = $studentLabelsProjects; // Par défaut affiche les étudiants

    // ========== STATISTIQUES VOTE JOUR J ==========
    // Statistiques générales Jour J
    $totalVotesJourJ = Vote::count();
    $totalVotantsJourJ = Vote::distinct('telephone')->count('telephone');
    
    // Votes Jour J validés vs hors zone
    $votesJourJValides = VoteJourJ::where('validation_status', 'success')->count();
    $votesJourJHorsZone = VoteJourJ::where('validation_status', 'outside_zone')->count();
    
    // Top 5 projets Jour J
    $projetsTopJourJ = Projet::whereIn('id', $preselectedProjectIds)
        ->whereHas('votes')
        ->with('secteur')
        ->withCount('votes')
        ->orderBy('votes_count', 'desc')
        ->take(5)
        ->get();
    
    $projetLabelsJourJ = $projetsTopJourJ->pluck('nom_projet');
    $projetDataJourJ = $projetsTopJourJ->pluck('votes_count');
    
    // Votes Jour J par événement
    $votesParEvent = VoteEvent::with('votes')
        ->get()
        ->map(function ($event) {
            return [
                'nom' => $event->nom,
                'total' => $event->votes->count(),
                'valides' => $event->votes()->whereHas('voteJourJ', function ($q) {
                    $q->where('validation_status', 'success');
                })->count(),
                'hors_zone' => $event->votes()->whereHas('voteJourJ', function ($q) {
                    $q->where('validation_status', 'outside_zone');
                })->count()
            ];
        });
    
    $eventLabels = $votesParEvent->pluck('nom');
    $eventTotalData = $votesParEvent->pluck('total');
    $eventValidesData = $votesParEvent->pluck('valides');
    $eventHorsZoneData = $votesParEvent->pluck('hors_zone');

    // ✅ Envoi à la vue
    return view('admin.dashboard', compact(
        'projets', 'currentStatus', 'voteJourJEnabled',
        'totalProjets', 'totalVotes', 'totalVotants', 'projetEnTete',
        'profileTypeLabels', 'profileTypeData',
        'categorieLabels', 'categorieData',
        'dailyVoteLabels', 'allSeriesData', 'allLegendNames', 'top3Projects',
        'secteurLabels', 'studentLabelsProjects', 'startupLabelsProjects', 'otherLabelsProjects',
        'studentData', 'startupData', 'otherData',
        'studentDataProjects', 'startupDataProjects', 'otherDataProjects',
        'heatmapHours', 'heatmapDays', 'heatmapData', 'heatmapMax',
        'totalVotesJourJ', 'totalVotantsJourJ', 'votesJourJValides', 'votesJourJHorsZone',
        'projetsTopJourJ', 'projetLabelsJourJ', 'projetDataJourJ',
        'votesParEvent', 'eventLabels', 'eventTotalData', 'eventValidesData', 'eventHorsZoneData'
    ));
}



    /**
     * Affiche la page des statistiques de vote.
     */
    public function statistiques(): View
    {
        $preselectedProjectIds = DB::table('liste_preselectionnes')
            ->where('is_finaliste', 1)
            ->select('projet_id');  
        $totalVotes = Vote::count();
        $totalProjets = Projet::whereIn('id', $preselectedProjectIds)->count();

        // Utiliser withCount pour plus d'efficacité
        $projetsAvecVotes = Projet::whereIn('id', $preselectedProjectIds)
            ->withCount('votes')
            ->get();

        $projetGagnant = $projetsAvecVotes->sortByDesc('votes_count')->first();
        $projetPerdant = $projetsAvecVotes->sortBy('votes_count')->first();

        // Calculer les votes par secteur
        $votesParSecteur = Secteur::with(['projets' => function ($query) use ($preselectedProjectIds) {
            $query->whereIn('id', $preselectedProjectIds)->withCount('votes');
        }])
        ->get()
        ->map(function ($secteur) {
            $secteur->total_votes = $secteur->projets->sum('votes_count');
            return $secteur;
        })
        ->sortByDesc('total_votes');

        // Préparation des données pour le graphique
        $secteurLabels = $votesParSecteur->pluck('nom');
        $secteurData = $votesParSecteur->pluck('total_votes');

        return view('Admin.statistiques', compact(
            'totalVotes',
            'totalProjets',
            'projetGagnant',
            'projetPerdant',
            'votesParSecteur',
            'secteurLabels',
            'secteurData'
        ));
    }

    /**
     * Affiche la page des statistiques détaillées Vote Jour J
     */
    public function statistiquesJourJ(): View
    {
        $preselectedProjectIds = DB::table('liste_preselectionnes')
            ->where('is_finaliste', 1)
            ->select('projet_id');

        // Statistiques générales Jour J
        $totalVotesJourJ = Vote::count();
        $totalVotantsJourJ = Vote::distinct('telephone')->count('telephone');
        
        // Votes Jour J validés vs hors zone
        $votesJourJValides = VoteJourJ::where('validation_status', 'success')->count();
        $votesJourJHorsZone = VoteJourJ::where('validation_status', 'outside_zone')->count();
        
        // Top 5 projets Jour J
        $projetsTopJourJ = Projet::whereIn('id', $preselectedProjectIds)
            ->whereHas('votes')
            ->with('secteur')
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->take(5)
            ->get();
        
        $projetLabelsJourJ = $projetsTopJourJ->pluck('nom_projet');
        $projetDataJourJ = $projetsTopJourJ->pluck('votes_count');
        
        // Votes Jour J par événement
        $votesParEvent = VoteEvent::with('votes')
            ->get()
            ->map(function ($event) {
                return [
                    'nom' => $event->nom ?? 'Événement ' . $event->id,
                    'total' => $event->votes->count(),
                    'valides' => $event->votes()->whereHas('voteJourJ', function ($q) {
                        $q->where('validation_status', 'success');
                    })->count(),
                    'hors_zone' => $event->votes()->whereHas('voteJourJ', function ($q) {
                        $q->where('validation_status', 'outside_zone');
                    })->count()
                ];
            });
        
        $eventLabels = $votesParEvent->pluck('nom');
        $eventTotalData = $votesParEvent->pluck('total');
        $eventValidesData = $votesParEvent->pluck('valides');
        $eventHorsZoneData = $votesParEvent->pluck('hors_zone');

        return view('admin.statistiques-jour-j', compact(
            'totalVotesJourJ', 'totalVotantsJourJ', 'votesJourJValides', 'votesJourJHorsZone',
            'projetsTopJourJ', 'projetLabelsJourJ', 'projetDataJourJ',
            'votesParEvent', 'eventLabels', 'eventTotalData', 'eventValidesData', 'eventHorsZoneData'
        ));
    }

    /**
     * Récupère les données statistiques communes.
     *
     * @return array
     */
    private function getStatistiquesData(): array
    {
         $preselectedProjectIds = DB::table('liste_preselectionnes')
             ->where('is_finaliste', 1)
             ->select('projet_id'); 
        $totalVotes = Vote::count();
        $totalProjets = Projet::whereIn('id', $preselectedProjectIds)->count();


        // On récupère tous les projets validés, avec leur secteur et le compte de leurs votes, classés par votes.
        $projets = Projet::whereIn('id', $preselectedProjectIds)
            ->with('secteur')
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->get();

        $projetGagnant = $projets->first();

        // Calculer les votes par secteur
        $votesParSecteur = Secteur::with(['projets' => function ($query) use ($preselectedProjectIds) {
            $query->whereIn('id', $preselectedProjectIds)->withCount('votes');
        }])
        ->get()
        ->map(function ($secteur) {
            $secteur->total_votes = $secteur->projets->sum('votes_count');
            return $secteur;
        })
        ->sortByDesc('total_votes');

        return compact('totalVotes', 'totalProjets', 'projetGagnant', 'votesParSecteur', 'projets');
    }

    /**
     * Exporte les statistiques en format PDF.
     *
     * @return Response
     */
    public function exportStatistiquesPDF(): Response
    {
        $data = $this->getStatistiquesData();
        $pdf = Pdf::loadView('Admin.statistiques_pdf', $data);

        return $pdf->download('statistiques-govathon-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Exporte les statistiques en format CSV.
     *
     * @return Response
     */
    public function exportStatistiquesCSV(): Response
    {
        $data = $this->getStatistiquesData();
        $votesParSecteur = $data['votesParSecteur'];
        $totalVotes = $data['totalVotes'];
        $projets = $data['projets'];

        $fileName = 'statistiques-govathon-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($votesParSecteur, $totalVotes, $projets) {
            $file = fopen('php://output', 'w');
            // Ajout de l'UTF-8 BOM pour une meilleure compatibilité avec Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes pour la répartition par secteur
            fputcsv($file, ['Secteur', 'Nombre de Votes', 'Pourcentage du Total (%)']);

            // Données pour la répartition par secteur
            foreach ($votesParSecteur as $secteur) {
                $pourcentage = $totalVotes > 0 ? number_format(($secteur->total_votes / $totalVotes) * 100, 2) : '0.00';
                fputcsv($file, [$secteur->nom, $secteur->total_votes, $pourcentage]);
            }

            // Ligne vide pour séparer les deux tableaux
            fputcsv($file, []);

            // En-têtes pour le classement des projets
            fputcsv($file, ['Rang', 'Nom du Projet', 'Équipe', 'Secteur', 'Nombre de Votes']);

            foreach ($projets as $index => $projet) {
                fputcsv($file, [$index + 1, $projet->nom_projet, $projet->nom_equipe, $projet->secteur->nom ?? 'N/A', $projet->votes_count]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Toggle all Vote Jour J events (enable/disable)
     */
    public function toggleVoteJourJAll(Request $request)
    {
        $status = $request->input('status', 'disable');
        $isEnable = $status === 'enable';

        try {
            // Mettre à jour tous les événements
            VoteEvent::query()->update(['is_active' => $isEnable]);

            return response()->json([
                'success' => true,
                'message' => $isEnable ? 'Vote Jour J activé' : 'Vote Jour J désactivé'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }
}
