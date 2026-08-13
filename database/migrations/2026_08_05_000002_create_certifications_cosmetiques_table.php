<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certifications_cosmetiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->unique()->constrained('produits')->onDelete('cascade');
            $table->text('liste_inci');
            $table->string('certificat_conformite')->nullable();
            $table->date('date_certification')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certifications_cosmetiques');
    }
};