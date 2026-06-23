@extends('layouts.admin')

@section('title', 'Rapports')

@section('topbar-title')
    Rapports & <span>exports</span>
@endsection

@section('topbar-actions')
    <div style="font-size:12px;color:var(--text-light);">{{ now()->format('d/m/Y') }}</div>
@endsection

@section('styles')
<style>
    /* ── Stats en ligne ── */
    .stats-row {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 14px; margin-bottom: 20px;
    }
    .stat-card {
        background: var(--white); border-radius: 14px;
        padding: 16px 18px; border: 1.5px solid var(--border);
        display: flex; align-items: center; gap: 12px;
        transition: all 0.2s;
    }
    .stat-card:hover { border-color: rgba(15,118,110,0.3); transform: translateY(-2px); box-shadow: 0 4px 16px rgba(15,118,110,0.08); }
    .stat-icon { width: 40px; height: 40px; border-radius: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-icon svg { width: 18px; height: 18px; }
    .stat-icon.teal   { background: #f0fdfa; color: #0F766E; }
    .stat-icon.green  { background: #f0fdf4; color: #007A4D; }
    .stat-icon.yellow { background: #fefce8; color: #a16207; }
    .stat-icon.red    { background: #fef2f2; color: #CE1126; }
    .stat-value { font-size: 24px; font-weight: 800; color: var(--text); line-height: 1; }
    .stat-label { font-size: 11.5px; color: var(--text-light); margin-top: 2px; }

    /* ── Layout principal ── */
    .main-layout {
        display: grid; grid-template-columns: 1fr 340px;
        gap: 20px; align-items: start;
    }

    /* ── Cards ── */
    .card { background: var(--white); border-radius: 16px; border: 1.5px solid var(--border); overflow: hidden; margin-bottom: 16px; }
    .card:last-child { margin-bottom: 0; }
    .card-header { padding: 15px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .card-title { font-size: 13.5px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 7px; }
    .card-title svg { width: 15px; height: 15px; color: var(--teal); }

    /* ── Répartition signalements ── */
    .sig-bars { padding: 20px; display: flex; flex-direction: column; gap: 14px; }
    .sig-bar-item { display: flex; align-items: center; gap: 12px; }
    .sig-bar-label { font-size: 12.5px; font-weight: 600; color: var(--text); width: 70px; flex-shrink: 0; }
    .sig-bar-track { flex: 1; height: 8px; background: var(--bg); border-radius: 20px; overflow: hidden; }
    .sig-bar-fill { height: 100%; border-radius: 20px; transition: width 0.6s ease; }
    .sig-bar-count { font-size: 12.5px; font-weight: 800; color: var(--text); width: 30px; text-align: right; flex-shrink: 0; }

    /* ── Exports ── */
    .export-list { padding: 12px 16px; display: flex; flex-direction: column; gap: 10px; }
    .export-item {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 14px; border-radius: 12px;
        background: var(--bg); border: 1.5px solid var(--border);
        transition: all 0.2s;
    }
    .export-item:hover { border-color: var(--teal); background: var(--teal-light); }
    .export-icon { width: 38px; height: 38px; border-radius: 10px; background: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1.5px solid var(--border); }
    .export-icon svg { width: 16px; height: 16px; color: var(--teal); }
    .export-name { font-size: 13px; font-weight: 700; color: var(--text); }
    .export-desc { font-size: 11px; color: var(--text-light); }
    .btn-dl {
        margin-left: auto; flex-shrink: 0;
        display: inline-flex; align-items: center; gap: 5px;
        background: var(--teal); color: white; border: none;
        border-radius: 8px; padding: 7px 14px; font-size: 12px;
        font-weight: 700; cursor: pointer; font-family: inherit;
        text-decoration: none; transition: all 0.2s;
    }
    .btn-dl:hover { background: var(--teal-dark); }
    .btn-dl svg { width: 12px; height: 12px; }

    /* ── Sidebar droite ── */
    .summary-item { display: flex; align-items: center; justify-content: space-between; padding: 11px 20px; border-bottom: 1px solid var(--border); font-size: 13px; }
    .summary-item:last-child { border-bottom: none; }
    .summary-label { color: var(--text-light); font-weight: 500; display: flex; align-items: center; gap: 7px; }
    .summary-label svg { width: 13px; height: 13px; color: var(--teal); }
    .summary-value { font-weight: 800; color: var(--text); }

    .donut-wrap { display: flex; align-items: center; justify-content: center; gap: 20px; padding: 20px; }
    .donut-legend { display: flex; flex-direction: column; gap: 10px; }
    .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text); }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .legend-count { font-weight: 700; margin-left: auto; padding-left: 12px; }
</style>
@endsection

@section('content')

{{-- Stats en ligne ── --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon teal">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div><div class="stat-value">{{ $stats['fabricants'] }}</div><div class="stat-label">Fabricants</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div><div class="stat-value">{{ $stats['produits'] }}</div><div class="stat-label">Produits</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div><div class="stat-value">{{ number_format($stats['scans']) }}</div><div class="stat-label">Scans</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div><div class="stat-value">{{ $stats['signalements'] }}</div><div class="stat-label">Signalements</div></div>
    </div>
</div>

<div class="main-layout">

    {{-- Colonne principale ── --}}
    <div>

        {{-- Répartition signalements ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Répartition des signalements
                </div>
                <span style="font-size:12px;color:var(--text-light);">Total : {{ $stats['signalements'] }}</span>
            </div>
            @php $total = max($stats['signalements'], 1); @endphp
            <div class="sig-bars">
                <div class="sig-bar-item">
                    <span class="sig-bar-label">En cours</span>
                    <div class="sig-bar-track"><div class="sig-bar-fill" style="width:{{ round($stats['en_cours']/$total*100) }}%;background:#FCD116;"></div></div>
                    <span class="sig-bar-count">{{ $stats['en_cours'] }}</span>
                </div>
                <div class="sig-bar-item">
                    <span class="sig-bar-label">Traités</span>
                    <div class="sig-bar-track"><div class="sig-bar-fill" style="width:{{ round($stats['traites']/$total*100) }}%;background:#4ade80;"></div></div>
                    <span class="sig-bar-count">{{ $stats['traites'] }}</span>
                </div>
                <div class="sig-bar-item">
                    <span class="sig-bar-label">Rejetés</span>
                    <div class="sig-bar-track"><div class="sig-bar-fill" style="width:{{ round($stats['rejetes']/$total*100) }}%;background:#94a3b8;"></div></div>
                    <span class="sig-bar-count">{{ $stats['rejetes'] }}</span>
                </div>
            </div>
        </div>

        {{-- Exports ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exports disponibles
                </div>
            </div>
            <div class="export-list">
                <div class="export-item">
                    <div class="export-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                    <div>
                        <div class="export-name">Rapport global de la plateforme</div>
                        <div class="export-desc">Fabricants · Produits · Scans · Signalements</div>
                    </div>
                    <a href="{{ route('admin.rapports.telecharger') }}?type=global" class="btn-dl">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3"/></svg>
                        PDF
                    </a>
                </div>
                <div class="export-item">
                    <div class="export-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                    <div>
                        <div class="export-name">Rapport des signalements</div>
                        <div class="export-desc">Tous les signalements avec statuts et détails</div>
                    </div>
                    <a href="{{ route('admin.rapports.telecharger') }}?type=signalements" class="btn-dl">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3"/></svg>
                        PDF
                    </a>
                </div>
                <div class="export-item">
                    <div class="export-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    <div>
                        <div class="export-name">Liste des fabricants</div>
                        <div class="export-desc">Tous les fabricants avec statuts et activité</div>
                    </div>
                    <a href="{{ route('admin.rapports.telecharger') }}?type=fabricants" class="btn-dl">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3"/></svg>
                        PDF
                    </a>
                </div>
                <div class="export-item">
                    <div class="export-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg></div>
                    <div>
                        <div class="export-name">Rapport QR Codes & lots</div>
                        <div class="export-desc">Tous les QR générés par fabricant</div>
                    </div>
                    <a href="{{ route('admin.rapports.telecharger') }}?type=qrcodes" class="btn-dl">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3"/></svg>
                        PDF
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Sidebar droite ── --}}
    <div>

        {{-- Résumé plateforme ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Résumé plateforme
                </div>
            </div>
            <div class="summary-item">
                <span class="summary-label"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Fabricants</span>
                <span class="summary-value">{{ $stats['fabricants'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>Produits</span>
                <span class="summary-value">{{ $stats['produits'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>Scans totaux</span>
                <span class="summary-value">{{ number_format($stats['scans']) }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>Signalements</span>
                <span class="summary-value">{{ $stats['signalements'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label" style="color:#a16207;">⚠ En cours</span>
                <span class="summary-value" style="color:#a16207;">{{ $stats['en_cours'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label" style="color:#007A4D;">✓ Traités</span>
                <span class="summary-value" style="color:#007A4D;">{{ $stats['traites'] }}</span>
            </div>
        </div>

        {{-- Donut ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                    Signalements par statut
                </div>
            </div>
            <div class="donut-wrap">
                <canvas id="donutChart" style="max-width:130px;max-height:130px;flex-shrink:0;"></canvas>
                <div class="donut-legend">
                    <div class="legend-item"><div class="legend-dot" style="background:#FCD116;"></div>En cours<span class="legend-count">{{ $stats['en_cours'] }}</span></div>
                    <div class="legend-item"><div class="legend-dot" style="background:#4ade80;"></div>Traités<span class="legend-count">{{ $stats['traites'] }}</span></div>
                    <div class="legend-item"><div class="legend-dot" style="background:#94a3b8;"></div>Rejetés<span class="legend-count">{{ $stats['rejetes'] }}</span></div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@section('scripts')
<script>
new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: ['En cours', 'Traités', 'Rejetés'],
        datasets: [{
            data: [{{ $stats['en_cours'] }}, {{ $stats['traites'] }}, {{ $stats['rejetes'] }}],
            backgroundColor: ['#FCD116', '#4ade80', '#94a3b8'],
            borderWidth: 0, hoverOffset: 4,
        }]
    },
    options: { responsive: true, cutout: '68%', plugins: { legend: { display: false } } }
});
</script>
@endsection