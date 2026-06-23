<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Souscription;

class TarifsController extends Controller
{
    public function index()
    {
        return view('tarifs');
    }

    public function souscrire(Request $request)
    {
        $validated = $request->validate([
            'plan'            => 'required|in:Starter,Pro,Entreprise',
            'nom_entreprise'  => 'required|string|max:255',
            'nom_responsable' => 'required|string|max:255',
            'telephone'       => 'required|string|max:20',
            'email'           => 'nullable|email|max:255',
            'ville'           => 'nullable|string|max:100',
            'message'         => 'nullable|string|max:1000',
        ]);

        Souscription::create($validated);

        return redirect()->route('tarifs.index')
                         ->with('success_souscription', 'Votre demande a bien été reçue ! Nous vous contactons sous 24h au ' . $validated['telephone'] . ' pour activer votre plan ' . $validated['plan'] . '.');
    }
}