<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function handleGoogleToken(Request $request)
    {
        try {
            $request->validate([
                'id_token' => 'required|string',
            ]);

            // Vérifie le token Google et récupère les infos utilisateur
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($request->id_token);

            // Cherche l'utilisateur en DB ou le crée
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Nouvel utilisateur → on le crée
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'password'          => bcrypt(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
            } else {
                // Utilisateur existant → on met à jour google_id si pas encore fait
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar'    => $googleUser->getAvatar(),
                    ]);
                }
            }

            // Génère le token Sanctum
            $token = $user->createToken('veriscan_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'token'   => $token,
                'user'    => [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'avatar' => $user->avatar,
                    'role'   => $user->role ?? 'consumer',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification Google échouée : ' . $e->getMessage(),
            ], 401);
        }
    }
}