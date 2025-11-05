<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vote_publics', function (Blueprint $table) {
            // Ajoute une contrainte d'unicité sur la combinaison de telephone et projet_id
            $table->unique(['telephone', 'projet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vote_publics', function (Blueprint $table) {
            // Supprime la contrainte d'unicité
            $table->dropUnique(['telephone', 'projet_id']);
        });
    }
};
