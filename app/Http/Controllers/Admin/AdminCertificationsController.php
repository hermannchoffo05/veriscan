<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Autorité de certification (rôle tenu par l'administrateur de la plateforme).
 *
 * Certification DOCUMENTAIRE : l'autorité vérifie la recevabilité du
 * justificatif déposé par le fabricant (AMM, certificat de conformité…),
 * puis certifie, rejette ou révoque le produit.
 */
class AdminCertificationsController extends Controller
{
    public function index(Request $request)
    {
        $statut = $request->query('statut', Produit::CERT_SOUMIS);
        $q      = trim((string) $request->query('q', ''));

        $query = Produit::with('fabricant')->latest();

        if ($statut !== 'tous') {
            $query->where('statut_certification', $statut);
        }
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nom', 'like', "%{$q}%")
                  ->orWhere('code_produit', 'like', "%{$q}%")
                  ->orWhere('numero_certificat', 'like', "%{$q}%")
                  ->orWhereHas('fabricant', fn ($f) => $f->where('nom_entreprise', 'like', "%{$q}%"));
            });
        }

        $produits = $query->paginate(15)->withQueryString();

        $compteurs = [
            'soumis'   => Produit::where('statut_certification', Produit::CERT_SOUMIS)->count(),
            'certifie' => Produit::where('statut_certification', Produit::CERT_CERTIFIE)->count(),
            'rejete'   => Produit::where('statut_certification', Produit::CERT_REJETE)->count(),
            'revoque'  => Produit::where('statut_certification', Produit::CERT_REVOQUE)->count(),
        ];

        return view('admin.certifications.index', compact('produits', 'statut', 'q', 'compteurs'));
    }

    public function show($id)
    {
        $produit = Produit::with([
            'fabricant', 'certificationPharmaceutique', 'certificationCosmetique',
        ])->findOrFail($id);

        $justificatifExiste = $produit->justificatif
            && Storage::disk('local')->exists($produit->justificatif);

        $extension = $produit->justificatif
            ? strtolower(pathinfo($produit->justificatif, PATHINFO_EXTENSION))
            : null;

        return view('admin.certifications.show', compact('produit', 'justificatifExiste', 'extension'));
    }

    /** Affiche le justificatif (fichier privé) à l'autorité connectée uniquement. */
    public function justificatif($id)
    {
        $produit = Produit::findOrFail($id);

        abort_unless(
            $produit->justificatif && Storage::disk('local')->exists($produit->justificatif),
            404, 'Justificatif introuvable.'
        );

        return Storage::disk('local')->response($produit->justificatif);
    }

    public function certifier($id)
    {
        $produit = Produit::findOrFail($id);

        if ($produit->statut_certification === Produit::CERT_CERTIFIE) {
            return back()->with('success', 'Ce produit est déjà certifié.');
        }

        $produit->update([
            'statut_certification' => Produit::CERT_CERTIFIE,
            'certifie_par'         => Auth::guard('admin')->id(),
            'certifie_le'          => now(),
            'numero_certificat'    => $produit->numero_certificat ?: Produit::genererNumeroCertificat($produit),
            'motif_decision'       => null,
        ]);

        return redirect()->route('admin.certifications.index')
            ->with('success', "Produit « {$produit->nom} » certifié (n° {$produit->numero_certificat}).");
    }

    public function rejeter(Request $request, $id)
    {
        $request->validate(['motif' => 'required|string|max:500']);

        $produit = Produit::findOrFail($id);
        $produit->update([
            'statut_certification' => Produit::CERT_REJETE,
            'motif_decision'       => $request->motif,
            'certifie_par'         => Auth::guard('admin')->id(),
            'certifie_le'          => null,
        ]);

        return redirect()->route('admin.certifications.index')
            ->with('success', "Produit « {$produit->nom} » rejeté.");
    }

    /**
     * Révocation : le produit n'est plus certifié ET tous ses QR codes
     * sont révoqués (un scan renverra « révoqué »).
     */
    public function revoquer(Request $request, $id)
    {
        $request->validate(['motif' => 'required|string|max:500']);

        $produit = Produit::with('lots')->findOrFail($id);

        $produit->update([
            'statut_certification' => Produit::CERT_REVOQUE,
            'motif_decision'       => $request->motif,
            'certifie_par'         => Auth::guard('admin')->id(),
        ]);

        $nb = QrCode::whereIn('lot_id', $produit->lots->pluck('id'))
            ->where('statut', 'actif')
            ->update(['statut' => 'revoque']);

        return redirect()->route('admin.certifications.show', $produit->id)
            ->with('success', "Certification révoquée. {$nb} QR code(s) révoqué(s).");
    }
}
