<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Fabricant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

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

        if (Auth::guard('fabricant')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('fabricant.dashboard'));
        }

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
            'telephone'      => ['nullable', 'string', 'max:20'],
            'adresse'        => ['nullable', 'string', 'max:255'],
            'pays'           => ['nullable', 'string', 'max:100'],
        ]);

        $fabricant = Fabricant::create([
            'nom_entreprise' => $data['nom_entreprise'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password']),
            'telephone'      => $data['telephone'] ?? null,
            'adresse'        => $data['adresse'] ?? null,
            'pays'           => $data['pays'] ?? 'Cameroun',
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

    public function showForgotPassword()
    {
        return view('fabricant.auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $fabricant = Fabricant::where('email', $request->email)->first();

        if (!$fabricant) {
            return back()->withErrors(['email' => 'Aucun compte trouvé avec cet email.']);
        }

        $code = rand(100000, 999999);
        Cache::put('password_reset_' . $request->email, $code, now()->addSeconds(60));

        Mail::raw("Votre code de réinitialisation VeriScan : $code", function ($message) use ($request) {
            $message->to($request->email)->subject('Code de réinitialisation VeriScan');
        });

        return redirect()->route('fabricant.password.verify');
    }

    public function showVerifyCode()
    {
        return view('fabricant.auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code'  => ['required'],
        ]);

        $cachedCode = Cache::get('password_reset_' . $request->email);

        if (!$cachedCode || $cachedCode !== (int) $request->code) {
            return back()->withErrors(['code' => 'Code invalide ou expiré.']);
        }

        session(['reset_email' => $request->email, 'reset_code_verified' => true]);

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