<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\QrCode;
use App\Models\Produit;
use App\Models\Signalement;
use App\Models\Verification;

class VerificationController extends Controller
{
    // Seuil : plus de X scans en moins de 24h = vélocité suspecte
    const SEUIL_SCANS_24H = 3;
    const FENETRE_HEURES  = 24;

    public function verifyToken(Request $request, string $token)
    {
        $qrCode = QrCode::with(['lot.produit.fabricant'])
            ->where('token', $token)
            ->first();

        if (!$qrCode) {
            return view('verify', [
                'statut'  => 'inconnu',
                'message' => "Ce QR code n'est pas reconnu par VeriScan.",
                'produit' => null,
                'qrCode'  => null,
                'token'   => $token,
            ]);
        }

        $hmacValide = $this->verifierHmac($token, $qrCode->hmac);

        if (!$hmacValide) {
            // Enregistrement de la vérification (signature invalide = contrefait)
            $this->enregistrerVerification($request, $qrCode, 'contrefait');

            return view('verify', [
                'statut'  => 'contrefait',
                'message' => 'La signature de ce QR code est invalide.',
                'produit' => null,
                'qrCode'  => $qrCode,
                'token'   => $token,
            ]);
        }

        if ($qrCode->statut === 'revoque') {
            // Enregistrement de la vérification (QR code révoqué)
            $this->enregistrerVerification($request, $qrCode, 'revoque');

            return view('verify', [
                'statut'  => 'revoque',
                'message' => "Ce QR code a été révoqué.",
                'produit' => $qrCode->lot->produit ?? null,
                'qrCode'  => $qrCode,
                'token'   => $token,
            ]);
        }

        // Produit dont la certification a été rejetée/révoquée : le QR ne prouve plus rien.
        $produitCertif = $qrCode->lot->produit ?? null;
        if ($produitCertif && !$produitCertif->estCertifie()) {
            $this->enregistrerVerification($request, $qrCode, 'revoque');

            return view('verify', [
                'statut'  => 'revoque',
                'message' => "Ce produit n'est pas (ou plus) certifié par VeriScan.",
                'produit' => $produitCertif,
                'qrCode'  => $qrCode,
                'token'   => $token,
            ]);
        }

        // Incrémenter nb_scans global
        $qrCode->increment('nb_scans');

        // Mettre à jour la fenêtre glissante 24h
        $this->mettreAJourVelocite($qrCode);

        // Déterminer le statut
        $statut = $this->determinerStatut($qrCode);

        // Enregistrement de la vérification (authentique / suspect)
        $this->enregistrerVerification($request, $qrCode, $statut);

        $produit = $qrCode->lot->produit ?? null;

        $signalementsEnCours = Signalement::where('qr_code_id', $qrCode->id)
            ->where('statut', 'en_cours')
            ->count();

        return view('verify', [
            'statut'            => $statut,
            'message'           => $this->messageStatut($statut, $qrCode),
            'produit'           => $produit,
            'qrCode'            => $qrCode,
            'lot'               => $qrCode->lot,
            'fabricant'         => $produit?->fabricant,
            'signalementsCount' => $signalementsEnCours,
            'token'             => $token,
            'alerte_velocite'   => $qrCode->alerte_velocite,
            'nb_scans_24h'      => $qrCode->nb_scans_24h,
        ]);
    }

    public function verifyCode(Request $request)
    {
        $code = trim($request->input('code', ''));

        if (empty($code)) {
            return redirect()->route('verify.home');
        }

        $produit = Produit::with(['fabricant', 'lots.qrCodes'])
            ->where('code_produit', strtoupper($code))
            ->first();

        if (!$produit) {
            return view('verify', [
                'statut'  => 'inconnu',
                'message' => 'Aucun produit trouvé avec ce code.',
                'produit' => null,
                'qrCode'  => null,
                'token'   => null,
                'code'    => strtoupper($code),
            ]);
        }

        $qrCodeIds = $produit->lots->flatMap(fn($l) => $l->qrCodes->pluck('id'));
        $signalementsEnCours = Signalement::whereIn('qr_code_id', $qrCodeIds)
            ->where('statut', 'en_cours')
            ->count();

        // Vélocité globale sur tous les QR codes du produit
        $alerteVelocite = QrCode::whereIn('id', $qrCodeIds)
            ->where('alerte_velocite', true)
            ->exists();

        $statut = $signalementsEnCours > 0 || $alerteVelocite ? 'suspect' : 'authentique';

        // Enregistrement de la vérification (recherche par code produit)
        // On rattache la vérification au dernier QR code du produit s'il existe
        $qrCodePourLog = QrCode::whereIn('id', $qrCodeIds)->latest()->first();
        if ($qrCodePourLog) {
            $this->enregistrerVerification($request, $qrCodePourLog, $statut);
        }

        return view('verify', [
            'statut'            => $statut,
            'message'           => $statut === 'authentique'
                ? 'Ce produit est authentique.'
                : 'Ce produit a reçu des signalements suspects.',
            'produit'           => $produit,
            'qrCode'            => null,
            'lot'               => $produit->lots->last(),
            'fabricant'         => $produit->fabricant,
            'signalementsCount' => $signalementsEnCours,
            'token'             => null,
            'code'              => strtoupper($code),
            'alerte_velocite'   => $alerteVelocite,
        ]);
    }

    public function home()
    {
        return view('verify', [
            'statut'  => null,
            'produit' => null,
            'qrCode'  => null,
            'token'   => null,
        ]);
    }

    public function signaler(Request $request)
    {
        $request->validate([
            'qr_code_id'        => 'required|exists:qr_codes,id',
            'description'       => 'required|string|min:10|max:500',
            'nom_signalant'     => 'nullable|string|max:100',
            'contact_signalant' => 'nullable|string|max:100',
            'photo_preuve'      => 'nullable|image|max:8192',
        ], [
            'qr_code_id.required'  => 'Identifiant QR code manquant.',
            'qr_code_id.exists'    => 'Ce QR code n\'existe pas.',
            'description.required' => 'La description est obligatoire.',
            'description.min'      => 'La description doit faire au moins 10 caractères.',
            'description.max'      => 'La description ne doit pas dépasser 500 caractères.',
            'photo_preuve.image'   => 'Le fichier doit être une image (jpg, png, gif...).',
            'photo_preuve.max'     => 'La photo ne doit pas dépasser 8 Mo.',
        ]);

        $data = [
            'qr_code_id'        => $request->qr_code_id,
            'description'       => $request->description,
            'nom_signalant'     => $request->nom_signalant,
            'contact_signalant' => $request->contact_signalant,
            'statut'            => 'en_cours',
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'localisation'      => $request->localisation,
        ];

        $analyseIA = null;

        if ($request->hasFile('photo_preuve')) {
            $path = $request->file('photo_preuve')->store('signalements/photos', 'public');
            $data['photo_preuve'] = $path;

            try {
                $analyseIA = $this->analyserPhotoIA(
                    $request->file('photo_preuve'),
                    $request->description
                );
                $data['analyse_ia'] = $analyseIA;
            } catch (\Exception $e) {
                // L'analyse IA est optionnelle
                Log::warning('Échec analyse IA (signalement) : ' . $e->getMessage());
            }
        }

        try {
            $signalement = Signalement::create($data);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error_signalement', 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer.');
        }

        return back()
            ->with('success_signalement', 'Votre signalement a bien été enregistré. Merci pour votre contribution.')
            ->with('analyse_ia', $analyseIA);
    }

    /**
     * Enregistre une vérification dans la table `verifications`
     * pour que les statistiques admin/fabricant/welcome soient réelles.
     * Réutilise le même schéma que VerifyApiController (user_id, qr_code_id,
     * ip_address, appareil, localisation, resultat).
     */
    private function enregistrerVerification(Request $request, QrCode $qrCode, string $statut): void
    {
        try {
            Verification::create([
                'user_id'      => auth()->id(), // null si visiteur non connecté (web public)
                'qr_code_id'   => $qrCode->id,
                'ip_address'   => $request->ip(),
                'appareil'     => $request->userAgent(),
                'localisation' => $request->input('localisation', null),
                'resultat'     => $statut,
            ]);
        } catch (\Exception $e) {
            // On ne bloque jamais l'affichage du résultat pour le visiteur
            // même si l'enregistrement de la vérification échoue.
            Log::warning('Échec enregistrement Verification (web) : ' . $e->getMessage());
        }
    }

    /**
     * Met à jour le compteur de vélocité sur une fenêtre glissante de 24h.
     * Réinitialise la fenêtre si elle est expirée.
     */
    private function mettreAJourVelocite(QrCode $qrCode): void
    {
        $maintenant = now();
        $fenetre    = self::FENETRE_HEURES;
        $seuil      = self::SEUIL_SCANS_24H;

        // Si pas de fenêtre ouverte ou fenêtre expirée → on repart de zéro
        if (
            is_null($qrCode->premier_scan_fenetre) ||
            $maintenant->diffInHours($qrCode->premier_scan_fenetre) >= $fenetre
        ) {
            $qrCode->update([
                'nb_scans_24h'         => 1,
                'premier_scan_fenetre' => $maintenant,
                'alerte_velocite'      => false,
            ]);
            return;
        }

        // Fenêtre active : incrémenter
        $nouveauCompte = $qrCode->nb_scans_24h + 1;
        $alerte        = $nouveauCompte > $seuil;

        $qrCode->update([
            'nb_scans_24h'    => $nouveauCompte,
            'alerte_velocite' => $alerte,
        ]);
    }

    /**
     * Détermine le statut final : prend en compte signalements ET vélocité.
     */
    private function determinerStatut(QrCode $qrCode): string
    {
        // Vélocité suspecte
        if ($qrCode->alerte_velocite) {
            return 'suspect';
        }

        // Signalements en cours
        $signalements = Signalement::where('qr_code_id', $qrCode->id)
            ->where('statut', 'en_cours')
            ->count();

        return $signalements > 0 ? 'suspect' : 'authentique';
    }

    /**
     * Retourne le message adapté au statut et à la cause.
     */
    private function messageStatut(string $statut, QrCode $qrCode): string
    {
        if ($statut === 'authentique') {
            return 'Ce produit est authentique.';
        }

        if ($qrCode->alerte_velocite) {
            return "Ce QR code a été scanné {$qrCode->nb_scans_24h} fois en moins de 24h — comportement suspect détecté automatiquement.";
        }

        return 'Ce produit a reçu des signalements suspects.';
    }

    private function analyserPhotoIA($photoFile, string $description): ?string
    {
        $apiKey = env('GROQ_API_KEY');
        if (!$apiKey) return null;

        $imageData = base64_encode(file_get_contents($photoFile->getRealPath()));
        $mimeType  = $photoFile->getMimeType();

        $prompt = "Tu es un expert en détection de contrefaçon pour VeriScan au Cameroun.
Description : \"$description\"
Analyse cette image et donne un avis en 2-3 phrases. Commence par le niveau de suspicion (Faible/Modéré/Élevé).";

        // Correction : espace manquant après "Bearer" (bug qui faisait échouer
        // systématiquement cette requête, silencieusement absorbé par le try/catch appelant).
        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'meta-llama/llama-4-scout-17b-16e-instruct',
                'messages'    => [
                    [
                        'role'    => 'user',
                        'content' => [
                            [
                                'type' => 'image_url',
                                'image_url' => ['url' => "data:{$mimeType};base64,{$imageData}"],
                            ],
                            ['type' => 'text', 'text' => $prompt],
                        ],
                    ],
                ],
                'max_tokens'  => 200,
                'temperature' => 0.3,
            ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content');
        }

        return null;
    }

    public function analyserPhoto(Request $request)
    {
        $request->validate(['signalement_id' => 'required|exists:signalements,id']);

        $signalement = Signalement::findOrFail($request->signalement_id);

        if (!$signalement->photo_preuve) {
            return response()->json(['error' => 'Aucune photo disponible.'], 400);
        }

        if ($signalement->analyse_ia) {
            return response()->json(['analyse' => $signalement->analyse_ia, 'cached' => true]);
        }

        try {
            $apiKey    = env('GROQ_API_KEY');
            $photoPath = storage_path('app/public/' . $signalement->photo_preuve);

            if (!file_exists($photoPath)) {
                return response()->json(['error' => 'Photo introuvable.'], 404);
            }

            $imageData = base64_encode(file_get_contents($photoPath));
            $mimeType  = mime_content_type($photoPath);

            $prompt = "Tu es un expert en détection de contrefaçon pour VeriScan au Cameroun.
Description : \"{$signalement->description}\"
Analyse cette image et fournis : niveau de suspicion, observations, signes de contrefaçon, recommandation. Maximum 4-5 phrases.";

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'meta-llama/llama-4-scout-17b-16e-instruct',
                    'messages'    => [
                        [
                            'role'    => 'user',
                            'content' => [
                                [
                                    'type'      => 'image_url',
                                    'image_url' => ['url' => "data:{$mimeType};base64,{$imageData}"],
                                ],
                                ['type' => 'text', 'text' => $prompt],
                            ],
                        ],
                    ],
                    'max_tokens'  => 300,
                    'temperature' => 0.3,
                ]);

            if ($response->successful()) {
                $analyse = $response->json('choices.0.message.content');
                $signalement->update(['analyse_ia' => $analyse]);
                return response()->json(['analyse' => $analyse]);
            }

            return response()->json(['error' => 'Erreur lors de l\'analyse IA.'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Service IA indisponible.'], 500);
        }
    }

    /**
     * NOTE : si $hmacStocke est null, la vérification est considérée comme
     * valide par défaut (comportement permissif). À confirmer : ce fallback
     * est-il volontaire (rétrocompatibilité avec des QR codes générés avant
     * l'introduction du HMAC) ou s'agit-il d'un oubli de sécurité ?
     * Si tous les QR codes doivent obligatoirement avoir un HMAC, remplacer
     * `if (!$hmacStocke) return true;` par `if (!$hmacStocke) return false;`.
     */
    private function verifierHmac(string $token, ?string $hmacStocke): bool
    {
        if (!$hmacStocke) return true;
        $cle         = config('app.key');
        $hmacCalcule = hash_hmac('sha256', $token, $cle);
        return hash_equals($hmacCalcule, $hmacStocke);
    }
}