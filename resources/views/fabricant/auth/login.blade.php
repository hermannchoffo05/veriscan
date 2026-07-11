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
        .slide-bg { position: absolute; inset: 0; background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0; transition: opacity 0.8s ease; z-index: 0; }
        .slide-bg.active { opacity: 1; }
        .slide-overlay { position: absolute; inset: 0; background: linear-gradient(145deg, rgba(15,12,55,0.45) 0%, rgba(27,24,84,0.30) 100%); z-index: 1; }
        .left::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 22px 22px; z-index: 2; }
        .sphere { position: absolute; border-radius: 50%; background: radial-gradient(circle at 35% 35%, rgba(255,255,255,0.20), rgba(27,24,84,0.55) 60%, rgba(15,12,55,0.75)); box-shadow: inset -6px -6px 20px rgba(0,0,0,0.3), inset 6px 6px 20px rgba(255,255,255,0.10); z-index: 3; }
        .sphere-tl { width: 220px; height: 220px; top: -80px; left: -60px; opacity: 0.85; }
        .sphere-bl { width: 180px; height: 180px; bottom: -70px; left: -30px; opacity: 0.75; }
        .sphere-tr { width: 110px; height: 110px; top: 30px; right: -20px; opacity: 0.55; }
        .caption-zone { position: relative; z-index: 4; display: flex; flex-direction: column; gap: 14px; }
        .slide-label { display: inline-flex; align-items: center; gap: 8px; background: rgba(245,166,35,0.18); border: 1px solid rgba(245,166,35,0.35); border-radius: 20px; padding: 5px 14px; font-size: 11px; font-weight: 600; width: fit-content; backdrop-filter: blur(4px); color: #FCD34D; }
        .slide-label svg { width: 13px; height: 13px; flex-shrink: 0; }
        .caption-text h2 { font-size: 28px; font-weight: 800; line-height: 1.2; margin-top: 8px; }
        .caption-text p { font-size: 13px; opacity: 0.72; margin-top: 8px; line-height: 1.6; max-width: 280px; }
        .carousel-dots { display: flex; gap: 8px; align-items: center; margin-top: 6px; }
        .dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.35); cursor: pointer; transition: all 0.3s; border: none; padding: 0; }
        .dot.active { width: 22px; border-radius: 4px; background: #F5A623; }

        /* ── RIGHT ── */
        .right { width: 50%; padding: 28px 40px 22px; display: flex; flex-direction: column; justify-content: center; background: white; overflow: hidden; }

        .form-card { background: #fffaf3; border: 1.5px solid rgba(245,166,35,0.22); border-radius: 18px; padding: 22px 24px 20px; box-shadow: 0 2px 16px rgba(27,24,84,0.06); }

        /* ── Logo centré via background-image ── */
        .brand-row { display: flex; flex-direction: column; align-items: center; margin-bottom: 12px; }
        .logo-circle {
            width: 88px; height: 88px;
            border-radius: 50%;
           background: white center 55% / 85% no-repeat;
            box-shadow: 0 4px 20px rgba(27,24,84,0.14);
            border: 1.5px solid rgba(245,166,35,0.20);
            transition: transform 0.3s;
        }
        .logo-circle:hover { transform: scale(1.04); }

        h1 { font-size: 24px; font-weight: 800; color: #1B1854; margin-bottom: 3px; text-align: center; }
        .sub { font-size: 12.5px; color: #6b7280; margin-bottom: 18px; text-align: center; }
        .error-box { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 11px; padding: 10px 14px; margin-bottom: 14px; font-size: 13px; }
        .field { margin-bottom: 13px; }
        .field label { display: block; font-size: 12px; font-weight: 500; color: #374151; margin-bottom: 5px; }
        .iw { position: relative; }
        .iw > svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #9ca3af; pointer-events: none; }
        input[type=email], input[type=password], input[type=text] { width: 100%; padding: 10px 13px 10px 36px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 13px; color: #111827; outline: none; font-family: inherit; transition: border-color 0.2s, box-shadow 0.2s; background: white; }
        input:focus { border-color: #F5A623; box-shadow: 0 0 0 3px rgba(245,166,35,0.18); }
        .eye-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0; display: flex; align-items: center; }
        .eye-btn:hover { color: #374151; }
        .row-mid { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .remember { display: flex; align-items: center; gap: 7px; font-size: 12px; color: #6b7280; cursor: pointer; }
        input[type=checkbox] { accent-color: #F5A623; width: 13px; height: 13px; }
        .forgot { font-size: 12px; color: #1B1854; font-weight: 600; text-decoration: none; }
        .forgot:hover { text-decoration: underline; }
        .btn { width: 100%; padding: 11px; background: #F5A623; color: #1B1854; border: none; border-radius: 10px; font-size: 13.5px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background 0.2s, box-shadow 0.2s; box-shadow: 0 4px 14px rgba(245,166,35,0.40); }
        .btn:hover { background: #e0961d; }
        .btn:active { transform: scale(0.99); }
        .footer-link { text-align: center; font-size: 12px; color: #6b7280; margin-top: 13px; }
        .footer-link a { color: #1B1854; font-weight: 700; text-decoration: none; }
        .admin-link { text-align: right; margin-top: 12px; }
        .admin-link a { font-size: 11px; color: #9ca3af; text-decoration: none; transition: color 0.25s; display: inline-flex; align-items: center; gap: 5px; font-weight: 600; }
        .admin-link a:hover { color: #F5A623; }
        .admin-link svg { width: 11px; height: 11px; }

        @media (max-width: 768px) {
            body { padding: 0; height: auto; overflow: auto; }
            .card { flex-direction: column; border-radius: 0; height: auto; max-height: none; box-shadow: none; }
            .left { width: 100%; min-height: 180px; border-radius: 0; padding: 24px; justify-content: flex-end; }
            .caption-text h2 { font-size: 20px; }
            .right { width: 100%; border-radius: 0; padding: 20px 16px; }
            .form-card { padding: 18px 14px; }
            .logo-circle { width: 72px; height: 72px; }
            .admin-link { text-align: center; }
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
                <button class="dot active" onclick="goToSlide(0)"></button>
                <button class="dot" onclick="goToSlide(1)"></button>
                <button class="dot" onclick="goToSlide(2)"></button>
                <button class="dot" onclick="goToSlide(3)"></button>
            </div>
        </div>
    </div>

    <div class="right">
        <div class="form-card">
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
            <p class="footer-link">Pas encore de compte ? <a href="{{ route('fabricant.register') }}">S'inscrire</a></p>
        </div>
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
        dots.forEach((d, i) => d.classList.toggle('active', i === index));
        current = index; resetTimer();
    }
    function nextSlide() { goToSlide((current + 1) % total); }
    function resetTimer() { clearInterval(timer); timer = setInterval(nextSlide, 3000); }
    timer = setInterval(nextSlide, 3000);
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