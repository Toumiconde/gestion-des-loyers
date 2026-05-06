<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->string('numero_contrat', 20)->unique(); // ex: C001-2025
            $table->foreignId('bien_id')->constrained()->restrictOnDelete();
            $table->foreignId('locataire_id')->constrained()->restrictOnDelete();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->integer('duree_mois')->nullable();
            $table->decimal('loyer', 10, 2);
            $table->decimal('depot_garantie', 10, 2);
            $table->tinyInteger('jour_echeance')->default(5); // le 5 de chaque mois
            $table->enum('statut', ['actif','resilie','expire','suspendu'])->default('actif');
            $table->enum('motif_resiliation', ['depart_volontaire','non_paiement','fin_bail','autre'])->nullable();
            $table->date('date_resiliation')->nullable();
            $table->decimal('taux_revision', 5, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};