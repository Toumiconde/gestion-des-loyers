<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['biens', 'locataires', 'proprietaires', 'contrats', 'paiements', 'incidents', 'messages'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        $tables = ['biens', 'locataires', 'proprietaires', 'contrats', 'paiements', 'incidents', 'messages'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
