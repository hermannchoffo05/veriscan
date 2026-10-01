<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Corrige un bug : AdminFabricantsController::rejeter() écrit
     * statut = 'rejete' alors que l'enum SQL d'origine ne contenait que
     * 'actif', 'suspendu', 'en_attente'. Sur un MySQL en mode strict,
     * cette mise à jour échoue (ou tronque silencieusement la valeur
     * hors mode strict) : le rejet d'un fabricant est donc actuellement
     * cassé en base. On élargit l'enum pour couvrir la valeur réellement
     * utilisée par le code.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE fabricants MODIFY statut ENUM('actif', 'suspendu', 'en_attente', 'rejete') DEFAULT 'en_attente'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE fabricants MODIFY statut ENUM('actif', 'suspendu', 'en_attente') DEFAULT 'en_attente'");
        }
    }
};
