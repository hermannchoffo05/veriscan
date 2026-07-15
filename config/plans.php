<?php
// config/plans.php
// Source de vérité unique pour les limites par plan (cf. page tarifs).
// null = illimité.

return [

    'gratuit' => [
        'label'            => 'Gratuit',
        'produits'         => 1,
        'qrcodes'          => 50,
        'rapports'         => 0,
        'carte_risques'    => false,
        'ia'               => false, // chatbot assistant — réservé Pro/Entreprise
        'ia_quota_mensuel' => 10,    // assistant produit (description + classification)
    ],

    'starter' => [
        'label'            => 'Starter',
        'produits'         => 5,
        'qrcodes'          => 500,
        'rapports'         => 1,
        'carte_risques'    => false,
        'ia'               => false,
        'ia_quota_mensuel' => 30,
    ],

    'pro' => [
        'label'            => 'Pro',
        'produits'         => null,
        'qrcodes'          => null,
        'rapports'         => null,
        'carte_risques'    => true,
        'ia'               => true,
        'ia_quota_mensuel' => null, // illimité
    ],

    'entreprise' => [
        'label'            => 'Entreprise',
        'produits'         => null,
        'qrcodes'          => null,
        'rapports'         => null,
        'carte_risques'    => true,
        'ia'               => true,
        'ia_quota_mensuel' => null,
    ],

];