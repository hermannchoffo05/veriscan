<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        // ✅ CORRIGÉ : 'fabricant/*' retiré. Cette exception désactivait le
        // CSRF sur absolument toutes les routes fabricant (changement de
        // mot de passe, suppression de produit, génération de QR codes,
        // etc.) — n'importe quel site tiers pouvait faire soumettre un
        // formulaire caché à un fabricant connecté et déclencher ces
        // actions à son insu. Audit fait sur tous les <form> et fetch()
        // des vues fabricant : tous protégés par @csrf ou X-CSRF-TOKEN,
        // rien ne casse au retrait de cette ligne.
        //
        // ✅ CORRIGÉ : 'admin/signalements/*' retiré pour la même raison.
        // Les actions traiter/escalader (formulaires @csrf) et
        // resume/analyser-photo (fetch + header X-CSRF-TOKEN) sont toutes
        // déjà protégées côté vue — vérifié dans show.blade.php et
        // index.blade.php. Sans CSRF, un site tiers pouvait faire
        // marquer un signalement comme traité/rejeté ou l'escalader à
        // MINCOMMERCE à l'insu d'un admin connecté.
        $middleware->validateCsrfTokens(except: [
            'admin/login',
            'fabricant/login',
            'verify/signaler',
            'verify-code',
        ]);
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        // ✅ AJOUTÉ : alias pour le gating de plan (carte des risques, IA)
        $middleware->alias([
            'plan.feature' => \App\Http\Middleware\CheckPlanFeature::class,
        ]);
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            // Les routes publiques de vérification ne redirigent pas vers login
            if ($request->is('verify*') || $request->is('verify-code')) {
                return null;
            }
            return route('fabricant.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();