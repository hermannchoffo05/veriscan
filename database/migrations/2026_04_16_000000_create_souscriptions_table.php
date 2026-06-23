<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('souscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('plan'); // Gratuit, Starter, Pro, Entreprise
            $table->string('nom_entreprise');
            $table->string('nom_responsable');
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->string('ville')->nullable();
            $table->text('message')->nullable();
            $table->string('statut')->default('nouveau'); // nouveau, contacte, converti
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('souscriptions');
    }
};