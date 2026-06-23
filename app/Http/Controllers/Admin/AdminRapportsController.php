<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fabricant;
use App\Models\Produit;
use App\Models\QrCode;
use App\Models\Signalement;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminRapportsController extends Controller
{
    public function index()
    {
        $stats = [
            'fabricants'   => Fabricant::count(),
            'produits'     => Produit::count(),
            'scans'        => \App\Models\Verification::count(),
            'signalements' => Signalement::count(),
            'en_cours'     => Signalement::where('statut', 'en_cours')->count(),
            'traites'      => Signalement::where('statut', 'traite')->count(),
            'rejetes'      => Signalement::where('statut', 'rejete')->count(),
        ];

        return view('admin.rapports.index', compact('stats'));
    }

    public function telecharger(\Illuminate\Http\Request $request)
    {
        $type = $request->query('type', 'global');

        switch ($type) {
            case 'signalements':
                return $this->exportSignalements();
            case 'fabricants':
                return $this->exportFabricants();
            case 'qrcodes':
                return $this->exportQrcodes();
            default:
                return $this->exportGlobal();
        }
    }

    private function exportGlobal()
    {
        $data = [
            'titre'        => 'Rapport Global VeriScan',
            'date'         => now()->format('d/m/Y à H:i'),
            'fabricants'   => Fabricant::count(),
            'produits'     => Produit::count(),
            'scans'        => \App\Models\Verification::count(),
            'signalements' => Signalement::count(),
            'en_cours'     => Signalement::where('statut', 'en_cours')->count(),
            'traites'      => Signalement::where('statut', 'traite')->count(),
            'rejetes'      => Signalement::where('statut', 'rejete')->count(),
            'top_fabricants' => Fabricant::withCount('produits')->orderByDesc('produits_count')->take(10)->get(),
        ];

        $pdf = Pdf::loadView('admin.rapports.pdf_global', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('veriscan_rapport_global_' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportSignalements()
    {
        $data = [
            'titre'        => 'Rapport des Signalements',
            'date'         => now()->format('d/m/Y à H:i'),
            'signalements' => Signalement::with('qrCode.lot.produit.fabricant')->latest()->get(),
        ];

        $pdf = Pdf::loadView('admin.rapports.pdf_signalements', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('veriscan_signalements_' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportFabricants()
    {
        $data = [
            'titre'       => 'Liste des Fabricants',
            'date'        => now()->format('d/m/Y à H:i'),
            'fabricants'  => Fabricant::withCount('produits')->latest()->get(),
        ];

        $pdf = Pdf::loadView('admin.rapports.pdf_fabricants', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('veriscan_fabricants_' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportQrcodes()
    {
        $data = [
            'titre'    => 'Rapport QR Codes & Lots',
            'date'     => now()->format('d/m/Y à H:i'),
            'produits' => Produit::with(['fabricant', 'lots.qrCodes'])->get(),
        ];

        $pdf = Pdf::loadView('admin.rapports.pdf_qrcodes', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('veriscan_qrcodes_' . now()->format('Y-m-d') . '.pdf');
    }
}
