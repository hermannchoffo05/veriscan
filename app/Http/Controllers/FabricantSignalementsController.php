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
        $signalements = Signalement::where(function ($q) use ($fabricant) {
                            $q->whereHas('qrCode.lot.produit', function ($q2) use ($fabricant) {
                                $q2->where('fabricant_id', $fabricant->id);
                            })
                            ->orWhereNull('qr_code_id');
                        })
                        ->with('qrCode.lot.produit')
                        ->latest()
                        ->paginate(15);
        return view('fabricant.signalements.index', compact('signalements', 'fabricant'));
    }

    public function show($id)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $signalement = Signalement::with('qrCode.lot.produit')->findOrFail($id);
        return view('fabricant.signalements.show', compact('signalement', 'fabricant'));
    }

    public function traiter($id)
    {
        $signalement = Signalement::findOrFail($id);
        $signalement->update(['statut' => 'traite']);
        return back()->with('success', 'Signalement marqué comme traité.');
    }
}