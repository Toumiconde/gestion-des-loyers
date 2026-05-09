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
            if (!Schema::hasColumn('proprietaires', 'signature')) {
                $table->string('signature')->nullable()->after('rib_bancaire');
            }
            if (!Schema::hasColumn('proprietaires', 'adresse_professionnelle')) {
                $table->string('adresse_professionnelle')->nullable()->after('signature');
            }
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
