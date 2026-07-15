<?php

namespace App\Http\Controllers;

use App\Models\Fabricant;
use App\Models\Verification;

class HomeController extends Controller
{
    public function index()
    {
        $totalVerifications = Verification::count();
        $totalContrefaits   = Verification::where('resultat', 'contrefait')->count();

        $stats = [
            'produits_verifies'   => $totalVerifications,
            'fabricants_inscrits' => Fabricant::count(),
            'contrefacons'        => $totalContrefaits,
            'fiabilite'           => $totalVerifications > 0
                ? round((($totalVerifications - $totalContrefaits) / $totalVerifications) * 100)
                : 100,
        ];

        return view('welcome', compact('stats'));
    }
}