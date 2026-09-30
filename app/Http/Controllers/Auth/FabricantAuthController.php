<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Fabricant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class FabricantAuthController extends Controller
{
    public function showLogin()
    {
        return view('fabricant.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ✅ AJOUTÉ : aucune limite de tentatives n'existait avant — brute-force
        // / credential stuffing possible sans friction sur /fabricant/login.
        $throttleKey = 'login|' . strtolower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
            ])->onlyInput('email');
        }

        if (Auth::guard('fabricant')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended(route('fabricant.dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Ces identifiants ne correspondent à aucun compte fabricant.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('fabricant.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nom_entreprise' => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'unique:fabricants'],
            'password'       => ['required', 'confirmed', 'min:8'],
            'indicatif'      => ['nullable', 'string', 'max:6'],
            'telephone'      => ['nullable', 'string', 'max:20'],
            'adresse'        => ['nullable', 'string', 'max:255'],
            'pays'           => ['nullable', 'string', 'max:100'],
        ]);

        // ✅ AJOUTÉ : le formulaire envoie l'indicatif (ex: +237) et le numéro
        // séparément (sélecteur avec drapeaux côté vue). On les concatène ici
        // pour stocker un seul numéro complet en base, comme avant.
        $telephoneComplet = null;
        if (!empty($data['telephone'])) {
            $telephoneComplet = trim(($data['indicatif'] ?? '') . ' ' . $data['telephone']);
        }

        $fabricant = Fabricant::create([
            'nom_entreprise' => $data['nom_entreprise'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password']),
            'telephone'      => $telephoneComplet,
            'adresse'        => $data['adresse'] ?? null,
            'pays'           => $data['pays'] ?? 'Cameroun',
            // Choix de conception : le fabricant accède immédiatement à son tableau
            // de bord (pas d'attente de validation du compte). Le contrôle de
            // confiance porte sur chaque PRODUIT : il doit être certifié par
            // l'autorité (admin) avant de pouvoir émettre des QR codes.
            'statut'         => 'actif',
        ]);

        Auth::guard('fabricant')->login($fabricant);

        return redirect()->route('fabricant.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('fabricant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('fabricant.login');
    }

    // ✅ AJOUTÉ : connexion via Google (Socialite). Redirige le fabricant vers
    // l'écran de consentement Google.
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // ✅ AJOUTÉ : callback appelé par Google après consentement. Cherche un
    // fabricant existant par google_id ou par email (cas d'un compte déjà créé
    // manuellement avec le même email) ; sinon en crée un nouveau avec un mot
    // de passe aléatoire (inutilisable directement, mais nécessaire car la
    // colonne reste requise ailleurs dans le code — la migration l'a rendue
    // nullable, ce bcrypt(Str::random(32)) est donc une sécurité en plus, pas
    // une obligation stricte).
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $fabricant = Fabricant::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($fabricant) {
            if (!$fabricant->google_id) {
                $fabricant->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            $fabricant = Fabricant::create([
                'nom_entreprise'    => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'password'          => bcrypt(Str::random(32)),
                'pays'              => 'Cameroun',
                'statut'            => 'actif',
                'email_verified_at' => now(),
            ]);
        }

        Auth::guard('fabricant')->login($fabricant, true);

        return redirect()->intended(route('fabricant.dashboard'));
    }

    public function showForgotPassword()
    {
        return view('fabricant.auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // ✅ AJOUTÉ : limite les demandes de code à 3 par 5 minutes par email.
        // Sans ça, rien n'empêchait de spammer un fabricant de codes (email
        // bombing) ou de réinitialiser sans cesse la fenêtre de 10 minutes
        // pour prolonger une tentative de brute-force sur verifyCode().
        $requestThrottleKey = 'reset-request|' . strtolower($request->email);
        if (RateLimiter::tooManyAttempts($requestThrottleKey, 3)) {
            $seconds = RateLimiter::availableIn($requestThrottleKey);
            return back()->withErrors([
                'email' => "Trop de demandes de code. Réessayez dans {$seconds} secondes.",
            ]);
        }
        RateLimiter::hit($requestThrottleKey, 300);

        $fabricant = Fabricant::where('email', $request->email)->first();

        if (!$fabricant) {
            return back()->withErrors(['email' => 'Aucun compte trouvé avec cet email.']);
        }

        $code = rand(100000, 999999);

        // 10 minutes de validité en cache
        Cache::put('password_reset_' . $request->email, $code, now()->addMinutes(10));

        Mail::raw("Votre code de réinitialisation VeriScan : $code", function ($message) use ($request) {
            $message->to($request->email)->subject('Code de réinitialisation VeriScan');
        });

        // ✅ On garde l'email en session pour la page de vérification et le renvoi de code
        session(['reset_email' => $request->email]);

        return redirect()->route('fabricant.password.verify');
    }

    public function showVerifyCode()
    {
        // Si on arrive ici sans être passé par sendResetCode, on renvoie vers la demande d'email
        if (!session('reset_email')) {
            return redirect()->route('fabricant.password.request');
        }

        return view('fabricant.auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required'],
        ]);

        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('fabricant.password.request')
                ->withErrors(['email' => 'Session expirée, veuillez recommencer.']);
        }

        // ✅ AJOUTÉ : c'est LE correctif critique de ce fichier. Un code à 6
        // chiffres (900 000 combinaisons) sans aucune limite de tentatives
        // était brute-forçable dans la fenêtre de 10 minutes par n'importe
        // qui connaissant l'email de la victime — prise de contrôle de compte
        // sans jamais avoir accès à sa boîte mail. On bloque après 5 essais
        // incorrects et on invalide le code en cours, forçant une nouvelle
        // demande.
        $verifyThrottleKey = 'reset-verify|' . strtolower($email);

        if (RateLimiter::tooManyAttempts($verifyThrottleKey, 5)) {
            Cache::forget('password_reset_' . $email);
            RateLimiter::clear($verifyThrottleKey);
            return redirect()->route('fabricant.password.request')
                ->withErrors(['email' => 'Trop de tentatives incorrectes. Veuillez redemander un nouveau code.']);
        }

        $cachedCode = Cache::get('password_reset_' . $email);

        if (!$cachedCode || $cachedCode !== (int) $request->code) {
            RateLimiter::hit($verifyThrottleKey, 600);
            return back()->withErrors(['code' => 'Code invalide ou expiré.']);
        }

        RateLimiter::clear($verifyThrottleKey);
        session(['reset_code_verified' => true]);

        return redirect()->route('fabricant.password.reset');
    }

    public function showResetPassword()
    {
        if (!session('reset_email') || !session('reset_code_verified')) {
            return redirect()->route('fabricant.password.request');
        }

        return view('fabricant.auth.reset-password', [
            'email' => session('reset_email'),
            'token' => session('reset_email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (!session('reset_email') || session('reset_email') !== $request->email) {
            return redirect()->route('fabricant.password.request');
        }

        if (!session('reset_code_verified')) {
            return redirect()->route('fabricant.password.request');
        }

        Fabricant::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        Cache::forget('password_reset_' . $request->email);
        session()->forget(['reset_email', 'reset_code_verified']);

        return redirect()->route('fabricant.login')->with('success', 'Mot de passe réinitialisé avec succès.');
    }
}