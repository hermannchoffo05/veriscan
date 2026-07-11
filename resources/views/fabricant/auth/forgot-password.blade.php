<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – Mot de passe oublié</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', system-ui, sans-serif; background: #dde1f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 24px; }
        .card { width: 100%; max-width: 440px; background: white; border-radius: 24px; box-shadow: 0 30px 80px rgba(0,0,0,0.18); padding: 44px 44px 36px; display: flex; flex-direction: column; align-items: center; animation: cardIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }
        @keyframes cardIn { from { opacity: 0; transform: translateY(24px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .logo-wrapper { width: 110px; height: 110px; border-radius: 50%; background: white center 55% / 85% no-repeat; box-shadow: 0 4px 6px rgba(0,0,0,0.04), 0 10px 30px rgba(46,58,107,0.10), 0 20px 50px rgba(46,58,107,0.07); border: 1.5px solid rgba(46,58,107,0.08); margin-bottom: 28px; transition: transform 0.35s ease; }
        .logo-wrapper:hover { transform: scale(1.03); }
       
        h1 { font-size: 24px; font-weight: 800; color: #111827; text-align: center; margin-bottom: 8px; }
        .sub { font-size: 13px; color: #6b7280; text-align: center; line-height: 1.6; margin-bottom: 28px; max-width: 320px; }
        .form-full { width: 100%; }
        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }
        label { font-size: 12.5px; font-weight: 600; color: #374151; }
        .iw { position: relative; }
        .iw > svg { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #9ca3af; pointer-events: none; }
        input[type=email] { width: 100%; padding: 11px 14px 11px 40px; border: 1.5px solid #e5e7eb; border-radius: 12px; font-size: 13.5px; color: #111827; background: #f9fafb; font-family: inherit; outline: none; transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; }
        input[type=email]:focus { border-color: #2E3A6B; box-shadow: 0 0 0 3px rgba(46,58,107,0.12); background: white; }
        .btn { width: 100%; padding: 13px; background: #F5A623; color: #171B3D; border: none; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background 0.2s, transform 0.1s, box-shadow 0.2s; box-shadow: 0 4px 14px rgba(245,166,35,0.35); margin-top: 4px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn:hover { background: #e0961d; box-shadow: 0 6px 20px rgba(245,166,35,0.45); }
        .btn:active { transform: scale(0.99); }
        .btn svg { width: 16px; height: 16px; }
        .back-link { margin-top: 22px; font-size: 12.5px; color: #6b7280; text-align: center; }
        .back-link a { color: #2E3A6B; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
        .back-link a:hover { text-decoration: underline; }
        .back-link a svg { width: 13px; height: 13px; }
        .error-box { width: 100%; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 11px; padding: 10px 14px; font-size: 13px; margin-bottom: 14px; }
        .success-box { width: 100%; background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; border-radius: 11px; padding: 10px 14px; font-size: 13px; margin-bottom: 14px; }
        .card > * { opacity: 0; animation: fadeUp 0.4s ease forwards; }
        .card > *:nth-child(1) { animation-delay: 0.08s; }
        .card > *:nth-child(2) { animation-delay: 0.13s; }
        .card > *:nth-child(3) { animation-delay: 0.17s; }
        .card > *:nth-child(4) { animation-delay: 0.21s; }
        .card > *:nth-child(5) { animation-delay: 0.25s; }
        .card > *:nth-child(6) { animation-delay: 0.29s; }
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
    <h1>Mot de passe oublié ?</h1>
    <p class="sub">Entrez votre adresse email et nous vous enverrons un code de vérification à 6 chiffres.</p>
    <div class="form-full">
        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif
        @if (session('status'))
            <div class="success-box">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('fabricant.password.email') }}">
            @csrf
            <div class="field">
                <label>Adresse email</label>
                <div class="iw">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="hermannchoffo05@gmail.com" autofocus>
                </div>
            </div>
            <button type="submit" class="btn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Envoyer le code
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
</body>
</html>