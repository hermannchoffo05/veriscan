<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'company_name',
        'address',
        'logo',
        'plan',
        'plan_expires_at',
        'campay_tx_ref',
        'is_active',
        'email_verified_at',
        'google_id',
        'avatar', 
        'facebook_id'  
         ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'plan_expires_at'   => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ═══════════════════════════════════════════
    // HELPERS RÔLES
    // ═══════════════════════════════════════════
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isFabricant(): bool
    {
        return $this->role === 'fabricant';
    }

    public function isConsommateur(): bool
    {
        return $this->role === 'consommateur';
    }

    // ═══════════════════════════════════════════
    // RELATIONS
    // ═══════════════════════════════════════════
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    public function lots()
    {
        return $this->hasMany(\App\Models\Lot::class);
    }

    public function qrcodes()
    {
        return $this->hasMany(\App\Models\QrCode::class);
    }

    public function reports()
    {
        return $this->hasMany(\App\Models\Report::class);
    }
}