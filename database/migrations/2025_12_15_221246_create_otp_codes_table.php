<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Téléphone normalisé, ex: +221773792737
            $table->string('phone', 30)->index();

            // Projet concerné par l’OTP
            $table->unsignedBigInteger('projet_id');

            // Hash du code OTP (jamais le code en clair)
            $table->string('code_hash');

            // Date d’expiration de ce code
            $table->dateTime('expires_at');

            // Nombre d’essais (pour limiter les brute-force)
            $table->unsignedTinyInteger('attempts')->default(0);

            // Quand il a été consommé (null = pas encore utilisé)
            $table->dateTime('consumed_at')->nullable();

            // IP ayant demandé l’OTP
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            // Si tu veux renforcer la cohérence :
            // $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
