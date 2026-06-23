<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Produit;
use App\Models\Fabricant;
use App\Models\Signalement;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AssistantApiController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = $request->user();

        // ── Contexte dynamique ──────────────────────────────────────────────
        $produits = Produit::with('fabricant')->latest()->take(20)->get()
            ->map(fn($p) => $p->nom . ' (' . ($p->fabricant->nom_entreprise ?? 'Inconnu') . ')')
            ->implode(', ');

        $fabricants = Fabricant::latest()->take(10)->get()
            ->map(fn($f) => $f->nom_entreprise . ' - ' . ($f->pays ?? 'Cameroun'))
            ->implode(', ');

        $signalements = Signalement::with('qrCode.lot.produit')->latest()->take(10)->get()
            ->map(function ($s) {
                $produit = $s->qrCode->lot->produit->nom ?? 'Inconnu';
                return "$produit (statut: {$s->statut}, le {$s->created_at->format('d/m/Y')})";
            })->implode(', ');

        $historiqueUser = '';
        if ($user) {
            $verifications = Verification::where('user_id', $user->id)
                ->with('qrCode.lot.produit')->latest()->take(5)->get()
                ->map(function ($v) {
                    $produit = $v->qrCode->lot->produit->nom ?? 'Inconnu';
                    return "$produit ({$v->created_at->format('d/m/Y')})";
                })->implode(', ');

            $mesSignalements = Signalement::where('user_id', $user->id)
                ->latest()->take(3)->get()
                ->map(fn($s) => "signalement statut: {$s->statut}")
                ->implode(', ');

            $historiqueUser = "
- Nom de l'utilisateur connecté : {$user->name}
- Ses dernières vérifications : " . ($verifications ?: 'Aucune') . "
- Ses signalements : " . ($mesSignalements ?: 'Aucun');
        }

        // ── Historique de la conversation (10 derniers messages) ────────────
        $historique = [];
        if ($user) {
            $anciens = Conversation::where('user_id', $user->id)
                ->latest()->take(10)->get()->reverse();
            foreach ($anciens as $msg) {
                $historique[] = [
                    'role'    => $msg->role,
                    'content' => $msg->message,
                ];
            }
        }

        $systemPrompt = "Tu es l'assistant IA de VeriScan, une plateforme anti-contrefaçon camerounaise.
Tu réponds uniquement en français de manière concise et professionnelle.
Tu aides les consommateurs à détecter les produits contrefaits et à utiliser VeriScan.

Voici le contexte actuel de la plateforme :
- Produits enregistrés : " . ($produits ?: 'Aucun') . "
- Fabricants vérifiés : " . ($fabricants ?: 'Aucun') . "
- Signalements récents : " . ($signalements ?: 'Aucun') . "
{$historiqueUser}

Utilise ce contexte pour répondre de manière personnalisée et précise.
Ne mentionne pas que tu as accès à une base de données — réponds naturellement.";

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $historique,
            [['role' => 'user', 'content' => $request->message]]
        );

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'llama-3.1-8b-instant',
                    'messages'    => $messages,
                    'max_tokens'  => 500,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $text = $response->json('choices.0.message.content')
                    ?? 'Désolé, je ne peux pas répondre pour le moment.';

                // Sauvegarde les deux messages
                if ($user) {
                    Conversation::create([
                        'user_id' => $user->id,
                        'role'    => 'user',
                        'message' => $request->message,
                    ]);
                    Conversation::create([
                        'user_id' => $user->id,
                        'role'    => 'assistant',
                        'message' => $text,
                    ]);
                }

                return response()->json([
                    'success'  => true,
                    'response' => $text,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $response->status(),
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Récupérer l'historique
    public function historique(Request $request)
    {
        $conversations = Conversation::where('user_id', $request->user()->id)
            ->oldest()
            ->get()
            ->map(fn($c) => [
                'role'    => $c->role,
                'text'    => $c->message,
                'created_at' => $c->created_at->format('d/m/Y H:i'),
            ]);

        return response()->json([
            'success'       => true,
            'conversations' => $conversations,
        ]);
    }

    // Effacer l'historique
    public function clearHistorique(Request $request)
    {
        Conversation::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Historique effacé.',
        ]);
    }
}