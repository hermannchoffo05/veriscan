@extends('layouts.fabricant')
@section('title', __('messages.signalement') . ' #' . $signalement->id)
@section('topbar-title')
    {{ __('messages.detail') }} <span>{{ __('messages.signalement') }}</span>
@endsection
@section('topbar-actions')
@endsection
@section('styles')
<style>
.breadcrumb{display:flex;align-items:center;gap:6px;margin-bottom:20px;font-size:13px;}
.breadcrumb a{color:#6b7280;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;transition:color 0.2s;}
.breadcrumb a:hover{color:#2E3A6B;}
.breadcrumb a svg{width:13px;height:13px;}
.breadcrumb span{color:#d1d5db;}
.breadcrumb strong{color:#1f2937;font-weight:600;}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;}
.card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;}
.card-header{padding:20px 24px;border-bottom:1px solid #f3f4f6;}
.card-title{font-size:16px;font-weight:700;color:#1f2937;}
.info-row{display:flex;padding:14px 24px;border-top:1px solid #f9fafb;}
.info-key{width:160px;font-size:13px;font-weight:600;color:#6b7280;flex-shrink:0;}
.info-val{font-size:14px;color:#1f2937;flex:1;}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;}
.badge-yellow{background:#fef9c3;color:#ca8a04;}
.badge-green{background:#dcfce7;color:#16a34a;}
.badge-red{background:#fee2e2;color:#dc2626;}
.description-box{padding:20px 24px;font-size:14px;color:#374151;line-height:1.7;}
.alert-info{background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:14px 20px;border-radius:10px;font-size:13px;margin-bottom:24px;}
.alert-warning{background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:14px 20px;border-radius:10px;font-size:13px;margin-bottom:24px;}
.gps-box{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 20px;margin:14px 24px;font-size:13px;color:#166534;}

@media (max-width: 768px) {
    .detail-grid { grid-template-columns: 1fr; gap: 16px; }
    .info-row { flex-direction: column; gap: 3px; padding: 12px 16px; }
    .info-key { width: 100%; font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; color: #9ca3af; }
    .info-val { font-size: 13px; }
    .card-header { padding: 14px 16px; }
    .description-box { padding: 14px 16px; }
}
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('fabricant.signalements.index') }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ __('messages.signalements') }}
    </a>
    <span>/</span>
    <strong>Détail #{{ $signalement->id }}</strong>
</div>

@if($signalement->statut === 'en_cours')
<div class="alert-info">⏳ {{ __('messages.signalement_en_cours') }}</div>
@endif

@if(!$signalement->qrCode)
<div class="alert-warning">⚠️ Ce signalement a été soumis sans QR code — il provient de l'application mobile.</div>
@endif

<div class="detail-grid">
    <div class="card">
        <div class="card-header"><div class="card-title">{{ __('messages.infos_signalement') }}</div></div>

        <div class="info-row">
            <span class="info-key">{{ __('messages.signalant') }}</span>
            <span class="info-val">{{ $signalement->nom_signalant ?? __('messages.anonyme') }}</span>
        </div>

        <div class="info-row">
            <span class="info-key">{{ __('messages.contact') }}</span>
            <span class="info-val">{{ $signalement->contact_signalant ?? '—' }}</span>
        </div>

        <div class="info-row">
            <span class="info-key">{{ __('messages.statut') }}</span>
            <span class="info-val">
                @if($signalement->statut === 'en_cours')
                    <span class="badge badge-yellow">{{ __('messages.en_cours') }}</span>
                @elseif($signalement->statut === 'traite')
                    <span class="badge badge-green">{{ __('messages.traite') }}</span>
                @else
                    <span class="badge badge-red">{{ __('messages.rejete') }}</span>
                @endif
            </span>
        </div>

        <div class="info-row">
            <span class="info-key">{{ __('messages.date') }}</span>
            <span class="info-val">{{ $signalement->created_at->format('d/m/Y à H:i') }}</span>
        </div>

        @if($signalement->qrCode && $signalement->qrCode->lot && $signalement->qrCode->lot->produit)
        <div class="info-row">
            <span class="info-key">{{ __('messages.produit') }}</span>
            <span class="info-val">
                <a href="{{ route('fabricant.produits.show', $signalement->qrCode->lot->produit->id) }}" style="color:#2E3A6B;text-decoration:none;font-weight:600">
                    {{ $signalement->qrCode->lot->produit->nom }}
                </a>
            </span>
        </div>
        <div class="info-row">
            <span class="info-key">{{ __('messages.numero_lot') }}</span>
            <span class="info-val">{{ $signalement->qrCode->lot->numero_lot }}</span>
        </div>
        <div class="info-row">
            <span class="info-key">Token QR</span>
            <span class="info-val" style="font-family:monospace;font-size:12px;word-break:break-all;">{{ $signalement->qrCode->token }}</span>
        </div>
        @else
        <div class="info-row">
            <span class="info-key">{{ __('messages.produit') }}</span>
            <span class="info-val" style="color:#9ca3af;font-style:italic;">Non associé à un produit</span>
        </div>
        @endif

        @if($signalement->latitude && $signalement->longitude)
        <div class="gps-box">
            📍 <strong>Position GPS capturée</strong><br>
            Lat: {{ $signalement->latitude }} — Lng: {{ $signalement->longitude }}<br>
            @if($signalement->localisation)
                📌 {{ $signalement->localisation }}
            @endif
            @if($signalement->region)
                — Région: {{ $signalement->region }}
            @endif
        </div>
        @endif

        @if($signalement->statut === 'en_cours')
        <div style="padding:16px 24px;">
            <form method="POST" action="{{ route('fabricant.signalements.traiter', $signalement->id) }}">
                @csrf
                @method('PATCH')
                <button type="submit" style="background:#F5A623;color:#171B3D;border:none;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;">
                    ✅ Marquer comme traité
                </button>
            </form>
        </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">{{ __('messages.description') }}</div></div>
        <div class="description-box">{{ $signalement->description ?? '—' }}</div>
        @if($signalement->photo_preuve)
        <div style="padding:0 24px 20px;">
            <img src="{{ asset('storage/'.$signalement->photo_preuve) }}" style="width:100%;border-radius:10px;max-height:220px;object-fit:cover;" alt="Photo preuve">
        </div>
        @endif
    </div>
</div>
@endsection