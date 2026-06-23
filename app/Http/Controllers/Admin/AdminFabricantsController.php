<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fabricant;
use Illuminate\Http\Request;

class AdminFabricantsController extends Controller
{
    public function index()
    {
        $fabricants = Fabricant::latest()->paginate(15);
        return view('admin.fabricants.index', compact('fabricants'));
    }

    public function show($id)
    {
        $fabricant = Fabricant::with(['produits.lots.qrCodes'])->findOrFail($id);
        return view('admin.fabricants.show', compact('fabricant'));
    }

    public function suspendre($id)
    {
        $fabricant = Fabricant::findOrFail($id);
        $fabricant->update(['statut' => 'suspendu']);
        return back()->with('success', 'Fabricant suspendu.');
    }

    public function destroy($id)
    {
        $fabricant = Fabricant::findOrFail($id);
        $fabricant->delete();
        return redirect()->route('admin.fabricants.index')
            ->with('success', 'Fabricant supprimé définitivement.');
    }
}