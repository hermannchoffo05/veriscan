<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Produit;
use App\Models\Lot;
use App\Models\QrCode;
use App\Models\Signalement;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FabricantRapportsController extends Controller
{
    public function index()
    {
        $fabricant  = Auth::guard('fabricant')->user();
        $produitIds = Produit::where('fabricant_id', $fabricant->id)->pluck('id');
        $lotIds     = Lot::whereIn('produit_id', $produitIds)->pluck('id');
        $qrcodeIds  = QrCode::whereIn('lot_id', $lotIds)->pluck('id');

        $totalProduits     = $produitIds->count();
        $totalLots         = $lotIds->count();
        $totalQrcodes      = $qrcodeIds->count();
        $totalSignalements = Signalement::whereIn('qr_code_id', $qrcodeIds)->count();

        return view('fabricant.rapports.index', compact(
            'fabricant',
            'totalProduits',
            'totalLots',
            'totalQrcodes',
            'totalSignalements'
        ));
    }

    public function telecharger(Request $request)
    {
        $type = $request->query('type', 'mensuel');

        switch ($type) {
            case 'certificats':
                return $this->exportCertificats();
            case 'signalements':
                return $this->exportSignalements();
            default:
                return $this->exportMensuel($request);
        }
    }

    private function exportMensuel(Request $request)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $mois      = $request->input('mois', Carbon::now()->format('Y-m'));
        $date      = Carbon::createFromFormat('Y-m', $mois);

        $produits = Produit::where('fabricant_id', $fabricant->id)
            ->with(['lots' => function ($q) use ($date) {
                $q->whereYear('date_fabrication', $date->year)
                  ->whereMonth('date_fabrication', $date->month);
            }])
            ->get();

        $produitIds   = Produit::where('fabricant_id', $fabricant->id)->pluck('id');
        $lotIds       = Lot::whereIn('produit_id', $produitIds)->pluck('id');
        $qrcodeIds    = QrCode::whereIn('lot_id', $lotIds)->pluck('id');
        $signalements = Signalement::whereIn('qr_code_id', $qrcodeIds)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->with('qrCode.lot.produit')
            ->get();

        $totalScans = QrCode::whereIn('lot_id', $lotIds)->sum('nb_scans');

        $pdf = Pdf::loadView('fabricant.rapports.pdf', compact(
            'fabricant', 'produits', 'signalements', 'date', 'totalScans'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('rapport-mensuel-veriscan-' . $mois . '.pdf');
    }

    private function exportCertificats()
    {
        $fabricant  = Auth::guard('fabricant')->user();
        $produits   = Produit::where('fabricant_id', $fabricant->id)
            ->with(['lots.qrCodes'])
            ->get();

        $pdf = Pdf::loadView('fabricant.rapports.pdf_certificats', compact(
            'fabricant', 'produits'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('certificats-authenticite-veriscan-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportSignalements()
    {
        $fabricant  = Auth::guard('fabricant')->user();
        $produitIds = Produit::where('fabricant_id', $fabricant->id)->pluck('id');
        $lotIds     = Lot::whereIn('produit_id', $produitIds)->pluck('id');
        $qrcodeIds  = QrCode::whereIn('lot_id', $lotIds)->pluck('id');

        $signalements = Signalement::whereIn('qr_code_id', $qrcodeIds)
            ->with('qrCode.lot.produit')
            ->latest()
            ->get();

        $pdf = Pdf::loadView('fabricant.rapports.pdf_signalements', compact(
            'fabricant', 'signalements'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('signalements-veriscan-' . now()->format('Y-m-d') . '.pdf');
    }
}