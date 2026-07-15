<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Produit;
use App\Models\Lot;
use App\Models\QrCode;
use App\Models\Signalement;
use App\Models\Verification;

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

        // ✅ AJOUTÉ : aucune validation n'existait avant. seuil_alerte et
        // frequence_calcul étaient castés en int sans bornes (négatif, énorme...),
        // et langue/format_date acceptaient n'importe quelle chaîne — risqué si
        // 'langue' sert ensuite à app()->setLocale().
        $validated = $request->validate([
            'seuil_alerte'     => 'nullable|integer|min:0|max:100',
            'frequence_calcul' => 'nullable|integer|min:1|max:24',
            'langue'           => 'nullable|in:fr,en',
            'format_date'      => 'nullable|in:d/m/Y,m/d/Y,Y-m-d',
        ]);

        $parametres = [
            'notif_signalement'     => $request->boolean('notif_signalement'),
            'notif_score_critique'  => $request->boolean('notif_score_critique'),
            'notif_rapport_mensuel' => $request->boolean('notif_rapport_mensuel'),
            'notif_resume_hebdo'    => $request->boolean('notif_resume_hebdo'),
            'seuil_alerte'          => $validated['seuil_alerte'] ?? 70,
            'frequence_calcul'      => $validated['frequence_calcul'] ?? 6,
            'langue'                => $validated['langue'] ?? 'fr',
            'format_date'           => $validated['format_date'] ?? 'd/m/Y',
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

        // ✅ AJOUTÉ : confirmation par mot de passe avant suppression définitive.
        // Avant, n'importe quelle requête POST authentifiée (session volée,
        // poste partagé oublié ouvert...) suffisait à supprimer le compte
        // — action irréversible — sans aucune friction supplémentaire.
        $request->validate([
            'password' => 'required|string',
        ]);

        if (! Hash::check($request->input('password'), $fabricant->password)) {
            return back()->with('error', "Mot de passe incorrect. Le compte n'a pas été supprimé.");
        }

        $produitIds = Produit::where('fabricant_id', $fabricant->id)->pluck('id');
        $lotIds     = Lot::whereIn('produit_id', $produitIds)->pluck('id');
        $qrcodeIds  = QrCode::whereIn('lot_id', $lotIds)->pluck('id');

        // ✅ AJOUTÉ : garde-fou n°1, cohérent avec FabricantLotsController.
        // On ne supprime jamais un compte avec un signalement en cours —
        // ce serait un moyen d'effacer une enquête active.
        $aDesSignalementsActifs = Signalement::whereIn('qr_code_id', $qrcodeIds)
            ->where('statut', 'en_cours')->exists();

        if ($aDesSignalementsActifs) {
            return back()->with('error',
                "Impossible de supprimer le compte : au moins un signalement est en cours de traitement sur vos produits. Traitez-le d'abord.");
        }

        // ✅ AJOUTÉ : garde-fou n°2. Si des consommateurs ont déjà scanné des
        // QR codes de ce fabricant, supprimer le compte casserait la
        // vérification de produits réellement distribués sur le marché
        // (le scan d'un produit authentique renverrait une erreur au lieu
        // d'une confirmation). On bloque plutôt que de laisser ce trou.
        $aDejaEteScanne = Verification::whereIn('qr_code_id', $qrcodeIds)->exists();

        if ($aDejaEteScanne) {
            return back()->with('error',
                "Impossible de supprimer le compte : certains de vos QR codes ont déjà été scannés par des consommateurs. Supprimer le compte casserait la vérification de produits déjà en circulation. Contactez le support pour une clôture assistée.");
        }

        // Aucun produit distribué, aucune enquête en cours : suppression
        // complète et propre plutôt que de laisser des lignes orphelines
        // en base (produits/lots/qrcodes sans fabricant_id valide).
        DB::transaction(function () use ($fabricant, $produitIds, $lotIds, $qrcodeIds) {
            QrCode::whereIn('id', $qrcodeIds)->delete();
            Lot::whereIn('id', $lotIds)->delete();
            Produit::whereIn('id', $produitIds)->delete();
            $fabricant->delete();
        });

        Auth::guard('fabricant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('fabricant.login')
                         ->with('success', 'Votre compte a été supprimé définitivement.');
    }
}