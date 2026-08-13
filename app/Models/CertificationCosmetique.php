<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificationCosmetique extends Model
{
    protected $table = 'certifications_cosmetiques';

    protected $fillable = [
        'produit_id', 'liste_inci', 'certificat_conformite', 'date_certification',
    ];

    protected $casts = [
        'date_certification' => 'date',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}