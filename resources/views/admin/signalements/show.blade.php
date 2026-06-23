@extends('layouts.admin')
@section('title', 'Signalement #' . $signalement->id)
@section('topbar-title')Signalement <span>#{{ $signalement->id }}</span>@endsection
@section('topbar-actions')@endsection
@section('styles')
<style>
.breadcrumb{display:flex;align-items:center;gap:6px;margin-bottom:20px;font-size:13px;}
.breadcrumb a{color:var(--text-light);text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;transition:color 0.2s;}
.breadcrumb a:hover{color:var(--teal);}
.breadcrumb a svg{width:13px;height:13px;}
.breadcrumb span{color:var(--border);}
.breadcrumb strong{color:var(--text);font-weight:600;}
.show-layout{display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;}
.detail-card{background:var(--white);border-radius:16px;border:1.5px solid var(--border);overflow:hidden;margin-bottom:20px;}
.detail-header{padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
.detail-title{font-size:14px;font-weight:800;color:var(--text);display:flex;align-items:center;gap:7px;}
.detail-title svg{width:15px;height:15px;color:var(--teal);}
.detail-body{padding:20px 22px;}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.info-item label{font-size:11px;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.05em;display:block;margin-bottom:4px;}
.info-item .val{font-size:13px;font-weight:600;color:var(--text);}
.status-badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;}
.status-badge::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor;}
.status-badge.en_cours{background:#fefce8;color:#a16207;}
.status-badge.traite{background:#f0fdf4;color:#007A4D;}
.status-badge.rejete{background:#f9fafb;color:#6b7280;}
.desc-box{background:var(--bg);border-radius:10px;padding:14px 16px;font-size:13px;color:var(--text);line-height:1.6;border:1px solid var(--border);}
.action-card{background:var(--white);border-radius:16px;border:1.5px solid var(--border);overflow:hidden;margin-bottom:16px;}
.action-header{padding:14px 18px;border-bottom:1px solid var(--border);font-size:13px;font-weight:800;color:var(--text);}
.action-body{padding:16px 18px;display:flex;flex-direction:column;gap:8px;}
.btn-action{width:100%;padding:9px 14px;border-radius:10px;font-size:12.5px;font-weight:700;cursor:pointer;border:1.5px solid transparent;font-family:inherit;transition:all 0.2s;display:flex;align-items:center;gap:7px;}
.btn-action svg{width:14px;height:14px;flex-shrink:0;}
.btn-traite{background:#f0fdf4;color:#007A4D;border-color:#bbf7d0;}
.btn-traite:hover{background:#dcfce7;}
.btn-rejete{background:#f9fafb;color:#6b7280;border-color:#e5e7eb;}
.btn-rejete:hover{background:#f3f4f6;}
.btn-escalade{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;}
.btn-escalade:hover{background:#dbeafe;}
.proof-img{width:100%;border-radius:10px;border:1px solid var(--border);object-fit:cover;max-height:200px;}
.prod-row{display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid var(--border);font-size:12.5px;}
.prod-row:last-child{border-bottom:none;}
.prod-label{color:var(--text-light);width:90px;flex-shrink:0;}
.prod-val{font-weight:600;color:var(--text);}

/* ── Analyse IA ── */
.btn-analyse-ia {
    display: flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 8px;
    background: linear-gradient(135deg, #042f2e, #0f766e);
    color: white; font-size: 12px; font-weight: 700;
    border: none; cursor: pointer; font-family: inherit;
    transition: opacity 0.2s;
}
.btn-analyse-ia:hover { opacity: 0.88; }
.btn-analyse-ia:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-analyse-ia svg { width: 13px; height: 13px; }

.ia-result {
    margin-top: 14px;
    background: linear-gradient(135deg, #042f2e08, #0f766e08);
    border: 1.5px solid rgba(15,118,110,0.2);
    border-radius: 12px;
    padding: 14px 16px;
    display: none;
}
.ia-result.visible { display: block; }
.ia-result-header {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 10px;
}
.ia-result-header svg { width: 16px; height: 16px; color: var(--teal); }
.ia-result-header span { font-size: 13px; font-weight: 700; color: var(--text); }
.ia-result-badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: var(--teal-light); color: var(--teal); margin-left: auto; }
.ia-result-text { font-size: 13px; color: var(--text); line-height: 1.7; }
.ia-result-date { font-size: 11px; color: var(--text-light); margin-top: 8px; }

/* Niveau suspicion badges */
.suspicion-faible { background: #f0fdf4; color: #007A4D; }
.suspicion-modere { background: #fffbeb; color: #a16207; }
.suspicion-eleve  { background: #fef2f2; color: #CE1126; }
.suspicion-critique { background: #450a0a; color: white; }

@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

@media (max-width: 768px) {
    .show-layout { grid-template-columns: 1fr; }
    .info-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')
@php
    $produit = $signalement->qrCode->lot->produit ?? null;
    $lot     = $signalement->qrCode->lot ?? null;
    $fab     = $produit->fabricant ?? null;
@endphp

<div class="breadcrumb">
    <a href="{{ route('admin.signalements.index') }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Signalements
    </a>
    <span>/</span>
    <strong>Signalement #{{ $signalement->id }}</strong>
</div>

<div class="show-layout">
    <div>
        {{-- Détails du signalement --}}
        <div class="detail-card">
            <div class="detail-header">
                <div class="detail-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Détails du signalement
                </div>
                <span class="status-badge {{ $signalement->statut }}">
                    {{ match($signalement->statut) { 'en_cours' => 'En cours', 'traite' => 'Traité', 'rejete' => 'Rejeté', default => $signalement->statut } }}
                </span>
            </div>
            <div class="detail-body">
                <div class="info-grid" style="margin-bottom:16px;">
                    <div class="info-item"><label>Nom du signalant</label><div class="val">{{ $signalement->nom_signalant ?? 'Anonyme' }}</div></div>
                    <div class="info-item"><label>Contact</label><div class="val">{{ $signalement->contact_signalant ?? '–' }}</div></div>
                    <div class="info-item"><label>Date du signalement</label><div class="val">{{ $signalement->created_at->format('d/m/Y à H:i') }}</div></div>
                    <div class="info-item"><label>Référence QR Code</label><div class="val" style="font-family:monospace;font-size:12px;">{{ $signalement->qrCode->token ?? '–' }}</div></div>
                </div>
                <div style="margin-bottom:6px;font-size:11px;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.05em;">Description</div>
                <div class="desc-box">{{ $signalement->description }}</div>
            </div>
        </div>

        {{-- Produit concerné --}}
        <div class="detail-card">
            <div class="detail-header">
                <div class="detail-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Produit concerné
                </div>
            </div>
            <div class="detail-body">
                <div class="prod-row"><span class="prod-label">Produit</span><span class="prod-val">{{ $produit->nom ?? '–' }}</span></div>
                <div class="prod-row"><span class="prod-label">Catégorie</span><span class="prod-val">{{ $produit->categorie ?? '–' }}</span></div>
                <div class="prod-row"><span class="prod-label">Code produit</span><span class="prod-val" style="font-family:monospace;font-size:12px;">{{ $produit->code_produit ?? '–' }}</span></div>
                <div class="prod-row"><span class="prod-label">N° de lot</span><span class="prod-val">{{ $lot->numero_lot ?? '–' }}</span></div>
                <div class="prod-row"><span class="prod-label">Fabrication</span><span class="prod-val">{{ $lot->date_fabrication ?? '–' }}</span></div>
                <div class="prod-row"><span class="prod-label">Expiration</span><span class="prod-val">{{ $lot->date_expiration ?? '–' }}</span></div>
                <div class="prod-row"><span class="prod-label">Fabricant</span><span class="prod-val">{{ $fab->nom_entreprise ?? '–' }}</span></div>
            </div>
        </div>

        {{-- Photo preuve + Analyse IA --}}
        @if($signalement->photo_preuve)
        <div class="detail-card">
            <div class="detail-header">
                <div class="detail-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Photo preuve
                </div>
                <button class="btn-analyse-ia" id="btnAnalyse" onclick="analyserPhotoIA()">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Analyser par IA
                </button>
            </div>
            <div class="detail-body">
                <img src="{{ asset('storage/' . $signalement->photo_preuve) }}" class="proof-img" alt="Photo preuve">

                {{-- Résultat analyse IA --}}
                <div class="ia-result {{ $signalement->analyse_ia ? 'visible' : '' }}" id="iaResult">
                    <div class="ia-result-header">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        <span>Analyse IA — Détection contrefaçon</span>
                        <span class="ia-result-badge">Groq Vision</span>
                    </div>
                    <div class="ia-result-text" id="iaResultText">
                        @if($signalement->analyse_ia)
                            {{ $signalement->analyse_ia }}
                        @endif
                    </div>
                    <div class="ia-result-date" id="iaResultDate">
                        @if($signalement->analyse_ia)
                            Analysé lors du signalement
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div>
        {{-- Actions modération --}}
        <div class="action-card">
            <div class="action-header">Modérer ce signalement</div>
            <div class="action-body">
                <form method="POST" action="{{ route('admin.signalements.traiter', $signalement->id) }}">
                    @csrf
                    <input type="hidden" name="statut" value="traite">
                    <button type="submit" class="btn-action btn-traite">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Marquer comme traité
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.signalements.traiter', $signalement->id) }}">
                    @csrf
                    <input type="hidden" name="statut" value="rejete">
                    <button type="submit" class="btn-action btn-rejete">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Rejeter le signalement
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.signalements.escalader', $signalement->id) }}">
                    @csrf
                    <button type="submit" class="btn-action btn-escalade">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        Escalader MINCOMMERCE
                    </button>
                </form>
            </div>
        </div>

        {{-- Fabricant --}}
        @if($fab)
        <div class="action-card">
            <div class="action-header">Fabricant concerné</div>
            <div class="action-body">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#0f766e,#14b8a6);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:white;flex-shrink:0;">
                        {{ strtoupper(substr($fab->nom_entreprise ?? 'F', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:var(--text);">{{ $fab->nom_entreprise }}</div>
                        <div style="font-size:11px;color:var(--text-light);">{{ $fab->email }}</div>
                    </div>
                </div>
                <a href="{{ route('admin.fabricants.show', $fab->id) }}" style="display:flex;align-items:center;justify-content:center;gap:6px;padding:8px;border-radius:10px;background:var(--teal-light);color:var(--teal);font-size:12.5px;font-weight:700;text-decoration:none;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Voir le profil fabricant
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
async function analyserPhotoIA() {
    const btn    = document.getElementById('btnAnalyse');
    const result = document.getElementById('iaResult');
    const text   = document.getElementById('iaResultText');
    const date   = document.getElementById('iaResultDate');

    btn.disabled = true;
    btn.innerHTML = '<svg style="animation:spin 1s linear infinite;width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Analyse en cours...';

    try {
        const response = await fetch('{{ route("admin.signalements.analyser-photo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ signalement_id: {{ $signalement->id }} })
        });

        const data = await response.json();

        if (data.analyse) {
            text.textContent = data.analyse;
            date.textContent = data.cached
                ? 'Analyse précédente récupérée'
                : 'Analysé le ' + new Date().toLocaleString('fr-FR');
            result.classList.add('visible');
            result.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            btn.innerHTML = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Ré-analyser';
        } else {
            alert(data.error || 'Erreur lors de l\'analyse.');
            btn.innerHTML = 'Analyser par IA';
        }
    } catch(e) {
        alert('Erreur réseau.');
        btn.innerHTML = 'Analyser par IA';
    } finally {
        btn.disabled = false;
    }
}
</script>
@endsection