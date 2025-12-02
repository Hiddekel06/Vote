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
        Schema::table('liste_preselectionnes', function (Blueprint $table) {
            // Add a nullable text column to store an external demo video URL (e.g. Google Drive / YouTube)
            $table->text('video_demonstration')->nullable()->comment('URL vers la video de demonstration (Drive/YouTube)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('liste_preselectionnes', function (Blueprint $table) {
            // Drop the column if it exists
            if (Schema::hasColumn('liste_preselectionnes', 'video_demonstration')) {
                $table->dropColumn('video_demonstration');
            }
        });
    }
};
