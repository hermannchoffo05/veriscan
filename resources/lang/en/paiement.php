<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment messages (CamPay Mobile Money)
    |--------------------------------------------------------------------------
    | Used in PaiementController::initier() when the call to the CamPay API
    | fails at different stages.
    */

    'erreur_connexion' => "Unable to connect to the payment service right now. Please try again in a moment.",

    'erreur_reseau' => "A network error occurred while communicating with the payment service. Please try again.",

    'erreur_initiation' => "The payment could not be initiated. Please check your phone number and try again.",

];