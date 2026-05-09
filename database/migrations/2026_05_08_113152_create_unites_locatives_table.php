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
        Schema::create('unites_locatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('biens')->onDelete('cascade');
            $table->string('libelle'); // ex: Appartement A1, Dalle 1, etc.
            $table->integer('niveau')->default(0); // 0 pour RDC, 1 pour 1er étage, etc.
            $table->integer('nombre_chambres')->default(1);
            $table->decimal('surface', 10, 2)->nullable();
            $table->decimal('prix_loyer', 15, 2);
            $table->string('statut')->default('libre'); // libre, occupe, reserve
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unites_locatives');
    }
};
