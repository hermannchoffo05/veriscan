@extends('layouts.fabricant')

@section('title', __('messages.dashboard'))

@section('topbar-title')
    {{ __('messages.dashboard') }}
@endsection

@section('topbar-actions')
    <div class="topbar-search" style="position:relative;">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="search-input" placeholder="{{ __('messages.rechercher_produit') }}" autocomplete="off">
        <div id="search-results" style="display:none;position:absolute;top:42px;left:0;right:0;background:white;border:1.5px solid #e5e7eb;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.1);z-index:999;max-height:280px;overflow-y:auto;"></div>
    </div>
    <div class="topbar-date">{{ now()->locale(app()->getLocale())->isoFormat('D MMM YYYY') }}</div>

    {{-- Cloche notifications --}}
    <div class="notif-btn" id="notif-toggle" style="position:relative;cursor:pointer;">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        @if($nbNotifications > 0)
            <div style="position:absolute;top:-6px;right:-6px;background:#CE1126;color:white;font-size:10px;font-weight:800;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid white;">{{ $nbNotifications > 9 ? '9+' : $nbNotifications }}</div>
        @endif
    </div>

    {{-- Dropdown notifications --}}
    <div id="notif-dropdown" style="display:none;position:fixed;top:64px;right:32px;width:340px;background:white;border:1.5px solid #e5e7eb;border-radius:16px;box-shadow:0 12px 40px rgba(0,0,0,0.12);z-index:9999;overflow:hidden;">
        <div style="padding:14px 18px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:14px;font-weight:800;color:#1F2937;">Notifications</span>
            @if($nbNotifications > 0)
                <span style="background:#fef2f2;color:#CE1126;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;">{{ $nbNotifications }} en cours</span>
            @endif
        </div>
        <div style="max-height:300px;overflow-y:auto;">
            @forelse($notifications as $notif)
                <a href="{{ route('fabricant.signalements.show', $notif->id) }}" style="display:flex;align-items:flex-start;gap:12px;padding:12px 18px;border-bottom:1px solid #f3f4f6;text-decoration:none;background:white;transition:background 0.2s;" onmouseover="this.style.background='#EEF0F8'" onmouseout="this.style.background='white'">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fef2f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="#CE1126" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12.5px;font-weight:700;color:#1F2937;">{{ $notif->qrCode?->lot?->produit?->nom ?? 'Produit inconnu' }}</div>
                        <div style="font-size:11.5px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $notif->description ?? 'Signalement en cours' }}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px;">{{ $notif->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            @empty
                <div style="padding:24px;text-align:center;color:#6b7280;font-size:13px;">Aucune notification</div>
            @endforelse
        </div>
        <a href="{{ route('fabricant.signalements.index') }}" style="display:block;padding:12px;text-align:center;font-size:12.5px;font-weight:700;color:#2E3A6B;text-decoration:none;border-top:1px solid #e5e7eb;">Voir tous les signalements →</a>
    </div>
@endsection

@section('styles')
<style>
    .welcome-banner { background: linear-gradient(135deg, #171B3D 0%, #2E3A6B 100%); border-radius: 20px; padding: 24px 28px; color: white; display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; position: relative; overflow: hidden; min-height: 90px; flex-wrap: wrap; gap: 16px; }
    .welcome-banner::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 20px 20px; pointer-events: none; }
    .welcome-banner::after { content: ''; position: absolute; width: 320px; height: 320px; border-radius: 50%; background: rgba(255,255,255,0.04); right: -80px; top: -80px; pointer-events: none; }
    .welcome-text { position: relative; z-index: 1; }
    .welcome-text h2 { font-size: 20px; font-weight: 800; margin-bottom: 4px; color: white; }
    .welcome-text p { font-size: 13px; color: rgba(255,255,255,0.7); }
    .welcome-actions { position: relative; z-index: 1; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .btn-white { background: #F5A623; color: #171B3D; border: none; border-radius: 10px; padding: 9px 18px; font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; display: flex; align-items: center; gap: 7px; transition: all 0.2s; text-decoration: none; white-space: nowrap; }
    .btn-white:hover { background: #e0961d; }
    .btn-white svg { width: 15px; height: 15px; flex-shrink: 0; }
    .btn-outline-white { background: rgba(255,255,255,0.15); color: white; border: 2px solid rgba(255,255,255,0.35); border-radius: 10px; padding: 9px 18px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; display: flex; align-items: center; gap: 7px; transition: all 0.2s; text-decoration: none; white-space: nowrap; }
    .btn-outline-white:hover { background: rgba(255,255,255,0.25); }
    .btn-outline-white svg { width: 15px; height: 15px; flex-shrink: 0; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: var(--white); border-radius: 16px; padding: 20px 22px; border: 1.5px solid var(--border); display: flex; flex-direction: column; gap: 12px; transition: transform 0.2s, box-shadow 0.2s; animation: fadeUp 0.4s ease both; cursor: pointer; text-decoration: none; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.07); }
    .stat-card:nth-child(1){animation-delay:.05s} .stat-card:nth-child(2){animation-delay:.10s} .stat-card:nth-child(3){animation-delay:.15s} .stat-card:nth-child(4){animation-delay:.20s}
    .stat-header { display: flex; align-items: center; justify-content: space-between; }
    .stat-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .stat-icon svg { width: 20px; height: 20px; }
    .stat-icon.teal{background:#EEF0F8;color:#2E3A6B} .stat-icon.green{background:#f0fdf4;color:#007A4D} .stat-icon.yellow{background:#fefce8;color:#a16207} .stat-icon.red{background:#fef2f2;color:#CE1126}
    .stat-trend { font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 20px; display: flex; align-items: center; gap: 3px; }
    .stat-trend.up{background:#f0fdf4;color:#007A4D} .stat-trend.neutral{background:#f3f4f6;color:#6b7280} .stat-trend.down{background:#fef2f2;color:#CE1126}
    .stat-value { font-size: 30px; font-weight: 800; color: var(--text); line-height: 1; }
    .stat-label { font-size: 12.5px; color: var(--text-light); font-weight: 500; }
    .main-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; margin-bottom: 20px; }
    .card { background: var(--white); border-radius: 16px; border: 1.5px solid var(--border); overflow: hidden; }
    .card-header { padding: 18px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .card-title { font-size: 14px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 8px; }
    .card-title svg { width: 16px; height: 16px; color: #2E3A6B; }
    .card-body { padding: 22px; }
    .card-action { font-size: 12px; font-weight: 600; color: #2E3A6B; text-decoration: none; display: flex; align-items: center; gap: 4px; cursor: pointer; background: none; border: none; font-family: inherit; transition: all 0.2s; }
    .card-action:hover { text-decoration: underline; }
    .card-action svg { width: 13px; height: 13px; }
    .chart-wrap { position: relative; height: 220px; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.06em; padding: 10px 14px; text-align: left; border-bottom: 1px solid var(--border); background: var(--bg); }
    td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid var(--border); vertical-align: middle; color: var(--text); }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--teal-light); }
    .product-name { font-weight: 700; color: var(--text); }
    .product-sector { font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px; background: #EEF0F8; color: #2E3A6B; }
    .status-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
    .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
    .status-badge.actif{background:#f0fdf4;color:#007A4D} .status-badge.actif::before{background:#007A4D}
    .status-badge.suspect{background:#fefce8;color:#a16207} .status-badge.suspect::before{background:#FCD116}
    .status-badge.contrefait{background:#fef2f2;color:#CE1126} .status-badge.contrefait::before{background:#CE1126}
    .signal-list { display: flex; flex-direction: column; gap: 12px; }
    .signal-item { display: flex; align-items: flex-start; gap: 12px; padding: 12px; border-radius: 12px; background: var(--bg); border: 1.5px solid var(--border); transition: all 0.2s; text-decoration: none; }
    .signal-item:hover { border-color: #2E3A6B; background: var(--teal-light); }
    .signal-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .signal-icon svg { width: 16px; height: 16px; }
    .signal-icon.yellow{background:#fefce8;color:#a16207} .signal-icon.red{background:#fef2f2;color:#CE1126} .signal-icon.green{background:#f0fdf4;color:#007A4D}
    .signal-content { flex: 1; min-width: 0; }
    .signal-content strong { display: block; font-size: 12.5px; font-weight: 700; color: var(--text); }
    .signal-content span { font-size: 11.5px; color: var(--text-light); }
    .signal-time { font-size: 11px; color: var(--text-light); white-space: nowrap; margin-top: 2px; }
    .bottom-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .lot-item { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border); }
    .lot-item:last-child { border-bottom: none; padding-bottom: 0; }
    .lot-icon { width: 38px; height: 38px; border-radius: 10px; background: var(--teal-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .lot-icon svg { width: 18px; height: 18px; color: #2E3A6B; }
    .lot-info { flex: 1; }
    .lot-info strong { display: block; font-size: 13px; font-weight: 700; color: var(--text); }
    .lot-info span { font-size: 11.5px; color: var(--text-light); }
    .lot-count { font-size: 13px; font-weight: 800; color: #2E3A6B; }
    .actions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .action-btn { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 16px 12px; border-radius: 14px; background: var(--bg); border: 1.5px solid var(--border); cursor: pointer; text-decoration: none; transition: all 0.2s; text-align: center; }
    .action-btn:hover { border-color: #2E3A6B; background: var(--teal-light); transform: translateY(-2px); }
    .action-btn .action-icon { width: 42px; height: 42px; border-radius: 12px; background: var(--teal-light); display: flex; align-items: center; justify-content: center; }
    .action-btn .action-icon svg { width: 20px; height: 20px; color: #2E3A6B; }
    .action-btn strong { font-size: 12.5px; font-weight: 700; color: var(--text); }
    .action-btn span { font-size: 11px; color: var(--text-light); }
    .empty-state { text-align: center; padding: 24px; color: var(--text-light); font-size: 13px; }

    @media (max-width: 768px) {
        .welcome-banner { padding: 20px; flex-direction: column; align-items: flex-start; }
        .welcome-text h2 { font-size: 17px; }
        .welcome-actions { width: 100%; }
        .welcome-actions a { flex: 1; justify-content: center; font-size: 12px; padding: 8px 12px; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .stat-value { font-size: 24px; }
        .main-grid { grid-template-columns: 1fr; }
        .bottom-grid { grid-template-columns: 1fr; }
        .chart-wrap { height: 180px; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .stat-card { padding: 14px; }
        .welcome-actions { flex-direction: column; }
        .welcome-actions a { width: 100%; }
    }
</style>
@endsection

@section('content')
    <div class="welcome-banner">
        <div class="welcome-text">
            <h2>{{ __('messages.bonjour') }}, {{ $fabricant->nom_entreprise ?? 'Fabricant' }}</h2>
            <p>{{ __('messages.bienvenue_desc') }}</p>
        </div>
        <div class="welcome-actions">
            <a href="{{ route('fabricant.produits.create') }}" class="btn-white">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('messages.nouveau_produit') }}
            </a>
            <a href="{{ route('fabricant.qrcodes.index') }}" class="btn-outline-white">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg>
                {{ __('messages.scanner_qr') }}
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <a href="{{ route('fabricant.produits.index') }}" class="stat-card">
            <div class="stat-header">
                <div class="stat-icon teal">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div class="stat-trend neutral">{{ $totalProduits > 0 ? __('messages.actif') : __('messages.vide') }}</div>
            </div>
            <div>
                <div class="stat-value">{{ $totalProduits }}</div>
                <div class="stat-label">{{ __('messages.produits_enregistres') }}</div>
            </div>
        </a>

        <a href="{{ route('fabricant.qrcodes.index') }}" class="stat-card">
            <div class="stat-header">
                <div class="stat-icon green">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg>
                </div>
                <div class="stat-trend neutral">{{ __('messages.phase_test') }}</div>
            </div>
            <div>
                <div class="stat-value">{{ $totalQrcodes }}</div>
                <div class="stat-label">{{ __('messages.qr_generes') }}</div>
            </div>
        </a>

        <a href="{{ route('fabricant.statistiques.index') }}" class="stat-card">
            <div class="stat-header">
                <div class="stat-icon yellow">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                @if($scansAujourdHui > 0)
                    <div class="stat-trend up">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                        +{{ $scansAujourdHui }} {{ __('messages.ce_jour') }}
                    </div>
                @else
                    <div class="stat-trend neutral">{{ __('messages.ce_jour') }}: 0</div>
                @endif
            </div>
            <div>
                <div class="stat-value">{{ $totalScans }}</div>
                <div class="stat-label">{{ __('messages.scans_effectues') }}</div>
            </div>
        </a>

        <a href="{{ route('fabricant.signalements.index') }}" class="stat-card">
            <div class="stat-header">
                <div class="stat-icon red">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                @if($signalementsEnCours > 0)
                    <div class="stat-trend down">{{ $signalementsEnCours }} {{ __('messages.en_cours') }}</div>
                @else
                    <div class="stat-trend neutral">0 {{ __('messages.en_cours') }}</div>
                @endif
            </div>
            <div>
                <div class="stat-value">{{ $totalSignalements }}</div>
                <div class="stat-label">{{ __('messages.signalements_recus') }}</div>
            </div>
        </a>
    </div>

    <div class="main-grid">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    {{ __('messages.activite_scans') }}
                </div>
                <a href="{{ route('fabricant.statistiques.index') }}" class="card-action">
                    {{ __('messages.voir_tout') }}
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="card-body">
                <div class="chart-wrap">
                    <canvas id="scanChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ __('messages.signalements_recents') }}
                </div>
                <a href="{{ route('fabricant.signalements.index') }}" class="card-action">
                    {{ __('messages.voir_tout') }}
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="card-body">
                <div class="signal-list">
                    @forelse($signalementsRecents as $signalement)
                        {{--
                            ✅ CORRIGÉ : la couleur reflète désormais le statut réel plutôt
                            que d'être un simple "en_cours ? jaune : rouge" (qui affichait un
                            signalement traité/résolu avec succès de la même couleur qu'un
                            signalement rejeté).
                            HYPOTHÈSE À VÉRIFIER : je ne connais pas l'enum exact utilisé pour
                            Signalement.statut au-delà de 'en_cours' (confirmé dans le
                            contrôleur). J'ai supposé 'rejete'/'rejeté'/'contrefait' pour le
                            rouge et tout le reste (traité, résolu, validé...) pour le vert.
                            Si les vraies valeurs diffèrent, dis-les-moi et j'ajuste le match.
                        --}}
                        @php
                            $signalIconClass = match($signalement->statut) {
                                'en_cours' => 'yellow',
                                'rejete', 'rejeté', 'contrefait' => 'red',
                                default => 'green',
                            };
                        @endphp
                        <a href="{{ route('fabricant.signalements.show', $signalement->id) }}" class="signal-item">
                            <div class="signal-icon {{ $signalIconClass }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/></svg>
                            </div>
                            <div class="signal-content">
                                <strong>{{ $signalement->qrCode?->lot?->numero_lot ?? 'Lot inconnu' }} – {{ ucfirst($signalement->statut) }}</strong>
                                <span>{{ $signalement->description ?? '' }}</span>
                            </div>
                            <div class="signal-time">{{ $signalement->created_at->diffForHumans() }}</div>
                        </a>
                    @empty
                        <div class="empty-state">Aucun signalement récent</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bottom-grid">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    {{ __('messages.produits_recents') }}
                </div>
                <a href="{{ route('fabricant.produits.index') }}" class="card-action">
                    {{ __('messages.gerer') }}
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('messages.produit') }}</th>
                                <th>{{ __('messages.secteur') }}</th>
                                <th>{{ __('messages.scans') }}</th>
                                <th>{{ __('messages.statut') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($produitsRecents as $produit)
                                <tr>
                                    <td>
                                        <div class="product-name">{{ $produit->nom }}</div>
                                        @if($produit->lots->first())
                                            <div style="font-size:11px;color:var(--text-light)">{{ $produit->lots->first()->numero_lot }}</div>
                                        @endif
                                    </td>
                                    <td><span class="product-sector">{{ $produit->categorie }}</span></td>
                                    <td style="font-weight:700;">{{ $produit->nb_scans ?? 0 }}</td>
                                    <td>
                                        @if($produit->est_suspect)
                                            <span class="status-badge suspect">{{ __('messages.suspect') }}</span>
                                        @else
                                            <span class="status-badge actif">{{ __('messages.authentique') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="empty-state">Aucun produit enregistré</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:20px;">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        {{ __('messages.actions_rapides') }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="actions-grid">
                        <a href="{{ route('fabricant.produits.create') }}" class="action-btn">
                            <div class="action-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg></div>
                            <strong>{{ __('messages.nouveau_produit') }}</strong>
                            <span>{{ __('messages.enregistrer') }}</span>
                        </a>
                        <a href="{{ route('fabricant.qrcodes.index') }}" class="action-btn">
                            <div class="action-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg></div>
                            <strong>{{ __('messages.generer_qr') }}</strong>
                            <span>{{ __('messages.par_lot') }}</span>
                        </a>
                        <a href="{{ route('fabricant.rapports.index') }}" class="action-btn">
                            <div class="action-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                            <strong>{{ __('messages.exporter_pdf') }}</strong>
                            <span>{{ __('messages.rapport') }}</span>
                        </a>
                        <a href="{{ route('fabricant.statistiques.index') }}" class="action-btn">
                            <div class="action-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                            <strong>{{ __('messages.statistiques') }}</strong>
                            <span>{{ __('messages.analyse') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg>
                        {{ __('messages.derniers_lots_qr') }}
                    </div>
                    <a href="{{ route('fabricant.qrcodes.index') }}" class="card-action">
                        {{ __('messages.tout_voir') }}
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="card-body">
                    @forelse($derniersLots as $lot)
                        <div class="lot-item">
                            <div class="lot-icon">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg>
                            </div>
                            <div class="lot-info">
                                <strong>{{ $lot->numero_lot }}</strong>
                                <span>{{ $lot->produit?->nom ?? '' }} · {{ $lot->created_at->locale(app()->getLocale())->isoFormat('D MMM YYYY') }}</span>
                            </div>
                            <div class="lot-count">{{ $lot->qrcodes_count }} QR</div>
                        </div>
                    @empty
                        <div class="empty-state">Aucun lot créé</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // ── Graphique scans ────────────────────────────────────────────────────
    const ctx = document.getElementById('scanChart').getContext('2d');
    const locale = '{{ app()->getLocale() }}';
    const labels = locale === 'en'
        ? ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']
        : ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
    const scansData    = @json($scans7jours);
    const suspectsData = @json($suspects7jours);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: locale === 'en' ? 'Authentic' : 'Authentiques', data: scansData, backgroundColor: 'rgba(46,58,107,0.85)', borderRadius: 8, borderSkipped: false },
                { label: locale === 'en' ? 'Suspect' : 'Suspects', data: suspectsData, backgroundColor: 'rgba(251,191,36,0.85)', borderRadius: 8, borderSkipped: false }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,0.04)' } }
            }
        }
    });

    // ── Recherche produit ──────────────────────────────────────────────────
    const searchInput   = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    let searchTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        const q = this.value.trim();
        if (q.length < 2) { searchResults.style.display = 'none'; return; }
        searchTimer = setTimeout(() => {
            fetch(`{{ route('fabricant.dashboard.search') }}?q=${encodeURIComponent(q)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    searchResults.innerHTML = '<div style="padding:16px;text-align:center;color:#6b7280;font-size:13px;">Aucun produit trouvé</div>';
                } else {
                    searchResults.innerHTML = data.map(p => `
                        <a href="${p.url}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;background:white;border-bottom:1px solid #f3f4f6;" onmouseover="this.style.background='#EEF0F8'" onmouseout="this.style.background='white'">
                            <div style="width:32px;height:32px;border-radius:8px;background:#EEF0F8;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg fill="none" viewBox="0 0 24 24" stroke="#2E3A6B" stroke-width="2" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#1F2937;">${p.nom}</div>
                                <div style="font-size:11px;color:#6b7280;">${p.categorie} ${p.lot ? '· ' + p.lot : ''}</div>
                            </div>
                        </a>
                    `).join('');
                }
                searchResults.style.display = 'block';
            });
        }, 300);
    });
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // ── Notifications dropdown ─────────────────────────────────────────────
    const notifToggle   = document.getElementById('notif-toggle');
    const notifDropdown = document.getElementById('notif-dropdown');
    notifToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        notifDropdown.style.display = notifDropdown.style.display === 'none' ? 'block' : 'none';
    });
    document.addEventListener('click', function(e) {
        if (!notifToggle.contains(e.target) && !notifDropdown.contains(e.target)) {
            notifDropdown.style.display = 'none';
        }
    });
</script>
@endsection