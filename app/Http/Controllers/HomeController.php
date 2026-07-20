<?php

namespace App\Http\Controllers;

use App\Models\Fabricant;
use App\Models\Verification;
use App\Models\Avis;

class HomeController extends Controller
{
    public function index()
    {
        $stats = $this->computeStats();

        // Variables necessaires a la section Avis de welcome.blade.php
        $avisListe   = Avis::where('approuve', true)->latest()->take(6)->get();
        $moyenneAvis = round(Avis::where('approuve', true)->avg('note') ?? 0, 1);
        $totalAvis   = Avis::where('approuve', true)->count();

        return view('welcome', compact('stats', 'avisListe', 'moyenneAvis', 'totalAvis'));
    }

    // AJOUTE : endpoint leger appele en polling JS depuis welcome.blade.php
    // pour rafraichir les 4 chiffres du bandeau sans recharger toute la page.
    // Reutilise exactement le meme calcul que index() (via computeStats()),
    // donc les valeurs restent garanties identiques entre chargement complet
    // et mise a jour live - aucune logique dupliquee ou divergente.
    public function statsLive()
    {
        return response()->json($this->computeStats());
    }

    // AJOUTE : calcul des statistiques extrait dans sa propre methode,
    // pour etre partage entre index() (chargement complet de la page) et
    // statsLive() (polling JS) sans dupliquer la logique.
    private function computeStats(): array
    {
        $totalVerifications = Verification::count();
        $totalContrefaits   = Verification::where('resultat', 'contrefait')->count();

        return [
            'produits_verifies'   => $totalVerifications,
            'fabricants_inscrits' => Fabricant::count(),
            'contrefacons'        => $totalContrefaits,
            'fiabilite'           => $totalVerifications > 0
                ? round((($totalVerifications - $totalContrefaits) / $totalVerifications) * 100)
                : 100,
        ];
    }
}