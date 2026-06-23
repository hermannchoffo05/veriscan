<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class PasswordResetApiController extends Controller
{
    // ═══════════════════════════════════════════
    // ÉTAPE 1 — Envoyer le code OTP
    // POST /api/forgot-password
    // ═══════════════════════════════════════════
    public function sendResetCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun compte trouvé avec cet email.',
            ], 404);
        }

        // Générer code 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Stocker en cache 10 minutes
        Cache::put('password_reset_' . $request->email, $code, now()->addMinutes(10));

        // Envoyer par email
        Mail::raw(
            "Bonjour {$user->name},\n\nVotre code de réinitialisation VeriScan est : {$code}\n\nCe code est valide pendant 10 minutes.\n\nSi vous n'avez pas demandé cette réinitialisation, ignorez cet email.",
            function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('VeriScan — Code de réinitialisation');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Code envoyé sur votre email.',
            'email'   => $request->email,
        ]);
    }

    // ═══════════════════════════════════════════
    // ÉTAPE 2 — Vérifier le code OTP
    // POST /api/verify-reset-code
    // ═══════════════════════════════════════════
    public function verifyCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code'  => ['required', 'string', 'size:6'],
        ]);

        $cachedCode = Cache::get('password_reset_' . $request->email);

        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Code incorrect ou expiré.',
            ], 400);
        }

        // Générer un token temporaire pour valider l'étape 3
        $resetToken = hash('sha256', $request->email . $request->code . now());
        Cache::put('password_reset_token_' . $request->email, $resetToken, now()->addMinutes(10));

        return response()->json([
            'success'      => true,
            'message'      => 'Code vérifié avec succès.',
            'reset_token'  => $resetToken,
        ]);
    }

    // ═══════════════════════════════════════════
    // ÉTAPE 3 — Réinitialiser le mot de passe
    // POST /api/reset-password
    // ═══════════════════════════════════════════
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email'        => ['required', 'email'],
            'reset_token'  => ['required', 'string'],
            'password'     => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // Vérifier le token temporaire
        $cachedToken = Cache::get('password_reset_token_' . $request->email);

        if (!$cachedToken || $cachedToken !== $request->reset_token) {
            return response()->json([
                'success' => false,
                'message' => 'Session expirée. Recommencez.',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur introuvable.',
            ], 404);
        }

        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Nettoyer le cache
        Cache::forget('password_reset_' . $request->email);
        Cache::forget('password_reset_token_' . $request->email);

        // Supprimer tous les tokens Sanctum
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès.',
        ]);
    }
}