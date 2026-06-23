<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token'             => 'nullable|string',
            'nom_signalant'     => 'nullable|string|max:100',
            'contact_signalant' => 'nullable|string|max:20',
            'description'       => 'required|string',
            'photo'             => 'nullable|image|max:5120',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'region'            => 'nullable|string|max:100',
            'localisation'      => 'nullable|string|max:255',
        ]);

        $qrCode = null;
        if ($request->token) {
            $qrCode = QrCode::where('token', $request->token)->first();
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('signalements', 'public');
        }

        $signalement = Signalement::create([
            'user_id'           => auth('sanctum')->id(),
            'qr_code_id'        => $qrCode?->id,
            'nom_signalant'     => $request->nom_signalant,
            'contact_signalant' => $request->contact_signalant,
            'description'       => $request->description,
            'photo_preuve'      => $photoPath,
            'statut'            => 'en_cours',
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'region'            => $request->region,
            'localisation'      => $request->localisation,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Signalement soumis avec succès',
            'data'    => [
                'id'     => $signalement->id,
                'statut' => $signalement->statut,
            ],
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $signalements = Signalement::where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(function ($s) {
                return [
                    'id'          => $s->id,
                    'description' => $s->description,
                    'statut'      => $s->statut,
                    'created_at'  => $s->created_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $signalements,
            'total'   => $signalements->count(),
        ]);
    }
}