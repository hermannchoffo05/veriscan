<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Souscription extends Model
{
    protected $fillable = [
        'plan',
        'nom_entreprise',
        'nom_responsable',
        'telephone',
        'email',
        'ville',
        'message',
        'statut', // nouveau, contacte, converti
    ];
}