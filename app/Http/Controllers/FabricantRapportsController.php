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
use Barryvdh\DomPDF\Facade\Pdf;

class FabricantRapportsController extends Controller
{
    public function index()
    {
        $fabricant  = Auth::guard('fabricant')->user();

        // ✅ AJOUTÉ : affiche la page "verrouillée" si le plan n'a droit à
        // aucun rapport (quota = 0, càd plan Gratuit), au lieu de laisser
        // l'utilisateur arriver sur une page de rapports qu'il ne pourra
        // jamais générer sans explication claire à l'écran.
        if ($fabricant->limites()['rapports'] === 0) {
            return view('fabricant.plan-locked', [
                'topbarTitre'         => __('messages.rapports'),
                'titrePage'           => __('messages.rapports'),
                'titreVerrouillage'   => app()->getLocale() === 'en' ? 'Reports locked' : 'Rapports bloqués',
                'messageVerrouillage' => app()->getLocale() === 'en'
                    ? 'Upgrade to Starter, Pro or Enterprise to generate PDF reports.'
                    : 'Passez au plan Starter, Pro ou Entreprise pour générer des rapports PDF.',
            ]);
        }

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

    // ✅ web.php référence ces 3 méthodes publiques
    // (routes rapports.pdf / rapports.certificats / rapports.signalements)
    // qui n'existaient pas sur ce contrôleur — chaque lien correspondant
    // plantait avec une erreur "méthode introuvable". Elles délèguent
    // simplement aux exports privés déjà écrits.
    public function downloadPdf(Request $request)
    {
        return $this->exportMensuel($request);
    }

    public function downloadCertificats()
    {
        return $this->exportCertificats();
    }

    public function downloadSignalements()
    {
        return $this->exportSignalements();
    }

    /**
     * ✅ AJOUTÉ : vérifie et consomme le quota mensuel de rapports PDF.
     * Retourne une redirection si le quota est épuisé, sinon null (et
     * incrémente). Partagée par les 3 types d'export (mensuel, certificats,
     * signalements) qui comptent tous contre le même quota "rapports" du plan
     * (gratuit=bloqué, starter=1/mois, pro/entreprise=illimité).
     */
    private function verifierEtIncrementerQuotaRapport()
    {
        $fabricant = Auth::guard('fabricant')->user();
        $restant   = $fabricant->quotaRapportsRestant();

        if ($restant !== null && $restant <= 0) {
            // ✅ CORRIGÉ : 'error' → 'warning'. Le layout fabricant n'affiche
            // que session('warning') dans son bandeau (voir CheckPlanFeature) —
            // avec 'error', ce message était calculé mais jamais visible à
            // l'écran, le fabricant était juste redirigé sans explication.
            return redirect()->route('fabricant.rapports.index')->with('warning',
                "Vous avez atteint la limite de rapports PDF de votre plan ({$fabricant->limites()['label']}). Passez à un plan supérieur pour en générer davantage.");
        }

        $fabricant->increment('rapports_generes_mois');
        return null;
    }

    private function exportMensuel(Request $request)
    {
        // ✅ AJOUTÉ : quota rapports selon le plan
        if ($blocage = $this->verifierEtIncrementerQuotaRapport()) {
            return $blocage;
        }

        $fabricant  = Auth::guard('fabricant')->user();
        $moisInput  = $request->input('mois', Carbon::now()->format('Y-m'));

        // ✅ Garde-fou sur le format de 'mois'. createFromFormat()
        // lève une exception non catchée sur une valeur malformée (paramètre
        // manipulé dans l'URL, lien cassé, etc.) — ça faisait planter le
        // téléchargement en 500 brut. On retombe sur le mois courant si le
        // format ne correspond pas à YYYY-MM.
        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $moisInput)) {
            $date = Carbon::createFromFormat('Y-m', $moisInput)->startOfMonth();
            $mois = $moisInput;
        } else {
            $date = Carbon::now()->startOfMonth();
            $mois = $date->format('Y-m');
        }

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

        // ✅ Même bug de divergence que dashboard/statistiques
        // (QrCode.nb_scans n'est pas incrémenté sur le parcours de saisie
        // manuelle de code — Verification est la source de vérité), plus
        // un souci propre à ce fichier : l'ancien total n'était scopé à
        // aucune date alors que le reste du rapport (signalements) l'est.
        // Un "rapport mensuel" doit refléter les scans DU mois choisi.
        $totalScans = Verification::whereIn('qr_code_id', $qrcodeIds)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();

        $pdf = Pdf::loadView('fabricant.rapports.pdf', compact(
            'fabricant', 'produits', 'signalements', 'date', 'totalScans'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('rapport-mensuel-veriscan-' . $mois . '.pdf');
    }

    private function exportCertificats()
    {
        // ✅ AJOUTÉ : quota rapports selon le plan
        if ($blocage = $this->verifierEtIncrementerQuotaRapport()) {
            return $blocage;
        }

        $fabricant  = Auth::guard('fabricant')->user();
        $produits   = Produit::where('fabricant_id', $fabricant->id)
            // ✅ CORRIGÉ : 'qrCodes' → 'qrcodes'. Partout ailleurs dans le code
            // (FabricantLotsController::withCount('qrcodes'),
            // FabricantQRCodesController::$lot->qrcodes) la relation est en
            // minuscules. Le camelCase ici provoquait une
            // RelationNotFoundException — export impossible.
            ->with(['lots.qrcodes'])
            ->get();

        $pdf = Pdf::loadView('fabricant.rapports.pdf_certificats', compact(
            'fabricant', 'produits'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('certificats-authenticite-veriscan-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportSignalements()
    {
        // ✅ AJOUTÉ : quota rapports selon le plan
        if ($blocage = $this->verifierEtIncrementerQuotaRapport()) {
            return $blocage;
        }

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
