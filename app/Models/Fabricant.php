<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Fabricant extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'nom_entreprise', 'email', 'password',
        'telephone', 'adresse', 'pays', 'logo', 'statut', 'parametres',
        'type_document', 'document_path',
        'motif_rejet',
        'verification_token',
        'plan', 'plan_expire_le', 'essai_deja_utilise',
        'qrcodes_generes_mois', 'rapports_generes_mois', 'usage_mois_reference',
        'ia_requetes_mois',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'parametres'         => 'array',
        'plan_expire_le'     => 'datetime',
        'essai_deja_utilise' => 'boolean',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    public function planActif(): string
    {
        if ($this->plan && $this->plan !== 'gratuit'
            && $this->plan_expire_le && $this->plan_expire_le->isPast()) {
            return 'gratuit';
        }
        return $this->plan ?: 'gratuit';
    }

    public function limites(): array
    {
        return config('plans.' . $this->planActif(), config('plans.gratuit'));
    }

    public function resetUsageSiNouveauMois(): void
    {
        $moisActuel = now()->format('Y-m');
        if ($this->usage_mois_reference !== $moisActuel) {
            $this->update([
                'qrcodes_generes_mois'  => 0,
                'rapports_generes_mois' => 0,
                'ia_requetes_mois'      => 0,
                'usage_mois_reference'  => $moisActuel,
            ]);
        }
    }

    public function quotaProduitsRestant(): ?int
    {
        $limite = $this->limites()['produits'];
        if ($limite === null) return null;
        return max(0, $limite - $this->produits()->count());
    }

    public function quotaQrcodesRestant(): ?int
    {
        $limite = $this->limites()['qrcodes'];
        if ($limite === null) return null;
        $this->resetUsageSiNouveauMois();
        return max(0, $limite - $this->qrcodes_generes_mois);
    }

    public function quotaRapportsRestant(): ?int
    {
        $limite = $this->limites()['rapports'];
        if ($limite === null) return null;
        $this->resetUsageSiNouveauMois();
        return max(0, $limite - $this->rapports_generes_mois);
    }

    public function aAccesCarteRisques(): bool
    {
        return (bool) $this->limites()['carte_risques'];
    }

    public function aAccesStatistiques(): bool
    {
        return (bool) ($this->limites()['statistiques'] ?? false);
    }

    /**
     * Accès à la page Rapports (le plan inclut-il des rapports du tout ?).
     * null = illimité (true), 0 = plan Gratuit (false), >0 = inclus (true).
     */
    public function aAccesRapports(): bool
    {
        $limite = $this->limites()['rapports'] ?? 0;
        return $limite === null || $limite > 0;
    }

    public function aAccesIA(): bool
    {
        return (bool) $this->limites()['ia'];
    }

    public function quotaIAAssistantMensuel(): ?int
    {
        return $this->limites()['ia_quota_mensuel'] ?? null;
    }

    public function peutUtiliserAssistantProduitIA(): bool
    {
        $limite = $this->quotaIAAssistantMensuel();
        if ($limite === null) return true;
        $this->resetUsageSiNouveauMois();
        return $this->ia_requetes_mois < $limite;
    }

    public function incrementerAssistantProduitIA(): void
    {
        if ($this->quotaIAAssistantMensuel() === null) return;
        $this->resetUsageSiNouveauMois();
        $this->increment('ia_requetes_mois');
    }

    public function quotaAssistantProduitIARestant(): ?int
    {
        $limite = $this->quotaIAAssistantMensuel();
        if ($limite === null) return null;
        $this->resetUsageSiNouveauMois();
        return max(0, $limite - $this->ia_requetes_mois);
    }

    public function prochaineReinitialisationIA(): \Carbon\Carbon
    {
        return now()->startOfMonth()->addMonth();
    }
}