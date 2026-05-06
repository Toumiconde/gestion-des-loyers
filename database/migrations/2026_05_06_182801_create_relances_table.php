<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained()->cascadeOnDelete();
            $table->enum('niveau', ['niveau_1','niveau_2','niveau_3']);
            $table->enum('canal', ['email','sms','email_sms']);
            $table->enum('statut', ['envoyee','acquittee','echouee'])->default('envoyee');
            $table->timestamp('date_envoi');
            $table->foreignId('acquitte_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_acquittement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relances');
    }
};