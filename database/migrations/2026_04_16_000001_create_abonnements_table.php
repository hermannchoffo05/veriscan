<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abonnements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabricant_id')->nullable()->constrained('fabricants')->onDelete('set null');
            $table->string('plan'); // starter, pro, entreprise
            $table->integer('montant');
            $table->string('telephone');
            $table->string('operateur'); // mtn, orange
            $table->string('reference')->unique(); // référence interne VS-PRO-XXXXXXXX
            $table->string('campay_reference')->nullable(); // référence CamPay
            $table->string('statut')->default('pending'); // pending, successful, failed
            $table->timestamps();
        });

        // Ajouter colonne plan sur fabricants si elle n'existe pas
        if (!Schema::hasColumn('fabricants', 'plan')) {
            Schema::table('fabricants', function (Blueprint $table) {
                $table->string('plan')->default('gratuit')->after('statut');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('abonnements');
    }
};