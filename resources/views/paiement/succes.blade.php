<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – {{ $locale === 'en' ? 'Payment successful' : 'Paiement réussi' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --teal: #0F766E; --teal-dark: #0a5c55; --teal-light: #F0FDFA; --text: #1F2937; --text-light: #6b7280; --border: #e5e7eb; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: white; border-radius: 24px; border: 1.5px solid #bbf7d0; padding: 40px 32px; max-width: 440px; width: 100%; text-align: center; box-shadow: 0 8px 32px rgba(15,118,110,0.1); }
        .icon { font-size: 72px; margin-bottom: 20px; animation: bounce 0.6s ease; }
        @keyframes bounce { 0%{transform:scale(0)} 60%{transform:scale(1.2)} 100%{transform:scale(1)} }
        .title { font-size: 24px; font-weight: 900; color: #16a34a; margin-bottom: 8px; }
        .subtitle { font-size: 14px; color: var(--text-light); line-height: 1.7; margin-bottom: 28px; }
        .recap { background: var(--teal-light); border-radius: 14px; padding: 20px; margin-bottom: 28px; text-align: left; }
        .recap-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid rgba(15,118,110,0.15); font-size: 13px; }
        .recap-row:last-child { border-bottom: none; }
        .recap-label { color: var(--text-light); }
        .recap-val { font-weight: 700; color: var(--teal); }
        .btn-dashboard { display: block; width: 100%; padding: 14px; background: var(--teal); color: white; border-radius: 12px; text-decoration: none; font-size: 15px; font-weight: 700; transition: all 0.2s; margin-bottom: 12px; }
        .btn-dashboard:hover { background: var(--teal-dark); }
        .btn-home { display: block; width: 100%; padding: 12px; background: white; color: var(--teal); border-radius: 12px; text-decoration: none; font-size: 14px; font-weight: 600; border: 1.5px solid var(--teal); transition: all 0.2s; }
        .btn-home:hover { background: var(--teal-light); }
        @media (max-width: 480px) { .card { padding: 28px 20px; } }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">🎉</div>
    <div class="title">{{ $locale === 'en' ? 'Payment successful!' : 'Paiement réussi !' }}</div>
    <div class="subtitle">{{ $locale === 'en' ? 'Your ' . ucfirst($abonnement->plan) . ' plan is now active. Welcome to VeriScan!' : 'Votre plan ' . ucfirst($abonnement->plan) . ' est maintenant actif. Bienvenue sur VeriScan !' }}</div>

    <div class="recap">
        <div class="recap-row"><span class="recap-label">Plan</span><span class="recap-val">{{ ucfirst($abonnement->plan) }}</span></div>
        <div class="recap-row"><span class="recap-label">{{ $locale === 'en' ? 'Amount paid' : 'Montant payé' }}</span><span class="recap-val">{{ number_format($abonnement->montant, 0, '.', ' ') }} XAF</span></div>
        <div class="recap-row"><span class="recap-label">{{ $locale === 'en' ? 'Number' : 'Numéro' }}</span><span class="recap-val">{{ $abonnement->telephone }}</span></div>
        <div class="recap-row"><span class="recap-label">Référence</span><span class="recap-val" style="font-size:11px;font-family:monospace;">{{ $abonnement->reference }}</span></div>
        <div class="recap-row"><span class="recap-label">Date</span><span class="recap-val">{{ $abonnement->updated_at->format('d/m/Y à H:i') }}</span></div>
    </div>

    @auth('fabricant')
    <a href="{{ route('fabricant.dashboard') }}" class="btn-dashboard">
        {{ $locale === 'en' ? 'Go to my dashboard' : 'Accéder à mon tableau de bord' }}
    </a>
    @else
    <a href="{{ route('fabricant.register') }}" class="btn-dashboard">
        {{ $locale === 'en' ? 'Create my account' : 'Créer mon compte' }}
    </a>
    @endauth
    <a href="{{ url('/') }}" class="btn-home">{{ $locale === 'en' ? 'Back to home' : 'Retour à l\'accueil' }}</a>
</div>
</body>
</html>