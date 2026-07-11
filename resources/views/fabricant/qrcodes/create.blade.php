@extends('layouts.fabricant')
@section('title', __('messages.generer_qr'))
@section('topbar-title'){{ __('messages.generer') }} <span>QR codes</span>@endsection
@section('topbar-actions')@endsection
@section('styles')
<style>
.breadcrumb{display:flex;align-items:center;gap:6px;margin-bottom:20px;font-size:13px;}
.breadcrumb a{color:#6b7280;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;transition:color 0.2s;}
.breadcrumb a:hover{color:#2E3A6B;}
.breadcrumb a svg{width:13px;height:13px;}
.breadcrumb span{color:#d1d5db;}
.breadcrumb strong{color:#1f2937;font-weight:600;}
.form-card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:32px;max-width:640px;margin:0 auto;}
.form-title{font-size:18px;font-weight:700;color:#1f2937;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid #f3f4f6;}
.form-group{margin-bottom:20px;}
.form-label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
.form-label .required{color:#ef4444;}
.form-control{width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#1f2937;background:#f9fafb;transition:.2s;box-sizing:border-box;}
.form-control:focus{outline:none;border-color:#2E3A6B;box-shadow:0 0 0 3px rgba(46,58,107,.1);background:#fff;}
.form-control.is-invalid{border-color:#ef4444;}
.invalid-feedback{font-size:12px;color:#ef4444;margin-top:4px;}
select.form-control{appearance:none;cursor:pointer;}
.info-box{background:#EEF0F8;border:1px solid #c7cce6;border-radius:10px;padding:14px 16px;font-size:13px;color:#2E3A6B;margin-bottom:20px;}
.form-actions{display:flex;gap:12px;justify-content:flex-end;margin-top:28px;padding-top:20px;border-top:1px solid #f3f4f6;}
.btn-primary{padding:10px 24px;background:#F5A623;color:#171B3D;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;}
.btn-primary:hover{background:#e0961d;}
.btn-secondary{padding:10px 24px;background:#f3f4f6;color:#374151;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;}
.btn-secondary:hover{background:#e5e7eb;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;}
.alert-error ul{margin:6px 0 0 16px;}
.range-display{font-size:13px;color:#2E3A6B;font-weight:600;margin-top:6px;}
</style>
@endsection
@section('content')
<div class="breadcrumb">
    <a href="{{ route('fabricant.qrcodes.index') }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        QR Codes
    </a>
    <span>/</span>
    <strong>{{ __('messages.generer_qr_codes') }}</strong>
</div>
<div class="form-card">
    <div class="form-title">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20" style="vertical-align:middle;margin-right:8px;color:#2E3A6B"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6z"/></svg>
        {{ __('messages.params_generation') }}
    </div>
    <div class="info-box">💡 {{ __('messages.info_qr_unique') }}</div>
    @if($errors->any())
    <div class="alert-error"><strong>{{ __('messages.erreurs_validation') }}</strong><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('fabricant.qrcodes.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label">{{ __('messages.lot_production') }} <span class="required">*</span></label>
            <select name="lot_id" class="form-control @error('lot_id') is-invalid @enderror">
                <option value="">-- {{ __('messages.selectionner_lot') }} --</option>
                @foreach($lots as $lot)
                    <option value="{{ $lot->id }}" {{ (old('lot_id', request('lot_id'))==$lot->id)?'selected':'' }}>
                        {{ $lot->produit->nom }} — {{ $lot->numero_lot }} ({{ $lot->quantite }} {{ __('messages.unites') }}, exp. {{ $lot->date_expiration->format('d/m/Y') }})
                    </option>
                @endforeach
            </select>
            @error('lot_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">{{ __('messages.nombre_qr') }} <span class="required">*</span></label>
            <input type="range" name="quantite" min="1" max="500" value="{{ old('quantite', 10) }}" class="form-control" style="padding:6px 0;background:transparent;border:none" oninput="document.getElementById('qtyDisplay').textContent=this.value">
            <div class="range-display" id="qtyDisplay">{{ old('quantite', 10) }}</div>
            @error('quantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-actions">
            <a href="{{ route('fabricant.qrcodes.index') }}" class="btn-secondary">{{ __('messages.annuler') }}</a>
            <button type="submit" class="btn-primary">{{ __('messages.generer_qr_codes') }}</button>
        </div>
    </form>
</div>
@endsection