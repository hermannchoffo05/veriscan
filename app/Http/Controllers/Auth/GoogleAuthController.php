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
            // Le mobile envoie un id_token (JWT) : on le fait valider par Google.
            $info = \Illuminate\Support\Facades\Http::timeout(15)
                ->get('https://oauth2.googleapis.com/tokeninfo', ['id_token' => $request->id_token]);

            if ($info->successful() && $info->json('email')) {
                $aud = $info->json('aud');
                $attendus = array_filter([config('services.google.client_id'), env('GOOGLE_MOBILE_SERVER_CLIENT_ID')]);
                if (!empty($attendus) && !in_array($aud, $attendus, true)) {
                    throw new \Exception('id_token destiné à une autre application.');
                }
                $googleUser = new class($info->json()) {
                    public function __construct(private array $d) {}
                    public function getId() { return $this->d['sub'] ?? null; }
                    public function getEmail() { return $this->d['email'] ?? null; }
                    public function getName() { return $this->d['name'] ?? ($this->d['email'] ?? 'Utilisateur'); }
                    public function getAvatar() { return $this->d['picture'] ?? null; }
                };
            } else {
                // Repli : le jeton est peut-être un access_token.
                $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->id_token);
            }

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