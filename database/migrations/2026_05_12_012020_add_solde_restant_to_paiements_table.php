<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->decimal('solde_restant', 15, 2)->default(0)->after('montant'); // Solde restant à payer pour ce mois
            $table->decimal('loyer_attendu', 15, 2)->nullable()->after('solde_restant'); // Loyer attendu au moment du paiement
            $table->decimal('total_verse', 15, 2)->default(0)->after('loyer_attendu'); // Cumul de tous les versements pour ce mois
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn(['solde_restant', 'loyer_attendu', 'total_verse']);
        });
    }
};
