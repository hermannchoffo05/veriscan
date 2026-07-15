<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Signalement;
use App\Models\Produit;
use App\Models\Lot;
use App\Models\QrCode;
use App\Models\Verification;

class FabricantCarteController extends Controller
{
    public function index()
    {
        $fabricant = Auth::guard('fabricant')->user();

        $produitIds = Produit::where('fabricant_id', $fabricant->id)->pluck('id');
        $lotIds     = Lot::whereIn('produit_id', $produitIds)->pluck('id');
        $qrcodeIds  = QrCode::whereIn('lot_id', $lotIds)->pluck('id');

        // ✅ Signalements avec GPS — champs renommés pour correspondre exactement
        // à ce qu'attend le JavaScript de fabricant/carte.blade.php
        // (lat, lng, score, statut, produit, secteur, lieu, lot, description, region, date)
        $signalements = Signalement::where(function ($q) use ($qrcodeIds) {
                            $q->whereIn('qr_code_id', $qrcodeIds)
                              ->orWhereNull('qr_code_id');
                        })
                        ->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->with('qrCode.lot.produit')
                        ->latest()
                        ->get()
                        ->map(function ($s) {
                            $produit = $s->qrCode?->lot?->produit;

                            return [
                                'lat'         => (float) $s->latitude,
                                'lng'         => (float) $s->longitude,
                                // Score IA du signalement (même logique que AdminCarteController)
                                'score'       => $s->score_ia ?? 50,
                                'statut'      => $s->statut,
                                'produit'     => $produit?->nom ?? 'Produit non associé',
                                'secteur'     => $produit?->categorie ?? '',
                                'lieu'        => $s->localisation ?? 'Localisation inconnue',
                                'region'      => $s->region ?? '',
                                'lot'         => $s->qrCode?->lot?->numero_lot ?? '',
                                'description' => $s->description,
                                'date'        => $s->created_at->format('d/m/Y H:i'),
                            ];
                        })
                        ->values();

        // Vérifications avec GPS (conservées pour un usage futur éventuel)
        $verifications = Verification::whereIn('qr_code_id', $qrcodeIds)
                        ->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->with('qrCode.lot.produit')
                        ->get()
                        ->map(function ($v) {
                            return [
                                'lat'       => (float) $v->latitude,
                                'lng'       => (float) $v->longitude,
                                'resultat'  => $v->resultat,
                                'produit'   => $v->qrCode?->lot?->produit?->nom ?? '',
                                'date'      => $v->created_at->format('d/m/Y H:i'),
                            ];
                        });

        // ✅ Stats pour la sidebar — cohérentes avec les seuils de la légende
        // (rouge >=70, orange 50-70, jaune 30-50, vert <30)
        $totalSignalements = $signalements->count();
        $totalContrefaits  = $signalements->where('score', '>=', 70)->count();
        $totalScans        = QrCode::whereIn('id', $qrcodeIds)->sum('nb_scans');
        $totalRegions      = $signalements->pluck('region')->filter()->unique()->count();

        return view('fabricant.carte', compact(
            'fabricant',
            'signalements',
            'verifications',
            'totalSignalements',
            'totalContrefaits',
            'totalScans',
            'totalRegions'
        ));
    }
}