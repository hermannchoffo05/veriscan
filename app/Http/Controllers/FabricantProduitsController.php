<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Produit;
use App\Models\QrCode;
use App\Models\Signalement;
use App\Models\Verification;

class FabricantProduitsController extends Controller
{
    public function index()
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produits  = Produit::where('fabricant_id', $fabricant->id)
                            ->with('lots')
                            ->latest()
                            ->paginate(12);

        foreach ($produits as $produit) {
            $lotIds  = $produit->lots->pluck('id');
            $qrIds   = QrCode::whereIn('lot_id', $lotIds)->pluck('id');

            $produit->total_qr    = $qrIds->count();
            $produit->total_scans = QrCode::whereIn('id', $qrIds)->sum('nb_scans');
            $produit->total_sigs  = Signalement::whereIn('qr_code_id', $qrIds)
                                        ->where('statut', 'en_cours')->count();
            $produit->dernier_lot = $produit->lots->sortByDesc('created_at')->first();
            $produit->est_suspect = $produit->total_sigs > 0;
        }

        return view('fabricant.produits.index', compact('produits', 'fabricant'));
    }

    public function create()
    {
        $fabricant = Auth::guard('fabricant')->user();
        return view('fabricant.produits.create', compact('fabricant'));
    }

    public function store(Request $request)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $validated = $request->validate([
            'nom'         => 'required|string|max:255',
            'categorie'   => 'required|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);
        $validated['fabricant_id'] = $fabricant->id;
        $validated['code_produit'] = 'VS-' . strtoupper(Str::random(8));

        $restant = $fabricant->quotaProduitsRestant();
        if ($restant !== null && $restant <= 0) {
           return back()->withInput()->with('warning',
    "Vous avez atteint la limite de produits de votre plan ({$fabricant->limites()['label']}). Passez à un plan supérieur pour en ajouter davantage.");
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('produits', 'public');
        }
        Produit::create($validated);
        return redirect()->route('fabricant.produits.index')
                         ->with('success', 'Produit créé avec succès.');
    }

    public function show($id)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit   = Produit::where('fabricant_id', $fabricant->id)
                            ->with(['lots.qrcodes'])
                            ->findOrFail($id);
        return view('fabricant.produits.show', compact('produit', 'fabricant'));
    }

    public function edit($id)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit   = Produit::where('fabricant_id', $fabricant->id)
                            ->findOrFail($id);
        return view('fabricant.produits.edit', compact('produit', 'fabricant'));
    }

    public function update(Request $request, $id)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit   = Produit::where('fabricant_id', $fabricant->id)
                            ->findOrFail($id);
        $validated = $request->validate([
            'nom'         => 'required|string|max:255',
            'categorie'   => 'required|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('produits', 'public');
        }
        $produit->update($validated);
        return redirect()->route('fabricant.produits.index')
                         ->with('success', 'Produit mis à jour.');
    }

    public function destroy($id)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit   = Produit::where('fabricant_id', $fabricant->id)
                            ->findOrFail($id);
        $produit->delete();
        return redirect()->route('fabricant.produits.index')
                         ->with('success', 'Produit supprimé.');
    }

    private function groqCall(string $prompt, int $maxTokens = 200): ?string
    {
        $apiKey = env('GROQ_API_KEY');
        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey,
        ])->timeout(15)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model'       => 'llama-3.1-8b-instant',
            'messages'    => [['role' => 'user', 'content' => $prompt]],
            'max_tokens'  => $maxTokens,
            'temperature' => 0.7,
        ]);
        return $response->json('choices.0.message.content');
    }

    /**
     * Réponse JSON standard quand le quota mensuel de l'assistant IA produit
     * (description + classification, compté ensemble) est épuisé.
     */
    private function reponseQuotaAtteint(): \Illuminate\Http\JsonResponse
    {
        $fabricant = Auth::guard('fabricant')->user();
        $limite    = $fabricant->quotaIAAssistantMensuel();
        $resetLe   = $fabricant->prochaineReinitialisationIA()->format('d/m/Y');

        return response()->json([
            'error'    => 'quota_atteint',
            'message'  => "Vous avez atteint votre quota mensuel de {$limite} requêtes IA pour l'assistant produit. Il sera réinitialisé le {$resetLe}.",
            'reset_le' => $resetLe,
        ], 429);
    }

    public function generateDescription(Request $request)
    {
        $fabricant = Auth::guard('fabricant')->user();

        // ✅ Quota mensuel (gratuit/starter) au lieu d'un blocage total
        if (!$fabricant->peutUtiliserAssistantProduitIA()) {
            return $this->reponseQuotaAtteint();
        }

        $request->validate([
            'nom'       => 'required|string|max:255',
            'categorie' => 'required|string',
        ]);
        $nom       = $request->input('nom');
        $categorie = $request->input('categorie');
        $prompt    = "Tu es un expert en rédaction de fiches produits professionnelles pour une plateforme de vérification d'authenticité appelée VeriScan. Génère une description produit concise, professionnelle et informative (3 à 4 phrases maximum) pour le produit suivant :\n\nNom du produit : {$nom}\nCatégorie : {$categorie}\n\nLa description doit :\n- Être rédigée en français\n- Présenter le produit de façon claire et rassurante\n- Mettre en avant la qualité et l'authenticité\n- Être adaptée à la catégorie du produit\n- Ne pas inventer de caractéristiques techniques précises\n\nRéponds uniquement avec la description, sans titre ni guillemets.";
        try {
            $description = $this->groqCall($prompt, 200);
            if ($description) {
                // ✅ On ne consomme le quota qu'en cas de succès réel
                $fabricant->incrementerAssistantProduitIA();
                return response()->json(['description' => trim($description)]);
            }
            return response()->json(['error' => 'Génération échouée'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function classifyCategory(Request $request)
    {
        $fabricant = Auth::guard('fabricant')->user();

        // ✅ Quota mensuel (gratuit/starter) au lieu d'un blocage total
        if (!$fabricant->peutUtiliserAssistantProduitIA()) {
            return $this->reponseQuotaAtteint();
        }

        $request->validate([
            'nom'       => 'required|string|max:255',
            'categorie' => 'nullable|string',
        ]);

        $nom       = $request->input('nom');
        $categorie = $request->input('categorie', '');

        $prompt = "Tu es un expert en classification de produits pour la plateforme VeriScan au Cameroun. "
            . "Propose UNE SEULE catégorie courte et précise (2-3 mots maximum) pour ce produit.\n"
            . "Produit : {$nom}\n"
            . ($categorie ? "Catégorie actuelle : {$categorie}\n" : "")
            . "Exemples de catégories : Alimentaire, Cosmétique, Pharmaceutique, Électronique, Textile, Boisson, Hygiène, Agriculture.\n"
            . "Réponds UNIQUEMENT avec le nom de la catégorie, sans ponctuation ni explication.";

        try {
            $category = $this->groqCall($prompt, 20);
            if ($category) {
                $fabricant->incrementerAssistantProduitIA();
                return response()->json(['category' => trim($category)]);
            }
            return response()->json(['error' => 'Classification échouée'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function chat(Request $request)
    {
        // Chatbot conversationnel — reste réservé Pro/Entreprise (décision distincte)
        $fabricant = Auth::guard('fabricant')->user();
        if (!$fabricant->aAccesIA()) {
            return response()->json(['answer' => "L'assistant IA est réservé aux plans Pro et Entreprise."], 403);
        }

        $request->validate([
            'question' => 'required|string|max:500',
            'locale'   => 'nullable|string|in:fr,en',
        ]);
        $question     = $request->input('question');
        $locale       = $request->input('locale', 'fr');
        $systemPrompt = $locale === 'fr'
            ? "Tu es l'assistant officiel de VeriScan, une plateforme SaaS camerounaise de vérification d'authenticité des produits via QR codes. Tu aides les fabricants à utiliser la plateforme. Réponds uniquement en français, de façon claire, concise et professionnelle (3 phrases max). Si la question ne concerne pas VeriScan, réponds poliment que tu ne peux aider que sur VeriScan. Fonctionnalités : créer des produits, lots, QR codes, voir statistiques, gérer signalements, télécharger rapports, modifier profil, voir carte des risques."
            : "You are the official assistant of VeriScan, a Cameroonian SaaS platform for product authenticity verification via QR codes. Reply only in English, clearly and concisely (max 3 sentences). If the question is not about VeriScan, politely say you can only help with VeriScan. Features: create products, batches, QR codes, view statistics, manage reports, edit profile, view risk map.";
        $prompt = $systemPrompt . "\n\nQuestion : " . $question;
        try {
            $answer = $this->groqCall($prompt, 150);
            return response()->json([
                'answer' => $answer ?? ($locale === 'fr'
                    ? "Je rencontre un problème temporaire. Réessayez dans quelques instants."
                    : "I'm having a temporary issue. Please try again.")
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'answer' => $locale === 'fr'
                    ? "Je ne suis pas disponible pour l'instant. Réessayez dans quelques instants."
                    : "I'm not available right now. Please try again."
            ]);
        }
    }
}