@extends('layouts.admin')

@section('title', 'Signalements')

@section('topbar-title')
    Gestion des <span>signalements</span>
@endsection

@section('topbar-actions')
    <div style="font-size:12.5px;color:var(--text-light);font-weight:500;">
        {{ $signalements->total() }} signalement(s) au total
    </div>
@endsection

@section('styles')
<style>
    .filter-bar { display: flex; gap: 10px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; }
    .filter-bar input { padding: 8px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--white); font-size: 13px; font-family: inherit; color: var(--text); outline: none; transition: border 0.2s; width: 240px; }
    .filter-bar input:focus { border-color: var(--teal); }
    .filter-bar select { padding: 8px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--white); font-size: 13px; font-family: inherit; color: var(--text); outline: none; cursor: pointer; }

    .sig-table-card {
        background: var(--white); border-radius: 16px;
        border: 1.5px solid var(--border);
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    table { width: 100%; border-collapse: collapse; min-width: 700px; }
    thead tr { background: var(--bg); }
    th { padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.06em; border-bottom: 1.5px solid var(--border); white-space: nowrap; }
    td { padding: 13px 16px; font-size: 13px; color: var(--text); border-bottom: 1px solid var(--border); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--teal-light); }
    .status-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; white-space: nowrap; }
    .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .status-badge.en_cours { background: #fefce8; color: #a16207; }
    .status-badge.traite   { background: #f0fdf4; color: #007A4D; }
    .status-badge.rejete   { background: #f9fafb; color: #6b7280; }
    .btn-voir { padding: 5px 12px; border-radius: 7px; font-size: 11.5px; font-weight: 700; text-decoration: none; background: #f0fdfa; color: #0F766E; transition: all 0.2s; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
    .btn-voir:hover { background: #ccfbf1; }
    .empty-state { text-align: center; padding: 60px 20px; color: var(--text-light); font-size: 13px; }
    .empty-state svg { width: 48px; height: 48px; color: var(--border); margin: 0 auto 12px; display: block; }
    .pagination-wrap { display: flex; justify-content: flex-end; padding: 16px 20px; border-top: 1px solid var(--border); }

    /* ── Résumé IA ── */
    .ia-resume-bar {
        background: linear-gradient(135deg, #042f2e 0%, #0f766e 100%);
        border-radius: 16px; padding: 18px 22px; margin-bottom: 20px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 16px; flex-wrap: wrap;
    }
    .ia-resume-left { display: flex; align-items: center; gap: 12px; }
    .ia-resume-icon { width: 40px; height: 40px; background: rgba(255,255,255,0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .ia-resume-icon svg { width: 20px; height: 20px; color: #5eead4; }
    .ia-resume-title { font-size: 14px; font-weight: 700; color: white; }
    .ia-resume-sub { font-size: 12px; color: rgba(255,255,255,0.65); margin-top: 2px; }
    .btn-generate-resume {
        padding: 9px 20px; background: white; color: #0f766e; border: none;
        border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer;
        font-family: inherit; display: flex; align-items: center; gap: 7px;
        transition: all 0.2s; flex-shrink: 0;
    }
    .btn-generate-resume:hover { background: #f0fdfa; }
    .btn-generate-resume:disabled { opacity: 0.6; cursor: not-allowed; }
    .btn-generate-resume svg { width: 16px; height: 16px; }

    .resume-result {
        background: white; border: 1.5px solid var(--border); border-radius: 16px;
        padding: 20px 24px; margin-bottom: 20px; display: none;
    }
    .resume-result.visible { display: block; }
    .resume-result-header { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .resume-result-header svg { width: 18px; height: 18px; color: var(--teal); }
    .resume-result-header span { font-size: 14px; font-weight: 700; color: var(--text); }
    .resume-stats { display: flex; gap: 12px; margin-bottom: 14px; flex-wrap: wrap; }
    .resume-stat { background: var(--bg); border-radius: 10px; padding: 8px 14px; font-size: 12px; font-weight: 600; color: var(--text-light); }
    .resume-stat strong { color: var(--text); font-size: 16px; display: block; }
    .resume-text { font-size: 14px; color: var(--text); line-height: 1.8; }
    .resume-date { font-size: 11px; color: var(--text-light); margin-top: 10px; }

    @keyframes spin { from { transform: rotate(0deg) } to { transform: rotate(360deg) } }

    @media (max-width: 768px) {
        .ia-resume-bar { flex-direction: column; align-items: flex-start; padding: 16px; }
        .btn-generate-resume { width: 100%; justify-content: center; }
        .filter-bar input { width: 100%; }
        .filter-bar select { width: 100%; }
    }
</style>
@endsection

@section('content')

{{-- ── Barre Résumé IA ── --}}
<div class="ia-resume-bar">
    <div class="ia-resume-left">
        <div class="ia-resume-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
        </div>
        <div>
            <div class="ia-resume-title">Analyse IA des signalements</div>
            <div class="ia-resume-sub">Génère un résumé intelligent basé sur les {{ $signalements->total() }} signalement(s)</div>
        </div>
    </div>
    <button class="btn-generate-resume" id="btnResume" onclick="genererResume()">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        Générer le résumé IA
    </button>
</div>

{{-- ── Zone résultat résumé ── --}}
<div class="resume-result" id="resumeResult">
    <div class="resume-result-header">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Résumé analytique — Généré par IA</span>
    </div>
    <div class="resume-stats" id="resumeStats"></div>
    <div class="resume-text" id="resumeText"></div>
    <div class="resume-date" id="resumeDate"></div>
</div>

{{-- ── Filtres ── --}}
<div class="filter-bar">
    <input type="text" id="searchInput" placeholder="Rechercher un produit ou fabricant..." onkeyup="filterTable()">
    <select id="statusFilter" onchange="filterTable()">
        <option value="">Tous les statuts</option>
        <option value="en_cours">En cours</option>
        <option value="traite">Traité</option>
        <option value="rejete">Rejeté</option>
    </select>
    <div style="margin-left:auto;font-size:12.5px;color:var(--text-light);">{{ $signalements->total() }} signalement(s)</div>
</div>

<div class="sig-table-card">
    <table id="sigTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Produit</th>
                <th>Fabricant</th>
                <th>Description</th>
                <th>Signalant</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($signalements as $sig)
            @php $produit = $sig->qrCode->lot->produit ?? null; $fab = $produit->fabricant ?? null; @endphp
            <tr data-search="{{ strtolower(($produit->nom ?? '') . ' ' . ($fab->nom_entreprise ?? '')) }}" data-status="{{ $sig->statut }}">
                <td style="color:var(--text-light);font-size:12px;">#{{ $sig->id }}</td>
                <td>
                    <div style="font-weight:700;">{{ $produit->nom ?? 'Inconnu' }}</div>
                    <div style="font-size:11px;color:var(--text-light);">Lot {{ $sig->qrCode->lot->numero_lot ?? '–' }}</div>
                </td>
                <td>
                    <div style="font-size:12.5px;font-weight:600;">{{ $fab->nom_entreprise ?? '–' }}</div>
                    <div style="font-size:11px;color:var(--text-light);">{{ $fab->email ?? '' }}</div>
                </td>
                <td style="max-width:180px;font-size:12.5px;">{{ Str::limit($sig->description, 50) }}</td>
                <td>
                    <div style="font-size:12.5px;font-weight:600;">{{ $sig->nom_signalant ?? 'Anonyme' }}</div>
                    <div style="font-size:11px;color:var(--text-light);">{{ $sig->contact_signalant ?? '' }}</div>
                </td>
                <td style="font-size:12px;color:var(--text-light);white-space:nowrap;">{{ $sig->created_at->format('d/m/Y') }}</td>
                <td>
                    <span class="status-badge {{ $sig->statut }}">
                        {{ match($sig->statut) { 'en_cours' => 'En cours', 'traite' => 'Traité', 'rejete' => 'Rejeté', default => $sig->statut } }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.signalements.show', $sig->id) }}" class="btn-voir">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Voir
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Aucun signalement pour le moment
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($signalements->hasPages())
    <div class="pagination-wrap">{{ $signalements->links() }}</div>
    @endif
</div>

@endsection

@section('scripts')
<script>
function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    document.querySelectorAll('#sigTable tbody tr').forEach(row => {
        const s = row.dataset.search ?? '';
        const t = row.dataset.status ?? '';
        row.style.display = ((!search || s.includes(search)) && (!status || t === status)) ? '' : 'none';
    });
}

async function genererResume() {
    const btn = document.getElementById('btnResume');
    const result = document.getElementById('resumeResult');

    btn.disabled = true;
    btn.innerHTML = '<svg style="animation:spin 1s linear infinite;width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Analyse en cours...';

    try {
        const response = await fetch('{{ route("admin.signalements.resume") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const data = await response.json();
        if (data.resume) {
            if (data.stats) {
                document.getElementById('resumeStats').innerHTML = `
                    <div class="resume-stat"><strong>${data.stats.total}</strong>Total</div>
                    <div class="resume-stat"><strong style="color:#a16207">${data.stats.en_cours}</strong>En cours</div>
                    <div class="resume-stat"><strong style="color:#007A4D">${data.stats.traites}</strong>Traités</div>
                    <div class="resume-stat"><strong style="color:#6b7280">${data.stats.rejetes}</strong>Rejetés</div>
                `;
            }
            document.getElementById('resumeText').textContent = data.resume;
            document.getElementById('resumeDate').textContent = 'Généré le ' + new Date().toLocaleString('fr-FR');
            result.classList.add('visible');
            result.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    } catch (e) {
        alert('Erreur lors de la génération du résumé.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Regénérer le résumé IA';
    }
}
</script>
@endsection