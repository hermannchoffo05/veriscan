<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_code_id')->constrained('qr_codes')->onDelete('cascade');
            $table->string('nom_signalant')->nullable();
            $table->string('contact_signalant')->nullable();
            $table->text('description');
            $table->string('photo_preuve')->nullable();
            $table->enum('statut', ['en_cours', 'traite', 'rejete'])->default('en_cours');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('signalements');
    }
};