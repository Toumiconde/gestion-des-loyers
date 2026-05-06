<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            // Un bien appartient à un propriétaire
            $table->foreignId('proprietaire_id')->constrained()->restrictOnDelete();
            $table->string('libelle', 200);
            $table->enum('type', ['appartement','maison','studio','bureau','commerce','autre']);
            $table->text('adresse');
            $table->decimal('surface', 8, 2)->nullable();
            $table->decimal('loyer_base', 10, 2);
            $table->decimal('charges', 10, 2)->default(0);
            $table->decimal('depot_garantie', 10, 2)->nullable();
            $table->enum('statut', ['disponible','occupe','en_travaux','archive'])->default('disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};