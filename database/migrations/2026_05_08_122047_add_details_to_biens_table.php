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
        Schema::table('biens', function (Blueprint $table) {
            $table->integer('nombre_chambres')->default(1)->after('type');
            $table->string('type_douche', 50)->nullable()->after('nombre_chambres'); // interne, externe
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            //
        });
    }
};
