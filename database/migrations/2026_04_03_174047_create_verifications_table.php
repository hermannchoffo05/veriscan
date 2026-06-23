<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_code_id')->constrained('qr_codes')->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->string('localisation')->nullable();
            $table->string('appareil')->nullable();
            $table->enum('resultat', ['authentique', 'suspect', 'contrefait'])->default('authentique');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('verifications');
    }
};