<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiskScore;
use App\Models\Signalement;
use App\Models\QrCode;
use App\Services\AIRiskScoringService;

class AdminCarteController extends Controller
{
    public function index()
    {
        $scoring = new AIRiskScoringService();

        // ── Scores IA (zones fixes) ─────────────────────────────────────────
        $scores = RiskScore::with('produit.fabricant')->get();
        $zones  = AIRiskScoringService::ZONES_CAMEROUN;

        $marqueurs = $scores->map(function ($rs) use ($zones) {
            $zone  = $zones[$rs->zone_code] ?? $zones['YAO'];
            $lotIds = $rs->produit?->lots()->pluck('id') ?? collect();
            $qrIds  = QrCode::whereIn('lot_id', $lotIds)->pluck('id');
            $nbSig  = Signalement::whereIn('qr_code_id', $qrIds)->count();
            $nbQr   = $qrIds->count();

            return [
                'lat'          => $zone['lat'],
                'lng'          => $zone['lng'],
                'produit'      => $rs->produit->nom ?? 'Inconnu',
                'fabricant'    => $rs->produit->fabricant->nom_entreprise ?? 'Inconnu',
                'categorie'    => $rs->produit->categorie ?? '—',
                'score'        => $rs->score,
                'niveau'       => $rs->niveau,
                'signalements' => $nbSig,
                'qrcodes'      => $nbQr,
                'type'         => 'score_ia',
            ];
        })->values()->toArray();

        // ── Signalements GPS réels (mobile + web) ───────────────────────────
        $signalementsGps = Signalement::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('qrCode.lot.produit.fabricant')
            ->latest()
            ->get()
            ->map(function ($s) {
                $produit   = $s->qrCode?->lot?->produit;
                $fabricant = $produit?->fabricant;
                $score     = $s->score_ia ?? 50;

                return [
                    'lat'          => $s->latitude,
                    'lng'          => $s->longitude,
                    'produit'      => $produit?->nom ?? 'Produit non associé',
                    'fabricant'    => $fabricant?->nom_entreprise ?? 'Mobile',
                    'categorie'    => $produit?->categorie ?? '—',
                    'score'        => $score,
                    // ✅ Seuils alignés sur la légende affichée :
                    // Faible 0–30 / Modéré 31–60 / Élevé 61–80 / Critique 81–100
                    'niveau'       => $this->niveauDepuisScore($score),
                    'signalements' => 1,
                    'qrcodes'      => 0,
                    'description'  => $s->description ?? '',
                    'localisation' => $s->localisation ?? 'Localisation inconnue',
                    'region'       => $s->region ?? '',
                    'statut'       => $s->statut,
                    'date'         => $s->created_at->format('d/m/Y H:i'),
                    'type'         => 'gps_reel',
                ];
            })->toArray();

        // ── Statistiques résumé ─────────────────────────────────────────────
        $totalScores = $scores->count();
        $scoreMoyen  = $totalScores > 0 ? round($scores->avg('score'), 1) : 0;
        $niveaux     = [
            'faible'   => $scores->where('niveau', 'faible')->count(),
            'modere'   => $scores->where('niveau', 'modere')->count(),
            'eleve'    => $scores->where('niveau', 'eleve')->count(),
            'critique' => $scores->where('niveau', 'critique')->count(),
        ];

        // ── Top 5 produits à risque ─────────────────────────────────────────
        $topRisques = RiskScore::with('produit.fabricant')
            ->orderByDesc('score')
            ->take(5)
            ->get();

        // ── Total signalements GPS ──────────────────────────────────────────
        $totalSignalementsGps = count($signalementsGps);

        return view('admin.carte', compact(
            'marqueurs',
            'signalementsGps',
            'totalScores',
            'scoreMoyen',
            'niveaux',
            'topRisques',
            'totalSignalementsGps'
        ));
    }

    /**
     * ✅ NOUVEAU : Détermine le niveau à partir d'un score, avec les mêmes
     * seuils que la légende affichée dans admin/carte.blade.php :
     * Faible 0–30 · Modéré 31–60 · Élevé 61–80 · Critique 81–100
     */
    private function niveauDepuisScore(float $score): string
    {
        if ($score >= 81) return 'critique';
        if ($score >= 61) return 'eleve';
        if ($score >= 31) return 'modere';
        return 'faible';
    }
}