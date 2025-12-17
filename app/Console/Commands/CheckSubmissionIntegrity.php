<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Projet;

class CheckSubmissionIntegrity extends Command
{
    protected $signature = 'check:submissions';
    protected $description = 'Vérifie l\'intégrité des relations submission pour les projets';

    public function handle()
    {
        $this->info('=== Vérification de l\'intégrité des submissions ===');
        $this->newLine();

        // Projets sans submission_token
        $projetsWithoutToken = Projet::whereNull('submission_token')->get();
        $this->warn("Projets sans submission_token: " . $projetsWithoutToken->count());
        foreach ($projetsWithoutToken as $p) {
            $this->line("  - ID: {$p->id} | Nom: {$p->nom_projet}");
        }
        $this->newLine();

        // Projets avec token mais pas de submission correspondante
        $projetsWithInvalidToken = DB::select("
            SELECT p.id, p.nom_projet, p.submission_token
            FROM projets p
            LEFT JOIN submissions s ON p.submission_token = s.submission_token
            WHERE p.submission_token IS NOT NULL 
            AND s.submission_token IS NULL
        ");
        $this->warn("Projets avec token invalide: " . count($projetsWithInvalidToken));
        foreach ($projetsWithInvalidToken as $p) {
            $this->line("  - ID: {$p->id} | Nom: {$p->nom_projet} | Token: {$p->submission_token}");
        }
        $this->newLine();

        // Projets avec submission mais sans profile_type
        $submissionsWithoutProfile = DB::select("
            SELECT p.id, p.nom_projet, s.submission_token, s.profile_type
            FROM projets p
            JOIN submissions s ON p.submission_token = s.submission_token
            WHERE s.profile_type IS NULL
        ");
        $this->warn("Submissions sans profile_type: " . count($submissionsWithoutProfile));
        foreach ($submissionsWithoutProfile as $p) {
            $this->line("  - ID: {$p->id} | Nom: {$p->nom_projet} | Token: {$p->submission_token}");
        }
        $this->newLine();

        // Statistiques par profile_type
        $stats = DB::select("
            SELECT 
                COALESCE(s.profile_type, 'NULL/MISSING') as profile_type,
                COUNT(*) as count
            FROM projets p
            LEFT JOIN submissions s ON p.submission_token = s.submission_token
            GROUP BY s.profile_type
        ");
        
        $this->info("Répartition des projets par profile_type:");
        foreach ($stats as $stat) {
            $this->line("  - {$stat->profile_type}: {$stat->count} projets");
        }
        $this->newLine();

        $totalProblems = $projetsWithoutToken->count() + count($projetsWithInvalidToken) + count($submissionsWithoutProfile);
        
        if ($totalProblems > 0) {
            $this->error("⚠️ TOTAL: {$totalProblems} projets avec des problèmes de submission !");
        } else {
            $this->info("✅ Toutes les relations submission sont valides !");
        }

        return 0;
    }
}
