<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Produit;
use App\Models\Lot;
use App\Models\QrCode;
use App\Models\Signalement;
use App\Models\Verification;
use Carbon\Carbon;

class FabricantStatistiquesController extends Controller
{
    public function index()
    {
        $fabricant = Auth::guard('fabricant')->user();

        // ✅ AJOUTÉ : Statistiques retirée du plan Gratuit. Affiche une page
        // "verrouillée" (façon carte/rapports) au lieu de calculer et afficher
        // des données auxquelles le plan n'a plus droit.
        if (!$fabricant->limites()['statistiques']) {
            return view('fabricant.plan-locked', [
                'topbarTitre'         => __('messages.mes') . ' ' . __('messages.statistiques'),
                'titrePage'           => __('messages.statistiques'),
                'titreVerrouillage'   => app()->getLocale() === 'en' ? 'Statistics locked' : 'Statistiques bloquées',
                'messageVerrouillage' => app()->getLocale() === 'en'
                    ? 'Upgrade to Starter, Pro or Enterprise to access your performance statistics.'
                    : 'Passez au plan Starter, Pro ou Entreprise pour accéder à vos statistiques de performance.',
            ]);
        }

        $produitIds = Produit::where('fabricant_id', $fabricant->id)->pluck('id');
        $lotIds     = Lot::whereIn('produit_id', $produitIds)->pluck('id');
        $qrcodeIds  = QrCode::whereIn('lot_id', $lotIds)->pluck('id');

        // -- Totaux -----------------------------------------------------------
        $totalProduits = $produitIds->count();
        $totalLots     = $lotIds->count();
        $totalQrcodes  = $qrcodeIds->count();
        $totalScans    = Verification::whereIn('qr_code_id', $qrcodeIds)->count();

        $totalSignalements = Signalement::whereIn('qr_code_id', $qrcodeIds)->count();

        // -- Signalements des 6 derniers mois ---------------------------------
        $signalementsParMois = [];
        for ($i = 5; $i >= 0; $i--) {
            $mois = Carbon::now()->subMonths($i);
            $signalementsParMois[] = [
                'mois'  => $mois->locale(app()->getLocale())->isoFormat('MMM YYYY'),
                'total' => Signalement::whereIn('qr_code_id', $qrcodeIds)
                                      ->whereYear('created_at', $mois->year)
                                      ->whereMonth('created_at', $mois->month)
                                      ->count(),
            ];
        }

        // -- Top 5 produits par scans -----------------------------------------
        $topProduits = Produit::where('fabricant_id', $fabricant->id)
            ->select('produits.*')
            ->selectSub(function ($query) {
                $query->selectRaw('COUNT(verifications.id)')
                    ->from('lots')
                    ->join('qr_codes', 'qr_codes.lot_id', '=', 'lots.id')
                    ->join('verifications', 'verifications.qr_code_id', '=', 'qr_codes.id')
                    ->whereColumn('lots.produit_id', 'produits.id');
            }, 'total_scans')
            ->orderByDesc('total_scans')
            ->take(5)
            ->get();

        // -- Répartition par catégorie ----------------------------------------
        $repartitionCategories = Produit::where('fabricant_id', $fabricant->id)
            ->selectRaw('categorie, count(*) as total')
            ->groupBy('categorie')
            ->get();

        // -- Scans 7 derniers jours -------------------------------------------
        $scans7jours    = [];
        $suspects7jours = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $scans7jours[] = Verification::whereIn('qr_code_id', $qrcodeIds)
                ->whereDate('created_at', $date)->count();
            $suspects7jours[] = Verification::whereIn('qr_code_id', $qrcodeIds)
                ->where('resultat', 'suspect')
                ->whereDate('created_at', $date)->count();
        }

        return view('fabricant.statistiques.index', compact(
            'fabricant',
            'totalProduits',
            'totalLots',
            'totalQrcodes',
            'totalScans',
            'totalSignalements',
            'signalementsParMois',
            'topProduits',
            'repartitionCategories',
            'scans7jours',
            'suspects7jours'
        ));
    }
}
