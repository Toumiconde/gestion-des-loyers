<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->text('admin_response')->nullable()->after('auto_response');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropColumn('admin_response');
        });
    }
};
