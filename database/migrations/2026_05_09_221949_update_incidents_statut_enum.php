<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE incidents MODIFY COLUMN statut ENUM('ouvert', 'en_devis', 'en_travaux', 'resolu', 'paye') NOT NULL DEFAULT 'ouvert'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE incidents MODIFY COLUMN statut ENUM('ouvert', 'en_cours', 'resolu', 'ferme') NOT NULL DEFAULT 'ouvert'");
    }
};
