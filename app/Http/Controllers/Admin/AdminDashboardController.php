<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fabricant;
use App\Models\Produit;
use App\Models\QrCode;
use App\Models\Signalement;
use App\Models\Verification;
use App\Services\AIRiskScoringService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// ✅ Espace corrigé : "extends Controller" (était "extendsController")
class AdminDashboardController extends Controller
{
    public function index()
    {
        // -- Totaux globaux ---------------------------------------------------
        $totalFabricants     = Fabricant::count();
        $totalProduits       = Produit::count();
        $totalScans          = QrCode::sum('nb_scans');
        $totalSignalements   = Signalement::count();
        $signalementsEnCours = Signalement::where('statut', 'en_cours')->count();

        // -- Variations aujourd'hui -------------------------------------------
        $fabricantsAujourdhui   = Fabricant::whereDate('created_at', today())->count();
        $signalementsAujourdhui = Signalement::whereDate('created_at', today())->count();

        // -- Derniers fabricants ----------------------------------------------
        $derniersFabricants = Fabricant::latest()->take(5)->get();

        // -- Derniers signalements --------------------------------------------
        $derniersSignalements = Signalement::with('qrCode.lot.produit')
            ->latest()
            ->take(6)
            ->get();

        // -- Fabricants en attente --------------------------------------------
        $fabricantsEnAttente = Fabricant::where('statut', 'en_attente')
            ->orWhereNull('statut')
            ->count();

        // -- Signalements par statut ------------------------------------------
        $sigParStatut = [
            'en_cours' => Signalement::where('statut', 'en_cours')->count(),
            'traite'   => Signalement::where('statut', 'traite')->count(),
            'rejete'   => Signalement::where('statut', 'rejete')->count(),
        ];

        // -- Activité scans 7 derniers jours ----------------------------------
        $scansParJour = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $scansParJour[] = [
                'jour'  => $date->isoFormat('ddd'),
                'total' => Verification::whereDate('created_at', $date->toDateString())->count(),
            ];
        }

        // -- Module IA – Résumé scoring ---------------------------------------
        $scoringService = new AIRiskScoringService();
        $resumeIA       = $scoringService->getResumeDashboard();

        // -- Top 5 produits à risque ------------------------------------------
        $topRisques = \App\Models\RiskScore::with('produit.fabricant')
            ->orderByDesc('score')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalFabricants',
            'totalProduits',
            'totalScans',
            'totalSignalements',
            'signalementsEnCours',
            'fabricantsAujourdhui',
            'signalementsAujourdhui',
            'derniersFabricants',
            'derniersSignalements',
            'fabricantsEnAttente',
            'sigParStatut',
            'scansParJour',
            'resumeIA',
            'topRisques'
        ));
    }

    /**
     * API endpoint pour le polling AJAX (refresh toutes les 60s)
     */
    public function stats()
    {
        $scoringService = new AIRiskScoringService();
        $resumeIA       = $scoringService->getResumeDashboard();

        return response()->json([
            'fabricants'         => Fabricant::count(),
            'produits'           => Produit::count(),
            'scans'              => QrCode::sum('nb_scans'),
            'signalements'       => Signalement::count(),
            'signalements_cours' => Signalement::where('statut', 'en_cours')->count(),
            'fabricants_attente' => Fabricant::where('statut', 'en_attente')
                                        ->orWhereNull('statut')->count(),
            'ia_critiques'       => $resumeIA['critiques'],
            'ia_score_moyen'     => $resumeIA['score_moyen'],
        ]);
    }
}