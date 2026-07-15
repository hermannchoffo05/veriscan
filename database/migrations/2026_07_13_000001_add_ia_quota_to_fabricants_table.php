<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fabricants', function (Blueprint $table) {
            $table->unsignedInteger('ia_requetes_mois')->default(0)->after('rapports_generes_mois');
        });
    }

    public function down(): void
    {
        Schema::table('fabricants', function (Blueprint $table) {
            $table->dropColumn('ia_requetes_mois');
        });
    }
};