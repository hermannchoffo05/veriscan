<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fabricants', function (Blueprint $table) {
            if (!Schema::hasColumn('fabricants', 'plan')) {
                $table->string('plan')->default('gratuit')->after('statut');
            }
            if (!Schema::hasColumn('fabricants', 'plan_expire_le')) {
                $table->timestamp('plan_expire_le')->nullable()->after('plan');
            }
            if (!Schema::hasColumn('fabricants', 'essai_deja_utilise')) {
                $table->boolean('essai_deja_utilise')->default(false)->after('plan_expire_le');
            }
            if (!Schema::hasColumn('fabricants', 'qrcodes_generes_mois')) {
                $table->unsignedInteger('qrcodes_generes_mois')->default(0)->after('essai_deja_utilise');
            }
            if (!Schema::hasColumn('fabricants', 'rapports_generes_mois')) {
                $table->unsignedInteger('rapports_generes_mois')->default(0)->after('qrcodes_generes_mois');
            }
            if (!Schema::hasColumn('fabricants', 'usage_mois_reference')) {
                $table->string('usage_mois_reference', 7)->nullable()->after('rapports_generes_mois');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fabricants', function (Blueprint $table) {
            $table->dropColumn([
                'plan_expire_le', 'essai_deja_utilise',
                'qrcodes_generes_mois', 'rapports_generes_mois', 'usage_mois_reference',
            ]);
            // 'plan' volontairement non supprimée par down() : elle préexistait
            // à cette migration dans ton cas, donc ce n'est pas à elle de la retirer.
        });
    }
};