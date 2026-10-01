<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// -- Importations des Contrôleurs Fabricants --
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FabricantDashboardController;
use App\Http\Controllers\Auth\FabricantAuthController;
use App\Http\Controllers\FabricantProfilController;
use App\Http\Controllers\FabricantProduitsController;
use App\Http\Controllers\FabricantLotsController;
use App\Http\Controllers\FabricantQRCodesController;
use App\Http\Controllers\FabricantSignalementsController;
use App\Http\Controllers\FabricantRapportsController;
use App\Http\Controllers\FabricantStatistiquesController;
use App\Http\Controllers\FabricantParametresController;
use App\Http\Controllers\FabricantCarteController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\TarifsController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\EssaiGratuitController;
use App\Http\Controllers\AvisController;

// -- Importations des Contrôleurs Admin --
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFabricantsController;
use App\Http\Controllers\Admin\AdminCertificationsController;
use App\Http\Controllers\Admin\AdminConsommateursController;
use App\Http\Controllers\Admin\AdminSignalementsController;
use App\Http\Controllers\Admin\AdminRapportsController;
use App\Http\Controllers\Admin\AdminCarteController;
use App\Http\Controllers\Admin\AdminParametresController;

// -- Langue -------------------------------------------------------------------
Route::get("/langue/{locale}", function ($locale) {
    if (in_array($locale, ["fr", "en"])) { session(["locale" => $locale]); }
    return redirect()->back();
})->name("langue.changer");

// -- Pages publiques ----------------------------------------------------------
Route::get("/", [HomeController::class, "index"])->name("home");
Route::get("/stats-live", [HomeController::class, "statsLive"])
    ->name("stats.live")
    ->middleware("throttle:20,1");
Route::get("/tarifs", [TarifsController::class, "index"])->name("tarifs");
Route::get("/conditions", function () { return view("public.conditions"); })->name("conditions");
Route::get("/politique", function () { return view("public.politique"); })->name("politique");

// -- Avis (public) --------------------------------------------------------------
Route::post("/avis", [AvisController::class, "store"])
    ->middleware("throttle:5,1")
    ->name("avis.store");

// -- Vérification produit (public) --------------------------------------------
Route::get("/verify-home", [VerificationController::class, "home"])->name("verify.home");
Route::get("/verify", [VerificationController::class, "home"])->name("verify.index");
Route::get("/verify/{token}", [VerificationController::class, "verifyToken"])->name("verify.token");
Route::post("/verify-code", [VerificationController::class, "verifyCode"])->name("verify.code");
Route::post("/verify/signaler", [VerificationController::class, "signaler"])->name("verify.signaler");


// -- Paiement -----------------------------------------------------------------
Route::prefix("paiement")->name("paiement.")->group(function () {
    Route::post("/webhook", [PaiementController::class, "webhook"])->name("webhook");

    Route::middleware("auth:fabricant")->group(function () {
        Route::get("/checkout/{plan}", [PaiementController::class, "show"])->name("checkout");
        Route::post("/initier", [PaiementController::class, "initier"])->name("initier");
        Route::get("/attente", [PaiementController::class, "attente"])->name("attente");
        Route::get("/statut", [PaiementController::class, "statut"])->name("statut");
        Route::get("/succes", [PaiementController::class, "succes"])->name("succes");
    });
});

// -- Auth Fabricant (non connecté) -------------------------------------------
Route::middleware("guest:fabricant")->prefix("fabricant")->name("fabricant.")->group(function () {
    Route::get("/login", [FabricantAuthController::class, "showLogin"])->name("login");
    Route::post("/login", [FabricantAuthController::class, "login"]);
    Route::get("/register", [FabricantAuthController::class, "showRegister"])->name("register");
    Route::post("/register", [FabricantAuthController::class, "register"]);
    Route::get("/forgot-password", [FabricantAuthController::class, "showForgotPassword"])->name("password.request");
    Route::post("/forgot-password", [FabricantAuthController::class, "sendResetCode"])->name("password.email");
    Route::get("/verify-code", [FabricantAuthController::class, "showVerifyCode"])->name("password.verify");
    Route::post("/verify-code", [FabricantAuthController::class, "verifyCode"])->name("password.verify.submit");
    Route::get("/reset-password", [FabricantAuthController::class, "showResetPassword"])->name("password.reset");
    Route::post("/reset-password", [FabricantAuthController::class, "resetPassword"])->name("password.update");

    Route::get("/auth/google", [FabricantAuthController::class, "redirectToGoogle"])->name("auth.google");
    Route::get("/auth/google/callback", [FabricantAuthController::class, "handleGoogleCallback"])->name("auth.google.callback");
});

// -- Fabricant (connecté, sans vérif statut actif) ---------------------------
Route::middleware("auth:fabricant")->prefix("fabricant")->name("fabricant.")->group(function () {
    Route::get("/attente", function () { return view("fabricant.attente"); })->name("attente");
    Route::post("/logout", [FabricantAuthController::class, "logout"])->name("logout");
});

// -- Fabricant (connecté) ----------------------------------------------------
Route::middleware(["auth:fabricant"])->prefix("fabricant")->name("fabricant.")->group(function () {
    Route::get("/dashboard", [FabricantDashboardController::class, "index"])->name("dashboard");
    Route::get("/dashboard/search", [FabricantDashboardController::class, "search"])->name("dashboard.search");

    Route::post("/essai/{plan}", [EssaiGratuitController::class, "activer"])->name("essai.activer");

    Route::get("/tarifs", [TarifsController::class, "index"])->name("tarifs");

    // Chatbot / Assistant virtuel — reste gaté Pro/Entreprise uniquement
    Route::post("/dashboard/chat", [FabricantProduitsController::class, "chat"])->name("dashboard.chat")->middleware("plan.feature:ia");
    Route::post("/chatbot/ask", [FabricantProduitsController::class, "chat"])->name("chatbot.ask")->middleware("plan.feature:ia");

    // Profil
    Route::get("/profil", [FabricantProfilController::class, "index"])->name("profil");
    Route::put("/profil", [FabricantProfilController::class, "update"])->name("profil.update");
    Route::post("/profil/infos", [FabricantProfilController::class, "updateInfos"])->name("profil.infos");
    Route::put("/profil/password", [FabricantProfilController::class, "updatePassword"])->name("profil.password");
    Route::post("/profil/logo", [FabricantProfilController::class, "updateLogo"])->name("profil.logo");

    // Paramètres
    Route::get("/parametres", [FabricantParametresController::class, "index"])->name("parametres.index");
    Route::put("/parametres", [FabricantParametresController::class, "update"])->name("parametres.update");
    Route::post("/parametres/reset", [FabricantParametresController::class, "reset"])->name("parametres.reset");
    Route::delete("/parametres/account", [FabricantParametresController::class, "deleteAccount"])->name("parametres.delete");

    // Produits
    Route::get("/produits", [FabricantProduitsController::class, "index"])->name("produits.index");
    Route::get("/produits/create", [FabricantProduitsController::class, "create"])->name("produits.create");
    Route::post("/produits", [FabricantProduitsController::class, "store"])->name("produits.store");
    Route::get("/produits/{id}", [FabricantProduitsController::class, "show"])->name("produits.show");
    Route::get("/produits/{id}/edit", [FabricantProduitsController::class, "edit"])->name("produits.edit");
    Route::put("/produits/{id}", [FabricantProduitsController::class, "update"])->name("produits.update");
    Route::delete("/produits/{id}", [FabricantProduitsController::class, "destroy"])->name("produits.destroy");
    Route::post("/produits/generate-description", [FabricantProduitsController::class, "generateDescription"])->name("produits.generate-description");
    Route::post("/produits/classify-category", [FabricantProduitsController::class, "classifyCategory"])->name("produits.classify-category");

    // Lots
    Route::get("/produits/{produitId}/lots", [FabricantLotsController::class, "index"])->name("lots.index");
    Route::get("/produits/{produitId}/lots/create", [FabricantLotsController::class, "create"])->name("lots.create");
    Route::post("/produits/{produitId}/lots", [FabricantLotsController::class, "store"])->name("lots.store");
    Route::get("/produits/{produitId}/lots/{id}/edit", [FabricantLotsController::class, "edit"])->name("lots.edit");
    Route::put("/produits/{produitId}/lots/{id}", [FabricantLotsController::class, "update"])->name("lots.update");
    Route::delete("/produits/{produitId}/lots/{id}", [FabricantLotsController::class, "destroy"])->name("lots.destroy");
    Route::get("/lots/{lotId}/download-pdf", [FabricantQRCodesController::class, "downloadLotPdf"])->name("lots.download-pdf");

    // QR Codes
    Route::get("/qrcodes", [FabricantQRCodesController::class, "index"])->name("qrcodes.index");
    Route::get("/qrcodes/create", [FabricantQRCodesController::class, "create"])->name("qrcodes.create");
    Route::post("/qrcodes", [FabricantQRCodesController::class, "store"])->name("qrcodes.store");
    Route::get("/qrcodes/{id}", [FabricantQRCodesController::class, "show"])->name("qrcodes.show");
    Route::get("/qrcodes/{id}/download", [FabricantQRCodesController::class, "download"])->name("qrcodes.download");
    Route::post("/qrcodes/{id}/revoke", [FabricantQRCodesController::class, "revoke"])->name("qrcodes.revoke");
    Route::get("/qrcodes/{id}/download-pdf", [FabricantQRCodesController::class, "downloadPdf"])->name("qrcodes.download-pdf");

    // Signalements
    Route::get("/signalements", [FabricantSignalementsController::class, "index"])->name("signalements.index");
    Route::get("/signalements/{id}", [FabricantSignalementsController::class, "show"])->name("signalements.show");
    Route::patch("/signalements/{id}/traiter", [FabricantSignalementsController::class, "traiter"])->name("signalements.traiter");

    // Statistiques
    Route::get("/statistiques", [FabricantStatistiquesController::class, "index"])->name("statistiques.index");

    // Rapports
    Route::get("/rapports", [FabricantRapportsController::class, "index"])->name("rapports.index");
    Route::get("/rapports/pdf", [FabricantRapportsController::class, "downloadPdf"])->name("rapports.pdf");
    Route::get("/rapports/certificats", [FabricantRapportsController::class, "downloadCertificats"])->name("rapports.certificats");
    Route::get("/rapports/signalements", [FabricantRapportsController::class, "downloadSignalements"])->name("rapports.signalements");
    Route::get("/rapports/telecharger", [FabricantRapportsController::class, "telecharger"])->name("rapports.telecharger");

    // Carte
    Route::get("/carte", [FabricantCarteController::class, "index"])->name("carte.index");
});

// -- Auth Admin (non connecté) -----------------------------------------------
Route::middleware("guest:admin")->prefix("admin")->name("admin.")->group(function () {
    Route::get("/login", [AdminAuthController::class, "showLogin"])->name("login");
    Route::post("/login", [AdminAuthController::class, "login"]);
});

// -- Admin (connecté) ---------------------------------------------------------
Route::middleware("auth:admin")->prefix("admin")->name("admin.")->group(function () {
    Route::post("/logout", [AdminAuthController::class, "logout"])->name("logout");
    Route::get("/dashboard", [AdminDashboardController::class, "index"])->name("dashboard");
    Route::get("/api/stats", [AdminDashboardController::class, "stats"])->name("api.stats");

    Route::get("/fabricants", [AdminFabricantsController::class, "index"])->name("fabricants.index");
    Route::get("/fabricants/{id}", [AdminFabricantsController::class, "show"])->name("fabricants.show");
    // ✅ AJOUTÉ : validation et rejet d'un fabricant en attente
    Route::post("/fabricants/{id}/valider", [AdminFabricantsController::class, "valider"])->name("fabricants.valider");
    Route::post("/fabricants/{id}/rejeter", [AdminFabricantsController::class, "rejeter"])->name("fabricants.rejeter");
    Route::post("/fabricants/{id}/suspendre", [AdminFabricantsController::class, "suspendre"])->name("fabricants.suspendre");
    Route::post("/fabricants/{id}/reactiver", [AdminFabricantsController::class, "reactiver"])->name("fabricants.reactiver");
    Route::delete("/fabricants/{id}", [AdminFabricantsController::class, "destroy"])->name("fabricants.destroy");

    // Certification documentaire des produits (autorité de certification)
    Route::get("/certifications", [AdminCertificationsController::class, "index"])->name("certifications.index");
    Route::get("/certifications/{id}", [AdminCertificationsController::class, "show"])->name("certifications.show");
    Route::get("/certifications/{id}/justificatif", [AdminCertificationsController::class, "justificatif"])->name("certifications.justificatif");
    Route::post("/certifications/{id}/certifier", [AdminCertificationsController::class, "certifier"])->name("certifications.certifier");
    Route::post("/certifications/{id}/rejeter", [AdminCertificationsController::class, "rejeter"])->name("certifications.rejeter");
    Route::post("/certifications/{id}/revoquer", [AdminCertificationsController::class, "revoquer"])->name("certifications.revoquer");

    // Gestion des comptes consommateurs
    Route::get("/consommateurs", [AdminConsommateursController::class, "index"])->name("consommateurs.index");
    Route::post("/consommateurs/{id}/suspendre", [AdminConsommateursController::class, "suspendre"])->name("consommateurs.suspendre");
    Route::post("/consommateurs/{id}/reactiver", [AdminConsommateursController::class, "reactiver"])->name("consommateurs.reactiver");

    Route::get("/signalements", [AdminSignalementsController::class, "index"])->name("signalements.index");
    Route::get("/signalements/{id}", [AdminSignalementsController::class, "show"])->name("signalements.show");
    Route::post("/signalements/{id}/traiter", [AdminSignalementsController::class, "traiter"])->name("signalements.traiter");
    Route::post("/signalements/{id}/escalader", [AdminSignalementsController::class, "escalader"])->name("signalements.escalader");
    Route::post("/signalements/analyser-photo", [AdminSignalementsController::class, "analyserPhoto"])->name("signalements.analyser-photo");
    Route::post("/signalements/resume", [AdminSignalementsController::class, "resume"])->name("signalements.resume");

    Route::get("/rapports", [AdminRapportsController::class, "index"])->name("rapports.index");
    Route::get("/rapports/pdf", [AdminRapportsController::class, "downloadPdf"])->name("rapports.pdf");
    Route::get("/rapports/telecharger", [AdminRapportsController::class, "telecharger"])->name("rapports.telecharger");

    Route::get("/carte", [AdminCarteController::class, "index"])->name("carte.index");
    Route::get("/carte/view", [AdminCarteController::class, "index"])->name("carte");

    Route::get("/parametres", [AdminParametresController::class, "index"])->name("parametres.index");
    Route::put("/parametres", [AdminParametresController::class, "update"])->name("parametres.update");
    Route::post("/parametres/photo", [AdminParametresController::class, "updatePhoto"])->name("parametres.photo");
    Route::post("/parametres/systeme", [AdminParametresController::class, "updateSysteme"])->name("parametres.systeme");
});