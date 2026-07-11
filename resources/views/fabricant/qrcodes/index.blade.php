{{-- resources/views/fabricant/qrcodes/index.blade.php --}}
@extends('layouts.fabricant')
@section('title', __('messages.qr_codes'))
@section('topbar-title') QR <span>Codes</span> @endsection
@section('topbar-actions')
    <a href="{{ route('fabricant.qrcodes.create') }}" class="btn-topbar btn-topbar-primary">
        + {{ __('messages.generer_qr') }}
    </a>
@endsection
@section('styles')
<style>
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 22px; font-weight: 800; color: var(--text); }
    .page-header p { font-size: 13px; color: var(--text-light); margin-top: 3px; }
    .btn-topbar-primary { background: #F5A623; color: #171B3D !important; border-radius: 8px; padding: 8px 16px; font-size: 13px; font-weight: 700; text-decoration: none; }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; }
    .card { background: white; border-radius: 16px; border: 1.5px solid var(--border); overflow: hidden; margin-bottom: 20px; }
    .card-header { padding: 18px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .card-title { font-size: 14px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 8px; }
    .card-title svg { width: 16px; height: 16px; color: var(--teal); }
    .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { width: 100%; border-collapse: collapse; min-width: 600px; }
    th { font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.06em; padding: 10px 14px; text-align: left; border-bottom: 1px solid var(--border); background: var(--bg); white-space: nowrap; }
    td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--teal-light); }
    .btn-sm { padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; transition: all 0.2s; border: none; white-space: nowrap; }
    .btn-sm svg { width: 12px; height: 12px; }
    .btn-sm.teal { background: var(--teal-light); color: var(--teal); }
    .btn-sm.teal:hover { background: var(--teal); color: white; }
    .btn-sm.gray { background: #f3f4f6; color: #374151; }
    .btn-sm.gray:hover { background: #e5e7eb; }
    .btn-sm.green { background: #f0fdf4; color: #007A4D; border: 1px solid #bbf7d0; }
    .btn-sm.green:hover { background: #dcfce7; }
    .badge { display: inline-block; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-green { background: #dcfce7; color: #16a34a; }
    .badge-red { background: #fee2e2; color: #dc2626; }
    .token-mono { font-family: monospace; font-size: 11px; color: var(--text-light); word-break: break-all; }
    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-state svg { width: 40px; height: 40px; margin: 0 auto 14px; display: block; color: var(--teal); opacity: 0.4; }
    .empty-state h3 { font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 6px; }
    .empty-state p { font-size: 13px; color: var(--text-light); margin-bottom: 16px; }

    .qr-mobile-list { display: none; }
    .qr-mobile-card { background: white; border: 1.5px solid var(--border); border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; }
    .qr-mobile-token { font-family: monospace; font-size: 11px; color: var(--text-light); margin-bottom: 8px; word-break: break-all; }
    .qr-mobile-produit { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .qr-mobile-meta { font-size: 12px; color: var(--text-light); margin-bottom: 10px; display: flex; gap: 12px; flex-wrap: wrap; }
    .qr-mobile-actions { display: flex; gap: 8px; flex-wrap: wrap; }

    @media (max-width: 768px) {
        .table-wrap { display: none; }
        .qr-mobile-list { display: block; padding: 14px; }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1>{{ __('messages.qr_codes') }}</h1>
    <p>{{ __('messages.qr_codes_desc') }}</p>
</div>

@if(session('success'))
    <div class="alert-success">✓ {{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg>
            {{ __('messages.historique_lots') }}
        </div>
        <span style="font-size:12px;color:var(--text-light);font-weight:600;">{{ $qrcodes->total() }} QR code(s)</span>
    </div>

    @if($qrcodes->isEmpty())
    <div class="empty-state">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg>
        <h3>{{ __('messages.aucun_qr') }}</h3>
        <p>{{ __('messages.aucun_qr_desc') }}</p>
        <a href="{{ route('fabricant.qrcodes.create') }}" class="btn-sm teal">{{ __('messages.generer_qr') }}</a>
    </div>
    @else

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Token</th>
                    <th>{{ __('messages.produit') }}</th>
                    <th>{{ __('messages.numero_lot') }}</th>
                    <th>{{ __('messages.statut') }}</th>
                    <th>{{ __('messages.nb_scans') }}</th>
                    <th>{{ __('messages.cree_le') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($qrcodes as $qr)
                <tr>
                    <td><span class="token-mono">{{ $qr->token }}</span></td>
                    <td><strong>{{ $qr->lot->produit->nom }}</strong></td>
                    <td>{{ $qr->lot->numero_lot }}</td>
                    <td><span class="badge {{ $qr->statut==='actif'?'badge-green':'badge-red' }}">{{ $qr->statut==='actif'?__('messages.actif'):__('messages.inactif') }}</span></td>
                    <td><strong>{{ $qr->nb_scans }}</strong></td>
                    <td>{{ $qr->created_at->format('d/m/Y') }}</td>
                    <td style="display:flex;gap:6px;align-items:center;flex-wrap:nowrap;">
                        <a href="{{ route('fabricant.qrcodes.download-pdf', $qr->id) }}" class="btn-sm teal">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>PDF</a>
                        <a href="{{ route('fabricant.qrcodes.show', $qr->id) }}" class="btn-sm gray">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            {{ __('messages.voir') }}
                        </a>
                        <a href="{{ route('verify.token', $qr->token) }}" target="_blank" class="btn-sm green">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('messages.verifier_produit') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="qr-mobile-list">
        @foreach($qrcodes as $qr)
        <div class="qr-mobile-card">
            <div class="qr-mobile-token">{{ $qr->token }}</div>
            <div class="qr-mobile-produit">{{ $qr->lot->produit->nom }}</div>
            <div class="qr-mobile-meta">
                <span>Lot : {{ $qr->lot->numero_lot }}</span>
                <span>{{ $qr->nb_scans }} scan(s)</span>
                <span>{{ $qr->created_at->format('d/m/Y') }}</span>
                <span class="badge {{ $qr->statut==='actif'?'badge-green':'badge-red' }}">{{ $qr->statut==='actif'?__('messages.actif'):__('messages.inactif') }}</span>
            </div>
            <div class="qr-mobile-actions">
                <a href="{{ route('fabricant.qrcodes.download-pdf', $qr->id) }}" class="btn-sm teal">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>PDF</a>
                <a href="{{ route('fabricant.qrcodes.show', $qr->id) }}" class="btn-sm gray">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('messages.voir') }}
                </a>
                <a href="{{ route('verify.token', $qr->token) }}" target="_blank" class="btn-sm green">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Vérifier
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div style="padding:16px 22px;">{{ $qrcodes->links() }}</div>
    @endif
</div>
@endsection