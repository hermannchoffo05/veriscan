<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – Connexion Fabricant</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: #e9e7f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 24px;
            overflow: hidden;
        }

        .card {
            display: flex;
            width: 100%;
            max-width: 960px;
            height: calc(100vh - 40px);
            max-height: 640px;
            border-radius: 24px;
            overflow: hidden;
            background: white;
            box-shadow: 0 30px 80px rgba(0,0,0,0.25);
        }

        /* ── LEFT ── */
        .left { width: 50%; position: relative; overflow: hidden; border-radius: 24px 0 0 24px; background: #1B1854; display: flex; flex-direction: column; justify-content: flex-end; align-items: flex-start; padding: 32px; color: white; }
        .left.paused .dot.active .fill { animation-play-state: paused; }
        .slide-bg { position: absolute; inset: 0; background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0; transition: opacity 0.9s ease; z-index: 0; }
        .slide-bg.active { opacity: 1; }
        /* ── Overlay localisé : transparent en haut (image visible), sombre
             seulement en bas où se trouve le texte, pour garantir la
             lisibilité quelle que soit l'illustration affichée ── */
        .slide-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(10,8,40,0.82) 0%, rgba(10,8,40,0.55) 22%, rgba(10,8,40,0.15) 45%, rgba(10,8,40,0) 62%); z-index: 1; pointer-events: none; }
        .left::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 22px 22px; z-index: 2; }
        .sphere { position: absolute; border-radius: 50%; background: radial-gradient(circle at 35% 35%, rgba(255,255,255,0.20), rgba(27,24,84,0.55) 60%, rgba(15,12,55,0.75)); box-shadow: inset -6px -6px 20px rgba(0,0,0,0.3), inset 6px 6px 20px rgba(255,255,255,0.10); z-index: 3; }
        .sphere-tl { width: 220px; height: 220px; top: -80px; left: -60px; opacity: 0.85; }
        .sphere-bl { width: 180px; height: 180px; bottom: -70px; left: -30px; opacity: 0.75; }
        .sphere-tr { width: 110px; height: 110px; top: 30px; right: -20px; opacity: 0.55; }
        .caption-zone { position: relative; z-index: 4; display: flex; flex-direction: column; gap: 14px; }
        .slide-label { display: inline-flex; align-items: center; gap: 8px; background: rgba(245,166,35,0.20); border: 1px solid rgba(245,166,35,0.40); border-radius: 20px; padding: 5px 14px; font-size: 11px; font-weight: 700; width: fit-content; backdrop-filter: blur(6px); color: #FCD34D; text-shadow: 0 1px 4px rgba(0,0,0,0.3); }
        .slide-label svg { width: 13px; height: 13px; flex-shrink: 0; }
        .caption-text h2 { font-size: 28px; font-weight: 800; line-height: 1.2; margin-top: 8px; text-shadow: 0 2px 12px rgba(0,0,0,0.45); }
        .caption-text p { font-size: 13px; opacity: 0.88; margin-top: 8px; line-height: 1.6; max-width: 280px; text-shadow: 0 1px 8px rgba(0,0,0,0.5); }

        /* ── Barres de progression type "Stories" à la place des simples points ── */
        .carousel-dots { display: flex; gap: 6px; align-items: center; margin-top: 10px; }
        .dot { position: relative; width: 30px; height: 4px; border-radius: 3px; background: rgba(255,255,255,0.25); cursor: pointer; border: none; padding: 0; overflow: hidden; }
        .dot .fill { position: absolute; left: 0; top: 0; height: 100%; width: 0%; background: #F5A623; border-radius: 3px; }
        .dot.active .fill { animation: fillBar 3s linear forwards; }
        .dot.done .fill { width: 100%; }
        @keyframes fillBar { from { width: 0%; } to { width: 100%; } }

        /* ── RIGHT ──────────────────────────────────────────────────
           Le formulaire occupe maintenant directement toute cette
           section : plus de boîte crème encadrée (.form-card
           supprimée), plus de padding/border/shadow autour du bloc.
           Le padding est déplacé ici sur .right pour garder un peu
           d'air par rapport aux bords du panneau. */
        .right { width: 50%; padding: 24px 44px; display: flex; flex-direction: column; justify-content: center; background: #fffaf3; overflow: hidden; }

        /* ── Logo centré via background-image ── */
        .brand-row { display: flex; flex-direction: column; align-items: center; margin-bottom: 8px; }
        .logo-circle {
            width: 68px; height: 68px;
            border-radius: 50%;
           background: white center 55% / 85% no-repeat;
            box-shadow: 0 4px 20px rgba(27,24,84,0.14);
            border: 1.5px solid rgba(245,166,35,0.20);
            transition: transform 0.3s;
        }
        .logo-circle:hover { transform: scale(1.04); }

        h1 { font-size: 22px; font-weight: 800; color: #1B1854; margin-bottom: 3px; text-align: center; }
        .sub { font-size: 12.5px; color: #6b7280; margin-bottom: 14px; text-align: center; }
        .error-box { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 11px; padding: 8px 14px; margin-bottom: 12px; font-size: 13px; }
        .field { margin-bottom: 11px; }
        .field label { display: block; font-size: 12.5px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        .iw { position: relative; }
        .iw > svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #9ca3af; pointer-events: none; }
        input[type=email], input[type=password], input[type=text] { width: 100%; padding: 9px 14px 9px 36px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 13.5px; color: #111827; outline: none; font-family: inherit; transition: border-color 0.2s, box-shadow 0.2s; background: white; }
        input:focus { border-color: #F5A623; box-shadow: 0 0 0 3px rgba(245,166,35,0.18); }
        .eye-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0; display: flex; align-items: center; }
        .eye-btn:hover { color: #374151; }
        .row-mid { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .remember { display: flex; align-items: center; gap: 7px; font-size: 12.5px; color: #6b7280; cursor: pointer; }
        input[type=checkbox] { accent-color: #F5A623; width: 13px; height: 13px; }
        .forgot { font-size: 12.5px; color: #1B1854; font-weight: 600; text-decoration: none; }
        .forgot:hover { text-decoration: underline; }
        .btn { width: 100%; padding: 10px; background: #F5A623; color: #1B1854; border: none; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background 0.2s, box-shadow 0.2s; box-shadow: 0 4px 14px rgba(245,166,35,0.40); }
        .btn:hover { background: #e0961d; }
        .btn:active { transform: scale(0.99); }
        .footer-link { text-align: center; font-size: 12.5px; color: #6b7280; margin-top: 10px; }
        .footer-link a { color: #1B1854; font-weight: 700; text-decoration: none; }
        .admin-link { text-align: right; margin-top: 10px; }
        .admin-link a { font-size: 11.5px; color: #9ca3af; text-decoration: none; transition: color 0.25s; display: inline-flex; align-items: center; gap: 5px; font-weight: 600; }
        .admin-link a:hover { color: #F5A623; }
        .admin-link svg { width: 11px; height: 11px; }

        /* ── Séparateur + bouton Google ── */
        .divider { display: flex; align-items: center; gap: 10px; margin: 12px 0; }
        .divider-line { flex: 1; height: 1px; background: #e5e7eb; }
        .divider-text { font-size: 11px; color: #9ca3af; font-weight: 600; letter-spacing: 0.05em; }
        .btn-google {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 11px; border: 1.5px solid #e5e7eb; border-radius: 10px;
            text-decoration: none; color: #374151; font-size: 13.5px; font-weight: 600;
            background: white; font-family: inherit; transition: all 0.2s;
        }
        .btn-google:hover { border-color: #d1d5db; background: #fafafa; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .btn-google svg { flex-shrink: 0; }

        @media (max-width: 768px) {
            body { padding: 0; height: auto; overflow: auto; }
            .card { flex-direction: column; border-radius: 0; height: auto; max-height: none; box-shadow: none; }
            .left { width: 100%; min-height: 180px; border-radius: 0; padding: 24px; justify-content: flex-end; }
            .caption-text h2 { font-size: 20px; }
            .right { width: 100%; border-radius: 0; padding: 28px 20px; }
            .logo-circle { width: 72px; height: 72px; }
            .admin-link { text-align: center; }
        }
             input[type="password"]::-ms-reveal,
             input[type="password"]::-ms-clear {
             display: none;
        }
            /* Chrome/Edge récents utilisent aussi cette pseudo-classe */
             input[type="password"]::-webkit-credentials-auto-fill-button,
             input[type="password"]::-webkit-strong-password-auto-fill-button {
             display: none !important;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="left">
        <div class="slide-bg active" style="background-image: url('{{ asset('images/Sign in-bro.png') }}');"></div>
        <div class="slide-bg" style="background-image: url('{{ asset('images/QR Code-bro.png') }}');"></div>
        <div class="slide-bg" style="background-image: url('{{ asset('images/Product quality-bro.png') }}');"></div>
        <div class="slide-bg" style="background-image: url('{{ asset('images/Warning-bro.png') }}');"></div>
        <div class="slide-overlay"></div>
        <div class="sphere sphere-tl"></div>
        <div class="sphere sphere-bl"></div>
        <div class="sphere sphere-tr"></div>
        <div class="caption-zone">
            <div class="slide-label" id="slide-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span id="label-text">Connexion sécurisée</span>
            </div>
            <div class="caption-text">
                <h2 id="caption-h2">Suivez vos produits<br>en temps réel</h2>
                <p id="caption-p">Accédez à votre espace fabricant protégé contre toute intrusion.</p>
            </div>
            <div class="carousel-dots">
                <button class="dot active" onclick="goToSlide(0)"><span class="fill"></span></button>
                <button class="dot" onclick="goToSlide(1)"><span class="fill"></span></button>
                <button class="dot" onclick="goToSlide(2)"><span class="fill"></span></button>
                <button class="dot" onclick="goToSlide(3)"><span class="fill"></span></button>
            </div>
        </div>
    </div>

    {{-- ── RIGHT : contenu du formulaire directement dans .right,
         plus de wrapper .form-card autour ── --}}
    <div class="right">
        <div class="brand-row">
            <div class="logo-circle" style="background-image: url('{{ asset('images/logo.png') }}');"></div>
        </div>
        <h1>Connexion</h1>
        <p class="sub">Connectez-vous à votre espace de travail</p>
        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('fabricant.login') }}">
            @csrf
            <div class="field">
                <label>Adresse Email</label>
                <div class="iw">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="hermannchoffo05@gmail.com">
                </div>
            </div>
            <div class="field">
                <label>Mot de passe</label>
                <div class="iw">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <input type="password" name="password" id="password-input" required placeholder="••••••••" style="padding-right:36px;">
                    <button type="button" class="eye-btn" onclick="togglePassword()">
                        <svg id="eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="row-mid">
                <label class="remember"><input type="checkbox" name="remember"> Se souvenir de moi</label>
                <a href="{{ route('fabricant.password.request') }}" class="forgot">Mot de passe oublié ?</a>
            </div>
            <button type="submit" class="btn">Se connecter</button>
        </form>

        {{-- Connexion Google --}}
        <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-text">OU</span>
            <div class="divider-line"></div>
        </div>
        <a href="{{ route('fabricant.auth.google') }}" class="btn-google">
            <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.66-.22-2.45H12v4.63h6.48c-.28 1.5-1.13 2.78-2.4 3.63v3h3.89c2.28-2.1 3.55-5.2 3.55-8.81z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.07 7.93-2.9l-3.89-3.02c-1.08.72-2.46 1.15-4.04 1.15-3.1 0-5.73-2.1-6.67-4.92H1.3v3.1C3.26 21.3 7.3 24 12 24z"/><path fill="#FBBC05" d="M5.33 14.31A7.2 7.2 0 014.94 12c0-.8.14-1.58.39-2.31V6.6H1.3A11.98 11.98 0 000 12c0 1.94.46 3.77 1.3 5.4l4.03-3.09z"/><path fill="#EA4335" d="M12 4.77c1.77 0 3.35.61 4.6 1.8l3.45-3.45C17.94 1.19 15.23 0 12 0 7.3 0 3.26 2.7 1.3 6.6l4.03 3.09C6.27 6.87 8.9 4.77 12 4.77z"/></svg>
            Continuer avec Google
        </a>

        <p class="footer-link">Pas encore de compte ? <a href="{{ route('fabricant.register') }}">S'inscrire</a></p>

        <div class="admin-link">
            <a href="{{ route('admin.login') }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Espace Administration
            </a>
        </div>
    </div>
</div>

<script>
    const captions = [
        { label: "Connexion sécurisée", icon: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`, h2: "Suivez vos produits<br>en temps réel", p: "Accédez à votre espace fabricant protégé contre toute intrusion." },
        { label: "QR Codes cryptés", icon: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg>`, h2: "Générez des codes<br>uniques par lot", p: "Chaque produit reçoit un QR code sécurisé et traçable instantanément." },
        { label: "Qualité garantie", icon: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`, h2: "Certifiez l'authenticité<br>de vos produits", p: "Vos clients peuvent vérifier la qualité en un simple scan." },
        { label: "Détection fraude", icon: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`, h2: "Bloquez les<br>contrefaçons", p: "Signalez et neutralisez les produits frauduleux en temps réel." }
    ];
    let current = 0; const total = 4; let timer;
    function goToSlide(index) {
        const dots = document.querySelectorAll('.dot');
        const bgs  = document.querySelectorAll('.slide-bg');
        const data = captions[index];
        const zone = document.querySelector('.caption-zone');
        bgs[current].classList.remove('active'); bgs[index].classList.add('active');
        zone.style.opacity = '0'; zone.style.transform = 'translateY(8px)'; zone.style.transition = 'opacity 0.3s, transform 0.3s';
        setTimeout(() => { document.getElementById('slide-label').innerHTML = data.icon + `<span>${data.label}</span>`; document.getElementById('caption-h2').innerHTML = data.h2; document.getElementById('caption-p').textContent = data.p; zone.style.opacity = '1'; zone.style.transform = 'translateY(0)'; }, 280);

        dots.forEach((d, i) => {
            d.classList.remove('active', 'done');
            if (i < index) d.classList.add('done');
        });
        // Forcer le navigateur à relancer l'animation CSS de la barre active
        // (sinon, ré-ajouter la même classe ne redémarre pas l'animation).
        void dots[index].offsetWidth;
        dots[index].classList.add('active');

        current = index; resetTimer();
    }
    function nextSlide() { goToSlide((current + 1) % total); }
    function resetTimer() { clearInterval(timer); timer = setInterval(nextSlide, 3000); }
    timer = setInterval(nextSlide, 3000);

    // Pause du défilement automatique (et de l'animation de la barre) au
    // survol du panneau gauche, reprise à la sortie de la souris.
    const leftPanel = document.querySelector('.left');
    leftPanel.addEventListener('mouseenter', () => { clearInterval(timer); leftPanel.classList.add('paused'); });
    leftPanel.addEventListener('mouseleave', () => { leftPanel.classList.remove('paused'); resetTimer(); });
    function togglePassword() {
        const input = document.getElementById('password-input');
        const icon  = document.getElementById('eye-icon');
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        icon.innerHTML = show ? `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>` : `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }
</script>
</body>
</html>