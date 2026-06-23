<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
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

    // Accès direct aux signalements via lots → qrcodes
    public function signalements()
    {
        return $this->hasManyThrough(
            Signalement::class,
            Lot::class,
            'produit_id',   // FK sur lots
            'lot_id',       // FK sur signalements... via qr_codes
        );
        // Note : pour les signalements, utiliser la méthode statique ci-dessous
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