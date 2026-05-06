<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            // Polymorphique = un document peut appartenir à un bien, contrat, locataire...
            $table->string('documentable_type', 100);
            $table->unsignedBigInteger('documentable_id');
            $table->string('nom', 200);
            $table->enum('type', ['contrat_pdf','quittance','photo','piece_identite','autre']);
            $table->string('chemin', 255);
            $table->integer('taille_ko')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};