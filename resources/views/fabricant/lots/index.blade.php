@extends('layouts.fabricant')

@section('title', __('messages.lots_de') . ' ' . $produit->nom)

@section('topbar-title')
    {{ __('messages.lots_production') }}
@endsection

@section('topbar-actions')
    <a href="{{ route('fabricant.lots.create', $produit->id) }}" class="btn-topbar btn-topbar-primary">
        + {{ __('messages.nouveau_lot') }}
    </a>
@endsection

@section('styles')
<style>
.breadcrumb { display:flex;align-items:center;gap:6px;margin-bottom:20px;font-size:13px; }
.breadcrumb a { color:#6b7280;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;transition:color 0.2s; }
.breadcrumb a:hover { color:#0f766e; }
.breadcrumb a svg { width:13px;height:13px; }
.breadcrumb span { color:#d1d5db; }
.breadcrumb strong { color:#1f2937;font-weight:600; }
.product-info-bar { background:#f0fdfa;border:1px solid #99f6e4;border-radius:12px;padding:14px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px; }
.product-info-bar strong { color:#0f766e; }
.product-info-bar span { font-size:13px;color:#374151; }
.table-card { background:#fff;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden; }
.table-header { padding:20px 24px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center; }
.table-title { font-size:16px;font-weight:700;color:#1f2937; }
table { width:100%;border-collapse:collapse; }
th { background:#f9fafb;padding:12px 16px;text-align:left;font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em; }
td { padding:14px 16px;border-top:1px solid #f3f4f6;font-size:14px;color:#1f2937; }
tr:hover td { background:#fafafa; }
.badge { display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600; }
.badge-green { background:#dcfce7;color:#16a34a; }
.badge-red { background:#fee2e2;color:#dc2626; }
.btn-sm { padding:5px 12px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:.2s;display:inline-block; }
.btn-sm-teal { background:#f0fdfa;color:#0f766e; }
.btn-sm-teal:hover { background:#ccfbf1; }
.btn-sm-red { background:#fef2f2;color:#dc2626; }
.btn-sm-red:hover { background:#fee2e2; }
.btn-topbar-primary { background:#0f766e;color:#fff !important;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600;text-decoration:none; }
.empty-state { text-align:center;padding:48px 24px;color:#6b7280; }
.empty-state svg { width:48px;height:48px;margin-bottom:12px;color:#d1d5db; }
.alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px; }
</style>
@endsection

@section('content')

<div class="breadcrumb">
    <a href="{{ route('fabricant.produits.index') }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ __('messages.mes_produits') }}
    </a>
    <span>/</span>
    <a href="{{ route('fabricant.produits.show', $produit->id) }}">{{ $produit->nom }}</a>
    <span>/</span>
    <strong>{{ __('messages.lots_production') }}</strong>
</div>

@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif

<div class="product-info-bar">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20" style="color:#0f766e;flex-shrink:0">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
    </svg>
    <span>{{ __('messages.produit') }} : <strong>{{ $produit->nom }}</strong> — {{ $produit->code_produit }}</span>
</div>

<div class="table-card">
    <div class="table-header">
        <span class="table-title">{{ $lots->total() }} {{ __('messages.lot_s') }}</span>
    </div>
    @if($lots->isEmpty())
    <div class="empty-state">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        <p>{{ __('messages.aucun_lot') }}</p>
        <a href="{{ route('fabricant.lots.create', $produit->id) }}" class="btn-sm btn-sm-teal">{{ __('messages.creer_lot') }}</a>
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th>{{ __('messages.numero_lot') }}</th>
                <th>{{ __('messages.date_fabrication') }}</th>
                <th>{{ __('messages.date_expiration') }}</th>
                <th>{{ __('messages.quantite') }}</th>
                <th>{{ __('messages.site_production') }}</th>
                <th>{{ __('messages.statut') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lots as $lot)
            <tr>
                <td><strong>{{ $lot->numero_lot }}</strong></td>
                <td>{{ $lot->date_fabrication->format('d/m/Y') }}</td>
                <td>{{ $lot->date_expiration->format('d/m/Y') }}</td>
                <td>{{ number_format($lot->quantite) }}</td>
                <td>{{ $lot->site_production ?? '—' }}</td>
                <td>
                    @if($lot->date_expiration->isPast())
                        <span class="badge badge-red">{{ __('messages.expire') }}</span>
                    @else
                        <span class="badge badge-green">{{ __('messages.actif') }}</span>
                    @endif
                </td>
                <td style="display:flex;gap:6px">
                    <a href="{{ route('fabricant.qrcodes.create') }}?lot_id={{ $lot->id }}" class="btn-sm btn-sm-teal">{{ __('messages.generer_qr') }}</a>
                    <a href="{{ route('fabricant.lots.download-pdf', $lot->id) }}" class="btn-sm btn-sm-teal">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        PDF lot
                    </a>
                    <form action="{{ route('fabricant.lots.destroy', [$produit->id, $lot->id]) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirmer_suppression_lot') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-sm-red">{{ __('messages.supprimer') }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding:16px 24px">{{ $lots->links() }}</div>
    @endif
</div>
@endsection