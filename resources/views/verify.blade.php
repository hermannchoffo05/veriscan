<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – Vérification Produit</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --teal: #0F766E; --teal-dark: #064e3b; --teal-light: #f0fdfa;
            --green: #007A4D; --yellow: #d97706; --red: #CE1126;
            --text: #1F2937; --light: #6b7280; --border: #e5e7eb; --bg: #f4f8f8;
        }
        body { font-family: 'DM Sans', system-ui, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; overflow-x: hidden; }

        /* ── NAVBAR ── */
        .navbar { background: white; border-bottom: 1px solid var(--border); padding: 0 24px; height: 64px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 10; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
        .navbar-brand { font-size: 19px; font-weight: 800; color: var(--teal); text-decoration: none; display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .navbar-brand img { width: 38px; height: 38px; object-fit: contain; border-radius: 6px; }
        .navbar-right { display: flex; gap: 8px; align-items: center; }
        .btn-nav-ghost { font-size: 13px; font-weight: 600; color: var(--light); text-decoration: none; padding: 7px 14px; border-radius: 8px; border: 1.5px solid var(--border); transition: all 0.2s; background: white; white-space: nowrap; }
        .btn-nav-ghost:hover { color: var(--teal); border-color: var(--teal); background: var(--teal-light); }
        .btn-nav-solid { font-size: 13px; font-weight: 700; color: white; text-decoration: none; padding: 7px 14px; border-radius: 8px; background: var(--teal); transition: all 0.2s; display: flex; align-items: center; gap: 6px; white-space: nowrap; }
        .btn-nav-solid:hover { background: var(--teal-dark); }
        .btn-nav-solid svg { width: 14px; height: 14px; }

        /* ── HERO ── */
        .hero { background: linear-gradient(135deg, #042f2e 0%, #0f766e 55%, #0d9488 100%); padding: 48px 16px 52px; text-align: center; position: relative; overflow: hidden; }
        .hero::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 22px 22px; pointer-events: none; }
        .hero-orb1 { position: absolute; width: 300px; height: 300px; border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,0.06), transparent 70%); top: -100px; right: -80px; pointer-events: none; }
        .hero-orb2 { position: absolute; width: 200px; height: 200px; border-radius: 50%; background: radial-gradient(circle, rgba(252,209,22,0.08), transparent 70%); bottom: -60px; left: -40px; pointer-events: none; }
        .hero-content { position: relative; z-index: 1; max-width: 540px; margin: 0 auto; width: 100%; }
        .hero-badge { display: inline-flex; align-items: center; gap: 7px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 5px 14px; font-size: 11.5px; font-weight: 700; color: rgba(255,255,255,0.9); margin-bottom: 16px; letter-spacing: 0.03em; }
        .hero-badge::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: #5eead4; display: inline-block; animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(0.8)} }
        .hero h1 { font-size: 26px; font-weight: 800; color: white; margin-bottom: 10px; line-height: 1.2; }
        .hero p { font-size: 14px; color: rgba(255,255,255,0.72); margin-bottom: 28px; padding: 0 8px; }

        /* ── SEARCH BOX ── */
        .search-box { background: white; border-radius: 18px; padding: 20px 18px; box-shadow: 0 12px 40px rgba(0,0,0,0.18); width: 100%; }
        .search-tabs { display: flex; gap: 8px; margin-bottom: 16px; }
        .search-tab { flex: 1; padding: 9px 8px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; border: 1.5px solid var(--border); background: var(--bg); color: var(--light); transition: all 0.2s; text-align: center; display: flex; align-items: center; justify-content: center; gap: 6px; }
        .search-tab svg { width: 14px; height: 14px; flex-shrink: 0; }
        .search-tab.active { background: var(--teal-light); border-color: var(--teal); color: var(--teal); font-weight: 700; }
        .search-form { display: flex; flex-direction: column; gap: 10px; }
        .search-input { width: 100%; padding: 12px 16px; border-radius: 10px; border: 1.5px solid var(--border); font-size: 14px; font-family: inherit; outline: none; transition: border 0.2s; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text); }
        .search-input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(15,118,110,0.08); }
        .search-input::placeholder { text-transform: none; letter-spacing: 0; color: #9ca3af; font-size: 13px; }
        .btn-search { width: 100%; background: var(--teal); color: white; border: none; border-radius: 10px; padding: 12px 24px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 7px; }
        .btn-search:hover { background: var(--teal-dark); }
        .btn-search svg { width: 16px; height: 16px; }
        .search-hint { font-size: 12px; color: var(--light); margin-top: 10px; text-align: center; }

        /* Instructions scan */
        .scan-instructions { background: var(--teal-light); border-radius: 12px; padding: 16px 18px; text-align: left; }
        .scan-instructions .scan-title { font-size: 13px; font-weight: 700; color: var(--teal); margin-bottom: 10px; }
        .scan-step { font-size: 12.5px; color: var(--text); padding: 5px 0; display: flex; gap: 8px; align-items: flex-start; }
        .scan-step strong { color: var(--text); }
        .scan-note { font-size: 11.5px; color: var(--light); margin-top: 10px; border-top: 1px solid rgba(15,118,110,0.15); padding-top: 8px; }

        /* ── CONTENU ── */
        .container { max-width: 700px; margin: 0 auto; padding: 28px 16px; }

        /* ── RÉSULTAT ── */
        .result-card { background: white; border-radius: 20px; border: 2px solid var(--border); overflow: hidden; margin-bottom: 20px; animation: fadeUp 0.4s ease; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .result-header { padding: 18px 20px; display: flex; align-items: center; gap: 14px; }
        .result-icon { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .result-icon svg { width: 24px; height: 24px; }
        .result-status { font-size: 18px; font-weight: 800; }
        .result-message { font-size: 13px; margin-top: 4px; line-height: 1.5; }
        .status-authentique .result-header { background: #f0fdf4; border-bottom: 2px solid #bbf7d0; }
        .status-authentique.result-card { border-color: #4ade80; }
        .status-authentique .result-icon { background: #dcfce7; color: #007A4D; }
        .status-authentique .result-status { color: #007A4D; }
        .status-authentique .result-message { color: #065f46; }
        .status-suspect .result-header { background: #fffbeb; border-bottom: 2px solid #fde68a; }
        .status-suspect.result-card { border-color: #fbbf24; }
        .status-suspect .result-icon { background: #fef3c7; color: #d97706; }
        .status-suspect .result-status { color: #d97706; }
        .status-suspect .result-message { color: #92400e; }
        .status-contrefait .result-header, .status-inconnu .result-header { background: #fef2f2; border-bottom: 2px solid #fecaca; }
        .status-contrefait.result-card, .status-inconnu.result-card { border-color: #f87171; }
        .status-contrefait .result-icon, .status-inconnu .result-icon { background: #fee2e2; color: #CE1126; }
        .status-contrefait .result-status, .status-inconnu .result-status { color: #CE1126; }
        .status-contrefait .result-message, .status-inconnu .result-message { color: #7f1d1d; }
        .status-revoque .result-header { background: #f9fafb; border-bottom: 2px solid #d1d5db; }
        .status-revoque.result-card { border-color: #9ca3af; }
        .status-revoque .result-icon { background: #f3f4f6; color: #6b7280; }
        .status-revoque .result-status { color: #374151; }
        .status-revoque .result-message { color: #6b7280; }

        /* ── INFOS PRODUIT ── */
        .product-body { padding: 18px 20px; }
        .product-name { font-size: 17px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
        .product-cat { display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: var(--teal-light); color: var(--teal); margin-bottom: 16px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .info-label { font-size: 11px; font-weight: 700; color: var(--light); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px; }
        .info-value { font-size: 13px; font-weight: 600; color: var(--text); }
        .info-value.mono { font-family: monospace; font-size: 12px; }
        .scan-counter { display: flex; align-items: center; gap: 8px; background: var(--bg); border-radius: 10px; padding: 10px 14px; margin-top: 16px; font-size: 12.5px; color: var(--light); }
        .scan-counter svg { width: 14px; height: 14px; color: var(--teal); flex-shrink: 0; }
        .scan-counter strong { color: var(--teal); }

        /* ── SIGNALEMENT ── */
        .signalement-section { background: white; border-radius: 20px; border: 1.5px solid var(--border); overflow: hidden; margin-bottom: 20px; }
        .signalement-header { padding: 14px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .signalement-header svg { width: 16px; height: 16px; color: var(--red); flex-shrink: 0; }
        .signalement-header span { font-size: 14px; font-weight: 800; color: var(--text); flex: 1; }
        .signalement-toggle { background: none; border: 1.5px solid var(--border); border-radius: 8px; padding: 5px 12px; font-size: 12px; font-weight: 600; color: var(--light); cursor: pointer; font-family: inherit; transition: all 0.2s; flex-shrink: 0; }
        .signalement-toggle:hover { border-color: var(--red); color: var(--red); }
        .signalement-form { padding: 18px 20px; display: none; }
        .signalement-form.open { display: block; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; font-weight: 700; color: var(--light); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 5px; }
        .form-control { width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); font-size: 13.5px; font-family: inherit; color: var(--text); outline: none; transition: border 0.2s; }
        .form-control:focus { border-color: var(--teal); }
        textarea.form-control { resize: vertical; min-height: 90px; }
        .form-row { display: grid; grid-template-columns: 1fr; gap: 12px; }
        .btn-signaler { background: #fef2f2; color: var(--red); border: 1.5px solid #fecaca; border-radius: 10px; padding: 10px 22px; font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s; display: flex; align-items: center; gap: 7px; }
        .btn-signaler:hover { background: #fee2e2; }
        .btn-signaler svg { width: 14px; height: 14px; }

        /* ── ALERT ── */
        .alert-success { background: #f0fdf4; border: 1.5px solid #bbf7d0; border-left: 4px solid #4ade80; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; font-size: 13px; color: #065f46; font-weight: 500; }

        /* ── EMPTY STATE ── */
        .empty-state { text-align: center; padding: 48px 20px; }
        .empty-state svg { width: 56px; height: 56px; color: #ccfbf1; margin: 0 auto 16px; display: block; }
        .empty-state h2 { font-size: 18px; font-weight: 800; color: var(--text); margin-bottom: 8px; }
        .empty-state p { font-size: 13.5px; color: var(--light); max-width: 360px; margin: 0 auto; line-height: 1.6; }

        /* ── FOOTER ── */
        .footer { text-align: center; padding: 24px 16px; font-size: 12px; color: var(--light); border-top: 1px solid var(--border); background: white; margin-top: 32px; }
        .footer strong { color: var(--teal); }

        /* ── RESPONSIVE ── */
        @media (max-width: 480px) {
            .navbar { padding: 0 16px; }
            .navbar-brand { font-size: 17px; }
            .navbar-brand img { width: 32px; height: 32px; }
            .btn-nav-ghost { display: none; }
            .btn-nav-solid { padding: 6px 12px; font-size: 12px; }
            .hero { padding: 36px 14px 44px; }
            .hero h1 { font-size: 22px; }
            .hero p { font-size: 13px; }
            .search-box { padding: 16px 14px; border-radius: 14px; }
            .search-tab { font-size: 12px; padding: 8px 6px; }
            .info-grid { grid-template-columns: 1fr; }
            .result-header { padding: 14px 16px; }
            .product-body { padding: 14px 16px; }
            .signalement-header { padding: 12px 16px; }
            .signalement-form { padding: 14px 16px; }
            .container { padding: 20px 12px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="{{ url('/') }}" class="navbar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="VeriScan">
        VeriScan
    </a>
    <div class="navbar-right">
        <a href="{{ url('/') }}" class="btn-nav-ghost">Accueil</a>
        <a href="{{ route('fabricant.login') }}" class="btn-nav-ghost">Espace Fabricant</a>
        <a href="{{ route('fabricant.register') }}" class="btn-nav-solid">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            S'inscrire
        </a>
    </div>
</nav>

<div class="hero">
    <div class="hero-orb1"></div>
    <div class="hero-orb2"></div>
    <div class="hero-content">
        <div class="hero-badge">Certifié HMAC-SHA256 · Cameroun</div>
        <h1>Vérifiez l'authenticité<br>de votre produit</h1>
        <p>Entrez le code imprimé sur l'emballage ou scannez le QR code avec votre téléphone</p>

        <div class="search-box">
            <div class="search-tabs">
                <div class="search-tab active" id="tab-code" onclick="switchTab('code')">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Saisie manuelle
                </div>
                <div class="search-tab" id="tab-scan" onclick="switchTab('scan')">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg>
                    Scanner un QR
                </div>
            </div>

            <div id="form-code">
                <form action="{{ route('verify.code') }}" method="POST" class="search-form">
                    @csrf
                    <input type="text" name="code" class="search-input"
                           placeholder="Ex : VS-E0CIKGLM"
                           value="{{ old('code', $code ?? '') }}"
                           maxlength="20" required>
                    <button type="submit" class="btn-search">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Vérifier
                    </button>
                </form>
                <div class="search-hint">Le code est imprimé sous le QR code sur l'emballage du produit</div>
            </div>

            <div id="form-scan" style="display:none;">
                <div class="scan-instructions">
                    <div class="scan-title">Comment scanner le QR code :</div>
                    <div class="scan-step">📱 <div><strong>Android :</strong> Ouvrez l'appareil photo natif et pointez vers le QR code</div></div>
                    <div class="scan-step">🍎 <div><strong>iPhone :</strong> Même chose avec l'appareil photo ou depuis le Centre de contrôle</div></div>
                    <div class="scan-step">📷 <div><strong>Autre :</strong> Utilisez Google Lens ou une application de scan QR</div></div>
                    <div class="scan-note">Le navigateur s'ouvrira automatiquement sur la page de vérification VeriScan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">

    @if(session('success_signalement'))
    <div class="alert-success">✓ {{ session('success_signalement') }}</div>
    @endif

    @if(isset($statut) && $statut)
        <div class="result-card status-{{ $statut }}">
            <div class="result-header">
                <div class="result-icon">
                    @if($statut === 'authentique')
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    @elseif($statut === 'suspect')
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                </div>
                <div>
                    <div class="result-status">
                        @if($statut === 'authentique') ✓ AUTHENTIQUE
                        @elseif($statut === 'suspect') ⚠ SUSPECT
                        @elseif($statut === 'contrefait') ✗ CONTREFAIT
                        @elseif($statut === 'revoque') ⊘ RÉVOQUÉ
                        @else ✗ NON RECONNU
                        @endif
                    </div>
                    <div class="result-message">{{ $message }}</div>
                </div>
            </div>

            @if($produit)
            <div class="product-body">
                <div class="product-name">{{ $produit->nom }}</div>
                <div class="product-cat">{{ $produit->categorie }}</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Fabricant</div>
                        <div class="info-value">{{ $fabricant->nom_entreprise ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Code produit</div>
                        <div class="info-value mono">{{ $produit->code_produit }}</div>
                    </div>
                    @if(isset($lot) && $lot)
                    <div class="info-item">
                        <div class="info-label">Numéro de lot</div>
                        <div class="info-value mono">{{ $lot->numero_lot ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date de fabrication</div>
                        <div class="info-value">{{ $lot->date_fabrication ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date d'expiration</div>
                        <div class="info-value">{{ $lot->date_expiration ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Site de production</div>
                        <div class="info-value">{{ $lot->site_production ?? '—' }}</div>
                    </div>
                    @endif
                </div>
                @if(isset($qrCode) && $qrCode)
                <div class="scan-counter">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Ce QR code a été scanné <strong>{{ $qrCode->nb_scans }}</strong> fois au total
                </div>
                @endif
                @if(isset($signalementsCount) && $signalementsCount > 0)
                <div style="background:#fffbeb;border-radius:10px;padding:10px 14px;margin-top:12px;font-size:12.5px;color:#92400e;display:flex;align-items:center;gap:8px;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <strong>{{ $signalementsCount }} signalement(s)</strong> en cours sur ce produit
                </div>
                @endif
            </div>
            @endif
        </div>

        @if(isset($qrCode) && $qrCode && in_array($statut, ['authentique', 'suspect']))
        <div class="signalement-section">
            <div class="signalement-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Signaler ce produit comme suspect</span>
                <button class="signalement-toggle" onclick="toggleSignalement()">Signaler</button>
            </div>
            <div class="signalement-form" id="signalementForm">
                <form method="POST" action="{{ route('verify.signaler') }}" enctype="multipart/form-data" id="signalementFormData">
    @csrf
    <input type="hidden" name="qr_code_id" value="{{ $qrCode->id }}">
    <input type="hidden" name="latitude" id="lat_input">
    <input type="hidden" name="longitude" id="lng_input">
    <input type="hidden" name="localisation" id="loc_input">
                    <div class="form-group">
                        <label>Description du problème *</label>
                        <textarea name="description" class="form-control" required
                            placeholder="Décrivez ce qui vous semble suspect (emballage, couleur, odeur, effet indésirable...)"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Votre nom (optionnel)</label>
                            <input type="text" name="nom_signalant" class="form-control" placeholder="Nom ou pseudonyme">
                        </div>
                        <div class="form-group">
                            <label>Contact (optionnel)</label>
                            <input type="text" name="contact_signalant" class="form-control" placeholder="Téléphone ou email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Photo preuve (optionnel)</label>
                        <input type="file" name="photo_preuve" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn-signaler">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/></svg>
                        Soumettre le signalement
                    </button>
                </form>
            </div>
        </div>
        @endif

    @else
        <div class="empty-state">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <h2>Vérifiez votre produit</h2>
            <p>Saisissez le code produit imprimé sous le QR code sur l'emballage, ou scannez directement le QR code.</p>
        </div>
    @endif

</div>

<footer class="footer">
    <strong>VeriScan</strong> · La fraude s'arrête ici · Cameroun &copy; 2026
</footer>

<script>
function switchTab(tab) {
    document.getElementById('form-code').style.display = tab === 'code' ? 'block' : 'none';
    document.getElementById('form-scan').style.display = tab === 'scan' ? 'block' : 'none';
    document.getElementById('tab-code').classList.toggle('active', tab === 'code');
    document.getElementById('tab-scan').classList.toggle('active', tab === 'scan');
}
function toggleSignalement() {
    const form = document.getElementById('signalementForm');
    form.classList.toggle('open');
}
function toggleSignalement() {
    const form = document.getElementById('signalementForm');
    form.classList.toggle('open');
    if (form.classList.contains('open') && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('lat_input').value = pos.coords.latitude;
            document.getElementById('lng_input').value = pos.coords.longitude;
            document.getElementById('loc_input').value = pos.coords.latitude + ',' + pos.coords.longitude;
        });
    }
}
</script>
</body>
</html>