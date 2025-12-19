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
        Schema::create('vote_jour_j', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vote_id')->constrained('votes')->onDelete('cascade');
            $table->foreignId('vote_event_id')->constrained('vote_events')->onDelete('cascade');
            $table->decimal('latitude_user', 10, 8);
            $table->decimal('longitude_user', 11, 8);
            $table->decimal('distance_metres', 8, 2);
            $table->string('qr_token_used');
            $table->dateTime('qr_token_expires_at');
            $table->enum('validation_status', ['success', 'gps_failed', 'outside_zone', 'token_expired']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_jour_j');
    }
};
