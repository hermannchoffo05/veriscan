<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Verification;
use App\Models\QrCode;
use App\Models\Notification;
use Illuminate\Http\Request;

class VerifyApiController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $qrCode = QrCode::with(['lot.produit.fabricant'])
            ->where('token', $request->token)
            ->first();

        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'resultat' => 'invalide',
                'message' => 'QR Code introuvable ou invalide.',
            ], 404);
        }

        $resultat = $qrCode->statut === 'actif' ? 'authentique' : 'suspect';

        Verification::create([
            'user_id'      => auth('sanctum')->id(),
            'qr_code_id'   => $qrCode->id,
            'ip_address'   => $request->ip(),
            'appareil'     => $request->userAgent(),
            'localisation' => $request->input('localisation', null),
            'resultat'     => $resultat,
        ]);

        $qrCode->increment('nb_scans');

        if (auth('sanctum')->id()) {
            Notification::create([
                'user_id' => auth('sanctum')->id(),
                'titre'   => $resultat === 'authentique' ? '✅ Produit authentique' : '⚠️ Produit suspect',
                'message' => 'Votre scan de "' . ($qrCode->lot?->produit?->nom ?? 'produit') . '" est ' . $resultat . '.',
                'type'    => 'scan_' . $resultat,
                'lu'      => false,
            ]);
        }

        return response()->json([
            'success'  => true,
            'resultat' => $resultat,
            'produit'  => [
                'nom'        => $qrCode->lot?->produit?->nom,
                'categorie'  => $qrCode->lot?->produit?->categorie,
                'fabricant'  => $qrCode->lot?->produit?->fabricant?->nom,
                'lot'        => $qrCode->lot?->numero_lot,
                'expiration' => $qrCode->lot?->date_expiration,
            ],
        ]);
    }

    public function history(Request $request)
    {
        $verifications = Verification::with(['qrCode.lot.produit'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($v) {
                return [
                    'id'         => $v->id,
                    'resultat'   => $v->resultat,
                    'created_at' => $v->created_at->format('d/m/Y H:i'),
                    'produit'    => $v->qrCode?->lot?->produit?->nom ?? 'Produit inconnu',
                    'categorie'  => $v->qrCode?->lot?->produit?->categorie ?? '',
                    'lot'        => $v->qrCode?->lot?->numero_lot ?? '',
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $verifications,
            'total'   => $verifications->count(),
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->input('q', '');

        if (strlen($q) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $produits = QrCode::with(['lot.produit.fabricant'])
            ->where('token', 'LIKE', "%{$q}%")
            ->orWhereHas('lot.produit', function ($query) use ($q) {
                $query->where('nom', 'LIKE', "%{$q}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($qr) {
                return [
                    'token'     => $qr->token,
                    'produit'   => $qr->lot?->produit?->nom ?? 'Produit inconnu',
                    'fabricant' => $qr->lot?->produit?->fabricant?->nom ?? '',
                    'categorie' => $qr->lot?->produit?->categorie ?? '',
                    'statut'    => $qr->statut,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $produits,
        ]);
    }
}