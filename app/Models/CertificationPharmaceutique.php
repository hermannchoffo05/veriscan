<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificationPharmaceutique extends Model
{
    protected $table = 'certifications_pharmaceutiques';

    protected $fillable = [
        'produit_id', 'numero_amm', 'laboratoire_fabricant', 'date_amm',
    ];

    protected $casts = [
        'date_amm' => 'date',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}