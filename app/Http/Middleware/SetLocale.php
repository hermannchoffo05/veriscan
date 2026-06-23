<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Lit la langue en session, FR par défaut
        $locale = session('locale', 'fr');

        // Vérifie que la langue est valide (fr ou en uniquement)
        if (!in_array($locale, ['fr', 'en'])) {
            $locale = 'fr';
        }

        // Dit à Laravel d'utiliser cette langue
        App::setLocale($locale);

        return $next($request);
    }
}