<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = [
        'produit_id', 'numero_lot', 'date_fabrication',
        'date_expiration', 'quantite', 'site_production'
    ];

    protected $casts = [
        'date_fabrication' => 'date',
        'date_expiration'  => 'date',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function qrcodes()
    {
        return $this->hasMany(QrCode::class);
    }

    public function signalements()
    {
        return $this->hasManyThrough(Signalement::class, QrCode::class);
    }
}