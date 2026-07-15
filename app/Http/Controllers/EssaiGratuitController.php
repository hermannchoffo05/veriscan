<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EssaiGratuitController extends Controller
{
    /**
     * Active un essai gratuit de 30 jours sur le plan choisi (starter ou pro),
     * conformément à la FAQ tarifs. Utilisable une seule fois par fabricant.
     */
    public function activer(Request $request, string $plan)
    {
        $fabricant = Auth::guard('fabricant')->user();

        if (!in_array($plan, ['starter', 'pro'], true)) {
            return back()->with('error', "Plan d'essai invalide.");
        }

        if ($fabricant->essai_deja_utilise) {
            return back()->with('error', "Vous avez déjà utilisé votre essai gratuit.");
        }

        $fabricant->update([
            'plan'               => $plan,
            'plan_expire_le'     => now()->addDays(30),
            'essai_deja_utilise' => true,
        ]);

        return redirect()->route('fabricant.dashboard')
            ->with('success', "Votre essai gratuit de 30 jours sur le plan " . ucfirst($plan) . " est activé !");
    }
}