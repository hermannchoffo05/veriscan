<?php
namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Lot;
use App\Models\QrCode;
use App\Models\Verification;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FabricantDashboardController extends Controller
{
    public function index()
    {
        $fabricant  = Auth::guard('fabricant')->user();
        $produitIds = Produit::where('fabricant_id', $fabricant->id)->pluck('id');
        $lotIds     = Lot::whereIn('produit_id', $produitIds)->pluck('id');
        $qrcodeIds  = QrCode::whereIn('lot_id', $lotIds)->pluck('id');

        $totalProduits    = $produitIds->count();
        $totalQrcodes     = $qrcodeIds->count();
        $totalScans       = Verification::whereIn('qr_code_id', $qrcodeIds)->count();
        $scansAujourdHui  = Verification::whereIn('qr_code_id', $qrcodeIds)->whereDate('created_at', today())->count();

        $totalSignalements = Signalement::where(function ($q) use ($qrcodeIds) {
            $q->whereIn('qr_code_id', $qrcodeIds)->orWhereNull('qr_code_id');
        })->count();

        $signalementsEnCours = Signalement::where(function ($q) use ($qrcodeIds) {
            $q->whereIn('qr_code_id', $qrcodeIds)->orWhereNull('qr_code_id');
        })->where('statut', 'en_cours')->count();

        $produitsRecents = Produit::where('fabricant_id', $fabricant->id)
            ->with(['lots' => fn($q) => $q->latest()->limit(1)])
            ->withCount(['lots as nb_scans' => function ($q) {
                $q->join('qr_codes', 'qr_codes.lot_id', '=', 'lots.id')
                  ->join('verifications', 'verifications.qr_code_id', '=', 'qr_codes.id');
            }])
            ->latest()->limit(4)->get();

        $signalementsRecents = Signalement::where(function ($q) use ($qrcodeIds) {
            $q->whereIn('qr_code_id', $qrcodeIds)->orWhereNull('qr_code_id');
        })->with(['qrCode.lot'])->latest()->limit(3)->get();

        $derniersLots = Lot::whereIn('id', $lotIds)
            ->with('produit')->withCount('qrcodes')->latest()->limit(3)->get();

        $scans7jours = $suspects7jours = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $scans7jours[]    = Verification::whereIn('qr_code_id', $qrcodeIds)->whereDate('created_at', $date)->count();
            $suspects7jours[] = Verification::whereIn('qr_code_id', $qrcodeIds)->where('resultat', 'suspect')->whereDate('created_at', $date)->count();
        }

        // Notifications : signalements non lus
        $notifications = Signalement::where(function ($q) use ($qrcodeIds) {
            $q->whereIn('qr_code_id', $qrcodeIds)->orWhereNull('qr_code_id');
        })->where('statut', 'en_cours')->with('qrCode.lot.produit')->latest()->limit(5)->get();

        $nbNotifications = $notifications->count();

        return view('fabricant.dashboard', compact(
            'fabricant', 'totalProduits', 'totalQrcodes', 'totalScans',
            'scansAujourdHui', 'totalSignalements', 'signalementsEnCours',
            'produitsRecents', 'signalementsRecents', 'derniersLots',
            'scans7jours', 'suspects7jours', 'notifications', 'nbNotifications'
        ));
    }

    public function search(Request $request)
    {
        $fabricant  = Auth::guard('fabricant')->user();
        $q          = $request->get('q', '');

        $produits = Produit::where('fabricant_id', $fabricant->id)
            ->where(function ($query) use ($q) {
                $query->where('nom', 'like', "%$q%")
                      ->orWhere('categorie', 'like', "%$q%")
                      ->orWhere('description', 'like', "%$q%");
            })
            ->with(['lots' => fn($q) => $q->latest()->limit(1)])
            ->latest()->get();

        return response()->json($produits->map(fn($p) => [
            'id'        => $p->id,
            'nom'       => $p->nom,
            'categorie' => $p->categorie,
            'lot'       => $p->lots->first()?->numero_lot ?? '',
            'url'       => route('fabricant.produits.show', $p->id),
        ]));
    }
}