@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('topbar-title')
    Tableau de <span>bord</span>
@endsection

@section('topbar-actions')
    <div class="topbar-date">{{ now()->isoFormat('D MMM YYYY') }}</div>
    <a href="{{ route('admin.rapports.index') }}" class="btn-export">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="15" height="15">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Exporter rapport
    </a>
@endsection

@section('styles')
<style>
    /* ── Topbar extras ── */
    .topbar-date { font-size: 12.5px; color: var(--text-light); font-weight: 500; }
    .btn-export {
        display: inline-flex; align-items: center; gap: 7px;
        background: #f0fdf4; color: #065f46;
        border: 1.5px solid #6ee7b7; border-radius: 10px;
        padding: 8px 16px; font-size: 12.5px; font-weight: 700;
        text-decoration: none; font-family: inherit;
        transition: all 0.2s;
    }
    .btn-export:hover { background: #d1fae5; transform: translateY(-1px); }

    /* ── Welcome banner ADMIN — marine ── */
    .admin-banner {
        background: linear-gradient(135deg, #171B3D 0%, #2E3A6B 60%, #4A5899 100%);
        border-radius: 20px; padding: 28px 32px; color: white;
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 28px; position: relative; overflow: hidden; min-height: 96px;
    }
    .admin-banner::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px);
        background-size: 22px 22px; pointer-events: none;
    }
    .admin-banner::after {
        content: ''; position: absolute;
        width: 340px; height: 340px; border-radius: 50%;
        background: rgba(255,255,255,0.05);
        right: -100px; top: -100px; pointer-events: none;
    }
    .banner-left { position: relative; z-index: 1; }
    .banner-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);
        border-radius: 20px; padding: 4px 12px; font-size: 11px; font-weight: 700;
        letter-spacing: 0.05em; margin-bottom: 10px;
        color: rgba(255,255,255,0.9);
    }
    .banner-badge::before {
        content: ''; width: 6px; height: 6px; border-radius: 50%;
        background: #8B93D1; display: inline-block;
        animation: pulse-dot 2s ease-in-out infinite;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.8); }
    }
    .banner-left h2 { font-size: 22px; font-weight: 800; color: white; margin-bottom: 5px; }
    .banner-left p { font-size: 12.5px; color: rgba(255,255,255,0.65); }
    .banner-right { position: relative; z-index: 1; display: flex; gap: 10px; align-items: center; }
    .btn-banner-white {
        background: white; color: #171B3D; border: none; border-radius: 10px;
        padding: 10px 18px; font-size: 13px; font-weight: 700; cursor: pointer;
        font-family: inherit; display: flex; align-items: center; gap: 7px;
        transition: all 0.2s; text-decoration: none; white-space: nowrap;
    }
    .btn-banner-white:hover { background: #EEF0F8; transform: translateY(-1px); }
    .btn-banner-white svg { width: 15px; height: 15px; flex-shrink: 0; }
    .btn-banner-ghost {
        background: rgba(255,255,255,0.12); color: white;
        border: 1.5px solid rgba(255,255,255,0.3);
        border-radius: 10px; padding: 10px 18px; font-size: 13px; font-weight: 600;
        display: flex; align-items: center; gap: 7px; transition: all 0.2s;
        text-decoration: none; white-space: nowrap; font-family: inherit;
    }
    .btn-banner-ghost:hover { background: rgba(255,255,255,0.22); transform: translateY(-1px); }
    .btn-banner-ghost svg { width: 15px; height: 15px; }

    /* ── Alert fabricants en attente ── */
    .alert-banner {
        display: flex; align-items: center; gap: 12px;
        background: #fffbeb; border: 1.5px solid #fcd34d;
        border-left: 4px solid #f59e0b;
        border-radius: 12px; padding: 13px 18px;
        margin-bottom: 22px; font-size: 13px; color: #92400e; font-weight: 500;
    }
    .alert-banner svg { width: 18px; height: 18px; flex-shrink: 0; color: #f59e0b; }
    .alert-banner strong { color: #78350f; }
    .alert-banner a {
        margin-left: auto; background: #fef3c7; border: 1.5px solid #fcd34d;
        color: #92400e; padding: 5px 14px; border-radius: 8px;
        font-size: 12px; font-weight: 700; text-decoration: none; transition: all 0.2s;
        white-space: nowrap;
    }
    .alert-banner a:hover { background: #fde68a; }

    /* ── Stats grid ── */
    .stats-grid {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 16px; margin-bottom: 24px;
    }
    .stat-card {
        background: var(--white); border-radius: 16px;
        padding: 20px 22px; border: 1.5px solid var(--border);
        display: flex; flex-direction: column; gap: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
        animation: fadeUp 0.4s ease both;
    }
    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.10s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }
    .stat-card:nth-child(4) { animation-delay: 0.20s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.07); }
    .stat-header { display: flex; align-items: center; justify-content: space-between; }
    .stat-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .stat-icon svg { width: 20px; height: 20px; }
    .stat-icon.indigo { background: #EEF0F8; color: #2E3A6B; }
    .stat-icon.teal   { background: #EEF0F8; color: #2E3A6B; }
    .stat-icon.yellow { background: #fefce8; color: #a16207; }
    .stat-icon.red    { background: #fef2f2; color: #CE1126; }
    .stat-change {
        font-size: 11px; font-weight: 700; padding: 3px 8px;
        border-radius: 20px; display: flex; align-items: center; gap: 3px;
    }
    .stat-change.up   { background: #f0fdf4; color: #007A4D; }
    .stat-change.down { background: #fef2f2; color: #CE1126; }
    .stat-change svg  { width: 11px; height: 11px; }
    .stat-value { font-size: 30px; font-weight: 800; color: var(--text); line-height: 1; }
    .stat-label { font-size: 12.5px; color: var(--text-light); font-weight: 500; }

    /* ── Cards ── */
    .card { background: var(--white); border-radius: 16px; border: 1.5px solid var(--border); overflow: hidden; }
    .card-header {
        padding: 18px 22px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
    }
    .card-title { font-size: 14px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 8px; }
    .card-title svg { width: 16px; height: 16px; color: #2E3A6B; }
    .card-body { padding: 22px; }
    .btn-sm {
        padding: 5px 12px; border-radius: 8px; font-size: 11.5px; font-weight: 700;
        text-decoration: none; font-family: inherit; cursor: pointer; border: none;
        transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-sm.primary { background: #EEF0F8; color: #2E3A6B; }
    .btn-sm.primary:hover { background: #dde2f5; }
    .btn-sm.outline { background: var(--bg); color: var(--text-light); border: 1.5px solid var(--border); }
    .btn-sm.outline:hover { border-color: #2E3A6B; color: #2E3A6B; }

    /* ── Grilles ── */
    .row-2   { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px; }
    .row-2-eq { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

    /* ── Chart ── */
    .chart-wrap { position: relative; height: 210px; padding: 16px 22px 22px; }

    /* ── Refresh dot ── */
    .live-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 11px; color: var(--text-light); font-weight: 500;
    }
    .live-dot {
        width: 7px; height: 7px; border-radius: 50%; background: #4ade80;
        animation: blink 2s infinite;
    }
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.25; } }

    /* ── Fabricants list ── */
    .fab-row {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 22px; border-bottom: 1px solid var(--border);
        transition: background 0.15s;
    }
    .fab-row:last-child { border-bottom: none; }
    .fab-row:hover { background: #EEF0F8; }
    .fab-avatar {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, #2E3A6B, #4A5899);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 800; color: white; flex-shrink: 0;
    }
    .fab-info .fab-name { font-size: 13px; font-weight: 700; color: var(--text); }
    .fab-info .fab-sub  { font-size: 11px; color: var(--text-light); }
    .fab-date { font-size: 11px; color: var(--text-light); margin-left: auto; white-space: nowrap; margin-right: 12px; }
    .fab-status {
        font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .fab-status.actif    { background: #f0fdf4; color: #007A4D; }
    .fab-status.attente  { background: #fefce8; color: #a16207; }
    .fab-status.suspendu { background: #fef2f2; color: #CE1126; }
    .fab-status::before  { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

    /* ── Signalements list ── */
    .sig-row {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 11px 22px; border-bottom: 1px solid var(--border); transition: background 0.15s;
    }
    .sig-row:last-child { border-bottom: none; }
    .sig-row:hover { background: #fff7f7; }
    .sig-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 4px; flex-shrink: 0; }
    .sig-dot.en_cours { background: #FCD116; }
    .sig-dot.traite   { background: #4ade80; }
    .sig-dot.rejete   { background: #94a3b8; }
    .sig-produit { font-size: 12.5px; font-weight: 700; color: var(--text); }
    .sig-desc    { font-size: 11px; color: var(--text-light); margin-top: 1px; }
    .sig-date    { font-size: 10.5px; color: var(--text-light); margin-left: auto; white-space: nowrap; margin-right: 10px; }

    /* ── Donut ── */
    .donut-wrap { display: flex; align-items: center; gap: 20px; padding: 16px 22px 20px; }
    .donut-legend { display: flex; flex-direction: column; gap: 12px; }
    .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text); }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .legend-count { font-weight: 700; margin-left: auto; color: var(--text); }

    /* ── Actions rapides ── */
    .actions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 16px 22px 20px; }
    .action-tile {
        display: flex; flex-direction: column; align-items: center; gap: 8px;
        padding: 16px 10px; border-radius: 14px; background: var(--bg);
        border: 1.5px solid var(--border); cursor: pointer; text-decoration: none;
        transition: all 0.2s; text-align: center;
    }
    .action-tile:hover { border-color: #2E3A6B; background: #EEF0F8; transform: translateY(-2px); }
    .action-tile .tile-icon {
        width: 42px; height: 42px; border-radius: 12px;
        background: #EEF0F8; display: flex; align-items: center; justify-content: center;
    }
    .action-tile .tile-icon svg { width: 20px; height: 20px; color: #2E3A6B; }
    .action-tile strong { font-size: 12.5px; font-weight: 700; color: var(--text); }
    .action-tile span   { font-size: 11px; color: var(--text-light); }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')

{{-- ── BANNER ADMIN ── --}}
<div class="admin-banner">
    <div class="banner-left">
        <div class="banner-badge">Espace Administration</div>
        <h2>Bonjour, {{ Auth::guard('admin')->user()->nom ?? 'Administrateur' }}</h2>
        <p>Vue globale de la plateforme VeriScan — {{ now()->isoFormat('dddd D MMMM YYYY') }}</p>
    </div>
    <div class="banner-right">
        <a href="{{ route('admin.fabricants.index') }}" class="btn-banner-white">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Gérer fabricants
        </a>
        <a href="{{ route('admin.signalements.index') }}" class="btn-banner-ghost">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Signalements
        </a>
    </div>
</div>

{{-- ── ALERTE FABRICANTS EN ATTENTE ── --}}
@if($fabricantsEnAttente > 0)
<div class="alert-banner">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <strong>{{ $fabricantsEnAttente }}</strong>&nbsp;fabricant(s) en attente de validation
    <a href="{{ route('admin.fabricants.index') }}">Gérer →</a>
</div>
@endif

{{-- ── STATS ── --}}
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon indigo">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            @if($fabricantsAujourdhui > 0)
            <div class="stat-change up">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                +{{ $fabricantsAujourdhui }} aujourd'hui
            </div>
            @endif
        </div>
        <div>
            <div class="stat-value" id="stat-fabricants">{{ $totalFabricants }}</div>
            <div class="stat-label">Fabricants inscrits</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon teal">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
        <div>
            <div class="stat-value" id="stat-produits">{{ $totalProduits }}</div>
            <div class="stat-label">Produits enregistrés</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon yellow">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div class="live-badge"><span class="live-dot"></span>Temps réel</div>
        </div>
        <div>
            <div class="stat-value" id="stat-scans">{{ number_format($totalScans) }}</div>
            <div class="stat-label">Scans effectués</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon red">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            @if($signalementsEnCours > 0)
            <div class="stat-change down">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/></svg>
                {{ $signalementsEnCours }} en cours
            </div>
            @endif
        </div>
        <div>
            <div class="stat-value" id="stat-signalements">{{ $totalSignalements }}</div>
            <div class="stat-label">Signalements reçus</div>
        </div>
    </div>

</div>

{{-- ── LIGNE 2 : Graphique + Donut ── --}}
<div class="row-2" style="margin-bottom:20px;">

    {{-- Activité scans --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Activité des scans — 7 derniers jours
            </div>
            <div class="live-badge"><span class="live-dot"></span>Temps réel</div>
        </div>
        <div class="chart-wrap">
            <canvas id="scansChart"></canvas>
        </div>
    </div>

    {{-- Donut + actions rapides --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    Signalements par statut
                </div>
            </div>
            <div class="donut-wrap">
                <canvas id="sigChart" style="max-width:130px;max-height:130px;flex-shrink:0;"></canvas>
                <div class="donut-legend">
                    <div class="legend-item">
                        <div class="legend-dot" style="background:#FCD116;"></div>
                        En cours
                        <span class="legend-count">{{ $sigParStatut['en_cours'] }}</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background:#4ade80;"></div>
                        Traités
                        <span class="legend-count">{{ $sigParStatut['traite'] }}</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background:#94a3b8;"></div>
                        Rejetés
                        <span class="legend-count">{{ $sigParStatut['rejete'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Actions rapides
                </div>
            </div>
            <div class="actions-grid">
                <a href="{{ route('admin.fabricants.index') }}" class="action-tile">
                    <div class="tile-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    <strong>Fabricants</strong><span>Valider / gérer</span>
                </a>
                <a href="{{ route('admin.signalements.index') }}" class="action-tile">
                    <div class="tile-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                    <strong>Signalements</strong><span>Modérer</span>
                </a>
                <a href="{{ route('admin.carte') }}" class="action-tile">
                    <div class="tile-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></div>
                    <strong>Carte risques</strong><span>Visualiser</span>
                </a>
                <a href="{{ route('admin.rapports.index') }}" class="action-tile">
                    <div class="tile-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                    <strong>Rapports</strong><span>Exporter PDF</span>
                </a>
            </div>
        </div>

    </div>
</div>

{{-- ── LIGNE 3 : Fabricants + Signalements ── --}}
<div class="row-2-eq">

    {{-- Derniers fabricants inscrits --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Derniers fabricants inscrits
            </div>
            <a href="{{ route('admin.fabricants.index') }}" class="btn-sm outline">Voir tout</a>
        </div>
        @forelse($derniersFabricants as $fab)
        <div class="fab-row">
            <div class="fab-avatar">{{ strtoupper(substr($fab->nom_entreprise ?? $fab->name ?? 'F', 0, 1)) }}</div>
            <div class="fab-info">
                <div class="fab-name">{{ $fab->nom_entreprise ?? $fab->name }}</div>
                <div class="fab-sub">{{ $fab->email }}</div>
            </div>
            <div class="fab-date">{{ $fab->created_at->diffForHumans() }}</div>
            <span class="fab-status {{ $fab->statut ?? 'actif' }}">{{ ucfirst($fab->statut ?? 'actif') }}</span>
        </div>
        @empty
        <div style="text-align:center;color:var(--text-light);padding:30px;font-size:13px;">Aucun fabricant inscrit</div>
        @endforelse
    </div>

    {{-- Signalements récents --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Signalements récents
            </div>
            <a href="{{ route('admin.signalements.index') }}" class="btn-sm outline">Voir tout</a>
        </div>
        @forelse($derniersSignalements as $sig)
        <div class="sig-row">
            <div class="sig-dot {{ $sig->statut }}"></div>
            <div style="flex:1;min-width:0;">
                <div class="sig-produit">{{ $sig->qrCode->lot->produit->nom ?? 'Produit inconnu' }}</div>
                <div class="sig-desc">{{ Str::limit($sig->description, 52) }}</div>
            </div>
            <div class="sig-date">{{ $sig->created_at->diffForHumans() }}</div>
            <a href="{{ route('admin.signalements.show', $sig->id) }}" class="btn-sm primary">Voir</a>
        </div>
        @empty
        <div style="text-align:center;color:var(--text-light);padding:30px;font-size:13px;">Aucun signalement</div>
        @endforelse
    </div>

</div>

@endsection

@section('scripts')
<script src="{{ asset('vendor/chart.js/chart.umd.min.js') }}"></script>
<script>
// ── Graphique scans 7 jours ──────────────────────────────────────────────
const scansData = @json($scansParJour);

new Chart(document.getElementById('scansChart'), {
    type: 'bar',
    data: {
        labels: scansData.map(d => d.jour),
        datasets: [{
            label: 'Scans',
            data: scansData.map(d => d.total),
            backgroundColor: 'rgba(46,58,107,0.6)',
            borderColor: '#2E3A6B',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1F2937',
                titleFont: { family: 'DM Sans', size: 12, weight: '700' },
                bodyFont: { family: 'DM Sans', size: 12 },
                padding: 12, cornerRadius: 10,
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { family: 'DM Sans', size: 12 }, color: '#9ca3af' }, border: { display: false } },
            y: { grid: { color: '#f3f4f6' }, ticks: { font: { family: 'DM Sans', size: 11 }, color: '#9ca3af', maxTicksLimit: 5 }, border: { display: false }, beginAtZero: true }
        },
        barPercentage: 0.6, categoryPercentage: 0.7,
    }
});

// ── Donut signalements ───────────────────────────────────────────────────
const sigData = @json($sigParStatut);

new Chart(document.getElementById('sigChart'), {
    type: 'doughnut',
    data: {
        labels: ['En cours', 'Traités', 'Rejetés'],
        datasets: [{
            data: [sigData.en_cours, sigData.traite, sigData.rejete],
            backgroundColor: ['#FCD116', '#4ade80', '#94a3b8'],
            borderWidth: 0,
            hoverOffset: 4,
        }]
    },
    options: {
        responsive: true, cutout: '68%',
        plugins: { legend: { display: false } }
    }
});

// ── Polling AJAX toutes les 60s ─────────────────────────────────────────
setInterval(function() {
    fetch('{{ route("admin.api.stats") }}')
        .then(r => r.json())
        .then(data => {
            document.getElementById('stat-fabricants').textContent   = data.fabricants;
            document.getElementById('stat-produits').textContent     = data.produits;
            document.getElementById('stat-scans').textContent        = data.scans.toLocaleString();
            document.getElementById('stat-signalements').textContent = data.signalements;
        })
        .catch(() => {});
}, 60000);
</script>
@endsection