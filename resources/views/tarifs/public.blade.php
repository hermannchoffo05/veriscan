<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – {{ $locale === 'en' ? 'Pricing' : 'Tarifs' }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --teal: #2E3A6B; --teal-dark: #212a52; --teal-light: #EEF0F8; --yellow: #F5A623; --yellow-dark: #e0961d; --text: #1F2937; --text-light: #6b7280; --border: #e5e7eb; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: var(--text); background: #f8fafc; overflow-x: hidden; }

        nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; background: rgba(255,255,255,0.96); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0,0,0,0.07); padding: 0 60px; height: 72px; display: flex; align-items: center; justify-content: space-between; }
        .nav-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .nav-logo img { width: 48px; height: 48px; object-fit: contain; }
        .nav-logo-text { font-size: 22px; font-weight: 900; color: var(--text); }
        .nav-logo-text span { color: var(--teal); }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .btn-ghost { padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; color: var(--teal); background: none; border: 1.5px solid var(--teal); text-decoration: none; transition: all 0.2s; }
        .btn-ghost:hover { background: var(--teal-light); }
        .btn-solid { padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; color: white; background: var(--teal); border: none; text-decoration: none; transition: all 0.2s; }
        .btn-solid:hover { background: var(--teal-dark); }
        .nav-hamburger { display: none; background: none; border: none; cursor: pointer; color: var(--text); }
        .nav-hamburger svg { width: 24px; height: 24px; }
        .nav-mobile-menu { display: none; position: fixed; top: 72px; left: 0; right: 0; background: white; border-bottom: 1px solid var(--border); padding: 16px 24px; flex-direction: column; gap: 0; z-index: 99; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .nav-mobile-menu.open { display: flex; }
        .nav-mobile-menu a { font-size: 15px; font-weight: 600; color: var(--text); text-decoration: none; padding: 12px 0; border-bottom: 1px solid var(--border); }
        .nav-mobile-menu a:last-child { border-bottom: none; }

        .page-hero { padding: 110px 60px 56px; text-align: center; background: linear-gradient(135deg, #171B3D 0%, #2E3A6B 60%, #4A5899 100%); position: relative; overflow: hidden; }
        .page-hero::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 24px 24px; }
        .page-hero-content { position: relative; z-index: 1; max-width: 640px; margin: 0 auto; }
        .page-hero-badge { display: inline-flex; align-items: center; gap: 7px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 5px 14px; font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.9); margin-bottom: 20px; }
        .page-hero h1 { font-size: 42px; font-weight: 900; color: white; line-height: 1.1; margin-bottom: 16px; }
        .page-hero h1 span { color: var(--yellow); }
        .page-hero p { font-size: 16px; color: rgba(255,255,255,0.75); line-height: 1.7; }

        .lang-bar { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 60px 0; }
        .lang-btn { padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; border: 1.5px solid var(--border); color: var(--text-light); transition: all 0.2s; }
        .lang-btn.active { background: var(--teal); color: white; border-color: var(--teal); }

        footer { background: #171B3D; padding: 24px 60px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        footer p { font-size: 13px; color: rgba(255,255,255,0.5); }
        .footer-links { display: flex; gap: 20px; flex-wrap: wrap; }
        .footer-links a { font-size: 13px; color: rgba(255,255,255,0.6); text-decoration: none; }
        .footer-links a:hover { color: white; }

        @media (max-width: 768px) {
            nav { padding: 0 20px; height: 64px; }
            .nav-actions .btn-ghost, .nav-actions .btn-solid { display: none; }
            .nav-hamburger { display: block; }
            .nav-mobile-menu { top: 64px; }
            .lang-bar { padding: 12px 20px 0; }
            .page-hero { padding: 88px 20px 44px; }
            .page-hero h1 { font-size: 28px; }
            footer { padding: 20px; flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
@php
    $fabricantConnecte = (bool) $fabricant;
@endphp

<nav>
    <a href="{{ $fabricantConnecte ? route('fabricant.dashboard') : url('/') }}" class="nav-logo">
        <img src="{{ asset('images/logo.png') }}" alt="VeriScan">
        <span class="nav-logo-text">Veri<span>Scan</span></span>
    </a>
    <div class="nav-actions">
        @if($fabricantConnecte)
            <a href="{{ route('fabricant.dashboard') }}" class="btn-ghost">{{ $locale === 'en' ? 'My dashboard' : 'Mon tableau de bord' }}</a>
            <form method="POST" action="{{ route('fabricant.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-solid" style="cursor:pointer;">{{ $locale === 'en' ? 'Log out' : 'Se déconnecter' }}</button>
            </form>
        @else
            <a href="{{ url('/') }}" class="btn-ghost">{{ $locale === 'en' ? 'Home' : 'Accueil' }}</a>
            <a href="{{ route('fabricant.login') }}" class="btn-ghost">{{ $locale === 'en' ? 'Log in' : 'Se connecter' }}</a>
            <a href="{{ route('fabricant.register') }}" class="btn-solid">{{ $locale === 'en' ? 'Sign up' : "S'inscrire" }}</a>
        @endif
    </div>
    <button class="nav-hamburger" onclick="document.getElementById('mobileMenu').classList.toggle('open')">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
</nav>

<div class="nav-mobile-menu" id="mobileMenu">
    @if($fabricantConnecte)
        <a href="{{ route('fabricant.dashboard') }}">{{ $locale === 'en' ? 'My dashboard' : 'Mon tableau de bord' }}</a>
        <form method="POST" action="{{ route('fabricant.logout') }}">
            @csrf
            <button type="submit" style="background:none;border:none;font:inherit;color:var(--teal);font-weight:700;padding:12px 0;cursor:pointer;">{{ $locale === 'en' ? 'Log out' : 'Se déconnecter' }}</button>
        </form>
    @else
        <a href="{{ url('/') }}">{{ $locale === 'en' ? 'Home' : 'Accueil' }}</a>
        <a href="{{ route('verify.home') }}" style="color:var(--teal);">✓ {{ $locale === 'en' ? 'Verify a product' : 'Vérifier un produit' }}</a>
        <a href="{{ route('fabricant.login') }}">{{ $locale === 'en' ? 'Log in' : 'Se connecter' }}</a>
        <a href="{{ route('fabricant.register') }}" style="color:var(--teal);font-weight:700;">{{ $locale === 'en' ? 'Sign up free' : "S'inscrire gratuitement" }}</a>
    @endif
</div>

<div class="lang-bar">
    <a href="{{ route('langue.changer', 'fr') }}" class="lang-btn {{ $locale === 'fr' ? 'active' : '' }}">FR</a>
    <a href="{{ route('langue.changer', 'en') }}" class="lang-btn {{ $locale === 'en' ? 'active' : '' }}">EN</a>
</div>

<div class="page-hero">
    <div class="page-hero-content">
        <div class="page-hero-badge">● {{ $locale === 'en' ? 'Transparent pricing · No surprises' : 'Tarifs transparents · Sans surprise' }}</div>
        <h1>{{ $locale === 'en' ? 'Prices adapted to' : 'Des tarifs adaptés à' }} <span>{{ $locale === 'en' ? 'every manufacturer' : 'chaque fabricant' }}</span></h1>
        <p>{{ $locale === 'en' ? 'Start for free and scale as needed. No credit card required to get started.' : 'Commencez gratuitement et évoluez selon vos besoins. Aucune carte bancaire requise pour démarrer.' }}</p>
    </div>
</div>

@include('tarifs._plans', ['locale' => $locale, 'fabricant' => $fabricant, 'planActif' => $planActif])

<footer>
    <p><strong style="color:rgba(255,255,255,0.8)">VeriScan</strong> · {{ $locale === 'en' ? 'Fraud stops here' : "La fraude s'arrête ici" }} · Cameroun © 2026</p>
    <div class="footer-links">
        <a href="{{ url('/') }}">{{ $locale === 'en' ? 'Home' : 'Accueil' }}</a>
        <a href="{{ route('verify.home') }}">{{ $locale === 'en' ? 'Verify a product' : 'Vérifier un produit' }}</a>
        @if(!$fabricantConnecte)
            <a href="{{ route('fabricant.register') }}">{{ $locale === 'en' ? 'Sign up' : "S'inscrire" }}</a>
        @endif
    </div>
</footer>
</body>
</html>