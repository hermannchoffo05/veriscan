<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    protected $fillable = [
        'fabricant_id',
        'plan',
        'montant',
        'telephone',
        'operateur',
        'reference',
        'campay_reference',
        'statut', // pending, successful, failed
    ];

    public function fabricant()
    {
        return $this->belongsTo(Fabricant::class);
    }
}
