<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – Nouveau mot de passe</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', system-ui, sans-serif; background: #dde1f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 24px; }
        .card { width: 100%; max-width: 440px; background: white; border-radius: 24px; box-shadow: 0 30px 80px rgba(0,0,0,0.18); padding: 44px 44px 36px; display: flex; flex-direction: column; align-items: center; animation: cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }
        @keyframes cardIn { from { opacity: 0; transform: translateY(24px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .logo-wrapper { width: 110px; height: 110px; border-radius: 50%; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.04), 0 10px 30px rgba(46,58,107,0.10), 0 20px 50px rgba(46,58,107,0.07); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1.5px solid rgba(46,58,107,0.08); margin-bottom: 28px; transition: transform 0.35s ease; }
        .logo-wrapper:hover { transform: scale(1.03); }
        .logo-wrapper img { width: 88px; height: 88px; object-fit: contain; mix-blend-mode: multiply; }
        h1 { font-size: 24px; font-weight: 800; color: #111827; text-align: center; margin-bottom: 8px; }
        .sub { font-size: 13px; color: #6b7280; text-align: center; line-height: 1.6; margin-bottom: 28px; max-width: 310px; }
        .form-full { width: 100%; }
        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
        label { font-size: 12.5px; font-weight: 600; color: #374151; }
        .iw { position: relative; }
        .iw > svg:first-child { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #9ca3af; pointer-events: none; }
        input[type=password], input[type=text] { width: 100%; padding: 11px 42px 11px 40px; border: 1.5px solid #e5e7eb; border-radius: 12px; font-size: 13.5px; color: #111827; background: #f9fafb; font-family: inherit; outline: none; transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; }
        input:focus { border-color: #2E3A6B; box-shadow: 0 0 0 3px rgba(46,58,107,0.12); background: white; }
        input.valid { border-color: #10b981; }
        input.invalid { border-color: #ef4444; }
        .eye-btn { position: absolute; right: 11px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9ca3af; padding: 0; display: flex; align-items: center; }
        .eye-btn:hover { color: #374151; }
        .eye-btn svg { width: 15px; height: 15px; }
        .strength-wrap { margin-top: 6px; }
        .strength-bar { height: 4px; border-radius: 4px; background: #e5e7eb; overflow: hidden; margin-bottom: 4px; }
        .strength-fill { height: 100%; border-radius: 4px; width: 0%; transition: width 0.3s ease, background 0.3s ease; }
        .strength-label { font-size: 11px; font-weight: 600; color: #9ca3af; }
        .match-msg { font-size: 11.5px; font-weight: 600; margin-top: 4px; display: none; }
        .match-msg.ok  { color: #10b981; display: block; }
        .match-msg.err { color: #ef4444; display: block; }
        .btn { width: 100%; padding: 13px; background: #F5A623; color: #171B3D; border: none; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background 0.2s, transform 0.1s, box-shadow 0.2s; box-shadow: 0 4px 14px rgba(245,166,35,0.35); margin-top: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn:hover { background: #e0961d; box-shadow: 0 6px 20px rgba(245,166,35,0.45); }
        .btn:active { transform: scale(0.99); }
        .btn svg { width: 16px; height: 16px; }
        .error-box { width: 100%; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 11px; padding: 10px 14px; font-size: 13px; margin-bottom: 14px; }
        .back-link { margin-top: 22px; font-size: 12.5px; color: #6b7280; text-align: center; }
        .back-link a { color: #2E3A6B; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
        .back-link a:hover { text-decoration: underline; }
        .back-link a svg { width: 13px; height: 13px; }
        .card > * { opacity: 0; animation: fadeUp 0.4s ease forwards; }
        .card > *:nth-child(1) { animation-delay: 0.08s; } .card > *:nth-child(2) { animation-delay: 0.13s; } .card > *:nth-child(3) { animation-delay: 0.17s; } .card > *:nth-child(4) { animation-delay: 0.21s; } .card > *:nth-child(5) { animation-delay: 0.25s; } .card > *:nth-child(6) { animation-delay: 0.29s; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        /* ── RESPONSIVE ── */
        @media (max-width: 480px) {
            body { padding: 16px; align-items: flex-start; padding-top: 40px; }
            .card { padding: 32px 24px 28px; border-radius: 20px; }
           .logo-wrapper { width: 110px; height: 110px; border-radius: 50%; background: white center 55% / 85% no-repeat; box-shadow: 0 4px 6px rgba(0,0,0,0.04), 0 10px 30px rgba(46,58,107,0.10), 0 20px 50px rgba(46,58,107,0.07); border: 1.5px solid rgba(46,58,107,0.08); margin-bottom: 28px; transition: transform 0.35s ease; }
            .logo-wrapper:hover { transform: scale(1.03); }
            h1 { font-size: 20px; }
            .sub { font-size: 12.5px; margin-bottom: 20px; }
        }
    </style>
</head>
<body>
<div class="card">
    <div class="logo-wrapper" style="background-image: url('{{ asset('images/logo.png') }}');"></div>
    <h1>Nouveau mot de passe</h1>
    <p class="sub">Choisissez un mot de passe fort pour sécuriser votre compte VeriScan.</p>
    <div class="form-full">
        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('fabricant.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <div class="field">
                <label>Nouveau mot de passe</label>
                <div class="iw">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <input type="password" name="password" id="pw1" required placeholder="••••••••" oninput="checkStrength(this.value); checkMatch()">
                    <button type="button" class="eye-btn" onclick="togglePw('pw1','eye1')">
                        <svg id="eye1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                <div class="strength-wrap">
                    <div class="strength-bar"><div class="strength-fill" id="str-fill"></div></div>
                    <span class="strength-label" id="str-label"></span>
                </div>
            </div>
            <div class="field">
                <label>Confirmer le mot de passe</label>
                <div class="iw">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <input type="password" name="password_confirmation" id="pw2" required placeholder="Répétez" oninput="checkMatch()">
                    <button type="button" class="eye-btn" onclick="togglePw('pw2','eye2')">
                        <svg id="eye2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                <span class="match-msg" id="match-msg"></span>
            </div>
            <button type="submit" class="btn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Réinitialiser le mot de passe
            </button>
        </form>
    </div>
    <p class="back-link">
        <a href="{{ route('fabricant.login') }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour à la connexion
        </a>
    </p>
</div>
<script>
    function togglePw(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        icon.innerHTML = show
            ? `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>`
            : `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }
    function checkStrength(val) {
        const fill  = document.getElementById('str-fill');
        const label = document.getElementById('str-label');
        if (!val) { fill.style.width = '0%'; label.textContent = ''; return; }
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const levels = [{ w:'25%',color:'#ef4444',text:'Très faible'},{ w:'50%',color:'#f97316',text:'Faible'},{ w:'75%',color:'#eab308',text:'Moyen'},{ w:'100%',color:'#10b981',text:'Fort'}];
        const lvl = levels[score - 1] || levels[0];
        fill.style.width = lvl.w; fill.style.background = lvl.color; label.style.color = lvl.color; label.textContent = lvl.text;
    }
    function checkMatch() {
        const pw1 = document.getElementById('pw1').value;
        const pw2 = document.getElementById('pw2').value;
        const msg = document.getElementById('match-msg');
        if (!pw2) { msg.className = 'match-msg'; return; }
        if (pw1 === pw2) { msg.className = 'match-msg ok'; msg.textContent = '✓ Les mots de passe correspondent'; }
        else { msg.className = 'match-msg err'; msg.textContent = '✗ Les mots de passe ne correspondent pas'; }
    }
</script>
</body>
</html>