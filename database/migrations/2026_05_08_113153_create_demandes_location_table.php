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
        Schema::create('demandes_location', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unite_locative_id')->constrained('unites_locatives')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Le locataire qui fait la demande
            $table->string('statut')->default('en_attente'); // en_attente, valide_proprietaire, valide_admin, accepte, rejete, paye
            $table->text('message')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes_location');
    }
};
