<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reversements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proprietaire_id')->constrained('proprietaires')->onDelete('cascade');
            $table->string('periode'); // Ex: "05-2026"
            $table->decimal('montant_total_loyers', 15, 2); // Somme des loyers collectés
            $table->decimal('commission_agence', 15, 2); // Part de l'agence
            $table->decimal('montant_net', 15, 2); // Ce que l'agence verse au proprio
            $table->date('date_reversement');
            $table->enum('mode_paiement', ['virement', 'especes', 'mobile_money', 'cheque'])->default('virement');
            $table->string('reference_transaction')->nullable();
            $table->enum('statut', ['en_attente', 'effectue'])->default('en_attente');
            $table->string('preuve_paiement')->nullable(); // Reçu de virement
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reversements');
    }
};
