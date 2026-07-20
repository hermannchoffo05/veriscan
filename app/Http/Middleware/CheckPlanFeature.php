<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPlanFeature
{
    private array $textes = [
        'carte_risques' => [
            'topbar'  => 'Carte',
            'titre'   => 'Carte verrouillée',
            'message' => "La carte des risques et signalements est réservée aux plans Pro et Entreprise.",
        ],
        'statistiques' => [
            'topbar'  => 'Statistiques',
            'titre'   => 'Statistiques bloquées',
            'message' => "Passez à un plan payant (Starter, Pro ou Entreprise) pour accéder à vos statistiques.",
        ],
        'rapports' => [
            'topbar'  => 'Rapports',
            'titre'   => 'Rapports verrouillés',
            'message' => "La génération de rapports PDF n'est pas incluse dans le plan Gratuit. Passez à un plan supérieur pour y accéder.",
        ],
        'ia' => [
            'topbar'  => 'Assistant IA',
            'titre'   => 'Assistant IA verrouillé',
            'message' => "Le chatbot assistant est réservé aux plans Pro et Entreprise.",
        ],
    ];

    public function handle(Request $request, Closure $next, string $feature)
    {
        $fabricant = Auth::guard('fabricant')->user();

        $autorise = match ($feature) {
            'carte_risques' => $fabricant->aAccesCarteRisques(),
            'statistiques'  => $fabricant->aAccesStatistiques(),
            'rapports'      => $fabricant->aAccesRapports(),
            'ia'            => $fabricant->aAccesIA(),
            default         => false,
        };

        if (!$autorise) {
            $texte = $this->textes[$feature] ?? [
                'topbar'  => '',
                'titre'   => "Fonctionnalité verrouillée",
                'message' => "Cette fonctionnalité n'est pas incluse dans votre plan actuel ({$fabricant->limites()['label']}). Passez à un plan supérieur pour y accéder.",
            ];

            if ($request->expectsJson()) {
                return response()->json(['error' => $texte['message']], 403);
            }

            return response()->view('fabricant.plan-locked', [
                'topbarTitre'         => $texte['topbar'],
                'titrePage'           => $texte['topbar'],
                'titreVerrouillage'   => $texte['titre'],
                'messageVerrouillage' => $texte['message'],
            ]);
        }

        return $next($request);
    }
}