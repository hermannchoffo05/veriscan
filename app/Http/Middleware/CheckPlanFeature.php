<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPlanFeature
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        $fabricant = Auth::guard('fabricant')->user();

        $autorise = match ($feature) {
            'carte_risques' => $fabricant->aAccesCarteRisques(),
            'ia'            => $fabricant->aAccesIA(),
            default         => false,
        };

        if (!$autorise) {
            $message = "Cette fonctionnalité n'est pas incluse dans votre plan actuel ({$fabricant->limites()['label']}). Passez à un plan supérieur pour y accéder.";

            if ($request->expectsJson()) {
                return response()->json(['error' => $message], 403);
            }

            return redirect()->route('fabricant.dashboard')->with('warning', $message);
        }

        return $next($request);
    }
}