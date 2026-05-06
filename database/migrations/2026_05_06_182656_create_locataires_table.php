<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locataires', function (Blueprint $table) {
            $table->id();
            // user_id nullable car un locataire peut ne pas avoir de compte
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('email', 191)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->text('adresse')->nullable();
            $table->string('piece_identite', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locataires');
    }
};