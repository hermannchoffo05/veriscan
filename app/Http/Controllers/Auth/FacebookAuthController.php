<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class FacebookAuthController extends Controller
{
    public function handleFacebookToken(Request $request)
    {
        try {
            $request->validate([
                'access_token' => 'required|string',
            ]);

            $facebookUser = Socialite::driver('facebook')
                ->stateless()
                ->userFromToken($request->access_token);

            $user = User::where('email', $facebookUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name'              => $facebookUser->getName(),
                    'email'             => $facebookUser->getEmail(),
                    'facebook_id'       => $facebookUser->getId(),
                    'avatar'            => $facebookUser->getAvatar(),
                    'password'          => bcrypt(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
            } else {
                if (!$user->facebook_id) {
                    $user->update([
                        'facebook_id' => $facebookUser->getId(),
                        'avatar'      => $facebookUser->getAvatar(),
                    ]);
                }
            }

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
                'message' => 'Authentification Facebook échouée : ' . $e->getMessage(),
            ], 401);
        }
    }
}