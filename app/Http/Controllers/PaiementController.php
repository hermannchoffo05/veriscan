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
    'starter'    => ['nom' => 'Starter',    'montant' => 2000,  'label' => '2 000 XAF/mois'],
    'pro'        => ['nom' => 'Pro',         'montant' => 5000,  'label' => '5 000 XAF/mois'],
    'entreprise' => ['nom' => 'Entreprise',  'montant' => 10000, 'label' => '10 000 XAF/mois'],
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

    /**
     * Vérifie le statut réel d'une transaction directement auprès de CamPay.
     * Ne fait JAMAIS confiance à un statut fourni par le client ou par un webhook :
     * on interroge nous-mêmes l'API avec notre propre token.
     */
    private function verifierStatutAupresDeCampay(string $campayReference): ?string
    {
        $token = $this->getCampayToken();
        if (!$token) return null;

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => 'Token ' . $token])
                ->get(config('services.campay.base_url') . 'transaction/' . $campayReference . '/');
        } catch (\Exception $e) {
            Log::error('CamPay verification exception: ' . $e->getMessage());
            return null;
        }

        if (!$response->successful()) {
            Log::error('CamPay verification failed', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        return strtoupper($response->json()['status'] ?? 'PENDING');
    }

    private function appliquerStatut(Abonnement $abonnement, string $statutVerifie): void
    {
        if ($statutVerifie === 'SUCCESSFUL' && $abonnement->statut !== 'successful') {
            $abonnement->update(['statut' => 'successful']);
            if ($abonnement->fabricant_id && $abonnement->fabricant) {
                $abonnement->fabricant->update(['plan' => $abonnement->plan]);
            }
        } elseif ($statutVerifie === 'FAILED' && $abonnement->statut !== 'failed') {
            $abonnement->update(['statut' => 'failed']);
        }
    }

    /**
     * Vérifie que l'abonnement demandé appartient bien au fabricant connecté.
     * Bloque l'accès (404, pour ne pas confirmer l'existence de la ressource) sinon.
     */
    private function autoriserAccesAbonnement(Abonnement $abonnement): void
    {
        $fabricantId = Auth::guard('fabricant')->id();
        abort_if($abonnement->fabricant_id !== $fabricantId, 404);
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
        // Sans compte fabricant authentifié, pas d'appel CamPay ni d'Abonnement créé.
        abort_if(!Auth::guard('fabricant')->check(), 403);

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
        $this->autoriserAccesAbonnement($abonnement);

        $locale = app()->getLocale();
        return view('paiement.attente', compact('abonnement', 'locale'));
    }

    public function statut(Request $request)
    {
        $reference  = $request->reference;
        $abonnement = Abonnement::where('reference', $reference)->firstOrFail();
        $this->autoriserAccesAbonnement($abonnement);

        if ($abonnement->statut === 'successful') return response()->json(['statut' => 'SUCCESSFUL']);
        if ($abonnement->statut === 'failed')     return response()->json(['statut' => 'FAILED']);
        if (!$abonnement->campay_reference)       return response()->json(['statut' => 'PENDING']);

        $statutVerifie = $this->verifierStatutAupresDeCampay($abonnement->campay_reference);
        if (!$statutVerifie) return response()->json(['statut' => 'PENDING']);

        $this->appliquerStatut($abonnement, $statutVerifie);

        return response()->json(['statut' => $statutVerifie]);
    }

    /**
     * Webhook CamPay.
     *
     * IMPORTANT : on ne fait JAMAIS confiance au champ "status" envoyé dans le corps
     * de la requête — n'importe qui peut poster ici avec un statut forgé. Le webhook
     * sert uniquement de déclencheur : on va nous-mêmes revérifier le statut réel
     * auprès de CamPay avec notre propre token avant de mettre quoi que ce soit à jour.
     */
    public function webhook(Request $request)
    {
        Log::info('CamPay webhook reçu', $request->all());

        $reference = $request->input('external_reference');
        if (!$reference) return response()->json(['ok' => false], 400);

        $abonnement = Abonnement::where('reference', $reference)->first();
        if (!$abonnement) return response()->json(['ok' => false], 404);

        $campayReference = $abonnement->campay_reference ?? $request->input('reference');
        if (!$campayReference) return response()->json(['ok' => false], 400);

        $statutVerifie = $this->verifierStatutAupresDeCampay($campayReference);
        if (!$statutVerifie) return response()->json(['ok' => false], 502);

        $this->appliquerStatut($abonnement, $statutVerifie);

        return response()->json(['ok' => true]);
    }

    public function succes(Request $request)
    {
        $reference  = $request->reference;
        $abonnement = Abonnement::where('reference', $reference)->firstOrFail();
        $this->autoriserAccesAbonnement($abonnement);

        $locale = app()->getLocale();
        return view('paiement.succes', compact('abonnement', 'locale'));
    }
}