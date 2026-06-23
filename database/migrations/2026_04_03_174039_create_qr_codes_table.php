<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->string('token')->unique();
            $table->string('hmac')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('nb_scans')->default(0);
            $table->enum('statut', ['actif', 'revoque'])->default('actif');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('qr_codes');
    }
};