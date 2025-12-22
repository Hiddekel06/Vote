<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            // Event Jour J
            $table->unsignedBigInteger('vote_event_id')->nullable()->after('projet_id');

            // Géoloc (minimale)
            $table->decimal('latitude_user', 10, 7)->nullable()->after('vote_event_id');
            $table->decimal('longitude_user', 10, 7)->nullable()->after('latitude_user');
            $table->decimal('distance_metres', 10, 2)->nullable()->after('longitude_user');

            // Statut (success / outside_zone / etc.)
            $table->string('validation_status', 30)->nullable()->after('distance_metres');

            $table->index(['vote_event_id', 'validation_status']);
            $table->index(['telephone', 'projet_id', 'vote_event_id']);
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropIndex(['vote_event_id', 'validation_status']);
            $table->dropIndex(['telephone', 'projet_id', 'vote_event_id']);

            $table->dropColumn([
                'vote_event_id',
                'latitude_user',
                'longitude_user',
                'distance_metres',
                'validation_status',
            ]);
        });
    }
};
