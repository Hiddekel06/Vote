<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Projet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListePreselectionnesSeeder extends Seeder
{
    public function run()
    {
        // Récupère tous les projets validés
        $projets = Projet::where('validation_admin', 1)->get();

        foreach ($projets as $projet) {
            DB::table('liste_preselectionnes')->updateOrInsert(
                ['projet_id' => $projet->id],
                [
                    'snapshot' => json_encode([
                        'id' => $projet->id,
                        'nom_equipe' => $projet->nom_equipe,
                        'nombre_membres' => $projet->nombre_membres,
                        'secteur_id' => $projet->secteur_id,
                        'theme_id' => $projet->theme_id,
                        'nom_projet' => $projet->nom_projet,
                        'resume' => $projet->resume,
                        'description' => $projet->description,
                        'a_prototype' => (bool) $projet->a_prototype,
                        'lien_prototype' => $projet->lien_prototype,
                        'validation_admin' => (bool) $projet->validation_admin,
                        'etat' => 0,
                        'champs_personnalises' => json_decode($projet->champs_personnalises, true),
                        'submission_token' => $projet->submission_token ?? (string) Str::uuid(),
                        'adresse_ip' => $projet->adresse_ip,
                        'user_agent' => $projet->user_agent,
                        'created_at' => $projet->created_at,
                        'updated_at' => $projet->updated_at,
                        'etat_id' => $projet->etat_id,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'encadrant_id' => null,
                ]
            );
        }
    }
}

