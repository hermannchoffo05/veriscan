<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Abonnement;

class PaiementController extends Controller
{
    // ── Montants des plans ────────────────────────────────────────────────
    // PRODUCTION
    private array $plans = [
        'starter'    => ['nom' => 'Starter',    'montant' => 5000,  'label' => '5 000 XAF/mois'],
        'pro'        => ['nom' => 'Pro',         'montant' => 15000, 'label' => '15 000 XAF/mois'],
        'entreprise' => ['nom' => 'Entreprise',  'montant' => 50000, 'label' => '50 000 XAF/mois'],
    ];

    private function getCampayToken(): ?string
    {
        try {
            $response = Http::timeout(15)
                ->post(config('services.campay.base_url') . 'token/', [
                    'username' => config('services.campay.username'),
                    'password' => config('services.campay.password'),
                ]);

            if ($response->successful() && $response->json('token')) {
                return $response->json('token');
            }

            Log::error('CamPay token failed', ['status' => $response->status(), 'body' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('CamPay token exception: ' . $e->getMessage());
            return null;
        }
    }

    public function show(Request $request, string $plan)
    {
        if (!array_key_exists($plan, $this->plans)) abort(404);
        $planData = $this->plans[$plan];
        $locale   = app()->getLocale();
        return view('paiement.checkout', compact('plan', 'planData', 'locale'));
    }

    public function initier(Request $request)
    {
        $request->validate([
            'plan'      => 'required|in:starter,pro,entreprise',
            'telephone' => 'required|string|min:9|max:9',
            'operateur' => 'required|in:mtn,orange',
        ]);

        $plan      = $this->plans[$request->plan];
        $reference = 'VS-' . strtoupper($request->plan) . '-' . Str::random(8) . '-' . time();

        $telephone = preg_replace('/\D/', '', $request->telephone);
        if (!str_starts_with($telephone, '237')) {
            $telephone = '237' . $telephone;
        }

        $token = $this->getCampayToken();
        if (!$token) {
            return back()->withInput()->withErrors(['paiement' => __('paiement.erreur_connexion')]);
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Authorization' => 'Token ' . $token, 'Content-Type' => 'application/json'])
                ->post(config('services.campay.base_url') . 'collect/', [
                    'amount'             => (string) $plan['montant'],
                    'currency'           => 'XAF',
                    'from'               => $telephone,
                    'description'        => 'Abonnement VeriScan ' . $plan['nom'],
                    'external_reference' => $reference,
                ]);
        } catch (\Exception $e) {
            Log::error('CamPay collect exception: ' . $e->getMessage());
            return back()->withInput()->withErrors(['paiement' => __('paiement.erreur_reseau')]);
        }

        Log::info('CamPay collect response', ['status' => $response->status(), 'body' => $response->body()]);

        if (!$response->successful()) {
            $errorBody = $response->json();
            $errorMsg  = $errorBody['detail'] ?? $errorBody['message'] ?? __('paiement.erreur_initiation');
            Log::error('CamPay collect failed', ['status' => $response->status(), 'body' => $response->body()]);
            return back()->withInput()->withErrors(['paiement' => $errorMsg]);
        }

        $data = $response->json();

        Abonnement::create([
            'fabricant_id'       => Auth::guard('fabricant')->id(),
            'plan'               => $request->plan,
            'montant'            => $plan['montant'],
            'telephone'          => $telephone,
            'operateur'          => $request->operateur,
            'reference'          => $reference,
            'campay_reference'   => $data['reference'] ?? null,
            'statut'             => 'pending',
        ]);

        return redirect()->route('paiement.attente', ['reference' => $reference]);
    }

    public function attente(Request $request)
    {
        $reference  = $request->reference;
        $abonnement = Abonnement::where('reference', $reference)->firstOrFail();
        $locale     = app()->getLocale();
        return view('paiement.attente', compact('abonnement', 'locale'));
    }

    public function statut(Request $request)
    {
        $reference  = $request->reference;
        $abonnement = Abonnement::where('reference', $reference)->firstOrFail();

        if ($abonnement->statut === 'successful') return response()->json(['statut' => 'SUCCESSFUL']);
        if ($abonnement->statut === 'failed')     return response()->json(['statut' => 'FAILED']);
        if (!$abonnement->campay_reference)       return response()->json(['statut' => 'PENDING']);

        $token = $this->getCampayToken();
        if (!$token) return response()->json(['statut' => 'PENDING']);

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => 'Token ' . $token])
                ->get(config('services.campay.base_url') . 'transaction/' . $abonnement->campay_reference . '/');
        } catch (\Exception $e) {
            Log::error('CamPay statut exception: ' . $e->getMessage());
            return response()->json(['statut' => 'PENDING']);
        }

        if ($response->successful()) {
            $statut = strtoupper($response->json()['status'] ?? 'PENDING');
            if ($statut === 'SUCCESSFUL') {
                $abonnement->update(['statut' => 'successful']);
                if ($abonnement->fabricant_id && $abonnement->fabricant) {
                    $abonnement->fabricant->update(['plan' => $abonnement->plan]);
                }
            } elseif ($statut === 'FAILED') {
                $abonnement->update(['statut' => 'failed']);
            }
            return response()->json(['statut' => $statut]);
        }

        return response()->json(['statut' => 'PENDING']);
    }

    public function webhook(Request $request)
    {
        Log::info('CamPay webhook reçu', $request->all());
        $data      = $request->all();
        $reference = $data['external_reference'] ?? null;

        if (!$reference) return response()->json(['ok' => false], 400);

        $abonnement = Abonnement::where('reference', $reference)->first();
        if (!$abonnement) return response()->json(['ok' => false], 404);

        $statut = strtoupper($data['status'] ?? 'PENDING');

        if ($statut === 'SUCCESSFUL') {
            $abonnement->update(['statut' => 'successful', 'campay_reference' => $data['reference'] ?? $abonnement->campay_reference]);
            if ($abonnement->fabricant_id && $abonnement->fabricant) {
                $abonnement->fabricant->update(['plan' => $abonnement->plan]);
            }
        } elseif ($statut === 'FAILED') {
            $abonnement->update(['statut' => 'failed']);
        }

        return response()->json(['ok' => true]);
    }

    public function succes(Request $request)
    {
        $reference  = $request->reference;
        $abonnement = Abonnement::where('reference', $reference)->firstOrFail();
        $locale     = app()->getLocale();
        return view('paiement.succes', compact('abonnement', 'locale'));
    }
}