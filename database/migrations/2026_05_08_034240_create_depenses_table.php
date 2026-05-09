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
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->string('categorie'); // maintenance, salaires, loyer_agence, impots, divers
            $table->decimal('montant', 15, 2);
            $table->date('date_depense');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
