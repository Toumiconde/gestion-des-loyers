<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->boolean('is_announcement')->default(false)->after('status');
            $table->unsignedBigInteger('parent_id')->nullable()->after('is_announcement');
            $table->foreign('parent_id')->references('id')->on('feedbacks')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['is_announcement', 'parent_id']);
        });
    }
};
