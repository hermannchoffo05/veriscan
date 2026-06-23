<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\QrCode;
use App\Models\Produit;
use App\Models\Signalement;

class VerificationController extends Controller
{
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
            return view('verify', [
                'statut'  => 'contrefait',
                'message' => 'La signature de ce QR code est invalide.',
                'produit' => null,
                'qrCode'  => $qrCode,
                'token'   => $token,
            ]);
        }

        if ($qrCode->statut === 'revoque') {
            return view('verify', [
                'statut'  => 'revoque',
                'message' => "Ce QR code a été révoqué.",
                'produit' => $qrCode->lot->produit ?? null,
                'qrCode'  => $qrCode,
                'token'   => $token,
            ]);
        }

        $qrCode->increment('nb_scans');

        $signalementsEnCours = Signalement::where('qr_code_id', $qrCode->id)
            ->where('statut', 'en_cours')
            ->count();

        $statut  = $signalementsEnCours > 0 ? 'suspect' : 'authentique';
        $produit = $qrCode->lot->produit ?? null;

        return view('verify', [
            'statut'            => $statut,
            'message'           => $statut === 'authentique'
                ? 'Ce produit est authentique.'
                : 'Ce produit a reçu des signalements suspects.',
            'produit'           => $produit,
            'qrCode'            => $qrCode,
            'lot'               => $qrCode->lot,
            'fabricant'         => $produit?->fabricant,
            'signalementsCount' => $signalementsEnCours,
            'token'             => $token,
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

        $statut = $signalementsEnCours > 0 ? 'suspect' : 'authentique';

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
            'photo_preuve'      => 'nullable|image|max:2048',
        ]);

        $data = [
            'qr_code_id'        => $request->qr_code_id,
            'description'       => $request->description,
            'nom_signalant'     => $request->nom_signalant,
            'contact_signalant' => $request->contact_signalant,
            'statut'            => 'en_cours',
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'localisation' => $request->localisation,
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
            }
        }

        $signalement = Signalement::create($data);

        return back()->with('success_signalement', 'Votre signalement a été enregistré.')
                     ->with('analyse_ia', $analyseIA);
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

    private function verifierHmac(string $token, ?string $hmacStocke): bool
    {
        if (!$hmacStocke) return true;
        $cle         = config('app.key');
        $hmacCalcule = hash_hmac('sha256', $token, $cle);
        return hash_equals($hmacCalcule, $hmacStocke);
    }
}