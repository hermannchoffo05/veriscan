<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Produit;
use App\Models\Lot;
use App\Models\QrCode;
use App\Models\Signalement;
use App\Models\Verification;

class FabricantLotsController extends Controller
{
    public function index($produitId)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit = Produit::where('fabricant_id', $fabricant->id)
                          ->findOrFail($produitId);
        $lots = Lot::where('produit_id', $produit->id)
                   ->withCount('qrcodes')
                   ->latest()
                   ->paginate(10);
        return view('fabricant.lots.index', compact('produit', 'lots', 'fabricant'));
    }

    public function create($produitId)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit = Produit::where('fabricant_id', $fabricant->id)
                          ->findOrFail($produitId);
        return view('fabricant.lots.create', compact('produit', 'fabricant'));
    }

    public function store(Request $request, $produitId)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit = Produit::where('fabricant_id', $fabricant->id)
                          ->findOrFail($produitId);

        $validated = $request->validate([
            'numero_lot'       => 'required|string|max:100|unique:lots,numero_lot',
            'date_fabrication' => 'required|date',
            'date_expiration'  => 'required|date|after:date_fabrication',
            'quantite'         => 'required|integer|min:1',
            'site_production'  => 'nullable|string|max:255',
        ]);

        $validated['produit_id'] = $produit->id;
        Lot::create($validated);

        return redirect()->route('fabricant.produits.show', $produit->id)
                         ->with('success', 'Lot créé avec succès.');
    }

    public function edit($produitId, $lotId)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit = Produit::where('fabricant_id', $fabricant->id)
                          ->findOrFail($produitId);
        $lot = Lot::where('produit_id', $produit->id)
                  ->findOrFail($lotId);

        return view('fabricant.lots.edit', compact('produit', 'lot', 'fabricant'));
    }

    public function update(Request $request, $produitId, $lotId)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit = Produit::where('fabricant_id', $fabricant->id)
                          ->findOrFail($produitId);
        $lot = Lot::where('produit_id', $produit->id)
                  ->findOrFail($lotId);

        $validated = $request->validate([
            'numero_lot'       => 'required|string|max:100|unique:lots,numero_lot,' . $lot->id,
            'date_fabrication' => 'required|date',
            'date_expiration'  => 'required|date|after:date_fabrication',
            'quantite'         => 'required|integer|min:1',
            'site_production'  => 'nullable|string|max:255',
        ]);

        $lot->update($validated);

        return redirect()->route('fabricant.produits.show', $produit->id)
                         ->with('success', 'Lot modifié avec succès.');
    }

    public function destroy($produitId, $lotId)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $produit = Produit::where('fabricant_id', $fabricant->id)
                          ->findOrFail($produitId);
        $lot = Lot::where('produit_id', $produit->id)
                  ->findOrFail($lotId);

        $qrIds = QrCode::where('lot_id', $lot->id)->pluck('id');

        // ✅ AJOUTÉ : garde-fou anti-suppression de preuves.
        // Sans ça, un fabricant peut supprimer un lot pour faire disparaître
        // son historique de scans et/ou un signalement en cours (potentielle
        // preuve de contrefaçon) — problématique sur une plateforme dont c'est
        // justement le rôle de tracer ça.
        $aDesSignalementsActifs = Signalement::whereIn('qr_code_id', $qrIds)
            ->where('statut', 'en_cours')->exists();

        if ($aDesSignalementsActifs) {
            return back()->with('error',
                "Impossible de supprimer ce lot : il fait l'objet d'un signalement en cours. Traitez d'abord le signalement avant de le supprimer.");
        }

        $aDejaEteScanne = Verification::whereIn('qr_code_id', $qrIds)->exists();

        if ($aDejaEteScanne) {
            return back()->with('error',
                "Impossible de supprimer ce lot : il a déjà été scanné au moins une fois. Sa suppression effacerait l'historique de vérification.");
        }

        // Filet de sécurité : si une contrainte FK bloque quand même la
        // suppression (ex. relation non prévue ci-dessus), on évite un 500 brut.
        try {
            $lot->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error',
                "Impossible de supprimer ce lot : des données associées l'en empêchent.");
        }

        return back()->with('success', 'Lot supprimé.');
    }
}