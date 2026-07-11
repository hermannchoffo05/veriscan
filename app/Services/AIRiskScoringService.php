<?php

namespace App\Services;

use App\Models\Produit;
use App\Models\Lot;
use App\Models\QrCode;
use App\Models\Signalement;
use App\Models\RiskScore;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * AIRiskScoringService – Module IA VeriScan
 *
 * Calcule un score de risque de contrefaçon (0-100) pour chaque produit
 * basé sur 6 features pondérées selon le cahier des charges.
 *
 * Formule Phase 1 (règles pondérées en PHP pur, sans librairie externe) :
 * Score = (w1 × freq_signalements + w2 × densite_geo + w3 × taux_scan_negatif)
 *         × poids_categorie × facteur_vetuste × facteur_fabricant
 *
 * Coefficients :
 *   w1 = 0.40 (fréquence signalements – poids le plus fort)
 *   w2 = 0.35 (densité géographique)
 *   w3 = 0.25 (taux de scan négatif)
 */
class AIRiskScoringService
{
    // -- Coefficients du modèle -----------------------------------------------
    const W1 = 0.40; // Poids fréquence signalements
    const W2 = 0.35; // Poids densité géographique
    const W3 = 0.25; // Poids taux scan négatif

    // -- Poids par catégorie de produit (sans accents pour fiabilité) ----------
    const POIDS_CATEGORIE = [
        'medicament'   => 1.5,
        'pharmacie'    => 1.5,
        'alimentation' => 1.2,
        'alimentaire'  => 1.2,
        'cosmetique'   => 1.1,
        'hygiene'      => 1.1,
        'automobile'   => 1.3,
        'auto'         => 1.3,
    ];

    // -- Zones du Cameroun (code → coordonnées centre) ------------------------
    const ZONES_CAMEROUN = [
        'YAO' => ['nom' => 'Yaoundé (Centre)',      'lat' => 3.848,  'lng' => 11.502],
        'DLA' => ['nom' => 'Douala (Littoral)',      'lat' => 4.061,  'lng' => 9.778],
        'GAR' => ['nom' => 'Garoua (Nord)',          'lat' => 9.301,  'lng' => 13.398],
        'BAF' => ['nom' => 'Bafoussam (Ouest)',      'lat' => 5.476,  'lng' => 10.421],
        'BAM' => ['nom' => 'Bamenda (Nord-Ouest)',   'lat' => 5.959,  'lng' => 10.145],
        'MAR' => ['nom' => 'Maroua (Extrême-Nord)', 'lat' => 10.591, 'lng' => 14.316],
        'NGA' => ['nom' => 'Ngaoundéré (Adamaoua)', 'lat' => 7.321,  'lng' => 13.584],
        'BER' => ['nom' => 'Bertoua (Est)',          'lat' => 4.578,  'lng' => 13.684],
        'EBO' => ['nom' => 'Ebolowa (Sud)',          'lat' => 2.900,  'lng' => 11.150],
        'BUE' => ['nom' => 'Buea (Sud-Ouest)',       'lat' => 4.156,  'lng' => 9.241],
    ];

    /**
     * Calcule et persiste le score de risque pour tous les produits.
     * Appelé par le Scheduler Laravel toutes les 6 heures.
     */
    public function calculerTousLesScores(): int
    {
        $produits = Produit::with(['fabricant', 'lots.qrCodes.signalements'])->get();
        $count = 0;

        foreach ($produits as $produit) {
            try {
                $this->calculerScoreProduit($produit);
                $count++;
            } catch (\Exception $e) {
                Log::error("VeriScan IA – Erreur scoring produit #{$produit->id}: " . $e->getMessage());
            }
        }

        Log::info("VeriScan IA – Scoring terminé : {$count} produits traités");
        return $count;
    }

    /**
     * Calcule le score de risque pour un produit spécifique.
     */
    public function calculerScoreProduit(Produit $produit): RiskScore
    {
        // -- Feature 1 : Fréquence de signalements sur 30 jours ---------------
        $lotIds = $produit->lots()->pluck('id');
        $qrIds  = QrCode::whereIn('lot_id', $lotIds)->pluck('id');

        $signalementsRecents = Signalement::whereIn('qr_code_id', $qrIds)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        // Normaliser sur 100 (max raisonnable = 20 signalements en 30j)
        $freqSignalements = min(100, ($signalementsRecents / 20) * 100);

        // -- Feature 2 : Densité géographique (simulation Phase 1) ------------
        $totalQr    = $qrIds->count();
        $totalSig   = Signalement::whereIn('qr_code_id', $qrIds)->count();
        $densiteGeo = $totalQr > 0 ? min(100, ($totalSig / $totalQr) * 100) : 0;

        // -- Feature 3 : Vétusté du lot (jours depuis création du dernier lot) -
        $dernierLot = $produit->lots()->latest()->first();
        $vetusteLot = 0;
        if ($dernierLot) {
            $jours      = $dernierLot->created_at->diffInDays(Carbon::now());
            $vetusteLot = min(100, ($jours / 365) * 100);
        }

        // -- Feature 4 : Taux de scan négatif ---------------------------------
        $qrAvecSig       = QrCode::whereIn('lot_id', $lotIds)->whereHas('signalements')->count();
        $tauxScanNegatif = $totalQr > 0 ? min(100, ($qrAvecSig / $totalQr) * 100) : 0;

        // -- Feature 5 : Poids catégorie produit ------------------------------
        // ✅ mb_strtolower + iconv pour normaliser les accents
        $categorieLower  = mb_strtolower($produit->categorie ?? '', 'UTF-8');
        $categorieAscii  = iconv('UTF-8', 'ASCII//TRANSLIT', $categorieLower);
        $poidsCategorie  = 1.0;

        // ✅ Espace ajouté : "POIDS_CATEGORIE as" (était "POIDS_CATEGORIEas")
        foreach (self::POIDS_CATEGORIE as $key => $poids) {
            if (str_contains($categorieAscii, $key)) {
                $poidsCategorie = $poids;
                break;
            }
        }

        // -- Feature 6 : Score historique fabricant ---------------------------
        $fabricantId   = $produit->fabricant_id;
        $totalProduits = Produit::where('fabricant_id', $fabricantId)->count();
        $totalSigFab   = Signalement::whereHas('qrCode.lot.produit', function ($q) use ($fabricantId) {
            $q->where('fabricant_id', $fabricantId);
        })->count();
        $scoreFabricant = $totalProduits > 0
            ? min(100, ($totalSigFab / ($totalProduits * 5)) * 100)
            : 0;

        // -- Calcul du score final --------------------------------------------
        $scoreBase = (
            self::W1 * $freqSignalements +
            self::W2 * $densiteGeo +
            self::W3 * $tauxScanNegatif
        );

        $scoreFinal = $scoreBase * $poidsCategorie;

        // Bonus vétusté (+10% si lot > 6 mois)
        if ($vetusteLot > 50) {
            $scoreFinal *= 1.10;
        }

        // Bonus historique fabricant (+5% si fabricant problématique)
        if ($scoreFabricant > 30) {
            $scoreFinal *= 1.05;
        }

        $scoreFinal = min(100, round($scoreFinal, 2));

        // -- Déterminer le niveau de risque -----------------------------------
        $niveau = match(true) {
            $scoreFinal >= 70 => 'critique',
            $scoreFinal >= 50 => 'eleve',
            $scoreFinal >= 30 => 'modere',
            default           => 'faible',
        };

        // -- Zone (Yaoundé par défaut en Phase 1) ----------------------------
        $zoneCode = 'YAO';

        // -- Persister le score -----------------------------------------------
        $riskScore = RiskScore::updateOrCreate(
            ['produit_id' => $produit->id, 'zone_code' => $zoneCode],
            [
                'score'             => $scoreFinal,
                'freq_signalements' => $freqSignalements,
                'densite_geo'       => $densiteGeo,
                'vetuste_lot'       => $vetusteLot,
                'taux_scan_negatif' => $tauxScanNegatif,
                'poids_categorie'   => $poidsCategorie,
                'score_fabricant'   => $scoreFabricant,
                'niveau'            => $niveau,
                'computed_at'       => Carbon::now(),
            ]
        );

        return $riskScore;
    }

    /**
     * Retourne les scores pour la heat-map Leaflet.js (format GeoJSON).
     */
    public function getScoresPourCarte(): array
    {
        $scores   = RiskScore::with('produit.fabricant')->orderByDesc('score')->get();
        $features = [];

        foreach ($scores as $score) {
            $zone = self::ZONES_CAMEROUN[$score->zone_code] ?? self::ZONES_CAMEROUN['YAO'];

            $features[] = [
                'type'     => 'Feature',
                'geometry' => [
                    'type'        => 'Point',
                    'coordinates' => [$zone['lng'], $zone['lat']],
                ],
                'properties' => [
                    'produit'   => $score->produit->nom ?? 'Inconnu',
                    'fabricant' => $score->produit->fabricant->nom_entreprise ?? 'Inconnu',
                    'score'     => $score->score,
                    'niveau'    => $score->niveau,
                    'couleur'   => $score->couleur,
                    'zone'      => $zone['nom'],
                    'computed'  => $score->computed_at?->format('d/m/Y H:i'),
                ],
            ];
        }

        return ['type' => 'FeatureCollection', 'features' => $features];
    }

    /**
     * Retourne un résumé des scores pour le dashboard admin.
     */
    public function getResumeDashboard(): array
    {
        return [
            'total_analyses' => RiskScore::count(),
            // ✅ 'critique' (singulier) — cohérent avec le match() ci-dessus
            'critiques'      => RiskScore::where('niveau', 'critique')->count(),
            'eleves'         => RiskScore::where('niveau', 'eleve')->count(),
            'moderes'        => RiskScore::where('niveau', 'modere')->count(),
            'faibles'        => RiskScore::where('niveau', 'faible')->count(),
            'score_moyen'    => round(RiskScore::avg('score') ?? 0, 1),
            'derniere_maj'   => RiskScore::max('computed_at'),
        ];
    }
}