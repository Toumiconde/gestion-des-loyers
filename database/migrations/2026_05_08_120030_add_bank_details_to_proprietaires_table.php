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
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->string('nom_banque', 100)->nullable()->after('rib_bancaire');
            $table->string('titulaire_compte', 100)->nullable()->after('nom_banque');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proprietaires', function (Blueprint $table) {
            //
        });
    }
};
