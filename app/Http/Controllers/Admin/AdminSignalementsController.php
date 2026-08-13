<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminSignalementsController extends Controller
{
    public function index()
    {
        $signalements = Signalement::with('qrCode.lot.produit.fabricant')
            ->latest()
            ->paginate(15);
        return view('admin.signalements.index', compact('signalements'));
    }

    public function show($id)
    {
        $signalement = Signalement::with('qrCode.lot.produit.fabricant')
            ->findOrFail($id);
        return view('admin.signalements.show', compact('signalement'));
    }

    public function traiter(Request $request, $id)
    {
        $request->validate([
            'statut' => 'required|in:en_cours,traite,rejete',
        ]);

        $signalement = Signalement::findOrFail($id);
        $signalement->update(['statut' => $request->statut]);

        return back()->with('success', 'Signalement mis à jour avec succès.');
    }

    public function escalader($id)
    {
        $signalement = Signalement::findOrFail($id);
        $signalement->update(['statut' => 'traite']);

        return back()->with('success', 'Signalement escaladé vers MINCOMMERCE/ANOR.');
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
                    // ⚠️ meta-llama/llama-4-scout-17b-16e-instruct a été déprécié par Groq.
                    // qwen/qwen3.6-27b est le modèle vision actuel (statut "preview" chez Groq,
                    // donc à re-vérifier périodiquement sur console.groq.com/docs/models).
                    'model'       => 'qwen/qwen3.6-27b',
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

            // DEBUG TEMPORAIRE : capture la vraie réponse d'erreur de Groq
            Log::error('Erreur Groq analyserPhoto', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return response()->json(['error' => 'Erreur lors de l\'analyse IA.'], 500);

        } catch (\Exception $e) {
            // DEBUG TEMPORAIRE : capture l'exception PHP réelle (timeout, DNS, etc.)
            Log::error('Exception analyserPhoto', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json(['error' => 'Service IA indisponible.'], 500);
        }
    }

    public function resume()
    {
        $signalements = Signalement::with('qrCode.lot.produit.fabricant')
            ->latest()
            ->take(50)
            ->get();

        $total   = $signalements->count();
        $enCours = $signalements->where('statut', 'en_cours')->count();
        $traites = $signalements->where('statut', 'traite')->count();
        $rejetes = $signalements->where('statut', 'rejete')->count();

        $statsFormattees = [
            'total'    => $total,
            'en_cours' => $enCours,
            'traites'  => $traites,
            'rejetes'  => $rejetes,
        ];

        if ($signalements->isEmpty()) {
            return response()->json([
                'resume' => 'Aucun signalement à analyser pour le moment.',
                'stats'  => $statsFormattees,
            ]);
        }

        // ✅ Opérateur ?-> pour éviter les TypeError sur relations nulles
        $data = $signalements->map(function ($sig) {
            $produit = $sig->qrCode?->lot?->produit ?? null;
            $fab     = $produit?->fabricant ?? null;
            return [
                'produit'     => $produit->nom ?? 'Inconnu',
                'fabricant'   => $fab->nom_entreprise ?? 'Inconnu',
                'description' => $sig->description ?? '',
                'statut'      => $sig->statut,
                'date'        => $sig->created_at->format('d/m/Y'),
            ];
        })->toArray();

        $produitsCount = $signalements->groupBy(function ($sig) {
            // ✅ Opérateur ?-> pour éviter les TypeError sur relations nulles
            return $sig->qrCode?->lot?->produit?->nom ?? 'Inconnu';
        })->map->count()->sortDesc()->take(3);

        $produitsTop = $produitsCount->map(function ($count, $nom) {
            return "$nom ($count signalement(s))";
        })->implode(', ');

        // ✅ Espaces corrigés dans le prompt
        $prompt = "Tu es un analyste anti-contrefaçon pour VeriScan au Cameroun.
Voici les données des $total derniers signalements de produits suspects :

- En cours : $enCours | Traités : $traites | Rejetés : $rejetes
- Produits les plus signalés : $produitsTop
- Données détaillées : " . json_encode(array_slice($data, 0, 20), JSON_UNESCAPED_UNICODE) . "

Génère un résumé analytique concis en français (5-7 phrases maximum) qui :
1. Indique le volume et la tendance des signalements
2. Identifie les produits et zones les plus touchés
3. Donne une recommandation d'action prioritaire pour l'admin
4. Reste professionnel et factuel

Ne commence pas par 'Voici' ou 'Bien sûr'. Va directement au résumé.";

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    // ⚠️ llama-3.1-8b-instant a été déprécié par Groq.
                    'model'       => 'openai/gpt-oss-120b',
                    'messages'    => [['role' => 'user', 'content' => $prompt]],
                    'max_tokens'  => 400,
                    'temperature' => 0.4,
                ]);

            if ($response->successful()) {
                $resume = $response->json('choices.0.message.content') ?? 'Résumé indisponible.';
                return response()->json([
                    'resume' => $resume,
                    'stats'  => $statsFormattees,
                ]);
            }

            return response()->json([
                'resume' => 'Erreur lors de la génération du résumé par Groq.',
                'stats'  => $statsFormattees,
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'resume' => 'Service IA temporairement indisponible.',
                'stats'  => $statsFormattees,
            ], 500);
        }
    }
}