<?php

// C'est bien ce fichier, le contrôleur du tableau de bord administrateur.
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Secteur;
use App\Models\Projet;
use App\Models\Submission;
use App\Models\Vote;
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

    // Statistiques générales
   $totalProjets = Projet::whereIn('id', $preselectedProjectIds)->count();
    $totalVotes = Vote::count();
    $totalVotants = Vote::distinct('telephone')->count('telephone');
    $projetEnTete = $projets->first();

    // ✅ Votes par Projet (Top 20)
    $projetsLesPlusVotes = Projet::whereIn('id', $preselectedProjectIds)
        ->withCount('votes')
        ->orderBy('votes_count', 'desc')
        ->take(20)
        ->get();

    $projetLabels = $projetsLesPlusVotes->pluck('nom_projet');
    $projetData = $projetsLesPlusVotes->pluck('votes_count');

    // ✅ Votes par type de profil (Étudiant, Startup, Citoyens)
    $votesParProfileType = Projet::whereIn('id', $preselectedProjectIds)
        ->with('submission')
        ->withCount('votes')
        ->get()
        ->groupBy(function ($projet) {
            return $projet->submission->profile_type ?? 'unknown';
        })
        ->map(function ($projectsGroup) {
            return $projectsGroup->sum('votes_count');
        });

    $profileTypeLabels = $votesParProfileType->keys()->map(function ($type) {
        return [
            'student' => 'Étudiant',
            'startup' => 'Startup',
            'other' => 'Citoyens',
        ][$type] ?? 'Inconnu';
    });
    $profileTypeData = $votesParProfileType->values();

    // ✅ Votes par Catégorie (ou Secteur)
   $votesParCategorie = Secteur::with(['projets' => function ($query) use ($preselectedProjectIds) {
    $query->whereIn('id', $preselectedProjectIds)->withCount('votes');
    }])
    ->get()
    ->map(function ($secteur) {
        $secteur->total_votes = $secteur->projets->sum('votes_count');
        return $secteur;
    });

    $categorieLabels = $votesParCategorie->pluck('nom');
    $categorieData = $votesParCategorie->pluck('total_votes');

    // --- NOUVEAU : Répartition par secteur pour chaque profile_type (Étudiant, Startup, Citoyens)
    // On récupère tous les secteurs avec leurs projets validés et la soumission pour déterminer le profile_type
    $secteurs = Secteur::with(['projets' => function ($query) use ($preselectedProjectIds) {
    $query->whereIn('id', $preselectedProjectIds)->with('submission')->withCount('votes');
    }])->get();

    $secteurLabels = $secteurs->pluck('nom')->toArray();

    $studentData = $secteurs->map(function ($s) {
        return $s->projets->filter(function ($p) {
            return (($p->submission->profile_type ?? 'other') === 'student');
        })->sum('votes_count');
    })->toArray();

    $startupData = $secteurs->map(function ($s) {
        return $s->projets->filter(function ($p) {
            return (($p->submission->profile_type ?? 'other') === 'startup');
        })->sum('votes_count');
    })->toArray();

    $otherData = $secteurs->map(function ($s) {
        return $s->projets->filter(function ($p) {
            $type = $p->submission->profile_type ?? 'other';
            return ($type !== 'student' && $type !== 'startup');
        })->sum('votes_count');
    })->toArray();
    // Récupérer toutes les dates où il y a eu des votes
    $allVoteDates = Vote::select(DB::raw('DATE(created_at) as vote_date'))
        ->distinct()
        ->orderBy('vote_date', 'asc')
        ->pluck('vote_date');

    $dailyVoteLabels = $allVoteDates->map(fn($date) => Carbon::parse($date)->format('d/m'))->toArray();

    // Calculer le total des votes par jour
    $totalDailyVotesCollection = Vote::select(DB::raw('DATE(created_at) as vote_date'), DB::raw('count(*) as total_votes_count'))
        ->groupBy('vote_date')
        ->orderBy('vote_date', 'asc')
        ->get()
        ->keyBy('vote_date');

    $dailyVoteData = [];
    foreach ($allVoteDates as $date) {
        $dailyVoteData[] = $totalDailyVotesCollection->has($date) ? $totalDailyVotesCollection[$date]->total_votes_count : 0;
    }

    // Récupérer les 3 projets les plus votés
    $top3Projects = Projet::whereIn('id', $preselectedProjectIds)
        ->withCount('votes')
        ->orderBy('votes_count', 'desc')
        ->take(3)
        ->get();

    $top3ProjectsDailyData = [];
    $top3ProjectNames = [];
    $colors = ['#5470C6', '#91CC75', '#EE6666']; // Couleurs pour les séries

    foreach ($top3Projects as $index => $project) {
        $projectDailyVotes = Vote::where('projet_id', $project->id)
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

    // --- Préparer un jeu de données par PROJET (labels 'Equipe — Projet')
    $chartProjects = Projet::whereIn('id', $preselectedProjectIds)
        ->with('submission')
        ->withCount('votes')
        ->orderBy('votes_count', 'desc')
        ->take(20)
        ->get();

    $projectLabels = $chartProjects->map(function ($p) {
        $team = $p->nom_equipe ?? 'Équipe inconnue';
        $proj = $p->nom_projet ?? 'Projet inconnu';
        return $team . ' — ' . $proj;
    })->toArray();

    // Construire les tableaux où chaque projet attribue son total aux bons profile_type
    $projectStudentData = $chartProjects->map(function ($p) {
        return ($p->submission->profile_type ?? 'other') === 'student' ? (int) $p->votes_count : 0;
    })->toArray();

    $projectStartupData = $chartProjects->map(function ($p) {
        return ($p->submission->profile_type ?? 'other') === 'startup' ? (int) $p->votes_count : 0;
    })->toArray();

    $projectOtherData = $chartProjects->map(function ($p) {
        $type = $p->submission->profile_type ?? 'other';
        return ($type !== 'student' && $type !== 'startup') ? (int) $p->votes_count : 0;
    })->toArray();

    // ✅ Envoi à la vue
    return view('admin.dashboard', compact(
        'projets', 'currentStatus',
        'totalProjets', 'totalVotes', 'totalVotants', 'projetEnTete',
        'profileTypeLabels', 'profileTypeData',
        'categorieLabels', 'categorieData',
        'dailyVoteLabels', 'allSeriesData', 'allLegendNames', 'top3Projects',
        'secteurLabels', 'studentData', 'startupData', 'otherData'
    ));
}
,
        'projectLabels', 'projectStudentData', 'projectStartupData', 'projectOtherData'


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
}
