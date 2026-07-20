@php
    $fabricantConnecte = (bool) $fabricant;
    $planActuel = $planActif;
@endphp

<style>
    .pricing-section, .faq-section {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    .pricing-section { max-width: 1100px; margin: 0 auto; padding: 12px 0 0; }
    .pricing-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; align-items: start; }
    .plan-card { background: white; border-radius: 16px; border: 1.5px solid #e5e7eb; padding: 18px 16px; display: flex; flex-direction: column; gap: 10px; position: relative; transition: transform 0.15s, box-shadow 0.15s; }
    .plan-card:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,0.07); }
    .plan-card.popular { border-color: #F5A623; box-shadow: 0 6px 20px rgba(245,166,35,0.15); }
    .popular-badge { position: absolute; top: -11px; left: 50%; transform: translateX(-50%); background: #F5A623; color: #212a52; font-size: 10px; font-weight: 800; padding: 3px 12px; border-radius: 20px; white-space: nowrap; letter-spacing: 0.05em; }
    .plan-name { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.07em; }
    .plan-price { display: flex; align-items: baseline; gap: 3px; flex-wrap: nowrap; }
    .plan-price .amount { font-size: 22px; font-weight: 900; color: #1F2937; white-space: nowrap; }
    .plan-price .currency { font-size: 12px; font-weight: 600; color: #6b7280; white-space: nowrap; }
    .plan-price .period { font-size: 11px; color: #6b7280; white-space: nowrap; }
    .plan-free .amount { color: #2E3A6B; }
    .plan-desc { font-size: 12px; color: #6b7280; line-height: 1.45; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    .plan-features { display: flex; flex-direction: column; gap: 6px; }
    .plan-feature { display: flex; align-items: flex-start; gap: 7px; font-size: 12.5px; color: #1F2937; line-height: 1.35; }
    .plan-feature svg { width: 14px; height: 14px; flex-shrink: 0; margin-top: 1px; }
    .plan-feature.included svg { color: #2E3A6B; }
    .plan-feature.excluded { color: #9ca3af; }
    .plan-feature.excluded svg { color: #d1d5db; }
    .current-plan-badge { display: inline-flex; align-items: center; gap: 5px; background: #EEF0F8; color: #2E3A6B; font-size: 10px; font-weight: 800; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 4px; }
    .btn-plan { display: block; width: 100%; padding: 9px; margin-top: 4px; border-radius: 10px; font-size: 13px; font-weight: 700; text-align: center; cursor: pointer; text-decoration: none; transition: all 0.15s; border: none; font-family: inherit; background: #2E3A6B; color: white; }
    .btn-plan:hover { background: #212a52; }
    .btn-plan.outline { background: white; color: #2E3A6B; border: 1.5px solid #2E3A6B; }
    .btn-plan.outline:hover { background: #EEF0F8; }
    .btn-plan.disabled { background: #e5e7eb; color: #9ca3af; cursor: default; pointer-events: none; }
    .plan-card.popular .btn-plan { background: #F5A623; color: #212a52; }
    .plan-card.popular .btn-plan:hover { background: #e0961d; }
    .faq-section { max-width: 680px; margin: 0 auto; padding: 32px 0 12px; }
    .faq-title { font-size: 19px; font-weight: 800; color: #1F2937; text-align: center; margin-bottom: 16px; }
    .faq-item { border: 1.5px solid #e5e7eb; border-radius: 12px; margin-bottom: 8px; overflow: hidden; background: white; }
    .faq-question { width: 100%; text-align: left; padding: 12px 16px; background: white; border: none; font-size: 13px; font-weight: 600; color: #1F2937; cursor: pointer; font-family: inherit; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .faq-question svg { width: 16px; height: 16px; color: #2E3A6B; flex-shrink: 0; transition: transform 0.2s; }
    .faq-question.open svg { transform: rotate(180deg); }
    .faq-answer { display: none; padding: 0 16px 12px; font-size: 13px; color: #6b7280; line-height: 1.6; }
    .faq-answer.open { display: block; }
    @media (max-width: 1024px) { .pricing-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; } }
    @media (max-width: 768px) { .pricing-grid { grid-template-columns: 1fr; gap: 14px; } .plan-card.popular { margin-top: 6px; } .btn-plan { font-size: 12px; padding: 9px 6px; white-space: nowrap; } }
</style>

<div class="pricing-section">
    <div class="pricing-grid">

        {{-- Gratuit --}}
        <div class="plan-card plan-free">
            <div>
                <div class="plan-name">{{ $locale === 'en' ? 'Free' : 'Gratuit' }}</div>
                <div class="plan-price"><span class="amount">0</span><span class="currency">FCFA</span><span class="period">/{{ $locale === 'en' ? 'month' : 'mois' }}</span></div>
                @if($planActuel === 'gratuit')
                    <div class="current-plan-badge" style="margin-top:8px;">{{ $locale === 'en' ? 'Current plan' : 'Plan actuel' }}</div>
                @endif
            </div>
            <div class="plan-desc">{{ $locale === 'en' ? 'For artisans and small manufacturers just getting started.' : 'Pour les artisans et petits fabricants qui démarrent.' }}</div>
            <div class="plan-features">
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? '1 registered product' : '1 produit enregistré' }}</div>
                <div class="plan-feature included"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $locale === 'en' ? '50 QR codes / month' : '50 QR codes / mois' }}</div>
                <div class="plan-feature excluded"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>{{ $locale === 'en' ? 'Statistics' : 'Statistiques' }}</div>
                <div class="plan-feature excluded"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>{{ $locale === 'en' ? 'PDF reports' : 'Rapports PDF' }}</div>
                <div class="plan-feature excluded"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>{{ $locale === 'en' ? 'Risk map' : 'Carte des risques' }}</div>
                <div class="plan-feature excluded"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>{{ $locale === 'en' ? 'Priority support' : 'Support prioritaire' }}</div>
            </div>
            @if($planActuel === 'gratuit')
                <span class="btn-plan disabled">{{ $locale === 'en' ? 'Current plan' : 'Plan actuel' }}</span>
            @elseif($fabricantConnecte)
                <span class="btn-plan outline disabled">{{ $locale === 'en' ? 'Contact support to downgrade' : 'Contactez le support pour rétrograder' }}</span>
            @else
                <a href="{{ route('fabricant.register') }}" class="btn-plan outline">{{ $locale === 'en' ? 'Start for free' : 'Commencer gratuitement' }}</a>
            @endif
        </div>

        {{-- Starter --}}
        <div class="plan-card">
            <div>
                <div class="plan-name">Starter</div>
                <div class="plan-price"><span class="amount">2 000</span><span class="currency">FCFA</span><span class="period">/{{ $locale === 'en' ? 'month' : 'mois' }}</span></div>
                @if($planActuel === 'starter')
                    <div class="current-plan-badge" style="margin-top:8px;">{{ $locale === 'en' ? 'Current plan' : 'Plan actuel' }}</div>
                @endif
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
            @if($planActuel === 'starter')
                <span class="btn-plan disabled">{{ $locale === 'en' ? 'Current plan' : 'Plan actuel' }}</span>
            @else
                <a href="{{ route('paiement.checkout', 'starter') }}" class="btn-plan">{{ $fabricantConnecte ? ($locale === 'en' ? 'Switch to Starter' : 'Passer à Starter') : ($locale === 'en' ? 'Subscribe' : 'Souscrire') }}</a>
            @endif
        </div>

        {{-- Pro --}}
        <div class="plan-card popular">
            <div class="popular-badge">⭐ {{ $locale === 'en' ? 'POPULAR' : 'POPULAIRE' }}</div>
            <div>
                <div class="plan-name">Pro</div>
                <div class="plan-price"><span class="amount">5 000</span><span class="currency">FCFA</span><span class="period">/{{ $locale === 'en' ? 'month' : 'mois' }}</span></div>
                @if($planActuel === 'pro')
                    <div class="current-plan-badge" style="margin-top:8px;">{{ $locale === 'en' ? 'Current plan' : 'Plan actuel' }}</div>
                @endif
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
            @if($planActuel === 'pro')
                <span class="btn-plan disabled">{{ $locale === 'en' ? 'Current plan' : 'Plan actuel' }}</span>
            @else
                <a href="{{ route('paiement.checkout', 'pro') }}" class="btn-plan">{{ $fabricantConnecte ? ($locale === 'en' ? 'Switch to Pro' : 'Passer à Pro') : ($locale === 'en' ? 'Subscribe to Pro' : 'Souscrire au plan Pro') }}</a>
            @endif
        </div>

        {{-- Entreprise --}}
        <div class="plan-card">
            <div>
                <div class="plan-name">{{ $locale === 'en' ? 'Enterprise' : 'Entreprise' }}</div>
                <div class="plan-price"><span class="amount">10 000</span><span class="currency">FCFA</span><span class="period">/{{ $locale === 'en' ? 'month' : 'mois' }}</span></div>
                @if($planActuel === 'entreprise')
                    <div class="current-plan-badge" style="margin-top:8px;">{{ $locale === 'en' ? 'Current plan' : 'Plan actuel' }}</div>
                @endif
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
            @if($planActuel === 'entreprise')
                <span class="btn-plan disabled">{{ $locale === 'en' ? 'Current plan' : 'Plan actuel' }}</span>
            @else
                <a href="{{ route('paiement.checkout', 'entreprise') }}" class="btn-plan">{{ $fabricantConnecte ? ($locale === 'en' ? 'Switch to Enterprise' : 'Passer à Entreprise') : ($locale === 'en' ? 'Subscribe — Enterprise' : 'Souscrire plan Entreprise') }}</a>
            @endif
        </div>

    </div>
</div>

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
        <div class="faq-answer">{{ $locale === 'en' ? 'Yes, you can upgrade your plan at any time from this page once logged in. The new plan takes effect as soon as your payment is confirmed.' : 'Oui, vous pouvez changer de plan à tout moment depuis cette page une fois connecté. Le nouveau plan prend effet dès que votre paiement est confirmé.' }}</div>
    </div>
    <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">{{ $locale === 'en' ? 'Is my data secure?' : 'Mes données sont-elles sécurisées ?' }}<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg></button>
        <div class="faq-answer">{{ $locale === 'en' ? 'All data is encrypted and stored securely. QR codes use HMAC-SHA256 cryptography, making any forgery technically impossible.' : 'Toutes les données sont chiffrées et stockées de manière sécurisée. Les QR codes utilisent la cryptographie HMAC-SHA256, rendant toute falsification techniquement impossible.' }}</div>
    </div>
</div>

<script>
function toggleFaq(btn) {
    btn.classList.toggle('open');
    btn.nextElementSibling.classList.toggle('open');
}
</script>