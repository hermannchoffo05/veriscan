@extends('layouts.admin')

@section('title', 'Consommateurs')

@section('topbar-title')
    Gestion des <span>consommateurs</span>
@endsection

@section('topbar-actions')
    <div style="font-size:12.5px;color:var(--text-light);font-weight:500;">
        {{ $total }} compte(s) · {{ $suspendus }} suspendu(s)
    </div>
@endsection

@section('styles')
<style>
    .filter-bar { display:flex; gap:10px; margin-bottom:18px; flex-wrap:wrap; }
    .filter-bar input, .filter-bar select {
        padding:8px 14px; border-radius:10px; border:1.5px solid var(--border);
        background:var(--white); font-size:13px; font-family:inherit; outline:none;
    }
    .filter-bar input { width:260px; }
    .filter-bar button {
        padding:8px 16px; border-radius:10px; border:none; background:var(--teal);
        color:#fff; font-weight:700; font-size:13px; cursor:pointer; font-family:inherit;
    }
    .card-table { background:var(--white); border-radius:16px; border:1.5px solid var(--border); overflow-x:auto; }
    table { width:100%; border-collapse:collapse; min-width:680px; }
    thead tr { background:var(--bg); }
    th {
        padding:11px 16px; text-align:left; font-size:11px; font-weight:700; color:var(--text-light);
        text-transform:uppercase; letter-spacing:.06em; border-bottom:1.5px solid var(--border); white-space:nowrap;
    }
    td { padding:13px 16px; font-size:13px; border-bottom:1px solid var(--border); vertical-align:middle; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:var(--teal-light); }
    .u-cell { display:flex; align-items:center; gap:10px; }
    .u-avatar {
        width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,#2E3A6B,#4A5899);
        display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; color:#fff;
    }
    .u-name { font-weight:700; }
    .u-mail { font-size:11px; color:var(--text-light); }
    .badge { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .badge.actif    { background:#f0fdf4; color:#007A4D; }
    .badge.suspendu { background:#fef2f2; color:#CE1126; }
    .btn-action { padding:5px 12px; border-radius:7px; font-size:11.5px; font-weight:700; border:none; cursor:pointer; font-family:inherit; }
    .btn-action.suspend { background:#fefce8; color:#a16207; }
    .btn-action.react   { background:#f0fdf4; color:#007A4D; }
    .empty-state { text-align:center; padding:60px 20px; color:var(--text-light); font-size:13px; }
    .pagination-wrap { display:flex; justify-content:flex-end; padding:16px 20px; border-top:1px solid var(--border); }
</style>
@endsection

@section('content')

<form method="GET" class="filter-bar">
    <input type="text" name="q" value="{{ $q }}" placeholder="Rechercher par nom ou email…">
    <select name="statut">
        <option value="">Tous les statuts</option>
        <option value="actif"    @selected($statut === 'actif')>Actifs</option>
        <option value="suspendu" @selected($statut === 'suspendu')>Suspendus</option>
    </select>
    <button type="submit">Filtrer</button>
</form>

<div class="card-table">
    <table>
        <thead>
            <tr>
                <th>Consommateur</th>
                <th>Inscription</th>
                <th>Vérifications</th>
                <th>Signalements</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($consommateurs as $u)
            <tr>
                <td>
                    <div class="u-cell">
                        <div class="u-avatar">{{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}</div>
                        <div>
                            <div class="u-name">{{ $u->name }}</div>
                            <div class="u-mail">{{ $u->email }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $u->created_at->format('d/m/Y') }}</td>
                <td>{{ $verifs[$u->id] ?? 0 }}</td>
                <td>{{ $sigs[$u->id] ?? 0 }}</td>
                <td>
                    @if($u->is_active)
                        <span class="badge actif">Actif</span>
                    @else
                        <span class="badge suspendu">Suspendu</span>
                    @endif
                </td>
                <td>
                    @if($u->is_active)
                        <form method="POST" action="{{ route('admin.consommateurs.suspendre', $u->id) }}"
                              onsubmit="return confirm('Suspendre ce compte ? L\'utilisateur sera déconnecté.')">
                            @csrf
                            <button type="submit" class="btn-action suspend">⏸ Suspendre</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.consommateurs.reactiver', $u->id) }}">
                            @csrf
                            <button type="submit" class="btn-action react">▶ Réactiver</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6"><div class="empty-state">Aucun consommateur trouvé.</div></td></tr>
        @endforelse
        </tbody>
    </table>

    @if($consommateurs->hasPages())
        <div class="pagination-wrap">{{ $consommateurs->links() }}</div>
    @endif
</div>

@endsection
