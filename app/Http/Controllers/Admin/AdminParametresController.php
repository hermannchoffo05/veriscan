<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParametreSysteme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminParametresController extends Controller
{
    public function index()
    {
        $admin  = Auth::guard('admin')->user();
        $params = ParametreSysteme::tous();

        return view('admin.parametres', compact('admin', 'params'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'nom'   => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
        ]);

        $data = ['nom' => $request->nom, 'email' => $request->email];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['min:8', 'confirmed'], // ✅ 'confirmed' au lieu de 'same:password'
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return back()->with('success', 'Paramètres mis à jour avec succès.');
    }

    public function updatePhoto(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'photo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
            Storage::disk('public')->delete($admin->photo);
        }

        $path = $request->file('photo')->store('admins/photos', 'public');
        $admin->update(['photo' => $path]);

        return back()->with('success', 'Photo de profil mise à jour avec succès.');
    }

    public function updateSysteme(Request $request)
    {
        $request->validate([
            'seuil_alerte_modere'       => 'required|integer|min:1|max:99',
            'seuil_alerte_eleve'        => 'required|integer|min:1|max:99',
            'seuil_alerte_critique'     => 'required|integer|min:1|max:99',
            'intervalle_scoring'        => 'required|integer|min:1|max:24',
            'nb_scans_max_avant_alerte' => 'required|integer|min:10',
        ]);

        ParametreSysteme::set('seuil_alerte_modere',       $request->seuil_alerte_modere);
        ParametreSysteme::set('seuil_alerte_eleve',        $request->seuil_alerte_eleve);
        ParametreSysteme::set('seuil_alerte_critique',     $request->seuil_alerte_critique);
        ParametreSysteme::set('intervalle_scoring',        $request->intervalle_scoring);
        ParametreSysteme::set('nb_scans_max_avant_alerte', $request->nb_scans_max_avant_alerte);
        ParametreSysteme::set('alertes_actives',           $request->has('alertes_actives') ? '1' : '0');

        if ($request->filled('categories_produits')) {
            ParametreSysteme::set('categories_produits', $request->categories_produits);
        }

        return back()->with('success', 'Paramètres système enregistrés avec succès.');
    }
}