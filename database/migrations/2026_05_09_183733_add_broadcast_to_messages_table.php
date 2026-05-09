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
        Schema::table('messages', function (Blueprint $table) {
            $table->string('broadcast_to')->nullable()->after('receiver_id');
            // Permettre receiver_id d'être nul pour les broadcasts
            $table->unsignedBigInteger('receiver_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('broadcast_to');
            $table->unsignedBigInteger('receiver_id')->nullable(false)->change();
        });
    }
};
