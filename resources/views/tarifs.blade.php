<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – {{ app()->getLocale() === 'en' ? 'Pricing' : 'Tarifs' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --teal: #0F766E; --teal-dark: #0a5c55; --teal-light: #F0FDFA; --text: #1F2937; --text-light: #6b7280; --border: #e5e7eb; }
        body { font-family: 'Inter', sans-serif; color: var(--text); background: #f8fafc; overflow-x: hidden; }

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

        .page-hero { padding: 110px 60px 56px; text-align: center; background: linear-gradient(135deg, #042f2e 0%, #0f766e 60%, #0d9488 100%); position: relative; overflow: hidden; }
        .page-hero::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 24px 24px; }
        .page-hero-content { position: relative; z-index: 1; max-width: 640px; margin: 0 auto; }
        .page-hero-badge { display: inline-flex; align-items: center; gap: 7px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 5px 14px; font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.9); margin-bottom: 20px; }
        .page-hero h1 { font-size: 42px; font-weight: 900; color: white; line-height: 1.1; margin-bottom: 16px; }
        .page-hero h1 span { color: #5eead4; }
        .page-hero p { font-size: 16px; color: rgba(255,255,255,0.75); line-height: 1.7; }

        .lang-bar { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 60px 0; }
        .lang-btn { padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; border: 1.5px solid var(--border); color: var(--text-light); transition: all 0.2s; }
        .lang-btn.active { background: var(--teal); color: white; border-color: var(--teal); }

        .pricing-section { max-width: 1100px; margin: 0 auto; padding: 56px 24px 72px; }
        .pricing-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .plan-card { background: white; border-radius: 20px; border: 2px solid var(--border); padding: 28px 22px; display: flex; flex-direction: column; gap: 18px; position: relative; transition: transform 0.2s, box-shadow 0.2s; }
        .plan-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,0.08); }
        .plan-card.popular { border-color: var(--teal); box-shadow: 0 8px 32px rgba(15,118,110,0.15); }
        .popular-badge { position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: var(--teal); color: white; font-size: 11px; font-weight: 800; padding: 4px 14px; border-radius: 20px; white-space: nowrap; letter-spacing: 0.05em; }
        .plan-name { font-size: 12px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.08em; }
        .plan-price { display: flex; align-items: baseline; gap: 4px; flex-wrap: wrap; }
        .plan-price .amount { font-size: 30px; font-weight: 900; color: var(--text); }
        .plan-price .currency { font-size: 13px; font-weight: 600; color: var(--text-light); }
        .plan-price .period { font-size: 12px; color: var(--text-light); }
        .plan-free .amount { color: var(--teal); }
        .plan-desc { font-size: 13px; color: var(--text-light); line-height: 1.6; border-top: 1px solid var(--border); padding-top: 14px; }
        .plan-features { display: flex; flex-direction: column; gap: 9px; flex: 1; }
        .plan-feature { display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: var(--text); }
        .plan-feature svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }
        .plan-feature.included svg { color: var(--teal); }
        .plan-feature.excluded { color: #9ca3af; }
        .plan-feature.excluded svg { color: #d1d5db; }

        /* ── TOUS LES BOUTONS EN VERT ── */
        .btn-plan { display: block; width: 100%; padding: 12px; border-radius: 12px; font-size: 14px; font-weight: 700; text-align: center; cursor: pointer; text-decoration: none; transition: all 0.2s; border: none; font-family: inherit; background: var(--teal); color: white; }
        .btn-plan:hover { background: var(--teal-dark); transform: translateY(-1px); }
        /* Plan gratuit : outline vert */
        .btn-plan.outline { background: white; color: var(--teal); border: 2px solid var(--teal); }
        .btn-plan.outline:hover { background: var(--teal-light); transform: translateY(-1px); }

        .faq-section { max-width: 720px; margin: 0 auto; padding: 0 24px 72px; }
        .faq-title { font-size: 28px; font-weight: 800; color: var(--text); text-align: center; margin-bottom: 36px; }
        .faq-item { border: 1.5px solid var(--border); border-radius: 14px; margin-bottom: 10px; overflow: hidden; background: white; }
        .faq-question { width: 100%; text-align: left; padding: 16px 20px; background: white; border: none; font-size: 14px; font-weight: 600; color: var(--text); cursor: pointer; font-family: inherit; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .faq-question svg { width: 18px; height: 18px; color: var(--teal); flex-shrink: 0; transition: transform 0.2s; }
        .faq-question.open svg { transform: rotate(180deg); }
        .faq-answer { display: none; padding: 0 20px 16px; font-size: 14px; color: var(--text-light); line-height: 1.8; }
        .faq-answer.open { display: block; }

        footer { background: #042f2e; padding: 24px 60px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        footer p { font-size: 13px; color: rgba(255,255,255,0.5); }
        .footer-links { display: flex; gap: 20px; flex-wrap: wrap; }
        .footer-links a { font-size: 13px; color: rgba(255,255,255,0.6); text-decoration: none; }
        .footer-links a:hover { color: white; }

        @media (max-width: 1024px) { .pricing-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; } }
        @media (max-width: 768px) {
            nav { padding: 0 20px; height: 64px; }
            .nav-actions .btn-ghost, .nav-actions .btn-solid { display: none; }
            .nav-hamburger { display: block; }
            .nav-mobile-menu { top: 64px; }
            .lang-bar { padding: 12px 20px 0; }
            .page-hero { padding: 88px 20px 44px; }
            .page-hero h1 { font-size: 28px; }
            .pricing-grid { grid-template-columns: 1fr; gap: 20px; }
            .plan-card.popular { margin-top: 8px; }
            .pricing-section { padding: 40px 16px 56px; }
            .faq-section { padding: 0 16px 56px; }
            footer { padding: 20px; flex-direction: column; align-items: flex-start; }
            .btn-plan { font-size: 12px; padding: 10px 6px; white-space: nowrap; }
        }
    </style>
</head>
<body>
@php $locale = app()->getLocale(); @endphp

<nav>
    <a href="{{ url('/') }}" class="nav-logo">
        <img src="{{ asset('images/logo.png') }}" alt="VeriScan">
        <span class="nav-logo-text">Veri<span>Scan</span></span>
    </a>
    <div class="nav-actions">
        <a href="{{ url('/') }}" class="btn-ghost">{{ $locale === 'en' ? 'Home' : 'Accueil' }}</a>
        <a href="{{ route('fabricant.login') }}" class="btn-ghost">{{ $locale === 'en' ? 'Log in' : 'Se connecter' }}</a>
        <a href="{{ route('fabricant.register') }}" class="btn-solid">{{ $locale === 'en' ? 'Sign up' : "S'inscrire" }}</a>
    </div>
    <button class="nav-hamburger" onclick="document.getElementById('mobileMenu').classList.toggle('open')">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
</nav>

<div class="nav-mobile-menu" id="mobileMenu">
    <a href="{{ url('/') }}">{{ $locale === 'en' ? 'Home' : 'Accueil' }}</a>
    <a href="{{ route('verify.home') }}" style="color:var(--teal);">✓ {{ $locale === 'en' ? 'Verify a product' : 'Vérifier un produit' }}</a>
    <a href="{{ route('fabricant.login') }}">{{ $locale === 'en' ? 'Log in' : 'Se connecter' }}</a>
    <a href="{{ route('fabricant.register') }}" style="color:var(--teal);font-weight:700;">{{ $locale === 'en' ? 'Sign up free' : "S'inscrire gratuitement" }}</a>
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

<div class="pricing-section">
    <div class="pricing-grid">

        {{-- Gratuit --}}
        <div class="plan-card plan-free">
            <div>
                <div class="plan-name">{{ $locale === 'en' ? 'Free' : 'Gratuit' }}</div>
                <div class="plan-price"><span class="amount">0</span><span class="currency">FCFA</span><span class="period">/{{ $locale === 'en' ? 'month' : 'mois' }}</span></div>
            </div>
            <div class="plan-desc">{{ $locale === 'en' ? 'For artisans and small manufacturers just getting started.' : 'Pour les artisans et petits fabricants qui démarrent.' }}</div>
            <div class="plan-features">
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? '1 registered product' : '1 produit enregistré' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? '50 QR codes / month' : '50 QR codes / mois' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'Basic statistics' : 'Statistiques basiques' }}</div>
                <div class="plan-feature excluded"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>{{ $locale === 'en' ? 'PDF reports' : 'Rapports PDF' }}</div>
                <div class="plan-feature excluded"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>{{ $locale === 'en' ? 'Risk map' : 'Carte des risques' }}</div>
                <div class="plan-feature excluded"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>{{ $locale === 'en' ? 'Priority support' : 'Support prioritaire' }}</div>
            </div>
            <a href="{{ route('fabricant.register') }}" class="btn-plan outline">{{ $locale === 'en' ? 'Start for free' : 'Commencer gratuitement' }}</a>
        </div>

        {{-- Starter --}}
        <div class="plan-card">
            <div>
                <div class="plan-name">Starter</div>
                <div class="plan-price"><span class="amount">5 000</span><span class="currency">FCFA</span><span class="period">/{{ $locale === 'en' ? 'month' : 'mois' }}</span></div>
            </div>
            <div class="plan-desc">{{ $locale === 'en' ? 'For SMEs wanting to secure multiple product lines.' : 'Pour les PME qui veulent sécuriser plusieurs gammes de produits.' }}</div>
            <div class="plan-features">
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? '5 registered products' : '5 produits enregistrés' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? '500 QR codes / month' : '500 QR codes / mois' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'Full statistics' : 'Statistiques complètes' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? '1 PDF report / month' : '1 rapport PDF / mois' }}</div>
                <div class="plan-feature excluded"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>{{ $locale === 'en' ? 'Risk map' : 'Carte des risques' }}</div>
                <div class="plan-feature excluded"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>{{ $locale === 'en' ? 'Priority support' : 'Support prioritaire' }}</div>
            </div>
            <a href="{{ route('paiement.checkout', 'starter') }}" class="btn-plan">{{ $locale === 'en' ? 'Subscribe' : 'Souscrire' }}</a>
        </div>

        {{-- Pro --}}
        <div class="plan-card popular">
            <div class="popular-badge">⭐ {{ $locale === 'en' ? 'POPULAR' : 'POPULAIRE' }}</div>
            <div>
                <div class="plan-name">Pro</div>
                <div class="plan-price"><span class="amount">15 000</span><span class="currency">FCFA</span><span class="period">/{{ $locale === 'en' ? 'month' : 'mois' }}</span></div>
            </div>
            <div class="plan-desc">{{ $locale === 'en' ? 'The ideal choice for companies wanting full control.' : 'Le choix idéal pour les entreprises qui veulent tout contrôler.' }}</div>
            <div class="plan-features">
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'Unlimited products' : 'Produits illimités' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'Unlimited QR codes' : 'QR codes illimités' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'Unlimited PDF reports' : 'Rapports PDF illimités' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'Risk map' : 'Carte des risques' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'AI anti-fraud scoring' : 'Scoring IA anti-fraude' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'Priority support' : 'Support prioritaire' }}</div>
            </div>
            <a href="{{ route('paiement.checkout', 'pro') }}" class="btn-plan">{{ $locale === 'en' ? 'Subscribe to Pro' : 'Souscrire au plan Pro' }}</a>
        </div>

        {{-- Entreprise --}}
        <div class="plan-card">
            <div>
                <div class="plan-name">{{ $locale === 'en' ? 'Enterprise' : 'Entreprise' }}</div>
                <div class="plan-price"><span class="amount">50 000</span><span class="currency">FCFA</span><span class="period">/{{ $locale === 'en' ? 'month' : 'mois' }}</span></div>
            </div>
            <div class="plan-desc">{{ $locale === 'en' ? 'For large companies with specific needs and high volume.' : 'Pour les grandes entreprises avec des besoins spécifiques et un volume élevé.' }}</div>
            <div class="plan-features">
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'All Pro features' : 'Tout le plan Pro' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'Dedicated API' : 'API dédiée' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'Custom integration' : 'Intégration personnalisée' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'Dedicated account manager' : 'Account manager dédié' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? '99.9% SLA guaranteed' : 'SLA garanti 99.9%' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? 'Team training included' : 'Formation équipe incluse' }}</div>
            </div>
            <a href="{{ route('paiement.checkout', 'entreprise') }}" class="btn-plan">{{ $locale === 'en' ? 'Subscribe — Enterprise' : 'Souscrire plan Entreprise' }}</a>
        </div>

    </div>
</div>

{{-- FAQ --}}
<div class="faq-section">
    <div class="faq-title">{{ $locale === 'en' ? 'Frequently asked questions' : 'Questions fréquentes' }}</div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">{{ $locale === 'en' ? 'Is the free plan really free?' : 'Est-ce que le plan gratuit est vraiment gratuit ?' }}<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg></button>
        <div class="faq-answer">{{ $locale === 'en' ? 'Yes, completely free and without time limit. No credit card required. You can register 1 product and generate up to 50 QR codes per month without paying anything.' : "Oui, totalement gratuit et sans limite de durée. Aucune carte bancaire requise. Vous pouvez enregistrer 1 produit et générer jusqu'à 50 QR codes par mois sans payer quoi que ce soit." }}</div>
    </div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">{{ $locale === 'en' ? 'How does payment work?' : 'Comment se passe le paiement ?' }}<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg></button>
        <div class="faq-answer">{{ $locale === 'en' ? 'Payment is made via MTN Mobile Money or Orange Money. Choose your operator, enter your number, and confirm the payment notification with your PIN. Activation is immediate.' : "Le paiement se fait via MTN Mobile Money ou Orange Money. Choisissez votre opérateur, entrez votre numéro, et confirmez la notification de paiement avec votre code PIN. L'activation est immédiate." }}</div>
    </div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">{{ $locale === 'en' ? 'Can I change my plan at any time?' : 'Puis-je changer de plan à tout moment ?' }}<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg></button>
        <div class="faq-answer">{{ $locale === 'en' ? 'Yes, you can upgrade or downgrade your plan at any time. The change takes effect at the beginning of the following month. No long-term commitment.' : 'Oui, vous pouvez upgrader ou downgrader votre plan à tout moment. Le changement prend effet au début du mois suivant. Aucun engagement longue durée.' }}</div>
    </div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">{{ $locale === 'en' ? 'Is my data secure?' : 'Mes données sont-elles sécurisées ?' }}<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg></button>
        <div class="faq-answer">{{ $locale === 'en' ? 'All data is encrypted and stored securely. QR codes use HMAC-SHA256 cryptography, making any forgery technically impossible.' : 'Toutes les données sont chiffrées et stockées de manière sécurisée. Les QR codes utilisent la cryptographie HMAC-SHA256, rendant toute falsification techniquement impossible.' }}</div>
    </div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">{{ $locale === 'en' ? 'Is there a trial period for paid plans?' : "Y a-t-il une période d'essai pour les plans payants ?" }}<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg></button>
        <div class="faq-answer">{{ $locale === 'en' ? 'Yes, we offer 30 days free trial for Starter and Pro plans. No payment required during this period.' : "Oui, nous offrons 30 jours d'essai gratuit pour les plans Starter et Pro. Aucun paiement requis pendant cette période." }}</div>
    </div>
</div>

<footer>
    <p><strong style="color:rgba(255,255,255,0.8)">VeriScan</strong> · {{ $locale === 'en' ? 'Fraud stops here' : "La fraude s'arrête ici" }} · Cameroun © 2026</p>
    <div class="footer-links">
        <a href="{{ url('/') }}">{{ $locale === 'en' ? 'Home' : 'Accueil' }}</a>
        <a href="{{ route('verify.home') }}">{{ $locale === 'en' ? 'Verify a product' : 'Vérifier un produit' }}</a>
        <a href="{{ route('fabricant.register') }}">{{ $locale === 'en' ? 'Sign up' : "S'inscrire" }}</a>
    </div>
</footer>

<script>
function toggleFaq(btn) {
    btn.classList.toggle('open');
    btn.nextElementSibling.classList.toggle('open');
}
</script>
</body>
</html>