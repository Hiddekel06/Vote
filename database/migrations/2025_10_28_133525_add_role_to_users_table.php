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
        Schema::table('users', function (Blueprint $table) {
            //
            // On ajoute la colonne 'role' après la colonne 'email'
            // Le rôle par défaut sera 'jury' (ou 'user', 'visiteur', etc. selon votre logique)
            // L'admin sera une exception.
            $table->string('role')->after('email')->default('jury');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('role');
        });
    }
};
