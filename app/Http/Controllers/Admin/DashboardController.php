<?php

// C'est bien ce fichier, le contrôleur du tableau de bord administrateur.
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Secteur;
use App\Models\Projet;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord principal de l'administration.
     */
    public function index(): View
    {
        // On récupère tous les projets qui ont été validés pour le vote.
        // On utilise withCount('votes') pour que Laravel compte automatiquement
        // le nombre de votes associés à chaque projet.
        $projets = Projet::where('validation_admin', 1)
            ->with('secteur') // On charge aussi le secteur pour l'afficher
            ->withCount('votes') // Crée une colonne 'votes_count'
            ->orderBy('votes_count', 'desc') // On trie par nombre de votes
            ->get();

        // On récupère le statut actuel du vote
        $voteStatus = Configuration::where('cle', 'vote_status')->first();

        // Récupérer les heures de début et de fin du vote
        $voteStartTimeConfig = Configuration::where('cle', 'vote_start_time')->first();
        $voteEndTimeConfig = Configuration::where('cle', 'vote_end_time')->first();

        // Si la configuration n'existe pas, on la crée avec la valeur 'inactive' par défaut
        $currentStatus = $voteStatus ? $voteStatus->valeur : 'inactive';
        $voteStartTime = $voteStartTimeConfig ? $voteStartTimeConfig->valeur : null;
        $voteEndTime = $voteEndTimeConfig ? $voteEndTimeConfig->valeur : null;

        
    return view('admin.dashboard', compact('projets', 'currentStatus', 'voteStartTime', 'voteEndTime'));
    }

    /**
     * Affiche la page des statistiques de vote.
     */
    public function statistiques(): View
    {
        $totalVotes = Vote::count();
        $totalProjets = Projet::where('validation_admin', 1)->count();

        // Utiliser withCount pour plus d'efficacité
        $projetsAvecVotes = Projet::where('validation_admin', 1)->withCount('votes')->get();

        $projetGagnant = $projetsAvecVotes->sortByDesc('votes_count')->first();
        $projetPerdant = $projetsAvecVotes->sortBy('votes_count')->first();

        // Calculer les votes par secteur
        $votesParSecteur = Secteur::with(['projets' => function ($query) {
            $query->where('validation_admin', 1)->withCount('votes');
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
        $totalVotes = Vote::count();
        $totalProjets = Projet::where('validation_admin', 1)->count();

        // On récupère tous les projets validés, avec leur secteur et le compte de leurs votes, classés par votes.
        $projets = Projet::where('validation_admin', 1)
            ->with('secteur')
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->get();

        $projetGagnant = $projets->first();

        // Calculer les votes par secteur
        $votesParSecteur = Secteur::with(['projets' => function ($query) { // Correction: on ne charge que les projets validés
            $query->where('validation_admin', 1)->withCount('votes');
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
