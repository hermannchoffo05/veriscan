<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FabricantProfilController extends Controller
{
    public function index()
    {
        $fabricant = Auth::guard('fabricant')->user();
        return view('fabricant.profil', compact('fabricant'));
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

        // ✅ Espace corrigé : "avec succès" (était "avecsuccès")
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
            // ✅ Espace corrigé : "mot de passe actuel" (était "mot de passeactuel")
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