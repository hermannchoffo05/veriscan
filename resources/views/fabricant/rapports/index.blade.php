{{-- rapports/index.blade.php --}}
@extends('layouts.fabricant')
@section('title', __('messages.rapports'))
@section('topbar-title') {{ __('messages.mes') }} <span>{{ __('messages.rapports') }}</span> @endsection

@section('styles')
<style>
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 22px; font-weight: 800; color: var(--text); }
    .page-header p { font-size: 13px; color: var(--text-light); margin-top: 3px; }
    .rapports-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
    .rapport-card { background: white; border: 1.5px solid var(--border); border-radius: 14px; padding: 22px; transition: all 0.2s; }
    .rapport-card:hover { border-color: var(--teal); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.07); }
    .rapport-icon { width: 48px; height: 48px; border-radius: 12px; background: var(--teal-light); display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
    .rapport-icon svg { width: 22px; height: 22px; color: var(--teal); }
    .rapport-title { font-size: 14px; font-weight: 800; color: var(--text); margin-bottom: 5px; }
    .rapport-desc { font-size: 12px; color: var(--text-light); margin-bottom: 16px; line-height: 1.5; }
    .btn-primary { background: #F5A623; color: #171B3D; border: none; border-radius: 8px; padding: 9px 16px; font-size: 12px; font-weight: 700; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; text-decoration: none; width: 100%; justify-content: center; }
    .btn-primary:hover { background: #e0961d; }
    .btn-primary svg { width: 13px; height: 13px; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1>{{ __('messages.rapports_exports') }}</h1>
        <p>{{ __('messages.rapports_desc') }}</p>
    </div>

    <div class="rapports-grid">

        {{-- Rapport mensuel --}}
        <div class="rapport-card">
            <div class="rapport-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="rapport-title">{{ __('messages.rapport_mensuel') }}</div>
            <div class="rapport-desc">{{ __('messages.rapport_mensuel_desc') }}</div>
            <a href="{{ route('fabricant.rapports.telecharger') }}?type=mensuel" class="btn-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('messages.exporter_pdf') }}
            </a>
        </div>

        {{-- Certificats d'authenticité --}}
        <div class="rapport-card">
            <div class="rapport-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <div class="rapport-title">{{ __('messages.certificats_authenticite') }}</div>
            <div class="rapport-desc">{{ __('messages.certificats_authenticite_desc') }}</div>
            <a href="{{ route('fabricant.rapports.telecharger') }}?type=certificats" class="btn-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('messages.exporter_pdf') }}
            </a>
        </div>

        {{-- Export QR --}}
        <div class="rapport-card">
            <div class="rapport-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg>
            </div>
            <div class="rapport-title">{{ __('messages.export_qr_pdf') }}</div>
            <div class="rapport-desc">{{ __('messages.export_qr_pdf_desc') }}</div>
            <a href="{{ route('fabricant.qrcodes.index') }}" class="btn-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('messages.aller_qrcodes') }}
            </a>
        </div>

        {{-- Rapport signalements --}}
        <div class="rapport-card">
            <div class="rapport-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="rapport-title">{{ __('messages.rapport_signalements') }}</div>
            <div class="rapport-desc">{{ __('messages.rapport_signalements_desc') }}</div>
            <a href="{{ route('fabricant.rapports.telecharger') }}?type=signalements" class="btn-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('messages.exporter_pdf') }}
            </a>
        </div>

    </div>
@endsection