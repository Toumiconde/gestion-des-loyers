<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained()->restrictOnDelete();
            $table->date('mois_concerne'); // ex: 2025-05-01 = mois de mai 2025
            $table->decimal('montant', 10, 2);
            $table->date('date_paiement');
            $table->enum('mode_reglement', ['especes','virement','mobile_money','cheque','autre']);
            $table->string('reference', 100)->nullable();
            $table->enum('statut', ['paye','partiel','en_retard','annule'])->default('paye');
            $table->decimal('penalite', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            // Règle métier RP-01 : un seul paiement par mois par contrat
            $table->unique(['contrat_id', 'mois_concerne'], 'unique_paiement_mois');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};