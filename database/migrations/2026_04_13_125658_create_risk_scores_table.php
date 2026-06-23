<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->string('zone_code')->nullable(); // Code région Cameroun
            $table->float('score')->default(0);      // Score 0-100
            $table->integer('freq_signalements')->default(0);
            $table->float('densite_geo')->default(0);
            $table->integer('vetuste_lot')->default(0);
            $table->float('taux_scan_negatif')->default(0);
            $table->float('poids_categorie')->default(1.0);
            $table->float('score_fabricant')->default(0);
            $table->enum('niveau', ['faible', 'modere', 'eleve', 'critique'])->default('faible');
            $table->timestamp('computed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};