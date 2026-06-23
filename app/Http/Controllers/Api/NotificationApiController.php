<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    // Liste des notifications
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'titre'      => $n->titre,
                    'message'    => $n->message,
                    'type'       => $n->type,
                    'lu'         => $n->lu,
                    'created_at' => $n->created_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $notifications,
            'non_lues' => Notification::where('user_id', $request->user()->id)
                ->where('lu', false)->count(),
        ]);
    }

    // Marquer comme lue
    public function marquerLue(Request $request, $id)
    {
        Notification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->update(['lu' => true]);

        return response()->json(['success' => true]);
    }

    // Marquer toutes comme lues
    public function marquerToutesLues(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('lu', false)
            ->update(['lu' => true]);

        return response()->json(['success' => true]);
    }
}