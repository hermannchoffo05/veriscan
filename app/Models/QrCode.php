<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $fillable = [
        'lot_id', 'token', 'hmac',
        'image_path', 'nb_scans', 'statut',
        'nb_scans_24h', 'premier_scan_fenetre', 'alerte_velocite',
    ];

    protected $casts = [
        'premier_scan_fenetre' => 'datetime',
        'alerte_velocite'      => 'boolean',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function produit()
    {
        return $this->hasOneThrough(Produit::class, Lot::class, 'id', 'id', 'lot_id', 'produit_id');
    }

    public function verifications()
    {
        return $this->hasMany(Verification::class);
    }

    public function signalements()
    {
        return $this->hasMany(Signalement::class);
    }
}