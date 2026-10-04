# Lance Cloudflare Tunnel (HTTP/2) et ne met a jour .env / api_config.dart
# QUE si le tunnel est reellement connecte. Sinon, rien n'est modifie.

$port = 8000
$maxWait = 75   # secondes d'attente pour la connexion du tunnel

# Sauvegardes horodatees (dans G:\veriscan\backups_tunnel)
$backupDir = "G:\veriscan\backups_tunnel"
New-Item -ItemType Directory $backupDir -Force | Out-Null
$stamp = Get-Date -Format "yyyyMMdd_HHmmss"

Write-Host "Arret des anciens cloudflared..." -ForegroundColor DarkGray
Stop-Process -Name cloudflared -Force -ErrorAction SilentlyContinue

Write-Host "Demarrage du tunnel Cloudflare (http2)..." -ForegroundColor Cyan
$job = Start-Job -ScriptBlock {
    param($p)
    & cloudflared tunnel --url "http://127.0.0.1:$p" --protocol http2 2>&1 | ForEach-Object { "$_" }
} -ArgumentList $port

$all = ""
$url = $null
$connected = $false
$elapsed = 0

while (-not $connected -and $elapsed -lt $maxWait) {
    Start-Sleep -Seconds 1
    $elapsed++
    $chunk = Receive-Job $job
    if ($chunk) { $all += ($chunk -join "`n") + "`n" }

    if (-not $url -and $all -match "https://(?!api\.)[a-z0-9\-]+\.trycloudflare\.com") {
        $url = $matches[0]
        Write-Host "Adresse creee : $url (en attente de la connexion...)" -ForegroundColor DarkGray
    }
    if ($url -and $all -match "Registered tunnel connection") {
        $connected = $true
    }
}

if (-not $connected) {
    Write-Host ""
    Write-Host "ECHEC : le tunnel n'a pas pu se connecter a Cloudflare (port 7844 bloque ?)." -ForegroundColor Red
    Write-Host "AUCUN fichier n'a ete modifie (.env, database/.env, api_config.dart)." -ForegroundColor Red
    Write-Host "Essaie un autre reseau (partage de connexion du telephone), puis relance." -ForegroundColor Yellow
    Stop-Job $job -ErrorAction SilentlyContinue
    Remove-Job $job -Force -ErrorAction SilentlyContinue
    Stop-Process -Name cloudflared -Force -ErrorAction SilentlyContinue
    exit 1
}

Write-Host "Tunnel CONNECTE : $url" -ForegroundColor Green
$domain = $url -replace "https://", ""
$utf8 = New-Object System.Text.UTF8Encoding($false)

function Update-EnvFile {
    param([string]$Path, [hashtable]$Map)
    if (-not (Test-Path $Path)) {
        Write-Host "Introuvable (ignore) : $Path" -ForegroundColor Yellow
        return
    }
    Copy-Item $Path (Join-Path $backupDir ((Split-Path $Path -Leaf) + "_" + (Split-Path (Split-Path $Path) -Leaf) + "_$stamp.bak")) -Force
    $text = [System.IO.File]::ReadAllText($Path)
    foreach ($key in $Map.Keys) {
        $text = [regex]::Replace($text, "(?m)^$key=.*$", { param($m) "$key=" + $Map[$key] }.GetNewClosure())
    }
    [System.IO.File]::WriteAllText($Path, $text, $utf8)
    Write-Host "Mis a jour : $Path" -ForegroundColor Green
}

# .env principal et database/.env
Update-EnvFile "G:\veriscan\.env" @{
    "APP_URL"                  = $url
    "SANCTUM_STATEFUL_DOMAINS" = $domain
    "GOOGLE_REDIRECT_URI"      = "$url/api/auth/google/callback"
    "FACEBOOK_REDIRECT_URI"    = "$url/api/auth/facebook/callback"
}
Update-EnvFile "G:\veriscan\database\.env" @{
    "SANCTUM_STATEFUL_DOMAINS" = $domain
    "GOOGLE_REDIRECT_URI"      = "$url/api/auth/google/callback"
    "FACEBOOK_REDIRECT_URI"    = "$url/api/auth/facebook/callback"
}

# Vider le cache de config Laravel pour que la nouvelle URL soit prise en compte
Push-Location "G:\veriscan"
php artisan config:clear | Out-Null
Pop-Location

# Mobile : on cherche api_config.dart (le dossier peut varier)
$mobileDir = "G:\veriscan_mobile\lib"
$apiCfg = $null
if (Test-Path $mobileDir) {
    $apiCfg = Get-ChildItem $mobileDir -Recurse -Filter api_config.dart -ErrorAction SilentlyContinue | Select-Object -First 1 -ExpandProperty FullName
}
if ($apiCfg) {
    Copy-Item $apiCfg (Join-Path $backupDir "api_config.dart_$stamp.bak") -Force
    $t = [System.IO.File]::ReadAllText($apiCfg)
    $t = [regex]::Replace($t, "static const String baseUrl = '.*';", "static const String baseUrl = '$url';")
    [System.IO.File]::WriteAllText($apiCfg, $t, $utf8)
    Write-Host "Mis a jour : $apiCfg" -ForegroundColor Green
} else {
    Write-Host "ATTENTION : api_config.dart introuvable sous $mobileDir (mobile non mis a jour)" -ForegroundColor Red
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Yellow
Write-Host "Nouvelle URL : $url" -ForegroundColor Yellow
Write-Host "Mets a jour manuellement si utilises :" -ForegroundColor Yellow
Write-Host "  - Google Cloud Console (OAuth redirect URI)" -ForegroundColor Yellow
Write-Host "  - Facebook Developers (redirect URI)" -ForegroundColor Yellow
Write-Host "  - Supabase (Allowed URLs)" -ForegroundColor Yellow
Write-Host "Mobile : hot restart (touche R majuscule) dans flutter run." -ForegroundColor Yellow
Write-Host "Sauvegardes dans : $backupDir" -ForegroundColor Yellow
Write-Host "======================================" -ForegroundColor Yellow

Write-Host "`nLogs du tunnel (Ctrl+C pour arreter) :" -ForegroundColor Cyan
try {
    Receive-Job $job -Wait
} finally {
    Stop-Job $job -ErrorAction SilentlyContinue
    Remove-Job $job -Force -ErrorAction SilentlyContinue
    Stop-Process -Name cloudflared -Force -ErrorAction SilentlyContinue
}
