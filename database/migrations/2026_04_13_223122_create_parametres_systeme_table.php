<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres_systeme', function (Blueprint $table) {
            $table->id();
            $table->string('cle')->unique();
            $table->text('valeur')->nullable();
            $table->string('type')->default('string'); // string, integer, float, boolean, json
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insérer les paramètres par défaut
        DB::table('parametres_systeme')->insert([
            ['cle' => 'seuil_alerte_modere',   'valeur' => '30',  'type' => 'integer', 'description' => 'Score IA à partir duquel une alerte modérée est déclenchée', 'created_at' => now(), 'updated_at' => now()],
            ['cle' => 'seuil_alerte_eleve',     'valeur' => '50',  'type' => 'integer', 'description' => 'Score IA à partir duquel une alerte élevée est déclenchée',   'created_at' => now(), 'updated_at' => now()],
            ['cle' => 'seuil_alerte_critique',  'valeur' => '70',  'type' => 'integer', 'description' => 'Score IA à partir duquel une alerte critique est déclenchée',  'created_at' => now(), 'updated_at' => now()],
            ['cle' => 'intervalle_scoring',     'valeur' => '6',   'type' => 'integer', 'description' => 'Intervalle en heures entre chaque recalcul automatique des scores IA', 'created_at' => now(), 'updated_at' => now()],
            ['cle' => 'categories_produits',    'valeur' => '["Médicament","Alimentaire","Cosmétique","Automobile","Hygiène","Autre"]', 'type' => 'json', 'description' => 'Liste des catégories de produits disponibles', 'created_at' => now(), 'updated_at' => now()],
            ['cle' => 'alertes_actives',        'valeur' => '1',   'type' => 'boolean', 'description' => 'Activer ou désactiver les alertes automatiques', 'created_at' => now(), 'updated_at' => now()],
            ['cle' => 'nb_scans_max_avant_alerte', 'valeur' => '100', 'type' => 'integer', 'description' => 'Nombre de scans anormal déclenchant une alerte sur un QR code', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres_systeme');
    }
};