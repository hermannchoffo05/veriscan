@extends('layouts.admin')

@section('title', 'Fabricants')

@section('topbar-title')
    Gestion des <span>fabricants</span>
@endsection

@section('topbar-actions')
    <div style="font-size:12.5px;color:var(--text-light);font-weight:500;">
        {{ $fabricants->total() }} fabricant(s) au total
    </div>
@endsection

@section('styles')
<style>
    .filter-bar {
        display: flex; gap: 10px; align-items: center;
        margin-bottom: 20px; flex-wrap: wrap;
    }
    .filter-bar input {
        padding: 8px 14px; border-radius: 10px;
        border: 1.5px solid var(--border); background: var(--white);
        font-size: 13px; font-family: inherit; color: var(--text);
        outline: none; transition: border 0.2s; width: 240px;
    }
    .filter-bar input:focus { border-color: var(--teal); }
    .filter-bar select {
        padding: 8px 14px; border-radius: 10px;
        border: 1.5px solid var(--border); background: var(--white);
        font-size: 13px; font-family: inherit; color: var(--text);
        outline: none; cursor: pointer;
    }

    /* ── Fix scroll mobile : overflow-x:auto SANS overflow:hidden ── */
    .fab-table-card {
        background: var(--white); border-radius: 16px;
        border: 1.5px solid var(--border);
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    table { width: 100%; border-collapse: collapse; min-width: 600px; }
    thead tr { background: var(--bg); }
    th {
        padding: 11px 16px; text-align: left;
        font-size: 11px; font-weight: 700; color: var(--text-light);
        text-transform: uppercase; letter-spacing: 0.06em;
        border-bottom: 1.5px solid var(--border);
        white-space: nowrap;
    }
    td {
        padding: 13px 16px; font-size: 13px;
        color: var(--text); border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--teal-light); }

    .fab-cell { display: flex; align-items: center; gap: 10px; }
    .fab-avatar {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, #2E3A6B, #4A5899);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 800; color: white; flex-shrink: 0;
    }
    .fab-name  { font-weight: 700; color: var(--text); font-size: 13px; }
    .fab-email { font-size: 11px; color: var(--text-light); }

    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 11px; font-weight: 700; padding: 4px 10px;
        border-radius: 20px; white-space: nowrap;
    }
    .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .status-badge.actif      { background: #f0fdf4; color: #007A4D; }
    .status-badge.en_attente { background: #fefce8; color: #a16207; }
    .status-badge.suspendu   { background: #fef2f2; color: #CE1126; }

    .actions { display: flex; gap: 6px; align-items: center; flex-wrap: nowrap; }
    .btn-action {
        padding: 5px 10px; border-radius: 7px; font-size: 11.5px; font-weight: 700;
        text-decoration: none; font-family: inherit; cursor: pointer;
        border: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 4px;
        white-space: nowrap;
    }
    .btn-action.view     { background: #EEF0F8; color: #2E3A6B; }
    .btn-action.view:hover { background: #dde2f5; }
    .btn-action.validate { background: #f0fdf4; color: #007A4D; }
    .btn-action.validate:hover { background: #dcfce7; }
    .btn-action.suspend  { background: #fefce8; color: #a16207; }
    .btn-action.suspend:hover { background: #fef9c3; }
    .btn-action.delete   { background: #fef2f2; color: #CE1126; }
    .btn-action.delete:hover { background: #fee2e2; }

    .empty-state {
        text-align: center; padding: 60px 20px;
        color: var(--text-light); font-size: 13px;
    }
    .empty-state svg { width: 48px; height: 48px; color: var(--border); margin: 0 auto 12px; display: block; }

    .pagination-wrap {
        display: flex; justify-content: flex-end;
        padding: 16px 20px; border-top: 1px solid var(--border);
    }
    .pagination-wrap .pagination { display: flex; gap: 4px; list-style: none; }
    .pagination-wrap .page-item .page-link {
        padding: 6px 12px; border-radius: 8px; font-size: 12.5px;
        font-weight: 600; text-decoration: none; color: var(--text-light);
        border: 1.5px solid var(--border); background: var(--white);
        transition: all 0.2s; display: block;
    }
    .pagination-wrap .page-item.active .page-link { background: var(--teal); color: white; border-color: var(--teal); }
    .pagination-wrap .page-item .page-link:hover { border-color: var(--teal); color: var(--teal); }
</style>
@endsection

@section('content')

<div class="filter-bar">
    <input type="text" id="searchInput" placeholder="Rechercher un fabricant..." onkeyup="filterTable()">
    <select id="statusFilter" onchange="filterTable()">
        <option value="">Tous les statuts</option>
        <option value="actif">Actif</option>
        <option value="en_attente">En attente</option>
        <option value="suspendu">Suspendu</option>
    </select>
    <div style="margin-left:auto;font-size:12.5px;color:var(--text-light);">
        {{ $fabricants->total() }} fabricant(s)
    </div>
</div>

<div class="fab-table-card">
    <table id="fabTable">
        <thead>
            <tr>
                <th>Fabricant</th>
                <th>Téléphone</th>
                <th>Ville</th>
                <th>Inscription</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fabricants as $fab)
            <tr data-name="{{ strtolower($fab->nom_entreprise ?? '') }}" data-status="{{ $fab->statut ?? 'en_attente' }}">
                <td>
                    <div class="fab-cell">
                        <div class="fab-avatar">{{ strtoupper(substr($fab->nom_entreprise ?? 'F', 0, 1)) }}</div>
                        <div>
                            <div class="fab-name">{{ $fab->nom_entreprise ?? 'N/A' }}</div>
                            <div class="fab-email">{{ $fab->email }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $fab->telephone ?? '–' }}</td>
                <td>{{ $fab->ville ?? '–' }}</td>
                <td>{{ $fab->created_at->format('d/m/Y') }}</td>
                <td>
                    <span class="status-badge {{ $fab->statut ?? 'en_attente' }}">
                        {{ match($fab->statut ?? 'en_attente') {
                            'actif'      => 'Actif',
                            'en_attente' => 'En attente',
                            'suspendu'   => 'Suspendu',
                            default      => ucfirst($fab->statut ?? 'en_attente')
                        } }}
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <a href="{{ route('admin.fabricants.show', $fab->id) }}" class="btn-action view">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Voir
                        </a>
                    
                        @if(($fab->statut ?? '') !== 'suspendu')
                        <form method="POST" action="{{ route('admin.fabricants.suspendre', $fab->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-action suspend">⏸ Suspendre</button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('admin.fabricants.destroy', $fab->id) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce fabricant ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action delete">✕</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Aucun fabricant inscrit pour le moment
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($fabricants->hasPages())
    <div class="pagination-wrap">
        {{ $fabricants->links() }}
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    document.querySelectorAll('#fabTable tbody tr').forEach(row => {
        const name = row.dataset.name ?? '';
        const stat = row.dataset.status ?? '';
        row.style.display = ((!search || name.includes(search)) && (!status || stat === status)) ? '' : 'none';
    });
}
</script>
@endsection