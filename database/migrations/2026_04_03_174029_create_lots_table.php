<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->string('numero_lot')->unique();
            $table->date('date_fabrication');
            $table->date('date_expiration');
            $table->integer('quantite');
            $table->string('site_production')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('lots');
    }
};