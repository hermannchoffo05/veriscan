<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FabricantParametresController extends Controller
{
    /**
     * Afficher la page paramètres
     */
    public function index()
    {
        $fabricant = Auth::guard('fabricant')->user();

        $parametres = $fabricant->parametres ?? [
            'notif_signalement'     => true,
            'notif_score_critique'  => true,
            'notif_rapport_mensuel' => false,
            'notif_resume_hebdo'    => false,
            'seuil_alerte'          => 70,
            'frequence_calcul'      => 6,
            'langue'                => 'fr',
            'format_date'           => 'd/m/Y',
            'afficher_carte'        => true,
            'afficher_graphiques'   => true,
            'mode_leger'            => false,
        ];

        return view('fabricant.parametres.index', compact('fabricant', 'parametres'));
    }

    /**
     * Enregistrer les paramètres
     */
    public function update(Request $request)
    {
        $fabricant = Auth::guard('fabricant')->user();

        $parametres = [
            'notif_signalement'     => $request->boolean('notif_signalement'),
            'notif_score_critique'  => $request->boolean('notif_score_critique'),
            'notif_rapport_mensuel' => $request->boolean('notif_rapport_mensuel'),
            'notif_resume_hebdo'    => $request->boolean('notif_resume_hebdo'),
            'seuil_alerte'          => (int) $request->input('seuil_alerte', 70),
            'frequence_calcul'      => (int) $request->input('frequence_calcul', 6),
            'langue'                => $request->input('langue', 'fr'),
            'format_date'           => $request->input('format_date', 'd/m/Y'),
            'afficher_carte'        => $request->boolean('afficher_carte'),
            'afficher_graphiques'   => $request->boolean('afficher_graphiques'),
            'mode_leger'            => $request->boolean('mode_leger'),
        ];

        $fabricant->parametres = $parametres;
        $fabricant->save();

        return redirect()->route('fabricant.parametres.index')
                         ->with('success', 'Paramètres enregistrés avec succès.');
    }

    /**
     * Réinitialiser les paramètres aux valeurs par défaut
     */
    public function reset()
    {
        $fabricant = Auth::guard('fabricant')->user();

        $fabricant->parametres = [
            'notif_signalement'     => true,
            'notif_score_critique'  => true,
            'notif_rapport_mensuel' => false,
            'notif_resume_hebdo'    => false,
            'seuil_alerte'          => 70,
            'frequence_calcul'      => 6,
            'langue'                => 'fr',
            'format_date'           => 'd/m/Y',
            'afficher_carte'        => true,
            'afficher_graphiques'   => true,
            'mode_leger'            => false,
        ];

        $fabricant->save();

        return redirect()->route('fabricant.parametres.index')
                         ->with('success', 'Paramètres réinitialisés aux valeurs par défaut.');
    }

    /**
     * Supprimer le compte fabricant
     */
    public function deleteAccount(Request $request)
    {
        $fabricant = Auth::guard('fabricant')->user();

        Auth::guard('fabricant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $fabricant->delete();

        return redirect()->route('fabricant.login')
                         ->with('success', 'Votre compte a été supprimé définitivement.');
    }
}