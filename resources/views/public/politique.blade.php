<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – {{ app()->getLocale() === 'en' ? "Privacy Policy" : "Politique de confidentialité" }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: #F3F4FA;
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
            color: #2E3A6B;
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
        .nav-back:hover { color: #2E3A6B; }
        .nav-back svg { width: 16px; height: 16px; }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, #171B3D 0%, #2E3A6B 100%);
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
        .hero-badge svg { color: #F5A623; }
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
            color: #2E3A6B;
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
        .toc li a:hover { color: #2E3A6B; text-decoration: underline; }

        .section {
            margin-bottom: 36px;
        }
        .section h2 {
            font-size: 18px;
            font-weight: 800;
            color: #171B3D;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #EEF0F8;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section h2 .num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #2E3A6B;
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
            background: #FFF7EC;
            border-left: 4px solid #F5A623;
            border-radius: 0 8px 8px 0;
            padding: 14px 16px;
            margin: 12px 0;
            font-size: 13.5px;
            color: #7a4d0f;
            line-height: 1.6;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 13px;
        }
        .data-table th {
            background: #EEF0F8;
            padding: 10px 14px;
            text-align: left;
            font-weight: 700;
            color: #2E3A6B;
            border: 1px solid #e5e7eb;
        }
        .data-table td {
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            color: #4b5563;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) td { background: #f9fafb; }

        .footer-note {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px 24px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
        }
        .footer-note strong { color: #2E3A6B; }
        .footer-note a { color: #2E3A6B; font-weight: 600; text-decoration: none; }

        @media (max-width: 640px) {
            .hero h1 { font-size: 24px; }
            .container { padding: 24px 16px 60px; }
            .data-table { font-size: 11.5px; }
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
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        Vos données protégées
    </div>
    <h1>Politique de confidentialité</h1>
    <p>Dernière mise à jour : {{ date('d/m/Y') }} — VeriScan Cameroun</p>
</div>

{{-- CONTENT --}}
<div class="container">

    {{-- Table des matières --}}
    <div class="toc">
        <h3>Sommaire</h3>
        <ol>
            <li><a href="#p1">Responsable du traitement</a></li>
            <li><a href="#p2">Données collectées</a></li>
            <li><a href="#p3">Finalités du traitement</a></li>
            <li><a href="#p4">Partage des données</a></li>
            <li><a href="#p5">Conservation des données</a></li>
            <li><a href="#p6">Sécurité des données</a></li>
            <li><a href="#p7">Vos droits</a></li>
            <li><a href="#p8">Cookies</a></li>
            <li><a href="#p9">Modifications</a></li>
            <li><a href="#p10">Contact</a></li>
        </ol>
    </div>

    {{-- Section 1 --}}
    <div class="section" id="p1">
        <h2><span class="num">1</span> Responsable du traitement</h2>
        <p>Le responsable du traitement des données à caractère personnel collectées via la plateforme VeriScan est l'équipe VeriScan, établie au Cameroun.</p>
        <p>Contact : <strong>privacy@veriscan.cm</strong></p>
    </div>

    {{-- Section 2 --}}
    <div class="section" id="p2">
        <h2><span class="num">2</span> Données collectées</h2>
        <p>Nous collectons les données suivantes lors de votre utilisation de la plateforme :</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Données</th>
                    <th>Obligatoire</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Identité</td>
                    <td>Nom de l'entreprise, pays</td>
                    <td>Oui</td>
                </tr>
                <tr>
                    <td>Contact</td>
                    <td>Email, numéro de téléphone</td>
                    <td>Oui</td>
                </tr>
                <tr>
                    <td>Paiement</td>
                    <td>Numéro Mobile Money (traité par CamPay)</td>
                    <td>Pour l'abonnement</td>
                </tr>
                <tr>
                    <td>Produits</td>
                    <td>Noms, descriptions, lots, QR codes générés</td>
                    <td>Oui</td>
                </tr>
                <tr>
                    <td>Navigation</td>
                    <td>Adresse IP, pages visitées, horodatages des scans</td>
                    <td>Automatique</td>
                </tr>
            </tbody>
        </table>
        <div class="highlight-box">
            VeriScan ne collecte jamais de données bancaires directes. Les paiements sont intégralement traités par CamPay, qui dispose de sa propre politique de confidentialité.
        </div>
    </div>

    {{-- Section 3 --}}
    <div class="section" id="p3">
        <h2><span class="num">3</span> Finalités du traitement</h2>
        <p>Vos données sont utilisées pour :</p>
        <ul>
            <li>Créer et gérer votre compte fabricant</li>
            <li>Générer et gérer vos QR codes produit</li>
            <li>Traiter vos paiements d'abonnement</li>
            <li>Vous envoyer des notifications importantes (alertes, signalements)</li>
            <li>Améliorer la qualité et la sécurité de la plateforme</li>
            <li>Respecter nos obligations légales au Cameroun</li>
        </ul>
    </div>

    {{-- Section 4 --}}
    <div class="section" id="p4">
        <h2><span class="num">4</span> Partage des données</h2>
        <p>Vos données personnelles ne sont jamais vendues à des tiers. Elles peuvent être partagées uniquement dans les cas suivants :</p>
        <ul>
            <li><strong>CamPay</strong> — pour le traitement des paiements Mobile Money</li>
            <li><strong>Autorités compétentes</strong> — en cas d'obligation légale ou de réquisition judiciaire</li>
            <li><strong>Hébergeurs</strong> — dans le cadre de l'infrastructure technique de la plateforme</li>
        </ul>
    </div>

    {{-- Section 5 --}}
    <div class="section" id="p5">
        <h2><span class="num">5</span> Conservation des données</h2>
        <p>Vos données sont conservées pendant toute la durée de votre utilisation active de la plateforme, puis :</p>
        <ul>
            <li><strong>Données de compte</strong> : supprimées dans les 30 jours suivant la fermeture du compte</li>
            <li><strong>Données de paiement</strong> : conservées 5 ans conformément aux obligations comptables camerounaises</li>
            <li><strong>Logs de navigation</strong> : conservés 12 mois maximum</li>
        </ul>
    </div>

    {{-- Section 6 --}}
    <div class="section" id="p6">
        <h2><span class="num">6</span> Sécurité des données</h2>
        <p>VeriScan met en œuvre les mesures de sécurité suivantes pour protéger vos données :</p>
        <ul>
            <li>Chiffrement HTTPS de toutes les communications</li>
            <li>Hachage sécurisé (bcrypt) des mots de passe</li>
            <li>Authentification par token HMAC-SHA256 pour les QR codes</li>
            <li>Accès aux données restreint aux administrateurs autorisés</li>
            <li>Sauvegardes régulières de la base de données</li>
        </ul>
    </div>

    {{-- Section 7 --}}
    <div class="section" id="p7">
        <h2><span class="num">7</span> Vos droits</h2>
        <p>Conformément aux lois applicables, vous disposez des droits suivants sur vos données personnelles :</p>
        <ul>
            <li><strong>Droit d'accès</strong> : obtenir une copie de vos données</li>
            <li><strong>Droit de rectification</strong> : corriger des données inexactes</li>
            <li><strong>Droit à l'effacement</strong> : demander la suppression de vos données</li>
            <li><strong>Droit à la portabilité</strong> : recevoir vos données dans un format lisible</li>
            <li><strong>Droit d'opposition</strong> : vous opposer à certains traitements</li>
        </ul>
        <p>Pour exercer ces droits, contactez-nous à <strong>privacy@veriscan.cm</strong>. Nous répondrons dans un délai de 30 jours.</p>
    </div>

    {{-- Section 8 --}}
    <div class="section" id="p8">
        <h2><span class="num">8</span> Cookies</h2>
        <p>VeriScan utilise des cookies essentiels au fonctionnement de la plateforme :</p>
        <ul>
            <li><strong>Cookie de session</strong> — maintient votre connexion active</li>
            <li><strong>Cookie CSRF</strong> — protège contre les attaques cross-site</li>
            <li><strong>Cookie de langue</strong> — mémorise votre préférence linguistique (FR/EN)</li>
        </ul>
        <p>Ces cookies ne sont pas des cookies publicitaires ou de tracking. Ils sont strictement nécessaires au fonctionnement du service et ne peuvent pas être désactivés.</p>
    </div>

    {{-- Section 9 --}}
    <div class="section" id="p9">
        <h2><span class="num">9</span> Modifications de cette politique</h2>
        <p>VeriScan peut modifier cette politique à tout moment. En cas de modification substantielle, vous serez informé par email au moins 15 jours avant l'entrée en vigueur des nouvelles dispositions.</p>
        <p>La poursuite de l'utilisation de la plateforme après notification vaut acceptation de la nouvelle politique.</p>
    </div>

    {{-- Section 10 --}}
    <div class="section" id="p10">
        <h2><span class="num">10</span> Contact</h2>
        <p>Pour toute question relative à la protection de vos données personnelles :</p>
        <ul>
            <li><strong>Email</strong> : hermannchoffo05@gmail.com</li>
            <li><strong>Email général</strong> : hermannchoffo05@gmail.com</li>
        </ul>
    </div>

    {{-- Footer note --}}
    <div class="footer-note">
        <p>Politique en vigueur depuis le <strong>{{ date('d/m/Y') }}</strong>.</p>
        <p style="margin-top:8px;">
            Consulter également nos
            <a href="{{ route('conditions') }}">Conditions d'utilisation</a>
            &nbsp;|&nbsp;
            <a href="{{ url('/') }}">Retour à l'accueil</a>
        </p>
    </div>

</div>
</body>
</html>