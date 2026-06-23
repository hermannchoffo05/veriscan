<?php

use App\Models\User;
use App\Models\Fabricant;

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'fabricant' => [
            'driver' => 'session',
            'provider' => 'fabricants',
        ],
          'admin' => [                    // ← AJOUTE CECI
        'driver' => 'session',
        'provider' => 'admins',
    ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],
        'fabricants' => [
            'driver' => 'eloquent',
            'model' => Fabricant::class,
        ],
            'admins' => [                   // ← AJOUTE CECI
        'driver' => 'eloquent',
        'model' => App\Models\Admin::class,
    ],
        
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
        'fabricants' => [
            'provider' => 'fabricants',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'admins' => [                       // ← AJOUTE CECI
    'provider' => 'admins',
    'table' => 'password_reset_tokens',
    'expire' => 60,
    'throttle' => 60,
],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];