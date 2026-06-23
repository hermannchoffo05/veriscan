<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fabricants', function (Blueprint $table) {
            $table->id();
            $table->string('nom_entreprise');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->string('pays')->default('Cameroun');
            $table->string('logo')->nullable();
            $table->enum('statut', ['actif', 'suspendu', 'en_attente'])->default('en_attente');
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('fabricants');
    }
};