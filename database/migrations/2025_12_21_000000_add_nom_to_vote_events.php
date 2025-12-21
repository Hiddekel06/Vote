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
        Schema::table('vote_events', function (Blueprint $table) {
            if (!Schema::hasColumn('vote_events', 'nom')) {
                $table->string('nom')->default('Événement GPS')->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vote_events', function (Blueprint $table) {
            if (Schema::hasColumn('vote_events', 'nom')) {
                $table->dropColumn('nom');
            }
        });
    }
};
