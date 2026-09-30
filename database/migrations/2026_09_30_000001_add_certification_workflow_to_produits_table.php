<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Workflow de certification documentaire au niveau du PRODUIT.
 *
 * Le fabricant accède librement à la plateforme dès son inscription ;
 * en revanche un produit doit être certifié par l'autorité (admin)
 * avant de pouvoir émettre des QR codes.
 *
 * statut_certification : soumis | certifie | rejete | revoque
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->string('statut_certification', 20)->default('soumis')->after('code_produit');
            $table->string('justificatif')->nullable()->after('statut_certification');
            $table->string('motif_decision', 500)->nullable()->after('justificatif');
            $table->unsignedBigInteger('certifie_par')->nullable()->after('motif_decision');
            $table->timestamp('certifie_le')->nullable()->after('certifie_par');
            $table->string('numero_certificat', 40)->nullable()->unique()->after('certifie_le');
            $table->index('statut_certification');
        });

        // Les produits déjà existants (données de démonstration) passent en
        // « certifié » pour ne pas casser les QR codes et les scénarios de démo.
        $produits = DB::table('produits')->select('id')->get();
        foreach ($produits as $p) {
            DB::table('produits')->where('id', $p->id)->update([
                'statut_certification' => 'certifie',
                'certifie_le'          => now(),
                'numero_certificat'    => sprintf('VS-CERT-%s-%05d', now()->format('Y'), $p->id),
                'motif_decision'       => 'Produit antérieur au workflow de certification (migration automatique).',
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropIndex(['statut_certification']);
            $table->dropUnique(['numero_certificat']);
            $table->dropColumn([
                'statut_certification', 'justificatif', 'motif_decision',
                'certifie_par', 'certifie_le', 'numero_certificat',
            ]);
        });
    }
};
