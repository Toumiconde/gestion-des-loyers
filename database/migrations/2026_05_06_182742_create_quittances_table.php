<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quittances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paiement_id')->constrained()->restrictOnDelete();
            $table->string('numero_quittance', 30)->unique();
            $table->string('pdf_path', 255)->nullable(); // chemin vers le fichier PDF généré
            $table->boolean('envoye_par_email')->default(false);
            $table->timestamp('date_envoi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quittances');
    }
};