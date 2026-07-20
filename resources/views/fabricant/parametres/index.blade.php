@extends('layouts.fabricant')

@section('title', __('messages.parametres'))

@section('topbar-title')
    {{ __('messages.parametres') }} <span>{{ __('messages.du_compte') }}</span>
@endsection

@section('styles')
<style>
    .params-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; max-width: 960px; }
    .param-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; }
    .param-card-header { padding: 20px 24px 16px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
    .param-card-icon { width: 38px; height: 38px; border-radius: 10px; background: var(--teal-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .param-card-icon svg { width: 18px; height: 18px; color: var(--teal); }
    .param-card-title { font-size: 14px; font-weight: 700; color: var(--text); }
    .param-card-subtitle { font-size: 12px; color: var(--text-light); margin-top: 2px; }
    .param-card-body { padding: 20px 24px; }
    .toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border); gap: 12px; }
    .toggle-row:last-child { border-bottom: none; padding-bottom: 0; }
    .toggle-label { font-size: 13px; color: var(--text); font-weight: 500; }
    .toggle-desc { font-size: 11px; color: var(--text-light); margin-top: 2px; }
    .toggle-switch { position: relative; width: 42px; height: 24px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; inset: 0; background: #d1d5db; border-radius: 24px; cursor: pointer; transition: background 0.2s; }
    .toggle-slider::before { content: ''; position: absolute; width: 18px; height: 18px; border-radius: 50%; background: white; left: 3px; top: 3px; transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
    .toggle-switch input:checked + .toggle-slider { background: var(--teal); }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(18px); }
    .param-select { width: 100%; padding: 9px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 13px; color: var(--text); background: var(--bg); outline: none; cursor: pointer; transition: border-color 0.2s; margin-top: 10px; }
    .param-select:focus { border-color: var(--teal); }
    .range-row { margin-top: 12px; }
    .range-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .range-header span { font-size: 13px; font-weight: 500; color: var(--text); }
    .range-value { font-size: 13px; font-weight: 800; color: var(--teal); background: var(--teal-light); padding: 2px 10px; border-radius: 20px; }
    .param-range { width: 100%; accent-color: var(--teal); cursor: pointer; }
    .range-labels { display: flex; justify-content: space-between; margin-top: 4px; }
    .range-labels span { font-size: 10px; color: var(--text-light); }
    .danger-card { background: #fff5f5; border: 1px solid #fecaca; border-radius: 16px; padding: 20px 24px; max-width: 960px; margin-top: 8px; }
    .danger-header { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
    .danger-header svg { width: 18px; height: 18px; color: var(--red); }
    .danger-header strong { font-size: 14px; color: var(--red); font-weight: 700; }
    .danger-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #fecaca; gap: 12px; flex-wrap: wrap; }
    .danger-row:last-child { border-bottom: none; padding-bottom: 0; }
    .danger-label { font-size: 13px; font-weight: 500; color: #7f1d1d; }
    .danger-desc { font-size: 11px; color: #b91c1c; margin-top: 2px; }
    .btn-danger { padding: 7px 16px; border-radius: 8px; background: transparent; border: 1.5px solid var(--red); color: var(--red); font-size: 12px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s; white-space: nowrap; }
    .btn-danger:hover { background: var(--red); color: white; }
    .btn-save { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; border-radius: 10px; background: var(--teal); color: white; border: none; font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background 0.2s; margin-top: 20px; }
    .btn-save:hover { background: var(--teal-dark); }
    .btn-save svg { width: 15px; height: 15px; }
    .btn-reset { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; border-radius: 10px; background: transparent; color: var(--text-light); border: 1.5px solid var(--border); font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s; margin-top: 20px; margin-left: 10px; }
    .btn-reset:hover { border-color: var(--text-light); color: var(--text); }
    .btn-reset svg { width: 15px; height: 15px; }
    .version-info { margin-top: 24px; font-size: 11px; color: var(--text-light); text-align: center; }

    /* ── Modal de confirmation suppression de compte ── */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: var(--white); border-radius: 16px; padding: 26px; max-width: 420px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.25); }
    .modal-box h3 { font-size: 16px; font-weight: 800; color: var(--red); margin-bottom: 10px; }
    .modal-box p { font-size: 13px; color: var(--text-light); line-height: 1.6; margin-bottom: 16px; }
    .modal-box label { display: block; font-size: 12px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
    .modal-box input[type=password] { width: 100%; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 13px; font-family: inherit; outline: none; margin-bottom: 16px; }
    .modal-box input[type=password]:focus { border-color: var(--red); }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
    .modal-cancel { padding: 9px 18px; border-radius: 9px; background: var(--bg); color: var(--text); border: 1.5px solid var(--border); font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; }
    .modal-confirm { padding: 9px 18px; border-radius: 9px; background: var(--red); color: white; border: none; font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; }
    .modal-confirm:hover { background: #a00e1f; }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
        .params-grid { grid-template-columns: 1fr; gap: 16px; }
        .param-card-header { padding: 14px 16px; }
        .param-card-body { padding: 14px 16px; }
        .danger-card { padding: 16px; }
        .btn-save, .btn-reset { width: 100%; justify-content: center; margin-left: 0; }
        .btn-reset { margin-top: 10px; }
    }
</style>
@endsection

@section('content')

@if(session('success'))
<div style="background:#ecfdf5;border:1px solid #6ee7b7;color:#065f46;padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:13px;font-weight:600;">
    ✓ {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:13px;font-weight:600;">
    {{ session('error') }}
</div>
@endif

{{-- ✅ CORRIGÉ : @method('PUT') ajouté — la route fabricant.parametres.update
     est déclarée en PUT dans web.php ; sans ce spoofing, Laravel rejetait la
     requête en 405 Method Not Allowed et aucun enregistrement n'était possible. --}}
<form method="POST" action="{{ route('fabricant.parametres.update') }}">
@csrf
@method('PUT')

<div class="params-grid">
    {{-- Notifications --}}
    <div class="param-card">
        <div class="param-card-header">
            <div class="param-card-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></div>
            <div><div class="param-card-title">{{ __('messages.notif_email') }}</div><div class="param-card-subtitle">{{ Auth::guard('fabricant')->user()->email }}</div></div>
        </div>
        <div class="param-card-body">
            <div class="toggle-row"><div><div class="toggle-label">{{ __('messages.notif_signalement') }}</div><div class="toggle-desc">{{ __('messages.notif_signalement_desc') }}</div></div><label class="toggle-switch"><input type="checkbox" name="notif_signalement" {{ old('notif_signalement', $parametres['notif_signalement'] ?? true) ? 'checked' : '' }}><span class="toggle-slider"></span></label></div>
            <div class="toggle-row"><div><div class="toggle-label">{{ __('messages.notif_score_critique') }}</div><div class="toggle-desc">{{ __('messages.notif_score_critique_desc') }}</div></div><label class="toggle-switch"><input type="checkbox" name="notif_score_critique" {{ old('notif_score_critique', $parametres['notif_score_critique'] ?? true) ? 'checked' : '' }}><span class="toggle-slider"></span></label></div>
            <div class="toggle-row"><div><div class="toggle-label">{{ __('messages.notif_rapport_mensuel') }}</div><div class="toggle-desc">{{ __('messages.notif_rapport_mensuel_desc') }}</div></div><label class="toggle-switch"><input type="checkbox" name="notif_rapport_mensuel" {{ old('notif_rapport_mensuel', $parametres['notif_rapport_mensuel'] ?? false) ? 'checked' : '' }}><span class="toggle-slider"></span></label></div>
            <div class="toggle-row"><div><div class="toggle-label">{{ __('messages.notif_resume_hebdo') }}</div><div class="toggle-desc">{{ __('messages.notif_resume_hebdo_desc') }}</div></div><label class="toggle-switch"><input type="checkbox" name="notif_resume_hebdo" {{ old('notif_resume_hebdo', $parametres['notif_resume_hebdo'] ?? false) ? 'checked' : '' }}><span class="toggle-slider"></span></label></div>
        </div>
    </div>

    {{-- Seuil IA --}}
    <div class="param-card">
        <div class="param-card-header">
            <div class="param-card-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
            <div><div class="param-card-title">{{ __('messages.seuil_alerte_ia') }}</div><div class="param-card-subtitle">{{ __('messages.seuil_alerte_ia_desc') }}</div></div>
        </div>
        <div class="param-card-body">
            <div class="range-row">
                <div class="range-header"><span>{{ __('messages.score_risque_min') }}</span><span class="range-value" id="seuil-val">{{ $parametres['seuil_alerte'] ?? 70 }}/100</span></div>
                <input type="range" class="param-range" name="seuil_alerte" min="30" max="90" step="5" value="{{ $parametres['seuil_alerte'] ?? 70 }}" oninput="document.getElementById('seuil-val').textContent = this.value + '/100'">
                <div class="range-labels"><span>{{ __('messages.faible') }} (30)</span><span>{{ __('messages.modere') }} (60)</span><span>{{ __('messages.critique') }} (90)</span></div>
            </div>
            <div style="margin-top:20px;">
                <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:8px;">{{ __('messages.frequence_calcul_ia') }}</div>
                <select class="param-select" name="frequence_calcul">
                    <option value="6" {{ ($parametres['frequence_calcul'] ?? 6) == 6 ? 'selected' : '' }}>{{ __('messages.toutes_6h') }}</option>
                    <option value="12" {{ ($parametres['frequence_calcul'] ?? 6) == 12 ? 'selected' : '' }}>{{ __('messages.toutes_12h') }}</option>
                    <option value="24" {{ ($parametres['frequence_calcul'] ?? 6) == 24 ? 'selected' : '' }}>{{ __('messages.une_fois_par_jour') }}</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Affichage --}}
    <div class="param-card">
        <div class="param-card-header">
            <div class="param-card-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
            <div><div class="param-card-title">{{ __('messages.affichage_dashboard') }}</div><div class="param-card-subtitle">{{ __('messages.affichage_dashboard_desc') }}</div></div>
        </div>
        <div class="param-card-body">
            <div class="toggle-row"><div><div class="toggle-label">{{ __('messages.afficher_carte') }}</div><div class="toggle-desc">{{ __('messages.afficher_carte_desc') }}</div></div><label class="toggle-switch"><input type="checkbox" name="afficher_carte" {{ old('afficher_carte', $parametres['afficher_carte'] ?? true) ? 'checked' : '' }}><span class="toggle-slider"></span></label></div>
            <div class="toggle-row"><div><div class="toggle-label">{{ __('messages.afficher_graphiques') }}</div><div class="toggle-desc">{{ __('messages.afficher_graphiques_desc') }}</div></div><label class="toggle-switch"><input type="checkbox" name="afficher_graphiques" {{ old('afficher_graphiques', $parametres['afficher_graphiques'] ?? true) ? 'checked' : '' }}><span class="toggle-slider"></span></label></div>
            <div class="toggle-row"><div><div class="toggle-label">{{ __('messages.mode_leger') }}</div><div class="toggle-desc">{{ __('messages.mode_leger_desc') }}</div></div><label class="toggle-switch"><input type="checkbox" name="mode_leger" {{ old('mode_leger', $parametres['mode_leger'] ?? false) ? 'checked' : '' }}><span class="toggle-slider"></span></label></div>
        </div>
    </div>
</div>

<button type="submit" class="btn-save">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    {{ __('messages.enregistrer_parametres') }}
</button>

</form>

{{-- ✅ AJOUTÉ : le contrôleur a une méthode reset() fonctionnelle et
     routée (fabricant.parametres.reset), mais rien dans la vue n'y menait
     jusqu'ici. Formulaire séparé, POST simple (la route n'est pas en PUT/DELETE). --}}
<form method="POST" action="{{ route('fabricant.parametres.reset') }}" style="display:inline;" onsubmit="return confirm('Réinitialiser tous vos paramètres aux valeurs par défaut ?');">
    @csrf
    <button type="submit" class="btn-reset">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        {{ __('messages.reinitialiser_parametres') ?? 'Réinitialiser aux valeurs par défaut' }}
    </button>
</form>

{{-- ✅ AJOUTÉ : zone dangereuse — la méthode deleteAccount() du contrôleur
     existe (avec ses garde-fous signalement actif / QR déjà scanné) mais
     n'était accessible depuis aucune vue. Confirmation par mot de passe
     via une modale, cohérente avec la validation `password` exigée côté
     contrôleur. --}}
<div class="danger-card">
    <div class="danger-header">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <strong>{{ __('messages.zone_dangereuse') ?? 'Zone dangereuse' }}</strong>
    </div>
    <div class="danger-row">
        <div>
            <div class="danger-label">{{ __('messages.supprimer_compte') ?? 'Supprimer mon compte' }}</div>
            <div class="danger-desc">{{ __('messages.supprimer_compte_desc') ?? 'Action définitive et irréversible. Impossible si un signalement est en cours ou si vos QR codes ont déjà été scannés.' }}</div>
        </div>
        <button type="button" class="btn-danger" onclick="document.getElementById('deleteModal').classList.add('open')">
            {{ __('messages.supprimer') ?? 'Supprimer' }}
        </button>
    </div>
</div>

<div class="version-info">VeriScan v1.0 · {{ __('messages.compte_cree_le') ?? 'Compte créé le' }} {{ Auth::guard('fabricant')->user()->created_at->format('d/m/Y') }}</div>

{{-- Modale de confirmation suppression --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <h3>{{ __('messages.confirmer_suppression') ?? 'Confirmer la suppression du compte' }}</h3>
        <p>{{ __('messages.confirmer_suppression_desc') ?? 'Cette action est irréversible. Entrez votre mot de passe pour confirmer la suppression définitive de votre compte VeriScan.' }}</p>
        <form method="POST" action="{{ route('fabricant.parametres.delete') }}">
            @csrf
            @method('DELETE')
            <label>{{ __('messages.mot_de_passe') ?? 'Mot de passe' }}</label>
            <input type="password" name="password" required placeholder="••••••••" autocomplete="current-password">
            <div class="modal-actions">
                <button type="button" class="modal-cancel" onclick="document.getElementById('deleteModal').classList.remove('open')">{{ __('messages.annuler') ?? 'Annuler' }}</button>
                <button type="submit" class="modal-confirm">{{ __('messages.supprimer_definitivement') ?? 'Supprimer définitivement' }}</button>
            </div>
        </form>
    </div>
</div>

@endsection