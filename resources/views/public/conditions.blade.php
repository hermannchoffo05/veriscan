<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – {{ app()->getLocale() === 'en' ? "Terms of Use" : "Conditions d'utilisation" }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: #f0faf9;
            color: #111827;
            min-height: 100vh;
        }

        /* ── NAVBAR ── */
        .navbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .nav-logo {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }
        .nav-name {
            font-size: 18px;
            font-weight: 800;
            color: #0F766E;
        }
        .nav-back {
            font-size: 13px;
            color: #6b7280;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }
        .nav-back:hover { color: #0F766E; }
        .nav-back svg { width: 16px; height: 16px; }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, #064e3b 0%, #0F766E 100%);
            color: white;
            padding: 48px 24px;
            text-align: center;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .hero h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
        }
        .hero p {
            font-size: 14px;
            color: rgba(255,255,255,0.75);
        }

        /* ── CONTENT ── */
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 24px 80px;
        }

        .toc {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 32px;
        }
        .toc h3 {
            font-size: 13px;
            font-weight: 700;
            color: #0F766E;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 12px;
        }
        .toc ol {
            padding-left: 18px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .toc li a {
            font-size: 13px;
            color: #374151;
            text-decoration: none;
        }
        .toc li a:hover { color: #0F766E; text-decoration: underline; }

        .section {
            margin-bottom: 36px;
        }
        .section h2 {
            font-size: 18px;
            font-weight: 800;
            color: #064e3b;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #d1fae5;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section h2 .num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #0F766E;
            color: white;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .section p {
            font-size: 14px;
            line-height: 1.75;
            color: #374151;
            margin-bottom: 10px;
        }
        .section ul {
            padding-left: 20px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 10px;
        }
        .section ul li {
            font-size: 14px;
            line-height: 1.6;
            color: #374151;
        }

        .highlight-box {
            background: #f0fdf4;
            border-left: 4px solid #0F766E;
            border-radius: 0 8px 8px 0;
            padding: 14px 16px;
            margin: 12px 0;
            font-size: 13.5px;
            color: #064e3b;
            line-height: 1.6;
        }

        .footer-note {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px 24px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
        }
        .footer-note strong { color: #0F766E; }
        .footer-note a { color: #0F766E; font-weight: 600; text-decoration: none; }

        @media (max-width: 640px) {
            .hero h1 { font-size: 24px; }
            .container { padding: 24px 16px 60px; }
        }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar">
    <a href="{{ url('/') }}" class="nav-brand">
        <img src="{{ asset('images/logo.png') }}" alt="VeriScan" class="nav-logo">
        <span class="nav-name">VeriScan</span>
    </a>
    <a href="javascript:history.back()" class="nav-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour
    </a>
</nav>

{{-- HERO --}}
<div class="hero">
    <div class="hero-badge">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        Document légal
    </div>
    <h1>Conditions d'utilisation</h1>
    <p>Dernière mise à jour : {{ date('d/m/Y') }} — VeriScan Cameroun</p>
</div>

{{-- CONTENT --}}
<div class="container">

    {{-- Table des matières --}}
    <div class="toc">
        <h3>Sommaire</h3>
        <ol>
            <li><a href="#art1">Présentation du service</a></li>
            <li><a href="#art2">Acceptation des conditions</a></li>
            <li><a href="#art3">Inscription et compte fabricant</a></li>
            <li><a href="#art4">Utilisation des QR codes</a></li>
            <li><a href="#art5">Abonnements et paiements</a></li>
            <li><a href="#art6">Obligations des utilisateurs</a></li>
            <li><a href="#art7">Responsabilités et garanties</a></li>
            <li><a href="#art8">Propriété intellectuelle</a></li>
            <li><a href="#art9">Résiliation</a></li>
            <li><a href="#art10">Droit applicable</a></li>
        </ol>
    </div>

    {{-- Article 1 --}}
    <div class="section" id="art1">
        <h2><span class="num">1</span> Présentation du service</h2>
        <p>VeriScan est une plateforme numérique de lutte contre la contrefaçon basée au Cameroun. Elle permet aux fabricants et distributeurs d'authentifier leurs produits via des QR codes sécurisés, et aux consommateurs de vérifier l'authenticité d'un produit en scannant ce code.</p>
        <p>La plateforme est éditée et exploitée par l'équipe VeriScan, dont le siège est au Cameroun. Pour toute question, contactez-nous à <strong>hermannchoffo05@gmail.com</strong>.</p>
    </div>

    {{-- Article 2 --}}
    <div class="section" id="art2">
        <h2><span class="num">2</span> Acceptation des conditions</h2>
        <p>En créant un compte ou en utilisant la plateforme VeriScan, vous acceptez pleinement et sans réserve les présentes conditions d'utilisation. Si vous n'acceptez pas ces conditions, vous ne devez pas utiliser le service.</p>
        <div class="highlight-box">
            En cochant la case "J'accepte les conditions d'utilisation" lors de votre inscription, vous confirmez avoir lu et compris ce document dans son intégralité.
        </div>
    </div>

    {{-- Article 3 --}}
    <div class="section" id="art3">
        <h2><span class="num">3</span> Inscription et compte fabricant</h2>
        <p>Pour accéder aux fonctionnalités de la plateforme, vous devez créer un compte fabricant en fournissant des informations exactes, complètes et à jour, notamment :</p>
        <ul>
            <li>Nom de l'entreprise ou raison sociale</li>
            <li>Adresse email professionnelle valide</li>
            <li>Numéro de téléphone</li>
            <li>Pays d'établissement</li>
        </ul>
        <p>Votre compte est personnel et non cessible. Vous êtes responsable de la confidentialité de vos identifiants de connexion. Tout accès à votre compte est réputé effectué par vous.</p>
        <p>VeriScan se réserve le droit de valider, suspendre ou supprimer tout compte dont les informations seraient incorrectes ou dont l'utilisation serait contraire aux présentes conditions.</p>
    </div>

    {{-- Article 4 --}}
    <div class="section" id="art4">
        <h2><span class="num">4</span> Utilisation des QR codes</h2>
        <p>Les QR codes générés par VeriScan sont liés à un produit et à un lot spécifique. Ils sont destinés à être imprimés et apposés sur les produits commercialisés par le fabricant enregistré.</p>
        <ul>
            <li>Chaque QR code est unique et ne peut être dupliqué ou réutilisé pour un autre produit</li>
            <li>Toute tentative de falsification, copie ou détournement d'un QR code est strictement interdite</li>
            <li>Le fabricant est seul responsable de l'utilisation correcte des QR codes sur ses produits</li>
            <li>En cas d'abus détecté, VeriScan se réserve le droit de désactiver les QR codes concernés</li>
        </ul>
    </div>

    {{-- Article 5 --}}
    <div class="section" id="art5">
        <h2><span class="num">5</span> Abonnements et paiements</h2>
        <p>L'accès aux fonctionnalités avancées de VeriScan est conditionné à la souscription d'un abonnement mensuel. Les tarifs en vigueur sont :</p>
        <ul>
            <li><strong>Plan Starter</strong> — 5 000 XAF/mois</li>
            <li><strong>Plan Pro</strong> — 15 000 XAF/mois</li>
            <li><strong>Plan Entreprise</strong> — 50 000 XAF/mois</li>
        </ul>
        <p>Les paiements sont traités via CamPay (Mobile Money MTN et Orange). Tout paiement validé est définitif et ne fait pas l'objet d'un remboursement, sauf disposition légale contraire applicable au Cameroun.</p>
        <div class="highlight-box">
            VeriScan se réserve le droit de modifier ses tarifs avec un préavis de 30 jours communiqué par email.
        </div>
    </div>

    {{-- Article 6 --}}
    <div class="section" id="art6">
        <h2><span class="num">6</span> Obligations des utilisateurs</h2>
        <p>En utilisant VeriScan, vous vous engagez à :</p>
        <ul>
            <li>Fournir des informations véridiques sur vos produits et votre entreprise</li>
            <li>Ne pas utiliser la plateforme à des fins frauduleuses ou illicites</li>
            <li>Ne pas tenter de contourner les mécanismes de sécurité de la plateforme</li>
            <li>Signaler immédiatement toute utilisation non autorisée de votre compte</li>
            <li>Respecter les lois camerounaises et internationales en matière de commerce</li>
        </ul>
    </div>

    {{-- Article 7 --}}
    <div class="section" id="art7">
        <h2><span class="num">7</span> Responsabilités et garanties</h2>
        <p>VeriScan s'engage à fournir un service disponible et sécurisé, mais ne peut garantir une disponibilité continue à 100%. En cas d'interruption planifiée, les utilisateurs seront informés à l'avance.</p>
        <p>VeriScan ne peut être tenu responsable des dommages indirects résultant de l'utilisation ou de l'impossibilité d'utiliser la plateforme, notamment en cas de force majeure ou de défaillance des opérateurs de télécommunications.</p>
    </div>

    {{-- Article 8 --}}
    <div class="section" id="art8">
        <h2><span class="num">8</span> Propriété intellectuelle</h2>
        <p>La marque VeriScan, le logo, l'interface, les algorithmes de génération de QR codes et l'ensemble des contenus de la plateforme sont la propriété exclusive de VeriScan. Toute reproduction, même partielle, est interdite sans autorisation écrite préalable.</p>
        <p>Les données et produits enregistrés par les fabricants restent leur propriété. VeriScan s'engage à ne pas les exploiter commercialement sans consentement.</p>
    </div>

    {{-- Article 9 --}}
    <div class="section" id="art9">
        <h2><span class="num">9</span> Résiliation</h2>
        <p>Vous pouvez supprimer votre compte à tout moment depuis la section Paramètres de votre espace fabricant. La résiliation prend effet immédiatement et entraîne la désactivation de tous vos QR codes actifs.</p>
        <p>VeriScan peut résilier ou suspendre votre compte sans préavis en cas de violation des présentes conditions, de fraude avérée ou de non-paiement.</p>
    </div>

    {{-- Article 10 --}}
    <div class="section" id="art10">
        <h2><span class="num">10</span> Droit applicable</h2>
        <p>Les présentes conditions sont régies par le droit camerounais. Tout litige relatif à leur interprétation ou exécution sera soumis aux tribunaux compétents de Yaoundé, Cameroun.</p>
        <p>Pour toute question juridique ou réclamation, contactez : <strong>hermannchoffo05@gmail.com</strong></p>
    </div>

    {{-- Footer note --}}
    <div class="footer-note">
        <p>Ces conditions sont en vigueur depuis le <strong>{{ date('d/m/Y') }}</strong>.</p>
        <p style="margin-top:8px;">
            Consulter également notre
            <a href="{{ route('politique') }}">Politique de confidentialité</a>
            &nbsp;|&nbsp;
            <a href="{{ url('/') }}">Retour à l'accueil</a>
        </p>
    </div>

</div>
</body>
</html>