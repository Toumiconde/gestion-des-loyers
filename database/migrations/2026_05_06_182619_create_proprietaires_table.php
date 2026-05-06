<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proprietaires', function (Blueprint $table) {
            $table->id();
            // Lien vers la table users — chaque propriétaire EST un user
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('telephone', 20)->nullable();
            $table->text('adresse')->nullable();
            $table->string('rib_bancaire', 100)->nullable();
            $table->timestamps(); // crée created_at et updated_at automatiquement
        });
    }

    // Cette fonction sert à annuler la migration si besoin
    public function down(): void
    {
        Schema::dropIfExists('proprietaires');
    }
};