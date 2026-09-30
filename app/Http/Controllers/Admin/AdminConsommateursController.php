<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Verification;
use App\Models\Signalement;
use Illuminate\Http\Request;

/**
 * Gestion des comptes consommateurs (application mobile) par l'administrateur.
 */
class AdminConsommateursController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $statut = $request->query('statut', '');

        $query = User::where('role', 'consommateur')->latest();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }
        if ($statut === 'actif')    { $query->where('is_active', true); }
        if ($statut === 'suspendu') { $query->where('is_active', false); }

        $consommateurs = $query->paginate(15)->withQueryString();

        $ids = $consommateurs->pluck('id');
        $verifs = Verification::whereIn('user_id', $ids)
            ->selectRaw('user_id, COUNT(*) as total')->groupBy('user_id')->pluck('total', 'user_id');
        $sigs = Signalement::whereIn('user_id', $ids)
            ->selectRaw('user_id, COUNT(*) as total')->groupBy('user_id')->pluck('total', 'user_id');

        $total     = User::where('role', 'consommateur')->count();
        $suspendus = User::where('role', 'consommateur')->where('is_active', false)->count();

        return view('admin.consommateurs.index',
            compact('consommateurs', 'verifs', 'sigs', 'q', 'statut', 'total', 'suspendus'));
    }

    public function suspendre($id)
    {
        $user = User::where('role', 'consommateur')->findOrFail($id);
        $user->update(['is_active' => false]);
        // Coupe immédiatement les sessions mobiles actives.
        $user->tokens()->delete();

        return back()->with('success', "Compte de {$user->name} suspendu.");
    }

    public function reactiver($id)
    {
        $user = User::where('role', 'consommateur')->findOrFail($id);
        $user->update(['is_active' => true]);

        return back()->with('success', "Compte de {$user->name} réactivé.");
    }
}
