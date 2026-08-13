<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certifications_pharmaceutiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->unique()->constrained('produits')->onDelete('cascade');
            $table->string('numero_amm');
            $table->string('laboratoire_fabricant')->nullable();
            $table->date('date_amm')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certifications_pharmaceutiques');
    }
};