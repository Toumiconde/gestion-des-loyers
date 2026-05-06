<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained()->restrictOnDelete();
            $table->foreignId('declare_par')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titre', 200);
            $table->text('description');
            $table->enum('priorite', ['faible','moyen','urgent'])->default('moyen');
            $table->enum('statut', ['ouvert','en_cours','resolu','ferme'])->default('ouvert');
            $table->date('date_resolution')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};