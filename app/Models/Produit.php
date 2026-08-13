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

    protected $fillable = [
        'fabricant_id', 'nom', 'categorie',
        'description', 'image', 'code_produit'
    ];

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