@extends('layouts.fabricant')

@section('title', __('messages.mon_profil'))

@section('topbar-title')
    {{ __('messages.mon') }} <span>{{ __('messages.profil') }}</span>
@endsection

@section('styles')
<style>
    .page-header { margin-bottom: 28px; }
    .page-header h1 { font-size: 24px; font-weight: 800; color: var(--text); }
    .page-header p { font-size: 14px; color: var(--text-light); margin-top: 4px; }
    .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #9ca3af; margin-bottom: 8px; }
    .breadcrumb a { color: var(--teal); text-decoration: none; font-weight: 500; }
    .breadcrumb span { color: #d1d5db; }
    .profil-layout { display: grid; grid-template-columns: 280px 1fr; gap: 20px; align-items: start; }
    .profil-sidebar-col { display: flex; flex-direction: column; gap: 14px; }
    .profil-card { background: white; border: 1.5px solid var(--border); border-radius: 16px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); }
    .identity-card { text-align: center; }
    .logo-wrap { width: 90px; height: 90px; border-radius: 16px; background: var(--teal-light); border: 2px dashed var(--teal); display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; overflow: hidden; cursor: pointer; transition: all 0.2s; }
    .logo-wrap:hover { background: #e6f7f5; border-style: solid; }
    .logo-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .logo-placeholder { display: flex; flex-direction: column; align-items: center; gap: 5px; }
    .logo-placeholder svg { width: 26px; height: 26px; color: var(--teal); opacity: 0.6; }
    .logo-placeholder span { font-size: 11px; color: var(--text-light); }
    .identity-name { font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 3px; }
    .identity-email { font-size: 12px; color: var(--text-light); margin-bottom: 14px; }
    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 700; }
    .status-badge.actif { background: #f0fdf4; color: #16a34a; }
    .status-badge.en_attente { background: #fefce8; color: #ca8a04; }
    .status-badge.suspendu { background: #fef2f2; color: #dc2626; }
    .status-dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-badge.actif .status-dot { background: #16a34a; }
    .status-badge.en_attente .status-dot { background: #ca8a04; animation: pulse 2s infinite; }
    .status-badge.suspendu .status-dot { background: #dc2626; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
    .quick-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 16px; }
    .quick-stat { background: var(--teal-light); border-radius: 10px; padding: 12px; text-align: center; }
    .quick-stat-val { font-size: 20px; font-weight: 800; color: var(--teal); }
    .quick-stat-lbl { font-size: 10.5px; color: var(--text-light); margin-top: 2px; }
    .tab-nav { display: flex; flex-direction: column; gap: 3px; }
    .tab-nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 13px; border-radius: 10px; font-size: 13.5px; font-weight: 500; color: var(--text-light); cursor: pointer; transition: all 0.2s; border: none; background: none; width: 100%; text-align: left; font-family: inherit; }
    .tab-nav-item:hover { background: var(--teal-light); color: var(--teal); }
    .tab-nav-item.active { background: var(--teal); color: white; font-weight: 700; }
    .tab-nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }
    .profil-main { display: flex; flex-direction: column; }
    .tab-panel { display: none; }
    .tab-panel.active { display: flex; flex-direction: column; gap: 16px; }
    .section-card { background: white; border: 1.5px solid var(--border); border-radius: 16px; padding: 28px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); }
    .section-card-header { margin-bottom: 24px; }
    .section-card-header h2 { font-size: 17px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
    .section-card-header p { font-size: 13px; color: var(--text-light); }

    /* ── Grille formulaire : 1 colonne partout ── */
    .form-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label { font-size: 13px; font-weight: 600; color: #374151; }
    .field input { padding: 11px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 14px; color: var(--text); outline: none; font-family: inherit; transition: border-color 0.2s, box-shadow 0.2s; background: white; width: 100%; box-sizing: border-box; }
    .field input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(15,118,110,0.12); }
    .field input:disabled { background: #f9fafb; color: #9ca3af; }

    .btn-save { padding: 11px 24px; background: var(--teal); color: white; border: none; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; margin-top: 20px; }
    .btn-save:hover { background: #0a5c55; transform: translateY(-1px); }
    .alert { padding: 12px 16px; border-radius: 10px; font-size: 13.5px; display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
    .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
    .alert svg { width: 16px; height: 16px; flex-shrink: 0; }
    .pwd-bars { display: flex; gap: 4px; margin: 6px 0 3px; }
    .pwd-bar { height: 4px; flex: 1; border-radius: 2px; background: #e5e7eb; transition: background 0.3s; }
    .pwd-bar.weak { background: #ef4444; }
    .pwd-bar.medium { background: #f59e0b; }
    .pwd-bar.strong { background: #10b981; }
    .pwd-label { font-size: 11px; color: var(--text-light); }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .profil-layout { grid-template-columns: 1fr; }
        .section-card { padding: 20px 16px; }
    }
</style>
@endsection

@section('content')

    <div class="page-header">
        <div class="breadcrumb">
            <a href="{{ route('fabricant.dashboard') }}">{{ __('messages.dashboard') }}</a>
            <span>›</span>
            <span>{{ __('messages.mon_profil') }}</span>
        </div>
        <h1>{{ __('messages.mon_profil') }}</h1>
        <p>{{ __('messages.profil_desc') }}</p>
    </div>

    <div class="profil-layout">

        {{-- COLONNE GAUCHE --}}
        <div class="profil-sidebar-col">
            <div class="profil-card identity-card">
                <div class="logo-wrap" id="logoPreviewWrap">
                    @if($fabricant->logo)
                        <img src="{{ Storage::url($fabricant->logo) }}" alt="Logo">
                    @else
                        <div class="logo-placeholder">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>{{ __('messages.votre_logo') }}</span>
                        </div>
                    @endif
                </div>
                <form method="POST" action="{{ route('fabricant.profil.logo') }}" enctype="multipart/form-data" style="margin-bottom:14px;">
                    @csrf
                    @if(session('success_logo'))
                        <div style="font-size:12px;color:#15803d;font-weight:600;margin-bottom:8px;">✓ {{ __('messages.logo_mis_a_jour') }}</div>
                    @endif
                    <label style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:var(--teal-light);border:1.5px solid var(--teal);border-radius:10px;font-size:13px;font-weight:700;color:var(--teal);cursor:pointer;transition:all 0.2s;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        {{ __('messages.changer_logo') }}
                        <input type="file" name="logo" accept="image/*" style="display:none;" onchange="previewAndSubmit(this)">
                    </label>
                </form>
                <div class="identity-name">{{ $fabricant->nom_entreprise }}</div>
                <div class="identity-email">{{ $fabricant->email }}</div>
                @php $statut = $fabricant->statut; @endphp
                <span class="status-badge {{ $statut }}">
                    <span class="status-dot"></span>
                    @if($statut === 'actif') {{ __('messages.compte_actif') }}
                    @elseif($statut === 'en_attente') {{ __('messages.en_attente_validation') }}
                    @else {{ __('messages.compte_suspendu') }}
                    @endif
                </span>
                <div class="quick-stats">
                    <div class="quick-stat"><div class="quick-stat-val">0</div><div class="quick-stat-lbl">{{ __('messages.mes_produits') }}</div></div>
                    <div class="quick-stat"><div class="quick-stat-val">0</div><div class="quick-stat-lbl">QR Codes</div></div>
                    <div class="quick-stat"><div class="quick-stat-val">0</div><div class="quick-stat-lbl">{{ __('messages.scans') }}</div></div>
                    <div class="quick-stat"><div class="quick-stat-val">0</div><div class="quick-stat-lbl">{{ __('messages.signalements') }}</div></div>
                </div>
            </div>

            <div class="profil-card" style="padding:10px;">
                <div class="tab-nav">
                    <button class="tab-nav-item active" onclick="showTab('infos', this)">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ __('messages.infos_generales') }}
                    </button>
                    <button class="tab-nav-item" onclick="showTab('password', this)">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        {{ __('messages.changer_mdp') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE --}}
        <div class="profil-main">

            {{-- TAB INFOS --}}
            <div class="tab-panel active" id="tab-infos">
                <div class="section-card">
                    <div class="section-card-header">
                        <h2>{{ __('messages.infos_generales') }}</h2>
                        <p>{{ __('messages.infos_generales_desc') }}</p>
                    </div>
                    @if(session('success_infos'))
                        <div class="alert alert-success">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ session('success_infos') }}
                        </div>
                    @endif
                    @if($errors->any() && !session('tab') && !$errors->has('logo'))
                        <div class="alert alert-error">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/></svg>
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('fabricant.profil.infos') }}">
                        @csrf
                        <div class="form-grid">
                            <div class="field">
                                <label>{{ __('messages.nom_entreprise_label') }} *</label>
                                <input type="text" name="nom_entreprise" value="{{ old('nom_entreprise', $fabricant->nom_entreprise) }}" required>
                            </div>
                            <div class="field">
                                <label>{{ __('messages.adresse_email') }} *</label>
                                <input type="email" name="email" value="{{ old('email', $fabricant->email) }}" required>
                            </div>
                            <div class="field">
                                <label>{{ __('messages.telephone') }}</label>
                                <input type="text" name="telephone" value="{{ old('telephone', $fabricant->telephone) }}" placeholder="+237 6XX XXX XXX">
                            </div>
                            <div class="field">
                                <label>{{ __('messages.adresse') }}</label>
                                <input type="text" name="adresse" value="{{ old('adresse', $fabricant->adresse) }}" placeholder="{{ __('messages.adresse_placeholder') }}">
                            </div>
                            <div class="field">
                                <label>{{ __('messages.pays') }}</label>
                                <input type="text" name="pays" value="{{ old('pays', $fabricant->pays) }}">
                            </div>
                            <div class="field">
                                <label>{{ __('messages.membre_depuis') }}</label>
                                <input type="text" value="{{ $fabricant->created_at->format('d/m/Y') }}" disabled>
                            </div>
                        </div>
                        <button type="submit" class="btn-save">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ __('messages.enregistrer_modifications') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- TAB PASSWORD --}}
            <div class="tab-panel" id="tab-password">
                <div class="section-card">
                    <div class="section-card-header">
                        <h2>{{ __('messages.changer_mdp') }}</h2>
                        <p>{{ __('messages.changer_mdp_desc') }}</p>
                    </div>
                    @if(session('success_password'))
                        <div class="alert alert-success">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ session('success_password') }}
                        </div>
                    @endif
                    @if($errors->has('current_password'))
                        <div class="alert alert-error">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ $errors->first('current_password') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('fabricant.profil.password') }}" style="max-width:480px;">
                        @csrf
                        <div class="form-grid">
                            <div class="field">
                                <label>{{ __('messages.mdp_actuel') }} *</label>
                                <input type="password" name="current_password" required placeholder="••••••••">
                            </div>
                            <div class="field">
                                <label>{{ __('messages.nouveau_mdp') }} *</label>
                                <input type="password" name="password" required placeholder="{{ __('messages.mdp_placeholder') }}" oninput="checkStrength(this.value)">
                                <div class="pwd-bars">
                                    <div class="pwd-bar" id="bar1"></div>
                                    <div class="pwd-bar" id="bar2"></div>
                                    <div class="pwd-bar" id="bar3"></div>
                                    <div class="pwd-bar" id="bar4"></div>
                                </div>
                                <span class="pwd-label" id="pwdLabel">{{ __('messages.entrez_mdp') }}</span>
                            </div>
                            <div class="field">
                                <label>{{ __('messages.confirmer_nouveau_mdp') }} *</label>
                                <input type="password" name="password_confirmation" required placeholder="{{ __('messages.repetez_mdp') }}">
                            </div>
                        </div>
                        <button type="submit" class="btn-save">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            {{ __('messages.mettre_a_jour_mdp') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
<script>
    function showTab(tab, btn) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-nav-item').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
        btn.classList.add('active');
    }

    @if(session('tab'))
        document.addEventListener('DOMContentLoaded', () => {
            const tab = '{{ session("tab") }}';
            const btn = document.querySelector(`[onclick="showTab('${tab}', this)"]`);
            if (btn) showTab(tab, btn);
        });
    @endif

    function previewAndSubmit(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                const wrap = document.getElementById('logoPreviewWrap');
                wrap.innerHTML = `<img src="${e.target.result}" alt="Logo" style="width:100%;height:100%;object-fit:cover;">`;
                const avatar = document.querySelector('.user-avatar');
                if (avatar) avatar.innerHTML = `<img src="${e.target.result}" alt="Logo" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
            };
            reader.readAsDataURL(input.files[0]);
            input.form.submit();
        }
    }

    function checkStrength(pwd) {
        const bars = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3'), document.getElementById('bar4')];
        const label = document.getElementById('pwdLabel');
        bars.forEach(b => { b.className = 'pwd-bar'; });
        let score = 0;
        if (pwd.length >= 8) score++;
        if (/[A-Z]/.test(pwd)) score++;
        if (/[0-9]/.test(pwd)) score++;
        if (/[^A-Za-z0-9]/.test(pwd)) score++;
        const colors = ['', 'weak', 'medium', 'strong', 'strong'];
        const labels = [
            '{{ __("messages.pwd_trop_court") }}',
            '{{ __("messages.pwd_faible") }}',
            '{{ __("messages.pwd_moyen") }}',
            '{{ __("messages.pwd_fort") }}',
            '{{ __("messages.pwd_tres_fort") }}'
        ];
        for (let i = 0; i < score; i++) bars[i].classList.add(colors[score]);
        label.textContent = labels[score] || '{{ __("messages.entrez_mdp") }}';
    }
</script>
@endsection