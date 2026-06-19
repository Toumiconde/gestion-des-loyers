<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bilans', function (Blueprint $table) {
            $table->string('mode_paiement')->nullable()->after('statut'); // virement, especes, chèque, etc.
        });
    }

    public function down(): void
    {
        Schema::table('bilans', function (Blueprint $table) {
            $table->dropColumn('mode_paiement');
        });
    }
};
