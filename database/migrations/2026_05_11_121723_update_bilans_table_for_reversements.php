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
            $table->string('statut')->default('en_attente')->after('montant_net');
            $table->date('date_virement')->nullable()->after('statut');
            $table->string('ref_virement')->nullable()->after('date_virement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bilans', function (Blueprint $table) {
            $table->dropColumn(['statut', 'date_virement', 'ref_virement']);
        });
    }
};
