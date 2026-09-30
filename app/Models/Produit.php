<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    /**
     * VeriScan ne certifie que ces secteurs. Toute valeur hors de cette
     * liste doit être rejetée par la validation (voir le contrôleur).
     */
    public const CATEGORIES_AUTORISEES = ['Pharmaceutique', 'Cosmétique'];

    // Statuts du workflow de certification documentaire
    public const CERT_SOUMIS  = 'soumis';
    public const CERT_CERTIFIE = 'certifie';
    public const CERT_REJETE  = 'rejete';
    public const CERT_REVOQUE = 'revoque';

    protected $fillable = [
        'fabricant_id', 'nom', 'categorie',
        'description', 'image', 'code_produit',
        'statut_certification', 'justificatif', 'motif_decision',
        'certifie_par', 'certifie_le', 'numero_certificat',
    ];

    protected $casts = [
        'certifie_le' => 'datetime',
    ];

    public function estCertifie(): bool
    {
        return $this->statut_certification === self::CERT_CERTIFIE;
    }

    public function getLibelleCertificationAttribute(): string
    {
        return match ($this->statut_certification) {
            self::CERT_CERTIFIE => 'Certifié',
            self::CERT_REJETE   => 'Rejeté',
            self::CERT_REVOQUE  => 'Révoqué',
            default             => 'En cours de validation',
        };
    }

    /** Génère un numéro de certificat unique, ex : VS-CERT-2026-00042 */
    public static function genererNumeroCertificat(self $produit): string
    {
        return sprintf('VS-CERT-%s-%05d', now()->format('Y'), $produit->id);
    }

    public function fabricant()
    {
        return $this->belongsTo(Fabricant::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    // Accès direct aux QR codes via les lots
    public function qrcodes()
    {
        return $this->hasManyThrough(QrCode::class, Lot::class);
    }

    public function certificationPharmaceutique()
    {
        return $this->hasOne(CertificationPharmaceutique::class);
    }

    public function certificationCosmetique()
    {
        return $this->hasOne(CertificationCosmetique::class);
    }

    /**
     * Retourne la fiche de certification liée à ce produit, quel que soit
     * son secteur — utile pour l'affichage (show.blade.php) sans avoir à
     * tester la catégorie manuellement à chaque fois.
     */
    public function getCertificationAttribute()
    {
        return match ($this->categorie) {
            'Pharmaceutique' => $this->certificationPharmaceutique,
            'Cosmétique'     => $this->certificationCosmetique,
            default          => null,
        };
    }

    // Compte total de signalements (via lots → qr_codes → signalements)
    public function getNbSignalementsAttribute()
    {
        return Signalement::whereHas('qrCode.lot', function ($q) {
            $q->where('produit_id', $this->id);
        })->count();
    }

    // Compte total de scans (somme nb_scans des qr_codes)
    public function getNbScansAttribute()
    {
        return QrCode::whereHas('lot', function ($q) {
            $q->where('produit_id', $this->id);
        })->sum('nb_scans');
    }
}