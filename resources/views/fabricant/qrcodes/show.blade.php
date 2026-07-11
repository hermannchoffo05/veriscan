@extends('layouts.fabricant')
@section('title', 'QR Code ' . $qrcode->token)
@section('topbar-title'){{ __('messages.detail') }} <span>QR code</span>@endsection
@section('topbar-actions')
    <a href="{{ route('fabricant.qrcodes.download', $qrcode->id) }}" class="btn-topbar btn-topbar-primary">
        ↓ {{ __('messages.telecharger_png') }}
    </a>
@endsection
@section('styles')
<style>
.breadcrumb{display:flex;align-items:center;gap:6px;margin-bottom:20px;font-size:13px;}
.breadcrumb a{color:#6b7280;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;transition:color 0.2s;}
.breadcrumb a:hover{color:#2E3A6B;}
.breadcrumb a svg{width:13px;height:13px;}
.breadcrumb span{color:#d1d5db;}
.breadcrumb strong{color:#1f2937;font-weight:600;}
.qr-layout{display:grid;grid-template-columns:280px 1fr;gap:24px;align-items:start;}
.qr-card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:24px;text-align:center;}
.qr-image{width:200px;height:200px;margin:0 auto 14px;}
.qr-image img{width:100%;height:100%;border-radius:8px;}
.qr-token{font-family:monospace;font-size:12px;color:#6b7280;word-break:break-all;margin-bottom:4px;}
.btn-download{display:block;width:100%;padding:10px;background:#F5A623;color:#171B3D;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none;margin-top:14px;text-align:center;}
.btn-download:hover{background:#e0961d;}
.btn-consumer{display:block;width:100%;padding:10px;background:#EEF0F8;color:#2E3A6B;border:1.5px solid rgba(46,58,107,0.2);border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;margin-top:10px;text-align:center;}
.info-card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;}
.info-header{padding:18px 22px;border-bottom:1px solid #f3f4f6;}
.info-title{font-size:16px;font-weight:700;color:#1f2937;}
.info-row{display:flex;padding:13px 22px;border-top:1px solid #f9fafb;}
.info-key{width:180px;font-size:13px;font-weight:600;color:#6b7280;flex-shrink:0;}
.info-val{font-size:14px;color:#1f2937;word-break:break-all;}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-green{background:#dcfce7;color:#16a34a;}
.badge-red{background:#fee2e2;color:#dc2626;}
.btn-topbar-primary{background:#F5A623;color:#171B3D !important;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:700;text-decoration:none;}

@media (max-width: 768px) {
    .qr-layout { grid-template-columns: 1fr; }
    .qr-card { padding: 20px 16px; }
    .qr-image { width: 180px; height: 180px; }
    .info-row { flex-direction: column; gap: 3px; padding: 12px 16px; }
    .info-key { width: 100%; font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; }
    .info-header { padding: 14px 16px; }
}
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('fabricant.qrcodes.index') }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        QR Codes
    </a>
    <span>/</span>
    <strong>{{ $qrcode->token }}</strong>
</div>

<div class="qr-layout">
    <div class="qr-card">
        <div class="qr-image">
            <img src="data:image/png;base64,{{ $qrImage }}" alt="QR Code">
        </div>
        <div class="qr-token">{{ $qrcode->token }}</div>
      <a href="{{ route('fabricant.qrcodes.download', $qrcode->id) }}" class="btn-download">
    ↓ Télécharger PNG
</a>
<a href="{{ route('fabricant.qrcodes.download-pdf', $qrcode->id) }}" class="btn-download" style="background:#171B3D;color:#fff;margin-top:8px;">
    ↓ Télécharger PDF
</a>
<a href="{{ route('verify.token', $qrcode->token) }}" target="_blank" class="btn-consumer">
    👁 Voir page consommateur
</a>
    </div>

    <div class="info-card">
        <div class="info-header">
            <div class="info-title">{{ __('messages.infos_qrcode') }}</div>
        </div>
        <div class="info-row">
            <span class="info-key">{{ __('messages.produit') }}</span>
            <span class="info-val">
                <a href="{{ route('fabricant.produits.show', $qrcode->lot->produit->id) }}" style="color:#2E3A6B;text-decoration:none;font-weight:600">
                    {{ $qrcode->lot->produit->nom }}
                </a>
            </span>
        </div>
        <div class="info-row">
            <span class="info-key">{{ __('messages.numero_lot') }}</span>
            <span class="info-val">{{ $qrcode->lot->numero_lot }}</span>
        </div>
        <div class="info-row">
            <span class="info-key">{{ __('messages.date_fabrication') }}</span>
            <span class="info-val">{{ $qrcode->lot->date_fabrication->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-key">{{ __('messages.date_expiration') }}</span>
            <span class="info-val">{{ $qrcode->lot->date_expiration->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-key">Token</span>
            <span class="info-val" style="font-family:monospace;font-size:12px;">{{ $qrcode->token }}</span>
        </div>
        <div class="info-row">
            <span class="info-key">{{ __('messages.statut') }}</span>
            <span class="info-val">
                <span class="badge {{ $qrcode->statut==='actif'?'badge-green':'badge-red' }}">
                    {{ $qrcode->statut==='actif'?__('messages.actif'):__('messages.inactif') }}
                </span>
            </span>
        </div>
        <div class="info-row">
            <span class="info-key">{{ __('messages.nb_scans') }}</span>
            <span class="info-val"><strong>{{ $qrcode->nb_scans }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-key">{{ __('messages.cree_le') }}</span>
            <span class="info-val">{{ $qrcode->created_at->format('d/m/Y à H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-key">URL publique</span>
            <span class="info-val" style="font-family:monospace;font-size:11px;color:#2E3A6B;">
                {{ route('verify.token', $qrcode->token) }}
            </span>
        </div>
    </div>
</div>
@endsection