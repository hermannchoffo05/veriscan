<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – {{ $locale === 'en' ? 'Payment' : 'Paiement' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --teal: #0F766E; --teal-dark: #0a5c55; --teal-light: #F0FDFA; --text: #1F2937; --text-light: #6b7280; --border: #e5e7eb; --mtn: #FFCC00; --orange: #FF6600; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }
        nav { background: white; border-bottom: 1px solid var(--border); padding: 0 32px; height: 64px; display: flex; align-items: center; justify-content: space-between; }
        .nav-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .nav-logo img.logo-brand { width: 40px; height: 40px; object-fit: contain; }
        .nav-logo-text { font-size: 20px; font-weight: 900; color: var(--text); }
        .nav-logo-text span { color: var(--teal); }
        .nav-back { font-size: 13px; font-weight: 600; color: var(--text-light); text-decoration: none; display: flex; align-items: center; gap: 6px; }
        .nav-back:hover { color: var(--teal); }
        .nav-back svg { width: 16px; height: 16px; }
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .checkout-wrap { width: 100%; max-width: 480px; }
        .plan-badge { background: var(--teal); color: white; border-radius: 14px; padding: 20px 24px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; }
        .plan-badge-name { font-size: 18px; font-weight: 800; }
        .plan-badge-price { font-size: 22px; font-weight: 900; }
        .plan-badge-sub { font-size: 12px; opacity: 0.8; margin-top: 2px; }
        .checkout-card { background: white; border-radius: 20px; border: 1.5px solid var(--border); padding: 28px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
        .checkout-title { font-size: 18px; font-weight: 800; color: var(--text); margin-bottom: 6px; }
        .checkout-sub { font-size: 13px; color: var(--text-light); margin-bottom: 24px; }
        .operateurs { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
        .op-card { border: 2px solid var(--border); border-radius: 12px; padding: 16px 12px; cursor: pointer; transition: all 0.2s; text-align: center; position: relative; }
        .op-card:hover { border-color: var(--teal); }
        .op-card.selected-mtn { border-color: var(--mtn); background: #fffbeb; box-shadow: 0 0 0 3px rgba(255,204,0,0.15); }
        .op-card.selected-orange { border-color: var(--orange); background: #fff7f0; box-shadow: 0 0 0 3px rgba(255,102,0,0.15); }
        .op-card input[type="radio"] { position: absolute; opacity: 0; }
        .op-logo-wrap { width: 72px; height: 56px; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; }
        .op-logo-wrap img { width: 100%; height: 100%; object-fit: contain; }
        .op-name { font-size: 13px; font-weight: 700; color: var(--text); }
        .op-desc { font-size: 11px; color: var(--text-light); margin-top: 2px; }
        .field { margin-bottom: 20px; }
        .field label { display: block; font-size: 12px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px; }
        .tel-input-wrap { display: flex; border: 1.5px solid var(--border); border-radius: 10px; overflow: hidden; transition: border-color 0.2s; }
        .tel-input-wrap:focus-within { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(15,118,110,0.1); }
        .tel-prefix { padding: 12px 14px; background: #f9fafb; border-right: 1.5px solid var(--border); font-size: 14px; font-weight: 600; color: var(--text-light); white-space: nowrap; }
        .tel-input { flex: 1; padding: 12px 14px; border: none; outline: none; font-size: 15px; font-family: inherit; letter-spacing: 0.05em; color: var(--text); }
        .tel-hint { font-size: 11px; color: var(--text-light); margin-top: 6px; }
        .error-msg { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 16px; }
        .btn-pay { width: 100%; padding: 14px; background: var(--teal); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-pay:hover { background: var(--teal-dark); transform: translateY(-1px); }
        .btn-pay:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .security-note { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-light); margin-top: 16px; justify-content: center; }
        .security-note svg { width: 14px; height: 14px; color: var(--teal); }
        @media (max-width: 480px) { nav { padding: 0 16px; } .checkout-card { padding: 20px 16px; } }
        @keyframes spin { from { transform: rotate(0deg) } to { transform: rotate(360deg) } }
    </style>
</head>
<body>
<nav>
    <a href="{{ url('/') }}" class="nav-logo">
        <img src="{{ asset('images/logo.png') }}" alt="VeriScan" class="logo-brand">
        <span class="nav-logo-text">Veri<span>Scan</span></span>
    </a>
    <a href="{{ route('tarifs') }}" class="nav-back">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ $locale === 'en' ? 'Back to pricing' : 'Retour aux tarifs' }}
    </a>
</nav>

<main>
    <div class="checkout-wrap">
        <div class="plan-badge">
            <div>
                <div class="plan-badge-name">Plan {{ ucfirst($plan) }}</div>
                <div class="plan-badge-sub">{{ $locale === 'en' ? 'Monthly subscription' : 'Abonnement mensuel' }}</div>
            </div>
            <div>
                <div class="plan-badge-price">{{ number_format($planData['montant'], 0, '.', ' ') }} XAF</div>
                <div class="plan-badge-sub" style="text-align:right;">/{{ $locale === 'en' ? 'month' : 'mois' }}</div>
            </div>
        </div>

        <div class="checkout-card">
            <div class="checkout-title">{{ $locale === 'en' ? 'Mobile Money Payment' : 'Paiement Mobile Money' }}</div>
            <div class="checkout-sub">{{ $locale === 'en' ? 'Choose your operator and enter your number to receive the payment notification.' : 'Choisissez votre opérateur et entrez votre numéro pour recevoir la notification de paiement.' }}</div>

            @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('paiement.initier') }}" id="payForm">
                @csrf
                <input type="hidden" name="plan" value="{{ $plan }}">

                <div class="operateurs">
                    <label class="op-card" id="card-mtn" onclick="selectOp('mtn')">
                        <input type="radio" name="operateur" value="mtn" required>
                        <div class="op-logo-wrap">
                            <img src="{{ asset('images/Mtn.png') }}" alt="MTN MoMo">
                        </div>
                        <div class="op-name">MTN MoMo</div>
                        <div class="op-desc">{{ $locale === 'en' ? 'Numbers 67x, 68x, 650...' : 'Numéros 67x, 68x, 650...' }}</div>
                    </label>
                    <label class="op-card" id="card-orange" onclick="selectOp('orange')">
                        <input type="radio" name="operateur" value="orange" required>
                        <div class="op-logo-wrap">
                            <img src="{{ asset('images/orange.webp') }}" alt="Orange Money">
                        </div>
                        <div class="op-name">Orange Money</div>
                        <div class="op-desc">{{ $locale === 'en' ? 'Numbers 69x, 655...' : 'Numéros 69x, 655...' }}</div>
                    </label>
                </div>

                <div class="field">
                    <label>{{ $locale === 'en' ? 'Phone number *' : 'Numéro de téléphone *' }}</label>
                    <div class="tel-input-wrap">
                        <span class="tel-prefix">🇨🇲 +237</span>
                        <input type="tel" name="telephone" class="tel-input"
                               placeholder="6XX XXX XXX" maxlength="9" pattern="[0-9]{9}" required
                               oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    </div>
                    <div class="tel-hint">{{ $locale === 'en' ? 'You will receive a notification to confirm payment with your PIN.' : 'Vous recevrez une notification pour confirmer le paiement avec votre code PIN.' }}</div>
                </div>

                <button type="submit" class="btn-pay" id="btnPay">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    {{ $locale === 'en' ? 'Pay ' . number_format($planData['montant'], 0, '.', ' ') . ' XAF' : 'Payer ' . number_format($planData['montant'], 0, '.', ' ') . ' XAF' }}
                </button>

                <div class="security-note">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    {{ $locale === 'en' ? 'Secured by CamPay · SSL encrypted' : 'Sécurisé par CamPay · Chiffrement SSL' }}
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function selectOp(op) {
    document.getElementById('card-mtn').className    = 'op-card' + (op === 'mtn'    ? ' selected-mtn'    : '');
    document.getElementById('card-orange').className = 'op-card' + (op === 'orange' ? ' selected-orange' : '');
    document.querySelector('input[value="' + op + '"]').checked = true;
}
document.getElementById('payForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnPay');
    btn.disabled = true;
    btn.innerHTML = '<svg style="animation:spin 1s linear infinite;width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> {{ $locale === 'en' ? 'Processing...' : 'Traitement...' }}';
});
</script>
</body>
</html>