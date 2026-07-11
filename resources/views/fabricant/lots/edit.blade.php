@extends('layouts.fabricant')

@section('title', __('Modification Lot'))

@section('topbar-title')
    {{ __('Modification Lot') }}
@endsection

@section('topbar-actions')
@endsection

@section('styles')
<style>
.breadcrumb { display:flex;align-items:center;gap:6px;margin-bottom:20px;font-size:13px;flex-wrap:wrap; }
.breadcrumb a { color:#6b7280;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;transition:color 0.2s; }
.breadcrumb a:hover { color:#2E3A6B; }
.breadcrumb a svg { width:13px;height:13px; }
.breadcrumb span { color:#d1d5db; }
.breadcrumb strong { color:#1f2937;font-weight:600; }
.form-card { background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:32px;max-width:720px;margin:0 auto; }
.product-info-bar { background:#EEF0F8;border:1px solid #c7cce6;border-radius:12px;padding:14px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px; }
.form-title { font-size:18px;font-weight:700;color:#1f2937;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid #f3f4f6; }
.form-group { margin-bottom:20px; }
.form-label { display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px; }
.form-label .required { color:#ef4444; }
.form-control { width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#1f2937;background:#f9fafb;transition:.2s;box-sizing:border-box; }
.form-control:focus { outline:none;border-color:#2E3A6B;box-shadow:0 0 0 3px rgba(46,58,107,.1);background:#fff; }
.form-control.is-invalid { border-color:#ef4444; }
.invalid-feedback { font-size:12px;color:#ef4444;margin-top:4px; }
.form-row { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.form-actions { display:flex;gap:12px;justify-content:flex-end;margin-top:28px;padding-top:20px;border-top:1px solid #f3f4f6;flex-wrap:wrap; }
.btn-primary { padding:10px 24px;background:#F5A623;color:#171B3D;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px; }
.btn-primary:hover { background:#e0961d; }
.btn-secondary { padding:10px 24px;background:#f3f4f6;color:#374151;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center; }
.btn-secondary:hover { background:#e5e7eb; }
.alert-error { background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px; }
.alert-error ul { margin:6px 0 0 16px; }
.lot-badge { display:inline-flex;align-items:center;gap:6px;background:#EEF0F8;border:1px solid #c7cce6;border-radius:8px;padding:4px 12px;font-size:12px;font-weight:700;color:#2E3A6B;margin-left:8px; }

@media (max-width: 768px) {
    .form-card { padding: 20px 16px; }
    .form-row { grid-template-columns: 1fr; }
    .form-actions { justify-content: stretch; }
    .form-actions a, .form-actions button { flex: 1; justify-content: center; }
}
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
    <strong>{{ __('Modification Lot') }}</strong>
    <span class="lot-badge">{{ $lot->numero_lot }}</span>
</div>

<div class="product-info-bar">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20" style="color:#2E3A6B;flex-shrink:0">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
    </svg>
    <span style="font-size:13px;color:#374151">{{ __('messages.produit') }} : <strong style="color:#2E3A6B">{{ $produit->nom }}</strong></span>
</div>

<div class="form-card">
    <div class="form-title">
        {{ __('Modifier Lot') }}
        <span class="lot-badge">{{ $lot->numero_lot }}</span>
    </div>

    @if($errors->any())
    <div class="alert-error">
        <strong>{{ __('messages.erreurs_validation') }}</strong>
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('fabricant.lots.update', [$produit->id, $lot->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">{{ __('messages.numero_lot') }} <span class="required">*</span></label>
            <input type="text" name="numero_lot"
                   class="form-control @error('numero_lot') is-invalid @enderror"
                   value="{{ old('numero_lot', $lot->numero_lot) }}"
                   placeholder="{{ __('messages.placeholder_numero_lot') }}">
            @error('numero_lot') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">{{ __('messages.date_fabrication') }} <span class="required">*</span></label>
                <input type="date" name="date_fabrication"
                       class="form-control @error('date_fabrication') is-invalid @enderror"
                       value="{{ old('date_fabrication', $lot->date_fabrication->format('Y-m-d')) }}">
                @error('date_fabrication') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('messages.date_expiration') }} <span class="required">*</span></label>
                <input type="date" name="date_expiration"
                       class="form-control @error('date_expiration') is-invalid @enderror"
                       value="{{ old('date_expiration', $lot->date_expiration->format('Y-m-d')) }}">
                @error('date_expiration') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">{{ __('messages.quantite_produite') }} <span class="required">*</span></label>
                <input type="number" name="quantite" min="1"
                       class="form-control @error('quantite') is-invalid @enderror"
                       value="{{ old('quantite', $lot->quantite) }}"
                       placeholder="{{ __('messages.placeholder_quantite') }}">
                @error('quantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('messages.site_production') }}</label>
                <input type="text" name="site_production"
                       class="form-control @error('site_production') is-invalid @enderror"
                       value="{{ old('site_production', $lot->site_production) }}"
                       placeholder="{{ __('messages.placeholder_site') }}">
                @error('site_production') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('fabricant.produits.show', $produit->id) }}" class="btn-secondary">
                {{ __('messages.annuler') }}
            </a>
            <button type="submit" class="btn-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('messages.enregistrer') }}
            </button>
        </div>
    </form>
</div>

@endsection