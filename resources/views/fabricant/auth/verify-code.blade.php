<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – Vérification du code</title>
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
        .sub { font-size: 13px; color: #6b7280; text-align: center; line-height: 1.6; margin-bottom: 6px; max-width: 320px; }
        .email-highlight { font-size: 13px; font-weight: 700; color: #2E3A6B; text-align: center; margin-bottom: 20px; }

        /* Timer d'expiration — couleurs sémantiques inchangées (vert/orange/rouge) */
        .expiry-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 10px; font-size: 12.5px;
            font-weight: 600; margin-bottom: 20px; transition: all 0.3s ease;
            background: #EEF0F8; border: 1px solid #D3D8ED; color: #2E3A6B;
        }
        .expiry-badge.warning { background: #fff7ed; border-color: #fed7aa; color: #ea580c; }
        .expiry-badge.expired { background: #fef2f2; border-color: #fecaca; color: #b91c1c; width: 100%; justify-content: center; text-align: center; padding: 10px 14px; line-height: 1.4; font-weight: 500; }
        .expiry-badge svg { flex-shrink: 0; }

        .otp-row { display: flex; gap: 10px; justify-content: center; margin-bottom: 24px; width: 100%; }
        .otp-input { width: 52px; height: 58px; border: 2px solid #e5e7eb; border-radius: 14px; font-size: 22px; font-weight: 800; color: #111827; text-align: center; background: #f9fafb; font-family: inherit; outline: none; transition: border-color 0.2s, box-shadow 0.2s, background 0.2s, transform 0.15s; caret-color: #2E3A6B; }
        .otp-input:focus { border-color: #2E3A6B; box-shadow: 0 0 0 3px rgba(46,58,107,0.14); background: white; transform: scale(1.06); }
        .otp-input.filled { border-color: #2E3A6B; background: #EEF0F8; color: #2E3A6B; }
        .otp-input.expired-cell { border-color: #fca5a5 !important; background: #fef2f2 !important; color: #ef4444 !important; box-shadow: none !important; transform: none !important; }
        .otp-input.error { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.12); animation: shake 0.35s ease; }
        @keyframes shake { 0%,100% { transform: translateX(0); } 20% { transform: translateX(-5px); } 40% { transform: translateX(5px); } 60% { transform: translateX(-4px); } 80% { transform: translateX(4px); } }

        .resend-row { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 20px; min-height: 28px; }
        #resend-btn { background: none; border: none; cursor: pointer; color: #2E3A6B; font-weight: 700; font-size: 13px; font-family: inherit; padding: 0; text-decoration: underline; display: inline-flex; align-items: center; gap: 5px; transition: color 0.2s; }
        #resend-btn:hover { color: #212a52; }
        #cooldown-text { font-size: 13px; color: #9ca3af; display: none; }

        .btn { width: 100%; padding: 13px; background: #F5A623; color: #171B3D; border: none; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background 0.2s, transform 0.1s, box-shadow 0.2s; box-shadow: 0 4px 14px rgba(245,166,35,0.35); display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn:hover:not(:disabled) { background: #e0961d; box-shadow: 0 6px 20px rgba(245,166,35,0.45); }
        .btn:active:not(:disabled) { transform: scale(0.99); }
        .btn svg { width: 16px; height: 16px; }
        .btn:disabled { opacity: 0.55; cursor: not-allowed; }

        .back-link { margin-top: 22px; font-size: 12.5px; color: #6b7280; text-align: center; }
        .back-link a { color: #2E3A6B; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
        .back-link a:hover { text-decoration: underline; }
        .back-link a svg { width: 13px; height: 13px; }

        .error-box { width: 100%; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 11px; padding: 10px 14px; font-size: 13px; margin-bottom: 14px; }
        .success-box { width: 100%; background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; border-radius: 11px; padding: 10px 14px; font-size: 13px; margin-bottom: 14px; display: none; }

        .card > * { opacity: 0; animation: fadeUp 0.4s ease forwards; }
        .card > *:nth-child(1) { animation-delay: 0.08s; } .card > *:nth-child(2) { animation-delay: 0.13s; } .card > *:nth-child(3) { animation-delay: 0.17s; } .card > *:nth-child(4) { animation-delay: 0.21s; } .card > *:nth-child(5) { animation-delay: 0.24s; } .card > *:nth-child(6) { animation-delay: 0.27s; } .card > *:nth-child(7) { animation-delay: 0.30s; } .card > *:nth-child(8) { animation-delay: 0.33s; } .card > *:nth-child(9) { animation-delay: 0.36s; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 480px) {
            body { padding: 16px; align-items: flex-start; padding-top: 40px; }
            .card { padding: 32px 20px 28px; border-radius: 20px; }
            h1 { font-size: 20px; }
            .otp-row { gap: 7px; }
            .otp-input { width: 44px; height: 50px; font-size: 18px; border-radius: 12px; }
        }
        @media (max-width: 360px) {
            .otp-input { width: 38px; height: 44px; font-size: 16px; }
            .otp-row { gap: 5px; }
        }
    </style>
</head>
<body>
<div class="card">
    <div class="logo-wrapper">
        <img src="{{ asset('images/logo.png') }}" alt="VeriScan">
    </div>
    <h1>Vérification</h1>
    <p class="sub">Un code à 6 chiffres a été envoyé à</p>
    <p class="email-highlight">{{ session('reset_email', 'votre adresse email') }}</p>

    @if ($errors->any())
        <div class="error-box">{{ $errors->first() }}</div>
    @endif

    <div class="success-box" id="success-box"></div>

    <!-- Badge expiration -->
    <div class="expiry-badge" id="expiry-badge">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
        <span id="expiry-text">Code valide encore 60s</span>
    </div>

    <form method="POST" action="{{ route('fabricant.password.verify.submit') }}" style="width:100%" id="otp-form">
        @csrf
        <input type="hidden" name="code" id="hidden-code">

        <div class="otp-row">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" data-index="0">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="1">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="2">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="3">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="4">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="5">
        </div>

        <div class="resend-row">
            <button type="button" id="resend-btn" onclick="resendCode()">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Renvoyer le code
            </button>
            <span id="cooldown-text"></span>
        </div>

        <button type="submit" class="btn" id="verify-btn" disabled>
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Vérifier le code
        </button>
    </form>

    <p class="back-link">
        <a href="{{ route('fabricant.password.request') }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Changer d'email
        </a>
    </p>
</div>

<script>
    const inputs      = document.querySelectorAll('.otp-input');
    const hiddenCode  = document.getElementById('hidden-code');
    const verifyBtn   = document.getElementById('verify-btn');
    const expiryBadge = document.getElementById('expiry-badge');
    const expiryText  = document.getElementById('expiry-text');
    const resendBtn   = document.getElementById('resend-btn');
    const cooldownTxt = document.getElementById('cooldown-text');
    const successBox  = document.getElementById('success-box');
    const otpForm     = document.getElementById('otp-form');

    let codeExpired = false;

    function startExpiryTimer() {
        codeExpired = false;
        let seconds = 60;

        expiryBadge.className = 'expiry-badge';
        expiryBadge.innerHTML = `
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
            <span id="expiry-text">Code valide encore ${seconds}s</span>
        `;

        const interval = setInterval(() => {
            seconds--;
            const txt = document.querySelector('#expiry-badge span');

            if (seconds <= 0) {
                clearInterval(interval);
                codeExpired = true;

                expiryBadge.className = 'expiry-badge expired';
                expiryBadge.innerHTML = `
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    Votre code a expiré. Cliquez sur "Renvoyer le code" pour en recevoir un nouveau.
                `;

                inputs.forEach(i => {
                    i.classList.add('expired-cell');
                    i.classList.remove('filled');
                });

                return;
            }

            if (txt) {
                txt.textContent = `Code valide encore ${seconds}s`;
            }

            if (seconds <= 15) {
                expiryBadge.className = 'expiry-badge warning';
            }
        }, 1000);
    }

    startExpiryTimer();

    otpForm.addEventListener('submit', function(e) {
        if (codeExpired) {
            e.preventDefault();
            inputs.forEach(i => {
                i.classList.add('error');
                setTimeout(() => i.classList.remove('error'), 400);
            });
            return;
        }
    });

    inputs.forEach((input, i) => {
        input.addEventListener('input', (e) => {
            const val = e.target.value.replace(/\D/g, '');
            e.target.value = val;
            if (val) {
                e.target.classList.add('filled');
                if (i < inputs.length - 1) inputs[i + 1].focus();
            } else {
                e.target.classList.remove('filled');
            }
            updateHidden();
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && i > 0) {
                inputs[i - 1].focus();
                inputs[i - 1].value = '';
                inputs[i - 1].classList.remove('filled');
                updateHidden();
            }
        });
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            pasted.split('').forEach((ch, idx) => {
                if (inputs[idx]) { inputs[idx].value = ch; inputs[idx].classList.add('filled'); }
            });
            if (inputs[pasted.length]) inputs[pasted.length].focus();
            else inputs[5].focus();
            updateHidden();
        });
    });

    function updateHidden() {
        const code = Array.from(inputs).map(i => i.value).join('');
        hiddenCode.value = code;
        verifyBtn.disabled = code.length < 6 || codeExpired;
    }

    function startCooldown() {
        let cooldown = 60;
        resendBtn.style.display = 'none';
        cooldownTxt.style.display = 'inline';
        cooldownTxt.textContent = `Renvoyer dans ${cooldown}s`;
        const interval = setInterval(() => {
            cooldown--;
            cooldownTxt.textContent = `Renvoyer dans ${cooldown}s`;
            if (cooldown <= 0) {
                clearInterval(interval);
                cooldownTxt.style.display = 'none';
                resendBtn.style.display = 'inline-flex';
            }
        }, 1000);
    }

    async function resendCode() {
        resendBtn.disabled = true;
        try {
            await fetch("{{ route('fabricant.password.email') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ email: '{{ session('reset_email') }}' }),
            });

            inputs.forEach(i => {
                i.value = '';
                i.classList.remove('filled', 'expired-cell');
            });
            updateHidden();
            inputs[0].focus();

            successBox.textContent = 'Nouveau code envoyé avec succès !';
            successBox.style.display = 'block';
            setTimeout(() => successBox.style.display = 'none', 4000);

            startExpiryTimer();

        } catch (e) {
            console.error('Erreur renvoi:', e);
        } finally {
            resendBtn.disabled = false;
            startCooldown();
        }
    }

    inputs[0].focus();
</script>
</body>
</html>