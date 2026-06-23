<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan — {{ app()->getLocale() === 'en' ? 'Fight against counterfeiting' : 'Luttez contre la contrefaçon' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --teal: #0F766E; --teal-dark: #0a5c55; --teal-mid: #14b8a6; --teal-light: #F0FDFA; --yellow: #FCD116; --text: #1F2937; --text-light: #6b7280; --border: #e5e7eb; --white: #ffffff; }
        body { font-family: 'Inter', sans-serif; color: var(--text); background: var(--white); overflow-x: hidden; }

        /* ── NAVBAR ── */
        nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; background: rgba(255,255,255,0.96); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0,0,0,0.07); padding: 0 48px; height: 72px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .nav-logo { display: flex; align-items: center; gap: 6px; text-decoration: none; flex-shrink: 0; }
        .nav-logo img { width: 48px; height: 48px; object-fit: contain; }
        .nav-logo-text { font-size: 20px; font-weight: 900; color: var(--text); letter-spacing: -0.5px; }
        .nav-logo-text span { color: var(--teal); }
        .nav-links { display: flex; align-items: center; gap: 24px; }
        .nav-links a { font-size: 13.5px; font-weight: 500; color: var(--text-light); text-decoration: none; transition: color 0.2s; white-space: nowrap; }
        .nav-links a:hover { color: var(--teal); }
        .nav-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

        /* ── Boutons navbar uniformes ── */
        .btn-nav { padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s; white-space: nowrap; cursor: pointer; border: none; font-family: inherit; }
        .btn-nav-teal { background: var(--teal-light); color: var(--teal); border: 1.5px solid rgba(15,118,110,0.25); }
        .btn-nav-teal:hover { background: #ccfbf1; }
        .btn-nav-outline { background: none; color: var(--teal); border: 1.5px solid var(--teal); }
        .btn-nav-outline:hover { background: var(--teal-light); }
        .btn-nav-solid { background: var(--teal); color: white; border: 1.5px solid var(--teal); box-shadow: 0 2px 8px rgba(15,118,110,0.25); }
        .btn-nav-solid:hover { background: var(--teal-dark); }

        /* ── Langue ── */
        .lang-switch { display: flex; gap: 2px; border: 1.5px solid var(--border); border-radius: 8px; overflow: hidden; }
        .lang-btn { padding: 6px 10px; font-size: 12px; font-weight: 700; color: var(--text-light); text-decoration: none; transition: all 0.2s; background: white; }
        .lang-btn.active { background: var(--teal); color: white; }
        .lang-btn:hover:not(.active) { background: var(--teal-light); color: var(--teal); }

        /* ── Hamburger mobile ── */
        .nav-hamburger { display: none; background: none; border: none; cursor: pointer; padding: 4px; color: var(--text); flex-shrink: 0; }
        .nav-hamburger svg { width: 24px; height: 24px; }
        .nav-mobile-menu { display: none; position: fixed; top: 72px; left: 0; right: 0; background: white; border-bottom: 1px solid var(--border); padding: 16px 24px; flex-direction: column; gap: 0; z-index: 99; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .nav-mobile-menu.open { display: flex; }
        .nav-mobile-menu a { font-size: 15px; font-weight: 600; color: var(--text); text-decoration: none; padding: 12px 0; border-bottom: 1px solid var(--border); }
        .nav-mobile-menu a:last-child { border-bottom: none; }
        .nav-mobile-menu a:hover { color: var(--teal); }
        .nav-mobile-lang { display: flex; gap: 8px; padding: 12px 0; }
        .nav-mobile-lang a { padding: 6px 16px; border-radius: 8px; border: 1.5px solid var(--border); font-size: 13px; font-weight: 700; text-decoration: none; }

        /* ── HERO ── */
        .hero { min-height: 100vh; background: #e8f7f5; display: flex; align-items: center; padding: 100px 60px 30px; position: relative; overflow: hidden; gap: 60px; }
        .hero-left { flex: 1; position: relative; z-index: 2; max-width: 560px; }
        .cameroon-map { position: absolute; width: 580px; height: 580px; top: 50%; left: -30px; transform: translateY(-50%); opacity: 0.14; z-index: 0; pointer-events: none; filter: invert(40%) sepia(80%) saturate(400%) hue-rotate(130deg); }
        .hero-left h1, .hero-left p, .hero-actions { position: relative; z-index: 1; }
        .hero h1 { font-size: 54px; font-weight: 900; color: var(--text); line-height: 1.08; margin-bottom: 22px; letter-spacing: -1px; }
        .hero h1 span { color: var(--teal); }
        .hero p { font-size: 16px; color: var(--text-light); line-height: 1.8; margin-bottom: 40px; max-width: 460px; }
        .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; }
        .btn-hero-primary { padding: 14px 28px; background: var(--teal); color: white; border-radius: 12px; border: none; font-size: 15px; font-weight: 700; cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.25s; box-shadow: 0 6px 24px rgba(15,118,110,0.35); }
        .btn-hero-primary:hover { background: var(--teal-dark); transform: translateY(-2px); }
        .btn-hero-primary svg { width: 18px; height: 18px; }
        .btn-hero-secondary { padding: 14px 28px; background: white; color: var(--text); border-radius: 12px; border: 1.5px solid var(--border); font-size: 15px; font-weight: 600; cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.25s; }
        .btn-hero-secondary:hover { border-color: var(--teal); color: var(--teal); transform: translateY(-2px); }
        .btn-hero-secondary svg { width: 18px; height: 18px; }
        .hero-right { flex: 1; display: flex; align-items: center; justify-content: center; position: relative; z-index: 2; }
        .hero-illustration { width: 460px; filter: drop-shadow(0 20px 40px rgba(15,118,110,0.15)); animation: fadeUp 0.8s ease 0.2s both; }

        /* ── STATS ── */
        .stats-band { background: #0F766E; padding: 48px 60px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; position: relative; overflow: hidden; }
        .stats-band::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 24px 24px; }
        .band-stat { text-align: center; color: white; position: relative; z-index: 1; padding: 16px; border-right: 1px solid rgba(255,255,255,0.15); }
        .band-stat:last-child { border-right: none; }
        .band-stat-value { font-size: 48px; font-weight: 900; letter-spacing: -1px; }
        .band-stat-value .accent { color: var(--yellow); }
        .band-stat-label { font-size: 13px; color: rgba(255,255,255,0.7); margin-top: 6px; font-weight: 500; }

        /* ── SECTIONS ── */
        section { padding: 90px 60px; }
        .section-header { margin-bottom: 56px; }
        .section-badge { display: inline-flex; align-items: center; gap: 6px; background: var(--teal-light); color: var(--teal); font-size: 11px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; padding: 5px 14px; border-radius: 20px; margin-bottom: 18px; }
        .section-title { font-size: 40px; font-weight: 900; color: var(--text); line-height: 1.15; margin-bottom: 16px; letter-spacing: -0.5px; }
        .section-title span { color: var(--teal); }
        .section-sub { font-size: 16px; color: var(--text-light); max-width: 500px; line-height: 1.75; }

        .how-section { background: white; }
        .how-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
        .how-card { background: #fafafa; border: 1.5px solid var(--border); border-radius: 24px; padding: 36px 28px; position: relative; transition: all 0.3s; }
        .how-card:hover { border-color: var(--teal); background: var(--teal-light); box-shadow: 0 16px 48px rgba(15,118,110,0.1); transform: translateY(-6px); }
        .how-number { position: absolute; top: -18px; left: 32px; width: 36px; height: 36px; background: var(--teal); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 900; box-shadow: 0 4px 12px rgba(15,118,110,0.4); }
        .how-icon { width: 56px; height: 56px; background: var(--teal-light); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; border: 1.5px solid rgba(15,118,110,0.15); }
        .how-icon svg { width: 28px; height: 28px; color: var(--teal); }
        .how-card h3 { font-size: 18px; font-weight: 800; margin-bottom: 12px; color: var(--text); }
        .how-card p { font-size: 14px; color: var(--text-light); line-height: 1.7; }

        .features-section { background: var(--teal-light); }
        .features-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .feature-card { background: white; border-radius: 20px; padding: 30px; display: flex; gap: 20px; border: 1.5px solid var(--border); transition: all 0.25s; position: relative; overflow: hidden; }
        .feature-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 0; background: var(--teal); transition: height 0.3s; }
        .feature-card:hover { border-color: rgba(15,118,110,0.3); box-shadow: 0 8px 32px rgba(15,118,110,0.08); transform: translateY(-2px); }
        .feature-card:hover::before { height: 100%; }
        .feature-icon { width: 52px; height: 52px; border-radius: 14px; background: var(--teal-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1.5px solid rgba(15,118,110,0.15); }
        .feature-icon svg { width: 24px; height: 24px; color: var(--teal); }
        .feature-card h3 { font-size: 16px; font-weight: 800; margin-bottom: 8px; color: var(--text); }
        .feature-card p { font-size: 14px; color: var(--text-light); line-height: 1.65; }

        /* ── TARIFS ── */
        .pricing-preview { background: white; }
        .pricing-preview-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; max-width: 900px; margin: 0 auto; }
        .pp-card { background: #fafafa; border: 2px solid var(--border); border-radius: 20px; padding: 28px 24px; display: flex; flex-direction: column; gap: 16px; position: relative; transition: all 0.2s; }
        .pp-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.07); }
        .pp-card.pp-popular { border-color: var(--teal); background: white; box-shadow: 0 8px 28px rgba(15,118,110,0.12); }
        .pp-badge { position: absolute; top: -13px; left: 50%; transform: translateX(-50%); background: var(--teal); color: white; font-size: 11px; font-weight: 800; padding: 3px 12px; border-radius: 20px; white-space: nowrap; }
        .pp-name { font-size: 12px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.08em; }
        .pp-price { font-size: 26px; font-weight: 900; color: var(--text); }
        .pp-price span { font-size: 13px; font-weight: 500; color: var(--text-light); }
        .pp-features { display: flex; flex-direction: column; gap: 7px; flex: 1; }
        .pp-features span { font-size: 13px; color: var(--text); }
        .pp-features span::before { content: '✓ '; color: var(--teal); font-weight: 700; }
        .pp-btn { display: block; width: 100%; padding: 11px; border-radius: 10px; font-size: 14px; font-weight: 700; text-align: center; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; font-family: inherit; }
        .pp-btn-outline { background: white; color: var(--teal); border: 2px solid var(--teal); }
        .pp-btn-outline:hover { background: var(--teal-light); }
        .pp-btn-filled { background: var(--teal); color: white; }
        .pp-btn-filled:hover { background: var(--teal-dark); }
        .pp-voir-tout { display: inline-flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: var(--teal); text-decoration: none; border-bottom: 2px solid transparent; transition: border-color 0.2s; }
        .pp-voir-tout:hover { border-color: var(--teal); }

        /* ── CTA ── */
        .cta-section { background: #0F766E; text-align: center; position: relative; overflow: hidden; }
        .cta-section::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 28px 28px; }
        .cta-orb-1 { position: absolute; width: 400px; height: 400px; border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,0.07), transparent 70%); top: -150px; left: -100px; }
        .cta-orb-2 { position: absolute; width: 300px; height: 300px; border-radius: 50%; background: radial-gradient(circle, rgba(252,209,22,0.1), transparent 70%); bottom: -100px; right: -80px; }
        .cta-content { position: relative; z-index: 1; }
        .cta-section h2 { font-size: 44px; font-weight: 900; color: white; margin-bottom: 16px; letter-spacing: -0.5px; }
        .cta-section p { font-size: 17px; color: rgba(255,255,255,0.75); margin-bottom: 40px; }
        .cta-actions { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .btn-cta-primary { padding: 15px 36px; background: white; color: #0F766E; border-radius: 12px; border: none; font-size: 15px; font-weight: 800; cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .btn-cta-primary:hover { background: var(--teal-light); transform: translateY(-2px); }
        .btn-cta-primary svg { width: 18px; height: 18px; }
        .btn-cta-secondary { padding: 15px 36px; background: rgba(255,255,255,0.12); color: white; border-radius: 12px; border: 1.5px solid rgba(255,255,255,0.35); font-size: 15px; font-weight: 600; cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
        .btn-cta-secondary:hover { background: rgba(255,255,255,0.2); transform: translateY(-2px); }
        .btn-cta-secondary svg { width: 18px; height: 18px; }

        footer { background: #053d38; padding: 36px 60px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 16px; }
        .footer-logo { display: flex; align-items: center; gap: 14px; }
        .footer-logo img { width: 56px; height: 56px; object-fit: contain; }
        .footer-logo span { font-size: 18px; font-weight: 900; color: white; }
        .footer-copy { font-size: 13px; color: rgba(255,255,255,0.35); }
        .footer-links { display: flex; gap: 28px; flex-wrap: wrap; }
        .footer-links a { font-size: 13px; color: rgba(255,255,255,0.45); text-decoration: none; transition: color 0.2s; }
        .footer-links a:hover { color: white; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(28px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up-2 { animation: fadeUp 0.7s ease 0.15s both; }
        .fade-up-3 { animation: fadeUp 0.7s ease 0.30s both; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            nav { padding: 0 32px; }
            .nav-links { gap: 16px; }
            .hero { padding: 100px 32px 40px; gap: 40px; }
            .hero h1 { font-size: 42px; }
            .hero-illustration { width: 340px; }
            .stats-band { padding: 36px 32px; }
            .band-stat-value { font-size: 36px; }
            section { padding: 70px 32px; }
            .section-title { font-size: 32px; }
            .how-grid { grid-template-columns: 1fr 1fr; gap: 24px; }
            .pricing-preview-grid { grid-template-columns: 1fr 1fr; }
            footer { padding: 28px 32px; }
        }
        @media (max-width: 768px) {
            nav { padding: 0 20px; height: 64px; }
            .nav-links { display: none; }
            .nav-actions { display: none; }
            .nav-hamburger { display: block; }
            .nav-mobile-menu { top: 64px; }
            .hero { flex-direction: column; padding: 84px 20px 40px; gap: 32px; text-align: center; min-height: auto; }
            .hero-left { max-width: 100%; }
            .cameroon-map { display: none; }
            .hero h1 { font-size: 32px; letter-spacing: -0.5px; }
            .hero p { font-size: 15px; margin-bottom: 28px; max-width: 100%; }
            .hero-actions { justify-content: center; flex-direction: column; gap: 12px; }
            .btn-hero-primary, .btn-hero-secondary { width: 100%; justify-content: center; }
            .hero-right { width: 100%; }
            .hero-illustration { width: 200px; margin-top: -10px; }
.hero-right { margin-top: -16px; }
            .stats-band { grid-template-columns: repeat(2, 1fr); padding: 28px 20px; gap: 0; }
            .band-stat { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.15); padding: 16px 8px; }
            .band-stat:nth-child(odd) { border-right: 1px solid rgba(255,255,255,0.15); }
            .band-stat:nth-child(3), .band-stat:nth-child(4) { border-bottom: none; }
            .band-stat-value { font-size: 30px; }
            section { padding: 50px 20px; }
            .section-title { font-size: 26px; }
            .section-sub { font-size: 14px; }
            .how-grid { grid-template-columns: 1fr; gap: 28px; }
            .features-grid { grid-template-columns: 1fr; }
            .feature-card { padding: 20px; }
            .pricing-preview-grid { grid-template-columns: 1fr; gap: 20px; }
            .pp-card.pp-popular { margin-top: 8px; }
            .cta-section { padding: 60px 20px !important; }
            .cta-section h2 { font-size: 22px; }
            .cta-section p { font-size: 15px; }
            .cta-actions { flex-direction: column; align-items: center; }
            .btn-cta-primary, .btn-cta-secondary { width: auto; padding: 12px 24px; font-size: 14px; min-width: 260px; justify-content: center; }
            footer { padding: 24px 20px; flex-direction: column; align-items: flex-start; gap: 12px; }
            .footer-links { gap: 16px; }
            .btn-cta-primary, .btn-cta-secondary { 
    width: auto; 
    padding: 12px 24px; 
    font-size: 14px; 
}
.cta-actions { 
    gap: 10px; 
}
        }
        @media (max-width: 480px) {
            .hero h1 { font-size: 26px; }
            .hero-illustration { width: 160px; }
            .hero-right { margin-top: -16px; }
            .band-stat-value { font-size: 26px; }
            .section-title { font-size: 22px; }
            .cta-section h2 { font-size: 24px; }
        }
    </style>
</head>
<body>
@php $locale = app()->getLocale(); @endphp

<nav>
    <a href="/" class="nav-logo">
        <img src="{{ asset('images/logo.png') }}" alt="VeriScan Logo">
        <span class="nav-logo-text">Veri<span>Scan</span></span>
    </a>
    <div class="nav-links">
        <a href="#comment">{{ $locale === 'en' ? 'How it works' : 'Comment ça marche' }}</a>
        <a href="#fonctionnalites">{{ $locale === 'en' ? 'Features' : 'Fonctionnalités' }}</a>
        <a href="#tarifs">{{ $locale === 'en' ? 'Pricing' : 'Tarifs' }}</a>
    </div>
    <div class="nav-actions">
        <div class="lang-switch">
            <a href="{{ route('langue.changer', 'fr') }}" class="lang-btn {{ $locale === 'fr' ? 'active' : '' }}">FR</a>
            <a href="{{ route('langue.changer', 'en') }}" class="lang-btn {{ $locale === 'en' ? 'active' : '' }}">EN</a>
        </div>
        <a href="{{ route('verify.home') }}" class="btn-nav btn-nav-teal">✓ {{ $locale === 'en' ? 'Verify' : 'Vérifier' }}</a>
        <a href="{{ route('fabricant.login') }}" class="btn-nav btn-nav-outline">{{ $locale === 'en' ? 'Log in' : 'Connexion' }}</a>
        <a href="{{ route('fabricant.register') }}" class="btn-nav btn-nav-solid">{{ $locale === 'en' ? 'Sign up' : "S'inscrire" }}</a>
    </div>
    <button class="nav-hamburger" onclick="toggleMobileMenu()" aria-label="Menu">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
</nav>

<div class="nav-mobile-menu" id="mobileMenu">
    <a href="#comment" onclick="toggleMobileMenu()">{{ $locale === 'en' ? 'How it works' : 'Comment ça marche' }}</a>
    <a href="#fonctionnalites" onclick="toggleMobileMenu()">{{ $locale === 'en' ? 'Features' : 'Fonctionnalités' }}</a>
    <a href="#tarifs" onclick="toggleMobileMenu()">{{ $locale === 'en' ? 'Pricing' : 'Tarifs' }}</a>
    <a href="{{ route('verify.home') }}" style="color:var(--teal);">✓ {{ $locale === 'en' ? 'Verify a product' : 'Vérifier un produit' }}</a>
    <a href="{{ route('fabricant.login') }}">{{ $locale === 'en' ? 'Log in' : 'Se connecter' }}</a>
    <a href="{{ route('fabricant.register') }}" style="color:var(--teal);font-weight:700;">{{ $locale === 'en' ? 'Sign up free' : "S'inscrire gratuitement" }}</a>
    <div class="nav-mobile-lang">
        <a href="{{ route('langue.changer', 'fr') }}" style="{{ $locale === 'fr' ? 'background:var(--teal);color:white;border-color:var(--teal);' : 'color:var(--text-light);' }}">FR</a>
        <a href="{{ route('langue.changer', 'en') }}" style="{{ $locale === 'en' ? 'background:var(--teal);color:white;border-color:var(--teal);' : 'color:var(--text-light);' }}">EN</a>
    </div>
</div>

<section class="hero">
    <div class="hero-left">
        <img src="{{ asset('images/cameroun.svg') }}" class="cameroon-map" alt="">
        <div class="fade-up-2" style="display:inline-flex;align-items:center;gap:7px;background:white;border:1.5px solid rgba(15,118,110,0.2);border-radius:20px;padding:5px 14px;font-size:12px;font-weight:700;color:var(--teal);margin-bottom:18px;box-shadow:0 2px 8px rgba(15,118,110,0.08);">
            <span style="width:7px;height:7px;border-radius:50%;background:var(--teal);display:inline-block;"></span>
            {{ $locale === 'en' ? 'HMAC-SHA256 Certified · Cameroon' : 'Certifié HMAC-SHA256 · Cameroun' }}
        </div>
        <h1 class="fade-up-2">
            @if($locale === 'en') Fraud <span>stops here.</span>
            @else La fraude<br>s'arrête <span>ici.</span>
            @endif
        </h1>
        <p class="fade-up-2">{{ $locale === 'en' ? 'VeriScan protects your products against counterfeiting with HMAC-SHA256 secured QR codes and real-time traceability across Cameroon.' : 'VeriScan protège vos produits contre la contrefaçon grâce à des QR codes sécurisés HMAC-SHA256 et une traçabilité en temps réel sur tout le territoire camerounais.' }}</p>
        <div class="hero-actions fade-up-3">
            <a href="{{ route('fabricant.register') }}" class="btn-hero-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ $locale === 'en' ? 'Create my account' : 'Créer mon compte' }}
            </a>
            <a href="#comment" class="btn-hero-secondary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $locale === 'en' ? 'How it works' : 'Comment ça marche' }}
            </a>
        </div>
    </div>
    <div class="hero-right">
        <img src="{{ asset('images/scan0.png') }}" alt="Scan QR Code VeriScan" class="hero-illustration">
    </div>
</section>

<div class="stats-band">
    <div class="band-stat"><div class="band-stat-value">47</div><div class="band-stat-label">{{ $locale === 'en' ? 'Verified products' : 'Produits vérifiés' }}</div></div>
    <div class="band-stat"><div class="band-stat-value">12</div><div class="band-stat-label">{{ $locale === 'en' ? 'Registered manufacturers' : 'Fabricants inscrits' }}</div></div>
    <div class="band-stat"><div class="band-stat-value">3</div><div class="band-stat-label">{{ $locale === 'en' ? 'Counterfeits detected' : 'Contrefaçons détectées' }}</div></div>
    <div class="band-stat"><div class="band-stat-value">98<span class="accent">%</span></div><div class="band-stat-label">{{ $locale === 'en' ? 'Detection reliability' : 'Fiabilité de détection' }}</div></div>
</div>

<section id="comment" class="how-section">
    <div class="section-header">
        <div class="section-badge">― {{ $locale === 'en' ? 'How it works' : 'Comment ça marche' }}</div>
        <div class="section-title">{{ $locale === 'en' ? 'Simple. Fast.' : 'Simple. Rapide.' }}<br><span>{{ $locale === 'en' ? 'Unfalsifiable.' : 'Infalsifiable.' }}</span></div>
        <div class="section-sub">{{ $locale === 'en' ? 'In three steps, secure your products and give your customers the ability to verify their authenticity instantly.' : 'En trois étapes, sécurisez vos produits et donnez à vos clients les moyens de vérifier leur authenticité instantanément.' }}</div>
    </div>
    <div class="how-grid">
        <div class="how-card"><div class="how-number">1</div><div class="how-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div><h3>{{ $locale === 'en' ? 'Register your products' : 'Enregistrez vos produits' }}</h3><p>{{ $locale === 'en' ? 'Create your manufacturer account, add your products and define your production batches in a few clicks.' : 'Créez votre compte fabricant, ajoutez vos produits et définissez vos lots de production en quelques clics depuis le dashboard web.' }}</p></div>
        <div class="how-card"><div class="how-number">2</div><div class="how-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg></div><h3>{{ $locale === 'en' ? 'Generate QR codes' : 'Générez les QR codes' }}</h3><p>{{ $locale === 'en' ? 'VeriScan automatically generates unique HMAC-SHA256 signed QR codes for each unit. Print and stick them on your products.' : 'VeriScan génère automatiquement des QR codes uniques signés HMAC-SHA256 pour chaque unité de votre lot. Imprimez et collez-les sur vos produits.' }}</p></div>
        <div class="how-card"><div class="how-number">3</div><div class="how-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><h3>{{ $locale === 'en' ? 'Consumers verify' : 'Les consommateurs vérifient' }}</h3><p>{{ $locale === 'en' ? 'Scan the QR code or enter the code manually on VeriScan and instantly get the result: Authentic, Suspect or Counterfeit.' : 'Scannez le QR code ou saisissez le code manuellement sur VeriScan et obtenez instantanément le résultat : Authentique, Suspect ou Contrefait.' }}</p></div>
    </div>
</section>

<section id="fonctionnalites" class="features-section">
    <div class="section-header">
        <div class="section-badge">― {{ $locale === 'en' ? 'Features' : 'Fonctionnalités' }}</div>
        <div class="section-title">{{ $locale === 'en' ? 'Everything you need to' : 'Tout ce dont vous avez besoin' }}<br>{{ $locale === 'en' ? '' : 'pour ' }}<span>{{ $locale === 'en' ? 'protect your products' : 'protéger vos produits' }}</span></div>
    </div>
    <div class="features-grid">
        <div class="feature-card"><div class="feature-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div><div><h3>{{ $locale === 'en' ? 'HMAC-SHA256 QR Codes' : 'QR Codes HMAC-SHA256' }}</h3><p>{{ $locale === 'en' ? 'Each QR code is cryptographically signed, making any falsification technically impossible.' : 'Chaque QR code est signé cryptographiquement, rendant toute falsification techniquement impossible.' }}</p></div></div>
        <div class="feature-card"><div class="feature-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div><div><h3>{{ $locale === 'en' ? 'Real-time statistics' : 'Statistiques en temps réel' }}</h3><p>{{ $locale === 'en' ? 'Track all scans, detect anomalies and analyze the geographic distribution of your products.' : 'Suivez tous les scans, détectez les anomalies et analysez la distribution géographique de vos produits.' }}</p></div></div>
        <div class="feature-card"><div class="feature-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div><div><h3>{{ $locale === 'en' ? 'Instant verification' : 'Vérification instantanée' }}</h3><p>{{ $locale === 'en' ? 'Scan the QR code or enter the product code. Immediate result: Authentic, Suspect or Counterfeit.' : 'Scannez le QR code ou saisissez le code produit. Résultat immédiat : Authentique, Suspect ou Contrefait.' }}</p></div></div>
        <div class="feature-card"><div class="feature-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div><div><h3>{{ $locale === 'en' ? 'Reporting system' : 'Système de signalement' }}</h3><p>{{ $locale === 'en' ? 'Consumers report a suspicious product directly from the verification page. You are alerted immediately.' : 'Les consommateurs signalent un produit suspect directement depuis la page de vérification. Vous êtes alerté immédiatement.' }}</p></div></div>
        <div class="feature-card"><div class="feature-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div><div><h3>{{ $locale === 'en' ? 'Exportable PDF reports' : 'Rapports exportables PDF' }}</h3><p>{{ $locale === 'en' ? 'Generate detailed reports for internal audits and official certifications.' : 'Générez des rapports détaillés de vos produits, lots et scans pour vos audits internes et certifications officielles.' }}</p></div></div>
        <div class="feature-card"><div class="feature-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div><h3>{{ $locale === 'en' ? 'AI anti-fraud scoring' : 'Scoring IA anti-fraude' }}</h3><p>{{ $locale === 'en' ? 'An intelligent algorithm analyzes scan patterns and automatically assigns a risk score to each product.' : 'Un algorithme intelligent analyse les patterns de scan et attribue un score de risque à chaque produit automatiquement.' }}</p></div></div>
    </div>
</section>

{{-- SECTION TARIFS --}}
<section id="tarifs" class="pricing-preview">
    <div class="section-header">
        <div class="section-badge">― {{ $locale === 'en' ? 'Pricing' : 'Tarifs' }}</div>
        <div class="section-title">{{ $locale === 'en' ? 'Prices for' : 'Des prix pour' }} <span>{{ $locale === 'en' ? 'every budget' : 'tous les budgets' }}</span></div>
        <div class="section-sub">{{ $locale === 'en' ? 'Start free, grow as you need. No credit card required.' : 'Commencez gratuitement, évoluez selon vos besoins. Aucune carte bancaire requise.' }}</div>
    </div>
    <div class="pricing-preview-grid">
        <div class="pp-card">
            <div class="pp-name">{{ $locale === 'en' ? 'Free' : 'Gratuit' }}</div>
            <div class="pp-price">0 <span>FCFA/{{ $locale === 'en' ? 'month' : 'mois' }}</span></div>
            <div class="pp-features">
                <span>1 {{ $locale === 'en' ? 'product' : 'produit' }}</span>
                <span>50 QR codes/{{ $locale === 'en' ? 'month' : 'mois' }}</span>
                <span>{{ $locale === 'en' ? 'Basic statistics' : 'Statistiques basiques' }}</span>
            </div>
            <a href="{{ route('fabricant.register') }}" class="pp-btn pp-btn-outline">{{ $locale === 'en' ? 'Get started' : 'Commencer' }}</a>
        </div>
        <div class="pp-card pp-popular">
            <div class="pp-badge">★ {{ $locale === 'en' ? 'POPULAR' : 'POPULAIRE' }}</div>
            <div class="pp-name">Pro</div>
            <div class="pp-price">15 000 <span>FCFA/{{ $locale === 'en' ? 'month' : 'mois' }}</span></div>
            <div class="pp-features">
                <span>{{ $locale === 'en' ? 'Unlimited products' : 'Produits illimités' }}</span>
                <span>{{ $locale === 'en' ? 'Unlimited QR codes' : 'QR codes illimités' }}</span>
                <span>{{ $locale === 'en' ? 'PDF reports' : 'Rapports PDF' }}</span>
                <span>{{ $locale === 'en' ? 'Risk map' : 'Carte des risques' }}</span>
            </div>
            <a href="{{ route('tarifs') }}" class="pp-btn pp-btn-filled">{{ $locale === 'en' ? 'Subscribe' : 'Souscrire' }}</a>
        </div>
        <div class="pp-card">
            <div class="pp-name">{{ $locale === 'en' ? 'Enterprise' : 'Entreprise' }}</div>
            <div class="pp-price">50 000 <span>FCFA/{{ $locale === 'en' ? 'month' : 'mois' }}</span></div>
            <div class="pp-features">
                <span>{{ $locale === 'en' ? 'Everything in Pro' : 'Tout le plan Pro' }}</span>
                <span>{{ $locale === 'en' ? 'Dedicated API' : 'API dédiée' }}</span>
                <span>{{ $locale === 'en' ? 'Account manager' : 'Account manager' }}</span>
            </div>
            <a href="{{ route('tarifs') }}" class="pp-btn pp-btn-outline">{{ $locale === 'en' ? 'Contact us' : 'Souscrire plan enterprise ' }}</a>
        </div>
    </div>
    <div style="text-align:center;margin-top:32px;">
        <a href="{{ route('tarifs') }}" class="pp-voir-tout">{{ $locale === 'en' ? 'See all plans →' : 'Voir tous les plans →' }}</a>
    </div>
</section>

<section class="cta-section" id="a-propos">
    <div class="cta-orb-1"></div>
    <div class="cta-orb-2"></div>
    <div class="cta-content">
        <h2>{{ $locale === 'en' ? 'Ready to protect your products?' : 'Prêt à protéger vos produits ?' }}</h2>
        <p>{{ $locale === 'en' ? 'Join the Cameroonian manufacturers who trust VeriScan.' : 'Rejoignez les fabricants camerounais qui font confiance à VeriScan.' }}</p>
        <div class="cta-actions">
            <a href="{{ route('fabricant.register') }}" class="btn-cta-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ $locale === 'en' ? 'Create my free account' : 'Créer mon compte gratuitement' }}
            </a>
            <a href="{{ route('fabricant.login') }}" class="btn-cta-secondary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                {{ $locale === 'en' ? 'I already have an account' : "J'ai déjà un compte" }}
            </a>
        </div>
    </div>
</section>

<footer>
    <div class="footer-logo">
        <img src="{{ asset('images/logo.png') }}" alt="VeriScan">
        <span>VeriScan</span>
    </div>
    <div class="footer-copy">© 2026 VeriScan · {{ $locale === 'en' ? 'Cameroon' : 'Cameroun' }}. {{ $locale === 'en' ? 'All rights reserved.' : 'Tous droits réservés.' }}</div>
    <div class="footer-links">
        <a href="#">{{ $locale === 'en' ? 'Privacy' : 'Confidentialité' }}</a>
        <a href="#">{{ $locale === 'en' ? 'Terms' : 'CGU' }}</a>
        <a href="{{ route('tarifs') }}">{{ $locale === 'en' ? 'Pricing' : 'Tarifs' }}</a>
        <a href="{{ route('verify.home') }}">{{ $locale === 'en' ? 'Verify a product' : 'Vérifier un produit' }}</a>
        <a href="#">Contact</a>
    </div>
</footer>

<script>
function toggleMobileMenu() {
    document.getElementById('mobileMenu').classList.toggle('open');
}
</script>
</body>
</html>