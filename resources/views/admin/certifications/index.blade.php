@extends('layouts.admin')

@section('title', 'Certifications')

@section('topbar-title')
    Certification des <span>produits</span>
@endsection

@section('topbar-actions')
    <div style="font-size:12.5px;color:var(--text-light);font-weight:500;">
        {{ $compteurs['soumis'] }} demande(s) à traiter
    </div>
@endsection

@section('styles')
<style>
    .tabs { display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
    .tab {
        padding:8px 14px; border-radius:10px; font-size:12.5px; font-weight:700;
        text-decoration:none; border:1.5px solid var(--border); background:var(--white);
        color:var(--text-light); transition:all .2s;
    }
    .tab:hover { border-color:var(--teal); color:var(--teal); }
    .tab.active { background:var(--teal); border-color:var(--teal); color:#fff; }
    .tab .count { margin-left:6px; opacity:.8; }

    .filter-bar { display:flex; gap:10px; margin-bottom:18px; flex-wrap:wrap; }
    .filter-bar input {
        padding:8px 14px; border-radius:10px; border:1.5px solid var(--border);
        background:var(--white); font-size:13px; font-family:inherit; width:280px; outline:none;
    }
    .filter-bar input:focus { border-color:var(--teal); }
    .filter-bar button {
        padding:8px 16px; border-radius:10px; border:none; background:var(--teal);
        color:#fff; font-weight:700; font-size:13px; cursor:pointer; font-family:inherit;
    }

    .card-table { background:var(--white); border-radius:16px; border:1.5px solid var(--border); overflow-x:auto; }
    table { width:100%; border-collapse:collapse; min-width:760px; }
    thead tr { background:var(--bg); }
    th {
        padding:11px 16px; text-align:left; font-size:11px; font-weight:700; color:var(--text-light);
        text-transform:uppercase; letter-spacing:.06em; border-bottom:1.5px solid var(--border); white-space:nowrap;
    }
    td { padding:13px 16px; font-size:13px; border-bottom:1px solid var(--border); vertical-align:middle; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:var(--teal-light); }
    .p-name { font-weight:700; }
    .p-sub  { font-size:11px; color:var(--text-light); }

    .badge {
        display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700;
        padding:4px 10px; border-radius:20px; white-space:nowrap;
    }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .badge.soumis   { background:#fefce8; color:#a16207; }
    .badge.certifie { background:#f0fdf4; color:#007A4D; }
    .badge.rejete   { background:#fef2f2; color:#CE1126; }
    .badge.revoque  { background:#f3f4f6; color:#4b5563; }

    .btn-action {
        padding:5px 12px; border-radius:7px; font-size:11.5px; font-weight:700; text-decoration:none;
        background:#EEF0F8; color:#2E3A6B; display:inline-flex; align-items:center; gap:4px; white-space:nowrap;
    }
    .btn-action:hover { background:#dde2f5; }
    .empty-state { text-align:center; padding:60px 20px; color:var(--text-light); font-size:13px; }
    .pagination-wrap { display:flex; justify-content:flex-end; padding:16px 20px; border-top:1px solid var(--border); }
</style>
@endsection

@section('content')

<div class="tabs">
    @foreach(['soumis' => 'À traiter', 'certifie' => 'Certifiés', 'rejete' => 'Rejetés', 'revoque' => 'Révoqués'] as $key => $label)
        <a href="{{ route('admin.certifications.index', ['statut' => $key]) }}"
           class="tab {{ $statut === $key ? 'active' : '' }}">
            {{ $label }}<span class="count">{{ $compteurs[$key] }}</span>
        </a>
    @endforeach
    <a href="{{ route('admin.certifications.index', ['statut' => 'tous']) }}"
       class="tab {{ $statut === 'tous' ? 'active' : '' }}">Tous</a>
</div>

<form method="GET" class="filter-bar">
    <input type="hidden" name="statut" value="{{ $statut }}">
    <input type="text" name="q" value="{{ $q }}" placeholder="Produit, code, n° de certificat ou fabricant…">
    <button type="submit">Rechercher</button>
</form>

<div class="card-table">
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Fabricant</th>
                <th>Secteur</th>
                <th>Soumis le</th>
                <th>Statut</th>
                <th>N° certificat</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($produits as $p)
            <tr>
                <td>
                    <div class="p-name">{{ $p->nom }}</div>
                    <div class="p-sub">{{ $p->code_produit }}</div>
                </td>
                <td>{{ $p->fabricant->nom_entreprise ?? '–' }}</td>
                <td>{{ $p->categorie }}</td>
                <td>{{ $p->created_at->format('d/m/Y') }}</td>
                <td><span class="badge {{ $p->statut_certification }}">{{ $p->libelle_certification }}</span></td>
                <td>{{ $p->numero_certificat ?? '–' }}</td>
                <td>
                    <a href="{{ route('admin.certifications.show', $p->id) }}" class="btn-action">
                        {{ $p->statut_certification === 'soumis' ? 'Examiner' : 'Voir' }}
                    </a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7"><div class="empty-state">Aucun produit dans cette catégorie.</div></td></tr>
        @endforelse
        </tbody>
    </table>

    @if($produits->hasPages())
        <div class="pagination-wrap">{{ $produits->links() }}</div>
    @endif
</div>

@endsection
