<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fabricants', function (Blueprint $table) {
            // Ajout de la colonne pour le token de vérification
            $table->string('verification_token')->nullable()->after('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fabricants', function (Blueprint $table) {
            // Suppression de la colonne en cas de rollback
            $table->dropColumn('verification_token');
        });
    }
};