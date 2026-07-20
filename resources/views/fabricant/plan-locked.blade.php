@extends('layouts.fabricant')

@section('title', $titrePage ?? (app()->getLocale() === 'en' ? 'Locked feature' : 'Fonctionnalité verrouillée'))

@section('topbar-title') {{ $topbarTitre }} @endsection

@section('content')
<div style="min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 24px;">
    <div style="max-width: 440px; width: 100%; background: white; border-radius: 20px; padding: 40px 32px; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.06); border: 1px solid var(--border, #eef0f8);">
        <div style="width: 64px; height: 64px; margin: 0 auto 20px; background: #fef3e2; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#F5A623" stroke-width="2">
                <rect x="5" y="11" width="14" height="9" rx="2"/>
                <path d="M8 11V7a4 4 0 018 0v4" stroke-linecap="round"/>
            </svg>
        </div>

        <h2 style="font-size: 20px; font-weight: 800; color: var(--text, #1F2937); margin-bottom: 12px;">
            {{ $titreVerrouillage }}
        </h2>

        <p style="font-size: 14px; color: var(--text-light, #6b7280); line-height: 1.6; margin-bottom: 24px;">
            {{ $messageVerrouillage }}
        </p>

        <a href="{{ route('fabricant.tarifs') }}"
           style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #fef3e2; color: #e0961d; font-weight: 700; font-size: 14px; border-radius: 12px; text-decoration: none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 6l-9.5 9.5-5-5L1 18" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M17 6h6v6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ app()->getLocale() === 'en' ? 'View plans' : 'Voir les plans' }}
        </a>
    </div>
</div>
@endsection
