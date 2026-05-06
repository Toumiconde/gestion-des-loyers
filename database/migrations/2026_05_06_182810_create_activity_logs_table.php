<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100); // ex: "paiement.créé", "contrat.résilié"
            $table->string('model_type', 100)->nullable(); // ex: "App\Models\Contrat"
            $table->unsignedBigInteger('model_id')->nullable(); // l'id de l'objet concerné
            $table->json('details')->nullable(); // infos supplémentaires en JSON
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};