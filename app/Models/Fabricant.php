<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Fabricant extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nom_entreprise', 'email', 'password',
        'telephone', 'adresse', 'pays', 'logo', 'statut', 'parametres',
        'type_document', 'document_path',
        'motif_rejet',        // migration 2026_06_23_141058
        'verification_token', // migration 2026_06_23_170854
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'parametres' => 'array',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
}