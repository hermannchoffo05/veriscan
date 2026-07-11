<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body { font-family: 'DM Sans', system-ui, sans-serif; background: #dde2f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 24px; }

        .card { width: 100%; max-width: 440px; background: white; border-radius: 24px; padding: 30px 40px; box-shadow: 0 16px 40px rgba(0,0,0,0.12); position: relative; animation: fadeUp 0.5s ease forwards; }

        .brand { display: flex; flex-direction: column; align-items: center; margin-bottom: 10px; }
        .logo-wrapper { width: 78px; height: 78px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; animation: logoEntrance 0.7s cubic-bezier(0.34,1.56,0.64,1) forwards; }
        .logo-circle { width: 78px; height: 78px; border-radius: 50%; background: white center 55% / 85% no-repeat; box-shadow: 0 2px 4px rgba(0,0,0,0.04), 0 8px 24px rgba(46,58,107,0.12), 0 20px 48px rgba(46,58,107,0.08); border: 1.5px solid rgba(46,58,107,0.1); transition: transform 0.35s ease; }
        .logo-circle:hover { transform: scale(1.03); }

        @keyframes logoEntrance { from { opacity: 0; transform: scale(0.7) translateY(-10px); } to { opacity: 1; transform: scale(1) translateY(0); } }

        .brand-tag { display: inline-flex; align-items: center; gap: 7px; background: #EEF0F8; border: 1px solid rgba(46,58,107,0.25); border-radius: 20px; padding: 5px 14px; font-size: 11px; font-weight: 700; color: #2E3A6B; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 12px; }
        .brand-tag .dot { width: 6px; height: 6px; border-radius: 50%; background: #2E3A6B; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(0.75); } }

        h1 { font-size: 26px; font-weight: 800; color: #111827; text-align: center; margin-bottom: 4px; }
        .sub { font-size: 13px; color: #6b7280; text-align: center; }
        .divider { height: 1px; background: #f3f4f6; margin: 14px 0; }

        .field { margin-bottom: 10px; }
        .field label { display: block; font-size: 12.5px; font-weight: 500; color: #374151; margin-bottom: 5px; }
        .iw { position: relative; }
        .iw > svg { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #9ca3af; pointer-events: none; z-index: 1; }

        input[type=email], input[type=password], input[type=text] { width: 100%; padding: 11px 14px 11px 40px; border: 1.5px solid #e5e7eb; border-radius: 11px; font-size: 13.5px; color: #111827; outline: none; font-family: 'DM Sans', system-ui, sans-serif; transition: border-color 0.2s, box-shadow 0.2s; background: #f9fafb !important; -webkit-text-fill-color: #111827; box-shadow: none; }
        input:-webkit-autofill, input:-webkit-autofill:hover, input:-webkit-autofill:focus { -webkit-box-shadow: 0 0 0px 1000px #f9fafb inset !important; -webkit-text-fill-color: #111827 !important; border: 1.5px solid #e5e7eb !important; font-family: 'DM Sans', system-ui, sans-serif !important; font-size: 13.5px !important; }
        input:focus { border-color: #2E3A6B; box-shadow: 0 0 0 3px rgba(46,58,107,0.13) !important; background: white !important; }
        input:focus:-webkit-autofill { -webkit-box-shadow: 0 0 0px 1000px white inset, 0 0 0 3px rgba(46,58,107,0.13) !important; }

        .eye-btn { position: absolute; right: 13px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0; display: flex; align-items: center; z-index: 2; }
        .eye-btn:hover { color: #374151; }

        .row-mid { display: flex; align-items: center; margin-bottom: 12px; }
        .remember { display: flex; align-items: center; gap: 7px; font-size: 12.5px; color: #6b7280; cursor: pointer; }
        input[type=checkbox] { accent-color: #2E3A6B; width: 14px; height: 14px; }

        .btn { width: 100%; padding: 12px; background: #2E3A6B; color: white; border: none; border-radius: 11px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background 0.2s, transform 0.1s, box-shadow 0.2s; box-shadow: 0 4px 14px rgba(46,58,107,0.35); display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn:hover { background: #232C54; box-shadow: 0 6px 20px rgba(46,58,107,0.45); }
        .btn:active { transform: scale(0.99); }
        .btn svg { width: 15px; height: 15px; }

        .error-box { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 11px; padding: 11px 14px; font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .error-box svg { width: 15px; height: 15px; flex-shrink: 0; }

        .back-link { display: flex; align-items: center; justify-content: center; gap: 5px; margin-top: 18px; font-size: 12px; color: #9ca3af; text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .back-link:hover { color: #2E3A6B; }
        .back-link svg { width: 12px; height: 12px; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* ── RESPONSIVE ── */
        @media (max-width: 480px) {
            body { padding: 16px; }
            .card { padding: 24px 20px; border-radius: 16px; }
            h1 { font-size: 22px; }
        }
    </style>
</head>
<body>
<div class="card">
    <div class="brand">
        <div class="logo-wrapper">
           <div class="logo-circle" style="background-image: url('{{ asset('images/logo.png') }}');"></div>
        </div>
        <div class="brand-tag"><span class="dot"></span>Espace Administration</div>
        <h1>Connexion Admin</h1>
        <p class="sub">Accès réservé aux administrateurs VeriScan</p>
    </div>
    <div class="divider"></div>
    @if ($errors->any())
    <div class="error-box">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        {{ $errors->first() }}
    </div>
    @endif
    <form method="POST" action="{{ route('admin.login') }}" autocomplete="on">
        @csrf
        <div class="field">
            <label>Adresse Email</label>
            <div class="iw">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@veriscan.cm" autocomplete="username" autofocus>
            </div>
        </div>
        <div class="field">
            <label>Mot de passe</label>
            <div class="iw">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <input type="password" name="password" id="pw" required placeholder="••••••••" style="padding-right:40px;" autocomplete="current-password">
                <button type="button" class="eye-btn" onclick="togglePw()">
                    <svg id="eye" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="row-mid">
            <label class="remember"><input type="checkbox" name="remember"> Se souvenir de moi</label>
        </div>
        <button type="submit" class="btn">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            Se connecter
        </button>
    </form>
    <a href="{{ route('fabricant.login') }}" class="back-link">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Retour à l'espace fabricant
    </a>
</div>
<script>
function togglePw() {
    const pw  = document.getElementById('pw');
    const eye = document.getElementById('eye');
    const show = pw.type === 'password';
    pw.type = show ? 'text' : 'password';
    eye.innerHTML = show
        ? `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>`
        : `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
}
</script>
</body>
</html>