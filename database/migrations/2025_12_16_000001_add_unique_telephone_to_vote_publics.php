<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Étape 1: Nettoyer les doublons éventuels (garde le plus ancien)
        DB::statement("
            DELETE v1 FROM vote_publics v1
            INNER JOIN vote_publics v2
            WHERE v1.id > v2.id
            AND v1.telephone = v2.telephone
            AND v1.est_verifie = 1
            AND v2.est_verifie = 1
        ");

        // Étape 2: Ajouter l'index unique
        Schema::table('vote_publics', function (Blueprint $table) {
            $table->unique('telephone', 'vote_publics_telephone_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vote_publics', function (Blueprint $table) {
            $table->dropUnique('vote_publics_telephone_unique');
        });
    }
};
