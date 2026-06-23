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

        $produitIds = Produit::where('fabricant_id', $fabricant->id)->pluck('id');
        $lotIds     = Lot::whereIn('produit_id', $produitIds)->pluck('id');
        $qrcodeIds  = QrCode::whereIn('lot_id', $lotIds)->pluck('id');

        // ── Totaux corrects ──────────────────────────────────────────────────
        $totalProduits     = $produitIds->count();
        $totalLots         = $lotIds->count();
        $totalQrcodes      = $qrcodeIds->count();
        $totalScans        = Verification::whereIn('qr_code_id', $qrcodeIds)->count();
       $totalSignalements = Signalement::where(function($q) use ($qrcodeIds) {
    $q->whereIn('qr_code_id', $qrcodeIds)->orWhereNull('qr_code_id');
})->count();

        // ── Signalements des 6 derniers mois ─────────────────────────────────
        $signalementsParMois = [];
        for ($i = 5; $i >= 0; $i--) {
            $mois = Carbon::now()->subMonths($i);
            $signalementsParMois[] = [
                'mois'  => $mois->format('M Y'),
                'total' => Signalement::whereIn('qr_code_id', $qrcodeIds)
                                      ->whereYear('created_at', $mois->year)
                                      ->whereMonth('created_at', $mois->month)
                                      ->count(),
            ];
        }

        // ── Top 5 produits par scans ──────────────────────────────────────────
        $topProduits = Produit::where('fabricant_id', $fabricant->id)
            ->get()
            ->map(function ($p) {
                $lotIds    = $p->lots()->pluck('id');
                $qrIds     = QrCode::whereIn('lot_id', $lotIds)->pluck('id');
                $p->total_scans = Verification::whereIn('qr_code_id', $qrIds)->count();
                return $p;
            })
            ->sortByDesc('total_scans')
            ->take(5);

        // ── Répartition par catégorie ─────────────────────────────────────────
        $repartitionCategories = Produit::where('fabricant_id', $fabricant->id)
                                        ->selectRaw('categorie, count(*) as total')
                                        ->groupBy('categorie')
                                        ->get();

        // ── Scans 7 derniers jours ────────────────────────────────────────────
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