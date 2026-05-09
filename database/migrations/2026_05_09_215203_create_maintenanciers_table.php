<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenanciers', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('specialite'); // plomberie, electricite, menuiserie, etc.
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->enum('disponibilite', ['disponible', 'occupe', 'indisponible'])->default('disponible');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenanciers');
    }
};
