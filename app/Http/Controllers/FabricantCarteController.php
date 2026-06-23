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

        // Signalements avec GPS — liés au fabricant OU sans QR (mobile)
        $signalements = Signalement::where(function ($q) use ($qrcodeIds) {
                            $q->whereIn('qr_code_id', $qrcodeIds)
                              ->orWhereNull('qr_code_id');
                        })
                        ->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->with('qrCode.lot.produit')
                        ->get()
                        ->map(function ($s) {
                            return [
                                'id'          => $s->id,
                                'latitude'    => $s->latitude,
                                'longitude'   => $s->longitude,
                                'description' => $s->description,
                                'statut'      => $s->statut,
                                'localisation'=> $s->localisation ?? 'Localisation inconnue',
                                'region'      => $s->region ?? '',
                                'produit'     => $s->qrCode?->lot?->produit?->nom ?? 'Produit non associé',
                                'date'        => $s->created_at->format('d/m/Y H:i'),
                            ];
                        });

        // Vérifications avec GPS
        $verifications = Verification::whereIn('qr_code_id', $qrcodeIds)
                        ->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->with('qrCode.lot.produit')
                        ->get()
                        ->map(function ($v) {
                            return [
                                'latitude'  => $v->latitude,
                                'longitude' => $v->longitude,
                                'resultat'  => $v->resultat,
                                'produit'   => $v->qrCode?->lot?->produit?->nom ?? '',
                                'date'      => $v->created_at->format('d/m/Y H:i'),
                            ];
                        });

        // Stats pour la sidebar de la carte
        $totalSignalements  = $signalements->count();
        $totalContrefaits   = $signalements->where('statut', 'en_cours')->count();
        $totalScans         = $verifications->count();
        $totalRegions       = $signalements->pluck('region')->filter()->unique()->count();

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