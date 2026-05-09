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
        Schema::table('incidents', function (Blueprint $table) {
            $table->decimal('cout_estime', 15, 2)->nullable()->after('priorite');
            $table->decimal('cout_reel', 15, 2)->nullable()->after('cout_estime');
            $table->string('technicien_nom')->nullable()->after('cout_reel');
            $table->string('technicien_tel')->nullable()->after('technicien_nom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            //
        });
    }
};
