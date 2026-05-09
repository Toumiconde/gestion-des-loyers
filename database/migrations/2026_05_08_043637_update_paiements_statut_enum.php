<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // On ajoute 'en_attente' à l'ENUM existant
        DB::statement("ALTER TABLE paiements MODIFY COLUMN statut ENUM('paye', 'partiel', 'en_retard', 'annule', 'en_attente') NOT NULL DEFAULT 'paye'");
    }

    public function down(): void
    {
        // Retour à l'état initial (on remet les 'en_attente' en 'paye' ou autre avant si nécessaire, mais ici on simplifie)
        DB::statement("ALTER TABLE paiements MODIFY COLUMN statut ENUM('paye', 'partiel', 'en_retard', 'annule') NOT NULL DEFAULT 'paye'");
    }
};
