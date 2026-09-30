<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Produit;
use App\Models\QrCode;
use App\Models\Signalement;
use App\Models\Verification;
use App\Models\CertificationPharmaceutique;
use App\Models\CertificationCosmetique;

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
            'categorie'   => ['required', 'string', Rule::in(Produit::CATEGORIES_AUTORISEES)],
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'justificatif' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'justificatif.required' => 'Le justificatif (AMM ou certificat de conformité) est obligatoire.',
            'justificatif.mimes'    => 'Le justificatif doit être un PDF, JPG ou PNG.',
            'justificatif.max'      => 'Le justificatif ne doit pas dépasser 5 Mo.',
        ]);

        // Champs de certification propres au secteur choisi — validés séparément
        // car les règles (et les noms de champs) changent selon la catégorie.
        $validatedCertif = $request->validate(
            $this->reglesCertification($validated['categorie'])
        );

        $restant = $fabricant->quotaProduitsRestant();
        if ($restant !== null && $restant <= 0) {
           return back()->withInput()->with('warning',
    "Vous avez atteint la limite de produits de votre plan ({$fabricant->limites()['label']}). Passez à un plan supérieur pour en ajouter davantage.");
        }

        $validated['fabricant_id'] = $fabricant->id;
        $validated['code_produit'] = 'VS-' . strtoupper(Str::random(8));

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('produits', 'public');
        }

        // Justificatif : stocké sur le disque PRIVÉ (non accessible par URL publique),
        // consulté uniquement par l'autorité de certification via une route protégée.
        $validated['justificatif']         = $request->file('justificatif')->store('justificatifs', 'local');
        $validated['statut_certification'] = Produit::CERT_SOUMIS;

        $produit = Produit::create($validated);
        $this->enregistrerCertification($produit, $validated['categorie'], $validatedCertif);

        return redirect()->route('fabricant.produits.index')
                         ->with('success', 'Produit créé. Il est en cours de validation par l\'autorité de certification : vous pourrez générer ses QR codes dès qu\'il sera certifié.');
    }

    public function show($id)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit   = Produit::where('fabricant_id', $fabricant->id)
                            ->with(['lots.qrcodes', 'certificationPharmaceutique', 'certificationCosmetique'])
                            ->findOrFail($id);
        return view('fabricant.produits.show', compact('produit', 'fabricant'));
    }

    public function edit($id)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit   = Produit::where('fabricant_id', $fabricant->id)
                            ->with(['certificationPharmaceutique', 'certificationCosmetique'])
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
            'categorie'   => ['required', 'string', Rule::in(Produit::CATEGORIES_AUTORISEES)],
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validatedCertif = $request->validate(
            $this->reglesCertification($validated['categorie'])
        );

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('produits', 'public');
        }

        // Un produit rejeté ne peut être re-soumis qu'avec un nouveau justificatif.
        if ($produit->statut_certification === Produit::CERT_REJETE && !$request->hasFile('justificatif')) {
            return back()->withInput()->with('warning',
                'Ce produit a été rejeté : déposez un nouveau justificatif pour le soumettre à nouveau.');
        }

        // Nouveau justificatif => on remplace l'ancien et on repasse en validation.
        if ($request->hasFile('justificatif')) {
            if ($produit->justificatif) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($produit->justificatif);
            }
            $validated['justificatif']         = $request->file('justificatif')->store('justificatifs', 'local');
            $validated['statut_certification'] = Produit::CERT_SOUMIS;
            $validated['motif_decision']       = null;
        } else {
            unset($validated['justificatif']);
        }

        $produit->update($validated);
        $this->enregistrerCertification($produit, $validated['categorie'], $validatedCertif);

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

    /**
     * Règles de validation des champs de certification, propres à chaque
     * secteur. C'est ici que "chaque produit a des règles de certification
     * différentes" devient vérifiable dans le code, et pas seulement dans
     * le mémoire.
     */
    private function reglesCertification(?string $categorie): array
    {
        return match ($categorie) {
            'Pharmaceutique' => [
                'numero_amm'            => 'required|string|max:100',
                'laboratoire_fabricant' => 'nullable|string|max:255',
                'date_amm'              => 'nullable|date',
            ],
            'Cosmétique' => [
                'liste_inci'            => 'required|string',
                'certificat_conformite' => 'nullable|string|max:255',
                'date_certification'    => 'nullable|date',
            ],
            default => [],
        };
    }

    /**
     * Crée ou met à jour la fiche de certification liée au produit,
     * dans la table correspondant à son secteur.
     */
    private function enregistrerCertification(Produit $produit, string $categorie, array $data): void
    {
        match ($categorie) {
            'Pharmaceutique' => CertificationPharmaceutique::updateOrCreate(
                ['produit_id' => $produit->id], $data
            ),
            'Cosmétique' => CertificationCosmetique::updateOrCreate(
                ['produit_id' => $produit->id], $data
            ),
            default => null,
        };
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

        if (!$fabricant->peutUtiliserAssistantProduitIA()) {
            return $this->reponseQuotaAtteint();
        }

        $request->validate([
            'nom'       => 'required|string|max:255',
            'categorie' => ['required', 'string', Rule::in(Produit::CATEGORIES_AUTORISEES)],
        ]);
        $nom       = $request->input('nom');
        $categorie = $request->input('categorie');
        $prompt    = "Tu es un expert en rédaction de fiches produits professionnelles pour une plateforme de vérification d'authenticité appelée VeriScan. Génère une description produit concise, professionnelle et informative (3 à 4 phrases maximum) pour le produit suivant :\n\nNom du produit : {$nom}\nCatégorie : {$categorie}\n\nLa description doit :\n- Être rédigée en français\n- Présenter le produit de façon claire et rassurante\n- Mettre en avant la qualité et l'authenticité\n- Être adaptée à la catégorie du produit\n- Ne pas inventer de caractéristiques techniques précises\n\nRéponds uniquement avec la description, sans titre ni guillemets.";
        try {
            $description = $this->groqCall($prompt, 200);
            if ($description) {
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

        if (!$fabricant->peutUtiliserAssistantProduitIA()) {
            return $this->reponseQuotaAtteint();
        }

        $request->validate([
            'nom'       => 'required|string|max:255',
            'categorie' => 'nullable|string',
        ]);

        $nom       = $request->input('nom');
        $categorie = $request->input('categorie', '');

        // Le prompt est volontairement restreint aux deux secteurs couverts
        // par VeriScan — il ne doit plus jamais suggérer "Agroalimentaire",
        // "Électronique", "Textile", etc. Un produit hors périmètre reçoit
        // "Hors périmètre".
        $prompt = "Tu es un expert en classification de produits pour la plateforme VeriScan au Cameroun. "
            . "VeriScan certifie exclusivement deux secteurs : Pharmaceutique, Cosmétique.\n"
            . "Produit : {$nom}\n"
            . ($categorie ? "Catégorie actuelle : {$categorie}\n" : "")
            . "Si le produit appartient à un de ces deux secteurs, réponds avec son nom exact. "
            . "Sinon, réponds exactement \"Hors périmètre\".\n"
            . "Réponds UNIQUEMENT avec un seul de ces trois mots, sans ponctuation ni explication : "
            . "Pharmaceutique, Cosmétique, Hors périmètre.";

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