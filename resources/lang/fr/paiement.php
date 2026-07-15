<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Messages de paiement (CamPay Mobile Money)
    |--------------------------------------------------------------------------
    | Utilisées dans PaiementController::initier() lorsque l'appel à l'API
    | CamPay échoue à différentes étapes.
    */

    'erreur_connexion' => "Impossible de se connecter au service de paiement pour le moment. Veuillez réessayer dans quelques instants.",

    'erreur_reseau' => "Une erreur réseau est survenue lors de la communication avec le service de paiement. Veuillez réessayer.",

    'erreur_initiation' => "Le paiement n'a pas pu être initié. Veuillez vérifier votre numéro et réessayer.",

];