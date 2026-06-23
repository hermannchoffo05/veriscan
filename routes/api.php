<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\VerifyApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\PasswordResetApiController;
use App\Http\Controllers\Api\AssistantApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\FacebookAuthController;

// ═══════════════════════════════════════════════
// ROUTES PUBLIQUES
// ═══════════════════════════════════════════════
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/verify', [VerifyApiController::class, 'verify']);
Route::post('/report', [ReportApiController::class, 'store']);
Route::get('/search', [VerifyApiController::class, 'search']);
Route::post('/forgot-password',    [PasswordResetApiController::class, 'sendResetCode']);
Route::post('/verify-reset-code',  [PasswordResetApiController::class, 'verifyCode']);
Route::post('/reset-password',     [PasswordResetApiController::class, 'resetPassword']);
Route::post('/auth/google', [GoogleAuthController::class, 'handleGoogleToken']);
Route::post('/auth/facebook', [FacebookAuthController::class, 'handleFacebookToken']);

// ═══════════════════════════════════════════════
// ROUTES PROTÉGÉES
// ═══════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/mes-verifications', [VerifyApiController::class, 'history']);
    Route::get('/mes-signalements', [ReportApiController::class, 'index']);
    Route::post('/assistant', [AssistantApiController::class, 'chat']);
    Route::get('/conversations', [AssistantApiController::class, 'historique']);
    Route::delete('/conversations', [AssistantApiController::class, 'clearHistorique']);

    // Notifications
    Route::get('/notifications', [NotificationApiController::class, 'index']);
    Route::patch('/notifications/{id}/lue', [NotificationApiController::class, 'marquerLue']);
    Route::patch('/notifications/toutes-lues', [NotificationApiController::class, 'marquerToutesLues']);

    // Profil utilisateur
    Route::put('/profile', [UserApiController::class, 'updateProfile']);
    Route::put('/password', [UserApiController::class, 'updatePassword']);
    Route::post('/fcm-token', [UserApiController::class, 'saveFcmToken']);
});