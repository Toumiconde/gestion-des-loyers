<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Maintenancier assigné par le gestionnaire
            $table->foreignId('maintenancier_id')->nullable()->constrained('maintenanciers')->nullOnDelete()->after('technicien_tel');
            // Devis
            $table->decimal('devis_montant', 12, 2)->nullable()->after('maintenancier_id');
            $table->text('devis_note')->nullable()->after('devis_montant');
            // Workflow validation propriétaire
            $table->enum('devis_statut', ['en_attente', 'envoye_proprio', 'accepte', 'refuse'])->default('en_attente')->after('devis_note');
            $table->text('refus_note')->nullable()->after('devis_statut');
            $table->timestamp('devis_envoye_at')->nullable()->after('refus_note');
            $table->timestamp('devis_valide_at')->nullable()->after('devis_envoye_at');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['maintenancier_id']);
            $table->dropColumn([
                'maintenancier_id', 'devis_montant', 'devis_note',
                'devis_statut', 'refus_note', 'devis_envoye_at', 'devis_valide_at'
            ]);
        });
    }
};
