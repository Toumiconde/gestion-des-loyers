<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bilans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proprietaire_id')->constrained()->onDelete('cascade');
            $table->integer('mois');
            $table->integer('annee');
            $table->decimal('total_revenus', 15, 2)->default(0);
            $table->decimal('total_depenses', 15, 2)->default(0);
            $table->decimal('montant_net', 15, 2)->default(0);
            $table->timestamp('envoye_le')->nullable();
            $table->timestamp('consulte_le')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            // Un seul bilan par mois/année par propriétaire
            $table->unique(['proprietaire_id', 'mois', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bilans');
    }
};
