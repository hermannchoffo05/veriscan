# Lance Cloudflare Tunnel et met a jour les .env automatiquement

Write-Host "Demarrage du tunnel Cloudflare..." -ForegroundColor Cyan

# Lance cloudflared et capture l'URL
$job = Start-Job -ScriptBlock {
    & cloudflared tunnel --url http://localhost:8000 2>&1
}

# Attend l'URL dans la sortie
$url = $null
$timeout = 30
$elapsed = 0

while (-not $url -and $elapsed -lt $timeout) {
    Start-Sleep -Seconds 1
    $elapsed++
    $output = Receive-Job $job
    foreach ($line in $output) {
        if ($line -match "https://[a-z0-9\-]+\.trycloudflare\.com") {
            $url = $matches[0]
            break
        }
    }
}

if (-not $url) {
    Write-Host "Impossible de recuperer l'URL. Verifie ta connexion." -ForegroundColor Red
    exit 1
}

Write-Host "URL obtenue : $url" -ForegroundColor Green
$domain = $url -replace "https://", ""

# Mise a jour .env principal
$envPath = "G:\veriscan\.env"
$content = Get-Content $envPath -Raw
$content = $content -replace "APP_URL=.*", "APP_URL=$url"
$content = $content -replace "SANCTUM_STATEFUL_DOMAINS=.*", "SANCTUM_STATEFUL_DOMAINS=$domain"
$content = $content -replace "GOOGLE_REDIRECT_URI=.*", "GOOGLE_REDIRECT_URI=$url/api/auth/google/callback"
$content = $content -replace "FACEBOOK_REDIRECT_URI=.*", "FACEBOOK_REDIRECT_URI=$url/api/auth/facebook/callback"
Set-Content $envPath $content -NoNewline
Write-Host ".env principal mis a jour" -ForegroundColor Green

# Mise a jour database/.env
$dbEnvPath = "G:\veriscan\database\.env"
$content2 = Get-Content $dbEnvPath -Raw
$content2 = $content2 -replace "SANCTUM_STATEFUL_DOMAINS=.*", "SANCTUM_STATEFUL_DOMAINS=$domain"
$content2 = $content2 -replace "GOOGLE_REDIRECT_URI=.*", "GOOGLE_REDIRECT_URI=$url/api/auth/google/callback"
$content2 = $content2 -replace "FACEBOOK_REDIRECT_URI=.*", "FACEBOOK_REDIRECT_URI=$url/api/auth/facebook/callback"
Set-Content $dbEnvPath $content2 -NoNewline
Write-Host "database/.env mis a jour" -ForegroundColor Green

Write-Host ""
Write-Host "======================================" -ForegroundColor Yellow
Write-Host "Nouvelle URL : $url" -ForegroundColor Yellow
Write-Host "Mets a jour manuellement :" -ForegroundColor Yellow
Write-Host "  - Google Cloud Console (OAuth redirect URI)" -ForegroundColor Yellow
Write-Host "  - Facebook Developers (redirect URI)" -ForegroundColor Yellow
Write-Host "  - Supabase (Allowed URLs)" -ForegroundColor Yellow
Write-Host "======================================" -ForegroundColor Yellow

# Continue d'afficher les logs du tunnel
Write-Host "`nLogs du tunnel (Ctrl+C pour arreter) :" -ForegroundColor Cyan
Receive-Job $job -Wait
