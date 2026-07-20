<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'         => 'nullable|string|max:80',
            'note'        => 'required|integer|min:1|max:5',
            'commentaire' => 'required|string|max:600',
        ]);

        Avis::create([
            'nom'         => $validated['nom'] ?: (app()->getLocale() === 'en' ? 'Anonymous' : 'Anonyme'),
            'note'        => $validated['note'],
            'commentaire' => $validated['commentaire'],
        ]);

        return back()->with('avis_success', app()->getLocale() === 'en'
            ? 'Thanks for your feedback!'
            : 'Merci pour votre avis !');
    }
}