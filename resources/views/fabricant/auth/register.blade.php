<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – Créer un compte</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%;
            overflow: hidden;
            font-family: 'DM Sans', system-ui, sans-serif;
            background: #dde1f0;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .card {
            display: flex;
            width: 100%;
            max-width: 980px;
            height: calc(100vh - 32px);
            max-height: 720px;
            border-radius: 24px;
            overflow: hidden;
            background: white;
            box-shadow: 0 30px 80px rgba(0,0,0,0.25);
        }
        .left {
            width: 50%;
            position: relative;
            overflow: hidden;
            border-radius: 24px 0 0 24px;
            background: #171B3D;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px;
            flex-shrink: 0;
            gap: 20px;
        }
        .left::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 22px 22px;
            z-index: 0;
        }
        .sphere {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, rgba(255,255,255,0.20), rgba(46,58,107,0.55) 60%, rgba(23,27,61,0.75));
            box-shadow: inset -6px -6px 20px rgba(0,0,0,0.3), inset 6px 6px 20px rgba(255,255,255,0.10);
            z-index: 1;
        }
        .sphere-tl { width:220px; height:220px; top:-75px; left:-60px; opacity:0.45; }
        .sphere-bl { width:170px; height:170px; bottom:-65px; left:-28px; opacity:0.35; }
        .sphere-tr { width:100px; height:100px; top:20px; right:-20px; opacity:0.25; }
        .shapes-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            pointer-events: none;
        }
        .logo-center {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }
        .logo-circle {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: white center 55% / 85% no-repeat;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            border: 2px solid rgba(255,255,255,0.15);
        }
        .left-tagline {
            position: relative;
            z-index: 3;
            text-align: center;
            color: rgba(255,255,255,0.68);
            font-size: 13px;
            font-weight: 500;
            line-height: 1.6;
        }
        .left-tagline strong {
            display: block;
            color: white;
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        .left-badges {
            position: relative;
            z-index: 3;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.18);
            color: rgba(255,255,255,0.85);
            font-size: 11px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
        }
        .badge svg { width: 11px; height: 11px; color: #F5A623; flex-shrink: 0; }
        .left-features {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            max-width: 260px;
        }
        .feat-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.80);
            font-size: 12.5px;
            font-weight: 500;
        }
        .feat-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .feat-icon svg { width: 15px; height: 15px; color: #F5A623; }
        .right {
            width: 50%;
            padding: 24px 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
            overflow: hidden;
        }
        .form-card {
            background: #F8F9FD;
            border: 1.5px solid rgba(46,58,107,0.18);
            border-radius: 16px;
            padding: 20px 22px 18px;
            box-shadow: 0 2px 16px rgba(46,58,107,0.06);
        }
        .form-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 10.5px;
            font-weight: 700;
            color: #2E3A6B;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .form-tag .dot { width:6px; height:6px; border-radius:50%; background:#2E3A6B; }
        h1 { font-size: 20px; font-weight: 800; color: #111827; margin-bottom: 2px; }
        .sub { font-size: 12px; color: #6b7280; margin-bottom: 14px; }
        .form-grid { display: grid; grid-template-columns: 1fr; gap: 10px; }
        .field { display: flex; flex-direction: column; gap: 3px; }
        label { font-size: 11.5px; font-weight: 500; color: #374151; }
        .iw { position: relative; }
        .iw > svg {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 13px;
            height: 13px;
            color: #9ca3af;
            pointer-events: none;
        }
        input[type=text], input[type=email], input[type=password], input[type=tel], select {
            width: 100%;
            padding: 8px 11px 8px 30px;
            border: 1.5px solid #e5e7eb;
            border-radius: 9px;
            font-size: 13px;
            color: #111827;
            background: white;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
        }
        input:focus, select:focus {
            border-color: #2E3A6B;
            box-shadow: 0 0 0 3px rgba(46,58,107,0.12);
        }
        .eye-btn {
            position: absolute;
            right: 9px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .eye-btn:hover { color: #374151; }
        .select-wrap { position: relative; width: 100%; }
        .select-wrap::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 0; height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid #9ca3af;
            pointer-events: none;
        }
        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 11.5px;
            color: #6b7280;
            margin-top: 2px;
        }
        .terms-row input[type=checkbox] {
            width: 13px; height: 13px;
            accent-color: #2E3A6B;
            margin-top: 1px;
            flex-shrink: 0;
        }
        .terms-row a { color: #2E3A6B; font-weight: 600; text-decoration: none; }
        .terms-row a:hover { text-decoration: underline; }
        .btn {
            width: 100%;
            padding: 10px;
            background: #F5A623;
            color: #171B3D;
            border: none;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(245,166,35,0.35);
            margin-top: 4px;
        }
        .btn:hover { background: #e0961d; }
        .btn:active { transform: scale(0.99); }
        .footer-link {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            margin-top: 12px;
        }
        .footer-link a { color: #2E3A6B; font-weight: 700; text-decoration: none; }
        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 10px;
            padding: 9px 13px;
            font-size: 12.5px;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            html, body { overflow: auto; }
            body { padding: 0; align-items: flex-start; }
            .card {
                flex-direction: column;
                border-radius: 0;
                height: auto;
                max-height: none;
                box-shadow: none;
            }
            .left {
                width: 100%;
                min-height: 120px;
                border-radius: 0;
                padding: 16px 20px;
                flex-direction: row;
                justify-content: flex-start;
                gap: 14px;
            }
            .logo-center { flex-direction: row; align-items: center; gap: 12px; }
            .logo-circle { width: 52px; height: 52px; }
            .left-badges { display: none; }
            .left-tagline { font-size: 11px; }
            .left-tagline strong { font-size: 15px; }
            .left-features { display: none; }
            .right {
                width: 100%;
                border-radius: 0;
                padding: 16px;
            }
        }
        @media (max-width: 480px) {
            .field-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="card">

    {{-- ── GAUCHE ── --}}
    <div class="left">
        <div class="sphere sphere-tl"></div>
        <div class="sphere sphere-bl"></div>
        <div class="sphere sphere-tr"></div>

        <svg class="shapes-layer" viewBox="0 0 500 720" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <radialGradient id="sg1" cx="35%" cy="30%" r="65%"><stop offset="0%" stop-color="#4A5899" stop-opacity="0.9"/><stop offset="100%" stop-color="#0d0f26" stop-opacity="0.6"/></radialGradient>
                <radialGradient id="sg2" cx="30%" cy="25%" r="70%"><stop offset="0%" stop-color="#2E3A6B" stop-opacity="0.95"/><stop offset="100%" stop-color="#08091a" stop-opacity="0.75"/></radialGradient>
                <radialGradient id="sg3" cx="40%" cy="35%" r="60%"><stop offset="0%" stop-color="#7480c2" stop-opacity="0.85"/><stop offset="100%" stop-color="#171B3D" stop-opacity="0.65"/></radialGradient>
                <linearGradient id="sring1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#4A5899" stop-opacity="0.65"/><stop offset="100%" stop-color="#171B3D" stop-opacity="0.05"/></linearGradient>
            </defs>
            <circle cx="420" cy="580" r="110" fill="url(#sg2)" opacity="0.20"/>
            <circle cx="250" cy="60" r="55" fill="url(#sg1)" opacity="0.20"/>
            <circle cx="55" cy="380" r="38" fill="url(#sg3)" opacity="0.25"/>
            <circle cx="370" cy="180" r="18" fill="url(#sg1)" opacity="0.60"/>
            <ellipse cx="440" cy="130" rx="52" ry="52" fill="none" stroke="url(#sring1)" stroke-width="14" opacity="0.65"/>
            <rect x="130" y="180" width="60" height="50" rx="2" fill="#171B3D" opacity="0.40"/>
            <g opacity="0.28" stroke="#4A5899" stroke-width="1.5" fill="none">
                <rect x="330" y="500" width="48" height="48" rx="3"/>
                <rect x="344" y="488" width="48" height="48" rx="3"/>
                <line x1="330" y1="500" x2="344" y2="488"/>
                <line x1="378" y1="500" x2="392" y2="488"/>
            </g>
            <rect x="220" y="480" width="34" height="34" rx="4" fill="url(#sg3)" opacity="0.42" transform="rotate(45 237 497)"/>
            <circle cx="195" cy="300" r="4" fill="#F5A623" opacity="0.45"/>
            <circle cx="370" cy="340" r="3" fill="#4A5899" opacity="0.55"/>
        </svg>

        <div class="logo-center">
            <div class="logo-circle" style="background-image: url('{{ asset('images/logo.png') }}');"></div>
            <div class="left-tagline">
                <strong>VeriScan</strong>
                La fraude s'arrête ici.
            </div>
        </div>

        <div class="left-badges">
            <span class="badge"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Certifié</span>
            <span class="badge"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Sécurisé</span>
            <span class="badge"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Fiable</span>
        </div>

        <div class="left-features">
            <div class="feat-item">
                <div class="feat-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                Génération de QR codes sécurisés
            </div>
            <div class="feat-item">
                <div class="feat-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                Traçabilité produit en temps réel
            </div>
            <div class="feat-item">
                <div class="feat-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                Alertes anti-contrefaçon instantanées
            </div>
            <div class="feat-item">
                <div class="feat-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                Tableau de bord analytique complet
            </div>
        </div>
    </div>

    {{-- ── DROITE ── --}}
    <div class="right">
        <div class="form-card">
            <div class="form-tag"><span class="dot"></span>Nouveau compte</div>
            <h1>Créer un compte</h1>
            <p class="sub">Remplissez les informations pour rejoindre la plateforme</p>

            @if ($errors->any())
                <div class="error-box">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('fabricant.register') }}">
                @csrf
                <div class="form-grid">

                    <div class="field">
                        <label>Nom de l'entreprise</label>
                        <div class="iw">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <input type="text" name="nom_entreprise" value="{{ old('nom_entreprise') }}" required placeholder="Ex : Pharmacie Centrale">
                        </div>
                    </div>

                    <div class="field">
                        <label>Adresse email</label>
                        <div class="iw">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="contact@entreprise.com">
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label>Téléphone</label>
                            <div class="iw">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <input type="tel" name="telephone" value="{{ old('telephone') }}" placeholder="+237 6XX XXX XXX">
                            </div>
                        </div>
                        <div class="field">
                            <label>Pays</label>
                            <div class="iw">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                                <div class="select-wrap">
                                    <select name="pays" style="padding-left:30px;">
                                        <option value="CM" selected>Cameroun</option>
                                        <option value="SN">Sénégal</option>
                                        <option value="CI">Côte d'Ivoire</option>
                                        <option value="NG">Nigeria</option>
                                        <option value="GH">Ghana</option>
                                        <option value="OTHER">Autre</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label>Mot de passe</label>
                            <div class="iw">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <input type="password" name="password" id="pw1" required placeholder="••••••••" style="padding-right:32px;">
                                <button type="button" class="eye-btn" onclick="togglePw('pw1','eye1')">
                                    <svg id="eye1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="field">
                            <label>Confirmer</label>
                            <div class="iw">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <input type="password" name="password_confirmation" id="pw2" required placeholder="Répétez" style="padding-right:32px;">
                                <button type="button" class="eye-btn" onclick="togglePw('pw2','eye2')">
                                    <svg id="eye2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="terms-row">
                        <input type="checkbox" name="terms" required>
                        <span>
                            J'accepte les
                            <a href="{{ route('conditions') }}" target="_blank">Conditions d'utilisation</a>
                            et la
                            <a href="{{ route('politique') }}" target="_blank">Politique de confidentialité</a>
                            de VeriScan
                        </span>
                    </div>

                    <button type="submit" class="btn">Créer mon compte</button>

                </div>
            </form>
        </div>
        <p class="footer-link">Déjà un compte ? <a href="{{ route('fabricant.login') }}">Se connecter</a></p>
    </div>

</div>
<script>
    function togglePw(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        icon.innerHTML = show
            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
</script>
</body>
</html>