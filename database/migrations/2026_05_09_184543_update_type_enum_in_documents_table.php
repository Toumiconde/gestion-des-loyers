<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // En Laravel 11/12, on peut utiliser des requêtes brutes pour modifier l'ENUM
        DB::statement("ALTER TABLE documents MODIFY COLUMN type ENUM('contrat_pdf','quittance','photo','piece_identite','document','autre') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE documents MODIFY COLUMN type ENUM('contrat_pdf','quittance','photo','piece_identite','autre') NOT NULL");
    }
};
