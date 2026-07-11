<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – {{ $locale === 'en' ? 'Waiting for confirmation' : 'En attente de confirmation' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --teal: #2E3A6B; --teal-dark: #232C54; --teal-light: #EEF0F8; --text: #1F2937; --text-light: #6b7280; --border: #e5e7eb; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; }
        .card { background: white; border-radius: 24px; border: 1.5px solid var(--border); padding: 40px 32px; max-width: 440px; width: 100%; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.06); }

        /* ── SPINNER ── */
        .spinner-wrap { width: 80px; height: 80px; margin: 0 auto 24px; position: relative; }
        .spinner { width: 80px; height: 80px; border: 4px solid var(--teal-light); border-top-color: var(--teal); border-radius: 50%; animation: spin 1s linear infinite; }
        .spinner-icon { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 28px; }
        @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

        .title { font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 8px; }
        .subtitle { font-size: 14px; color: var(--text-light); line-height: 1.7; margin-bottom: 28px; }

        /* ── STEPS ── */
        .steps { display: flex; flex-direction: column; gap: 12px; margin-bottom: 28px; text-align: left; }
        .step { display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px; background: var(--teal-light); border-radius: 10px; }
        .step-num { width: 24px; height: 24px; background: var(--teal); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; flex-shrink: 0; }
        .step-text { font-size: 13px; color: var(--text); line-height: 1.5; }

        /* ── INFOS ── */
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: var(--text-light); font-weight: 500; }
        .info-val { font-weight: 700; color: var(--text); }

        /* ── STATUS ICONS ── */
        .status-success { display: none; text-align: center; }
        .status-success .icon { font-size: 64px; margin-bottom: 16px; }
        .status-success .msg { font-size: 18px; font-weight: 800; color: #16a34a; }
        .status-failed { display: none; text-align: center; }
        .status-failed .icon { font-size: 64px; margin-bottom: 16px; }
        .status-failed .msg { font-size: 18px; font-weight: 800; color: #dc2626; margin-bottom: 16px; }
        .btn-retry { display: inline-block; padding: 10px 24px; background: var(--teal); color: white; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 700; }

        @media (max-width: 480px) {
            .card { padding: 28px 20px; }
        }
    </style>
</head>
<body>
<div class="card">

    {{-- En attente --}}
    <div id="status-pending">
        <div class="spinner-wrap">
            <div class="spinner"></div>
            <div class="spinner-icon">📱</div>
        </div>
        <div class="title">{{ $locale === 'en' ? 'Check your phone' : 'Vérifiez votre téléphone' }}</div>
        <div class="subtitle">{{ $locale === 'en' ? 'A notification has been sent to your number. Enter your PIN to confirm payment.' : 'Une notification a été envoyée sur votre numéro. Entrez votre code PIN pour confirmer le paiement.' }}</div>

        <div class="steps">
            <div class="step"><div class="step-num">1</div><div class="step-text">{{ $locale === 'en' ? 'Open the notification on your phone' : 'Ouvrez la notification sur votre téléphone' }}</div></div>
            <div class="step"><div class="step-num">2</div><div class="step-text">{{ $locale === 'en' ? 'Enter your Mobile Money PIN' : 'Entrez votre code PIN Mobile Money' }}</div></div>
            <div class="step"><div class="step-num">3</div><div class="step-text">{{ $locale === 'en' ? 'This page updates automatically' : 'Cette page se met à jour automatiquement' }}</div></div>
        </div>

        <div>
            <div class="info-row"><span class="info-label">Plan</span><span class="info-val">{{ ucfirst($abonnement->plan) }}</span></div>
            <div class="info-row"><span class="info-label">{{ $locale === 'en' ? 'Amount' : 'Montant' }}</span><span class="info-val">{{ number_format($abonnement->montant, 0, '.', ' ') }} XAF</span></div>
            <div class="info-row"><span class="info-label">{{ $locale === 'en' ? 'Number' : 'Numéro' }}</span><span class="info-val">{{ $abonnement->telephone }}</span></div>
            <div class="info-row"><span class="info-label">Référence</span><span class="info-val" style="font-size:11px;font-family:monospace;">{{ $abonnement->reference }}</span></div>
        </div>
    </div>

    {{-- Succès --}}
    <div class="status-success" id="status-success">
        <div class="icon">✅</div>
        <div class="msg">{{ $locale === 'en' ? 'Payment confirmed!' : 'Paiement confirmé !' }}</div>
        <p style="font-size:14px;color:var(--text-light);margin:12px 0 20px;">{{ $locale === 'en' ? 'Your plan is now active. Redirecting...' : 'Votre plan est maintenant actif. Redirection en cours...' }}</p>
    </div>

    {{-- Échec --}}
    <div class="status-failed" id="status-failed">
        <div class="icon">❌</div>
        <div class="msg">{{ $locale === 'en' ? 'Payment failed' : 'Paiement échoué' }}</div>
        <a href="{{ route('paiement.checkout', ['plan' => $abonnement->plan]) }}" class="btn-retry">{{ $locale === 'en' ? 'Try again' : 'Réessayer' }}</a>
    </div>

</div>

<script>
const reference = '{{ $abonnement->reference }}';
const successUrl = '{{ route('paiement.succes', ['reference' => $abonnement->reference]) }}';
let attempts = 0;
const maxAttempts = 24; // 2 minutes (5s * 24)

function checkStatus() {
    fetch('/paiement/statut?reference=' + reference)
        .then(r => r.json())
        .then(data => {
            if (data.statut === 'SUCCESSFUL') {
                document.getElementById('status-pending').style.display = 'none';
                document.getElementById('status-success').style.display = 'block';
                setTimeout(() => window.location.href = successUrl, 2000);
            } else if (data.statut === 'FAILED') {
                document.getElementById('status-pending').style.display = 'none';
                document.getElementById('status-failed').style.display = 'block';
            } else {
                attempts++;
                if (attempts < maxAttempts) {
                    setTimeout(checkStatus, 5000);
                }
            }
        })
        .catch(() => {
            attempts++;
            if (attempts < maxAttempts) setTimeout(checkStatus, 5000);
        });
}

// Démarrer le polling après 3s
setTimeout(checkStatus, 3000);
</script>
</body>
</html>