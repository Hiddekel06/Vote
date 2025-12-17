<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Projet;
use App\Models\Secteur;

class VerifyProjectCategorization extends Command
{
    protected $signature = 'check:categorization {--secteur= : Filtrer par secteur}';
    protected $description = 'Vérifie la catégorisation correcte des projets par profile_type';

    public function handle()
    {
        $this->info('=== Vérification de la catégorisation des projets ===');
        $this->newLine();

        $preselectedProjectIds = DB::table('liste_preselectionnes')
            ->where('is_finaliste', 1)
            ->pluck('projet_id');

        $query = Projet::with(['submission', 'secteur'])
            ->whereIn('id', $preselectedProjectIds);

        if ($secteurId = $this->option('secteur')) {
            $query->where('secteur_id', $secteurId);
        }

        $projets = $query->get();

        // Grouper par profile_type
        $grouped = $projets->groupBy(function ($p) {
            return $p->submission ? $p->submission->profile_type : 'SANS_SUBMISSION';
        });

        // Afficher les statistiques
        foreach ($grouped as $profileType => $projects) {
            $label = match($profileType) {
                'student' => '🎓 Étudiants',
                'startup' => '🚀 Startups',
                'other' => '👥 Citoyens',
                'SANS_SUBMISSION' => '❌ SANS SUBMISSION',
                default => "⚠️ Inconnu: {$profileType}"
            };

            $this->info("\n{$label} ({$projects->count()} projets)");
            $this->line(str_repeat('-', 80));

            foreach ($projects as $p) {
                $secteur = $p->secteur ? $p->secteur->nom : 'Secteur inconnu';
                $this->line(sprintf(
                    "  ID: %-4s | %-50s | Secteur: %s",
                    $p->id,
                    substr($p->nom_projet, 0, 50),
                    $secteur
                ));
            }
        }

        $this->newLine();
        $this->info("=== Résumé ===");
        $this->table(
            ['Profile Type', 'Nombre de projets'],
            $grouped->map(function ($projects, $type) {
                return [
                    $type,
                    $projects->count()
                ];
            })->toArray()
        );

        // Vérifier les incohérences
        $withoutSubmission = $projets->filter(fn($p) => !$p->submission);
        if ($withoutSubmission->count() > 0) {
            $this->error("\n⚠️ ATTENTION: {$withoutSubmission->count()} projets sans submission détectés!");
            $this->warn("Ces projets ne seront PAS comptabilisés correctement dans les statistiques admin.");
        } else {
            $this->info("\n✅ Tous les projets ont une submission valide!");
        }

        return 0;
    }
}
