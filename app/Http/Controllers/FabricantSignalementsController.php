<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Signalement;

class FabricantSignalementsController extends Controller
{
    public function index()
    {
        $fabricant = Auth::guard('fabricant')->user();

        // ✅ Suppression du orWhereNull : uniquement les signalements de ce fabricant
        $signalements = Signalement::whereHas('qrCode.lot.produit', function ($q) use ($fabricant) {
                            $q->where('fabricant_id', $fabricant->id);
                        })
                        ->with('qrCode.lot.produit')
                        ->latest()
                        ->paginate(15);

        return view('fabricant.signalements.index', compact('signalements', 'fabricant'));
    }

    public function show($id)
    {
        $fabricant = Auth::guard('fabricant')->user();

        // ✅ Vérification que le signalement appartient bien à ce fabricant
        $signalement = Signalement::whereHas('qrCode.lot.produit', function ($q) use ($fabricant) {
                            $q->where('fabricant_id', $fabricant->id);
                        })
                        ->with('qrCode.lot.produit')
                        ->findOrFail($id);

        return view('fabricant.signalements.show', compact('signalement', 'fabricant'));
    }

    public function traiter($id)
    {
        $fabricant = Auth::guard('fabricant')->user();

        // ✅ Vérification que le signalement appartient bien à ce fabricant
        $signalement = Signalement::whereHas('qrCode.lot.produit', function ($q) use ($fabricant) {
                            $q->where('fabricant_id', $fabricant->id);
                        })->findOrFail($id);

        $signalement->update(['statut' => 'traite']);

        return back()->with('success', 'Signalement marqué comme traité.');
    }
}