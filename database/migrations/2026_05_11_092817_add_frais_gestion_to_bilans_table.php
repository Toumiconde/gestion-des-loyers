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
        Schema::table('bilans', function (Blueprint $table) {
            $table->decimal('frais_gestion', 15, 2)->default(0)->after('total_depenses')->comment('Montant prélevé par l\'agence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bilans', function (Blueprint $table) {
            $table->dropColumn('frais_gestion');
        });
    }
};
