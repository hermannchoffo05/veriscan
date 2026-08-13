@extends('layouts.fabricant')
@section('title', __('messages.statistiques'))
@section('topbar-title') {{ __('messages.mes') }} <span>{{ __('messages.statistiques') }}</span> @endsection

@section('styles')
<style>
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 22px; font-weight: 800; color: var(--text); }
    .page-header p { font-size: 13px; color: var(--text-light); margin-top: 3px; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 24px 20px;
        border: 1.5px solid var(--border);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }
    .stat-icon svg { width: 24px; height: 24px; stroke-width: 2.2; }
    .stat-icon.teal { background: var(--teal-light); color: var(--teal); }
    .stat-icon.green { background: #f0fdf4; color: var(--green-ok); }
    .stat-icon.yellow { background: #fefce8; color: #a16207; }
    .stat-icon.red { background: #fef2f2; color: var(--red); }
    .stat-value { font-size: 30px; font-weight: 800; color: var(--text); line-height: 1.2; }
    .stat-label { font-size: 12.5px; color: var(--text-light); margin-top: 4px; }
    .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .card { background: white; border-radius: 16px; border: 1.5px solid var(--border); overflow: hidden; }
    .card-header { padding: 16px 20px; border-bottom: 1px solid var(--border); }
    .card-title { font-size: 14px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 8px; }
    .card-title svg { width: 15px; height: 15px; color: var(--teal); }
    .card-body { padding: 20px; }
    .chart-wrap { position: relative; height: 200px; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1>{{ __('messages.statistiques') }}</h1>
        <p>{{ __('messages.statistiques_desc') }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon teal">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="stat-value">{{ $totalProduits }}</div>
            <div class="stat-label">{{ __('messages.produits_actifs') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4z"/></svg>
            </div>
            <div class="stat-value">{{ $totalQrcodes }}</div>
            <div class="stat-label">{{ __('messages.qr_codes_generes') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon yellow">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"/></svg>
            </div>
            <div class="stat-value">{{ $totalScans }}</div>
            <div class="stat-label">{{ __('messages.scans_totaux') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/></svg>
            </div>
            <div class="stat-value">{{ $totalSignalements }}</div>
            <div class="stat-label">{{ __('messages.signalements') }}</div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    {{ __('messages.scans_par_jour') }}
                </div>
            </div>
            <div class="card-body">
                <div class="chart-wrap"><canvas id="scanChart"></canvas></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    {{ __('messages.scans_par_produit') }}
                </div>
            </div>
            <div class="card-body">
                <div class="chart-wrap"><canvas id="produitChart"></canvas></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    new Chart(document.getElementById('scanChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: [
                '{{ __("messages.lun") }}','{{ __("messages.mar") }}','{{ __("messages.mer") }}',
                '{{ __("messages.jeu") }}','{{ __("messages.ven") }}','{{ __("messages.sam") }}','{{ __("messages.dim") }}'
            ],
            datasets: [
                {
                    label: '{{ __("messages.authentiques") }}',
                    data: @json($scans7jours),
                    backgroundColor: 'rgba(46,58,107,0.85)',
                    borderRadius: 6
                },
                {
                    label: '{{ __("messages.suspects") }}',
                    data: @json($suspects7jours),
                    backgroundColor: 'rgba(252,209,22,0.85)',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top', labels: { font: { family: 'DM Sans', size: 11 } } } },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#f3f4f6' } } }
        }
    });

    new Chart(document.getElementById('produitChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: @json($topProduits->pluck('nom')),
            datasets: [{
                data: @json($topProduits->pluck('total_scans')->values()),
                backgroundColor: ['#2E3A6B','#4A5899','#F5A623','#e5e7eb','#94a3b8'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { font: { family: 'DM Sans', size: 11 } } } }
        }
    });
</script>
@endsection