<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vote_publics', function (Blueprint $table) {
            // IP publique au moment du vote
            $table->string('ip_address', 45)->nullable()->after('telephone');

            // User-Agent brut (navigateur / OS)
            $table->text('user_agent')->nullable()->after('ip_address');

            // Localisation approximative par IP
            $table->string('geo_country', 2)->nullable()->after('user_agent');   // SN, FR, …
            $table->string('geo_city', 100)->nullable()->after('geo_country');
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropColumn([
                'ip_address',
                'user_agent',
                'geo_country',
                'geo_city',
            ]);
        });
    }
};
