<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->integer('nb_scans_24h')->default(0)->after('nb_scans');
            $table->timestamp('premier_scan_fenetre')->nullable()->after('nb_scans_24h');
            $table->boolean('alerte_velocite')->default(false)->after('premier_scan_fenetre');
        });
    }

    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropColumn(['nb_scans_24h', 'premier_scan_fenetre', 'alerte_velocite']);
        });
    }
};