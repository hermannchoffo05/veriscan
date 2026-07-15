<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Signalement;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FabricantProfilController extends Controller
{
    public function index()
    {
        $fabricant = Auth::guard('fabricant')->user();

        // ✅ Stats réelles pour les 4 cartes "quick-stats"
        $totalProduits = $fabricant->produits()->count();

        $qrCodeIds = QrCode::whereHas('lot.produit', function ($q) use ($fabricant) {
            $q->where('fabricant_id', $fabricant->id);
        })->pluck('id');

        $totalQrCodes = $qrCodeIds->count();

        // ✅ CORRIGÉ : même bug de divergence des scans que dashboard/
        // statistiques/rapports/carte (QrCode.nb_scans n'est incrémenté que
        // sur le parcours scan QR, pas sur la saisie manuelle de code —
        // Verification est la source de vérité unique).
        $totalScans = Verification::whereIn('qr_code_id', $qrCodeIds)->count();

        $totalSignalements = Signalement::whereIn('qr_code_id', $qrCodeIds)->count();

        return view('fabricant.profil', compact(
            'fabricant',
            'totalProduits',
            'totalQrCodes',
            'totalScans',
            'totalSignalements'
        ));
    }

    public function updateInfos(Request $request)
    {
        $fabricant = Auth::guard('fabricant')->user();

        $data = $request->validate([
            'nom_entreprise' => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'unique:fabricants,email,' . $fabricant->id],
            'telephone'      => ['nullable', 'string', 'max:20'],
            'adresse'        => ['nullable', 'string', 'max:255'],
            'pays'           => ['nullable', 'string', 'max:100'],
        ]);

        $fabricant->update($data);

        return back()->with('success_infos', 'Informations mises à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $fabricant = Auth::guard('fabricant')->user();

        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        if (!Hash::check($request->current_password, $fabricant->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])->with('tab', 'password');
        }

        $fabricant->update(['password' => Hash::make($request->password)]);

        return back()->with('success_password', 'Mot de passe modifié avec succès.')->with('tab', 'password');
    }

    public function updateLogo(Request $request)
    {
        $fabricant = Auth::guard('fabricant')->user();

        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        if ($fabricant->logo && Storage::disk('public')->exists($fabricant->logo)) {
            Storage::disk('public')->delete($fabricant->logo);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $fabricant->update(['logo' => $path]);

        return back()->with('success_logo', 'Logo mis à jour avec succès.')->with('tab', 'logo');
    }
}