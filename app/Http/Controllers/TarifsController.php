<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class TarifsController extends Controller
{
    /**
     * - Visiteur non connecté → route publique "tarifs" → vue tarifs.public
     * - Fabricant connecté → route "fabricant.tarifs" → vue tarifs.fabricant
     *   (accessible depuis la sidebar, pour changer de plan à tout moment).
     * Les deux vues partagent le même partiel tarifs._plans pour la grille
     * de plans + FAQ.
     */
    public function index()
    {
        $locale    = app()->getLocale();
        $fabricant = Auth::guard('fabricant')->check() ? Auth::guard('fabricant')->user() : null;
        $planActif = $fabricant?->planActif();

        $vue = $fabricant ? 'tarifs.fabricant' : 'tarifs.public';

        return view($vue, compact('locale', 'fabricant', 'planActif'));
    }
}