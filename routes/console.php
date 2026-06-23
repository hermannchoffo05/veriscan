<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\AIRiskScoringService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── VeriScan Scheduler ────────────────────────────────────────────────────

// Recalcul des scores IA toutes les 6 heures
Schedule::call(function () {
    $service = new AIRiskScoringService();
    $count   = $service->calculerTousLesScores();
    \Illuminate\Support\Facades\Log::info("VeriScan Scheduler — {$count} scores recalculés");
})->everysixHours()->name('veriscan:scoring-ia');

// Nettoyage des scores obsolètes (> 7 jours sans recalcul) — tous les jours à minuit
Schedule::call(function () {
    $deleted = \App\Models\RiskScore::where('computed_at', '<', now()->subDays(7))->delete();
    \Illuminate\Support\Facades\Log::info("VeriScan Scheduler — {$deleted} scores obsolètes supprimés");
})->daily()->name('veriscan:cleanup-scores');