<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'nom',
        'email',
        'password',
        'role',
        'actif',
        'photo',
        'derniere_connexion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'actif'              => 'boolean',
        'derniere_connexion' => 'datetime',
        'password'           => 'hashed',
    ];

    /**
     * URL de la photo de profil (ou null si pas de photo)
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }
}