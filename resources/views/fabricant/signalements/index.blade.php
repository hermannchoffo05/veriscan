{{-- signalements/index.blade.php --}}
@extends('layouts.fabricant')
@section('title', __('messages.signalements'))
@section('topbar-title') <span>{{ __('messages.signalements') }}</span> @endsection

@section('styles')
<style>
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 22px; font-weight: 800; color: var(--text); }
    .page-header p { font-size: 13px; color: var(--text-light); margin-top: 3px; }
    .signal-card { background: white; border: 1.5px solid var(--border); border-radius: 14px; padding: 18px 20px; margin-bottom: 12px; display: flex; align-items: flex-start; gap: 14px; transition: all 0.2s; }
    .signal-card:hover { border-color: var(--teal); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .signal-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .signal-icon svg { width: 20px; height: 20px; }
    .signal-icon.yellow { background: #fefce8; color: #a16207; }
    .signal-icon.red { background: #fef2f2; color: var(--red); }
    .signal-body { flex: 1; }
    .signal-title { font-size: 14px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
    .signal-meta { font-size: 12px; color: var(--text-light); display: flex; gap: 14px; flex-wrap: wrap; }
    .signal-meta span { display: flex; align-items: center; gap: 4px; }
    .signal-meta svg { width: 12px; height: 12px; }
    .signal-actions { display: flex; gap: 8px; margin-top: 10px; }
    .btn-sm { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; transition: all 0.2s; border: none; }
    .btn-sm svg { width: 12px; height: 12px; }
    .btn-sm.teal { background: var(--teal-light); color: var(--teal); }
    .btn-sm.teal:hover { background: var(--teal); color: white; }
    .btn-sm.gray { background: #f3f4f6; color: #374151; }
    .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; flex-shrink: 0; align-self: flex-start; }
    .status-pill.yellow { background: #fefce8; color: #a16207; }
    .status-pill.red { background: #fef2f2; color: var(--red); }
    .empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 16px; border: 1.5px dashed var(--border); }
    .empty-state svg { width: 40px; height: 40px; margin: 0 auto 14px; display: block; color: var(--teal); opacity: 0.4; }
    .empty-state h3 { font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 6px; }
    .empty-state p { font-size: 13px; color: var(--text-light); }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1>{{ __('messages.signalements_recus') }}</h1>
        <p>{{ __('messages.signalements_desc') }}</p>
    </div>

    @forelse($signalements ?? [] as $signalement)
    <div class="signal-card">
        <div class="signal-icon {{ $signalement->type === 'contrefait' ? 'red' : 'yellow' }}">
            @if($signalement->type === 'contrefait')
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            @else
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            @endif
        </div>
        <div class="signal-body">
           <div class="signal-title">{{ $signalement->qrCode->lot->produit->nom ?? '—' }} — Lot #{{ $signalement->qrCode->lot->numero_lot ?? '—' }}</div>
            <div class="signal-meta">
                <span>
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    {{ $signalement->localisation ?? __('messages.localisation_inconnue') }}
                </span>
                <span>
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $signalement->created_at->diffForHumans() }}
                </span>
                <span>{{ __('messages.signale_par_consommateur') }}</span>
            </div>
            <div class="signal-actions">
                <a href="{{ route('fabricant.signalements.show', $signalement->id) }}" class="btn-sm teal">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    {{ __('messages.voir_details') }}
                </a>
                <form method="POST" action="{{ route('fabricant.signalements.traiter', $signalement->id) }}" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-sm gray">{{ __('messages.marquer_traite') }}</button>
                </form>
            </div>
        </div>
        <span class="status-pill {{ $signalement->type === 'contrefait' ? 'red' : 'yellow' }}">
            {{ strtoupper($signalement->type) }}
        </span>
    </div>
    @empty
    <div class="empty-state">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3>{{ __('messages.aucun_signalement') }}</h3>
        <p>{{ __('messages.aucun_signalement_desc') }}</p>
    </div>
    @endforelse

@endsection