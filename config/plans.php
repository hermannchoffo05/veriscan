<?php
// config/plans.php
// Source de vérité unique pour les limites par plan (cf. page tarifs).
// null = illimité.

return [

    'gratuit' => [
        'label'            => 'Gratuit',
        'montant'          => 0,
        'produits'         => 1,
        'qrcodes'          => 50,
        'rapports'         => 0,
        'statistiques'     => false, // ✅ RETIRÉ du plan Gratuit (avant : accès basique)
        'carte_risques'    => false,
        'ia'               => false, // chatbot assistant — réservé Pro/Entreprise
        'ia_quota_mensuel' => 10,    // assistant produit (description + classification)
    ],

    'starter' => [
        'label'            => 'Starter',
        'montant'          => 2000, // ✅ CORRIGÉ : 5000 → 2000
        'produits'         => 5,
        'qrcodes'          => 500,
        'rapports'         => 1,
        'statistiques'     => true,
        'carte_risques'    => false,
        'ia'               => false,
        'ia_quota_mensuel' => 30,
    ],

    'pro' => [
        'label'            => 'Pro',
        'montant'          => 5000, // ✅ CORRIGÉ : 15000 → 5000
        'produits'         => null,
        'qrcodes'          => null,
        'rapports'         => null,
        'statistiques'     => true,
        'carte_risques'    => true,
        'ia'               => true,
        'ia_quota_mensuel' => null, // illimité
    ],

    'entreprise' => [
        'label'            => 'Entreprise',
        'montant'          => 10000, // ✅ CORRIGÉ : 50000 → 10000
        'produits'         => null,
        'qrcodes'          => null,
        'rapports'         => null,
        'statistiques'     => true,
        'carte_risques'    => true,
        'ia'               => true,
        'ia_quota_mensuel' => null,
    ],

];
