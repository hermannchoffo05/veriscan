@extends('layouts.fabricant')

@section('title', __('messages.nouveau_lot'))

@section('topbar-title')
    {{ __('messages.nouveau_lot') }}
@endsection

@section('topbar-actions')
@endsection

@section('styles')
<style>
.breadcrumb { display:flex;align-items:center;gap:6px;margin-bottom:20px;font-size:13px; }
.breadcrumb a { color:#6b7280;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;transition:color 0.2s; }
.breadcrumb a:hover { color:#0f766e; }
.breadcrumb a svg { width:13px;height:13px; }
.breadcrumb span { color:#d1d5db; }
.breadcrumb strong { color:#1f2937;font-weight:600; }
.form-card { background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:32px;max-width:720px;margin:0 auto; }
.product-info-bar { background:#f0fdfa;border:1px solid #99f6e4;border-radius:12px;padding:14px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px; }
.form-title { font-size:18px;font-weight:700;color:#1f2937;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid #f3f4f6; }
.form-group { margin-bottom:20px; }
.form-label { display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px; }
.form-label .required { color:#ef4444; }
.form-control { width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#1f2937;background:#f9fafb;transition:.2s;box-sizing:border-box; }
.form-control:focus { outline:none;border-color:#0f766e;box-shadow:0 0 0 3px rgba(15,118,110,.1);background:#fff; }
.form-control.is-invalid { border-color:#ef4444; }
.invalid-feedback { font-size:12px;color:#ef4444;margin-top:4px; }
.form-row { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.form-actions { display:flex;gap:12px;justify-content:flex-end;margin-top:28px;padding-top:20px;border-top:1px solid #f3f4f6; }
.btn-primary { padding:10px 24px;background:#0f766e;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer; }
.btn-primary:hover { background:#0d6560; }
.btn-secondary { padding:10px 24px;background:#f3f4f6;color:#374151;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none; }
.btn-secondary:hover { background:#e5e7eb; }
.alert-error { background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px; }
.alert-error ul { margin:6px 0 0 16px; }
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
    <strong>{{ __('messages.nouveau_lot') }}</strong>
</div>

<div class="product-info-bar">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20" style="color:#0f766e;flex-shrink:0">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
    </svg>
    <span style="font-size:13px;color:#374151">{{ __('messages.produit') }} : <strong style="color:#0f766e">{{ $produit->nom }}</strong></span>
</div>

<div class="form-card">
    <div class="form-title">{{ __('messages.infos_lot') }}</div>
    @if($errors->any())
    <div class="alert-error">
        <strong>{{ __('messages.erreurs_validation') }}</strong>
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif
    <form action="{{ route('fabricant.lots.store', $produit->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label">{{ __('messages.numero_lot') }} <span class="required">*</span></label>
            <input type="text" name="numero_lot" class="form-control @error('numero_lot') is-invalid @enderror" value="{{ old('numero_lot') }}" placeholder="{{ __('messages.placeholder_numero_lot') }}">
            @error('numero_lot') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">{{ __('messages.date_fabrication') }} <span class="required">*</span></label>
                <input type="date" name="date_fabrication" class="form-control @error('date_fabrication') is-invalid @enderror" value="{{ old('date_fabrication') }}">
                @error('date_fabrication') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('messages.date_expiration') }} <span class="required">*</span></label>
                <input type="date" name="date_expiration" class="form-control @error('date_expiration') is-invalid @enderror" value="{{ old('date_expiration') }}">
                @error('date_expiration') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">{{ __('messages.quantite_produite') }} <span class="required">*</span></label>
                <input type="number" name="quantite" min="1" class="form-control @error('quantite') is-invalid @enderror" value="{{ old('quantite') }}" placeholder="{{ __('messages.placeholder_quantite') }}">
                @error('quantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('messages.site_production') }}</label>
                <input type="text" name="site_production" class="form-control @error('site_production') is-invalid @enderror" value="{{ old('site_production') }}" placeholder="{{ __('messages.placeholder_site') }}">
                @error('site_production') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="form-actions">
            <a href="{{ route('fabricant.produits.show', $produit->id) }}" class="btn-secondary">{{ __('messages.annuler') }}</a>
            <button type="submit" class="btn-primary">{{ __('messages.creer_lot') }}</button>
        </div>
    </form>
</div>
@endsection