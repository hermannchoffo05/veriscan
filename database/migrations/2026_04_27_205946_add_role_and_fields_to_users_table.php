<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Rôle utilisateur
            $table->enum('role', ['admin', 'fabricant', 'consommateur'])
                  ->default('consommateur')
                  ->after('email');

            // Informations fabricant
            $table->string('company_name', 150)->nullable()->after('role');
            $table->string('phone', 20)->nullable()->after('company_name');
            $table->text('address')->nullable()->after('phone');
            $table->string('logo', 255)->nullable()->after('address');

            // Plan abonnement
            $table->enum('plan', ['starter', 'pro', 'enterprise'])
                  ->default('starter')
                  ->after('logo');
            $table->timestamp('plan_expires_at')->nullable()->after('plan');
            $table->string('campay_tx_ref', 255)->nullable()->after('plan_expires_at');

            // Statut compte
            $table->boolean('is_active')->default(true)->after('campay_tx_ref');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'company_name',
                'phone',
                'address',
                'logo',
                'plan',
                'plan_expires_at',
                'campay_tx_ref',
                'is_active',
            ]);
        });
    }
};