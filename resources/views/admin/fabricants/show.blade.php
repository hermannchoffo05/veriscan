@extends('layouts.admin')

@section('title', 'Détail fabricant')

@section('topbar-title')
    Fabricant — <span>{{ $fabricant->nom_entreprise }}</span>
@endsection

@section('topbar-actions')
@endsection

@section('styles')
<style>
    .breadcrumb { display: flex; align-items: center; gap: 6px; margin-bottom: 20px; font-size: 13px; }
    .breadcrumb a { color: var(--text-light); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; transition: color 0.2s; }
    .breadcrumb a:hover { color: var(--teal); }
    .breadcrumb a svg { width: 13px; height: 13px; }
    .breadcrumb span { color: var(--border); }
    .breadcrumb strong { color: var(--text); font-weight: 600; }

    /* Layout 2 colonnes desktop */
    .fab-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 20px;
        align-items: start;
    }

    /* Profil card */
    .profile-card { background: var(--white); border-radius: 16px; border: 1.5px solid var(--border); overflow: hidden; }
    .profile-banner { height: 72px; background: linear-gradient(135deg, #171B3D 0%, #2E3A6B 100%); }
    .profile-header { padding: 0 18px 16px; border-bottom: 1px solid var(--border); }
    .profile-avatar { width: 52px; height: 52px; border-radius: 12px; background: linear-gradient(135deg, #2E3A6B, #4A5899); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: white; border: 3px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.12); margin-top: -26px; margin-bottom: 10px; text-transform: uppercase; }
    .profile-name { font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 2px; }
    .profile-email { font-size: 11.5px; color: var(--text-light); margin-bottom: 10px; }
    
    .status-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
    .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .status-badge.actif { background: #f0fdf4; color: #007A4D; }
    .status-badge.en_attente { background: #fefce8; color: #a16207; }
    .status-badge.rejete { background: #fef2f2; color: #b91c1c; }
    .status-badge.suspendu { background: #fef2f2; color: #CE1126; }

    .mini-stats { display: grid; grid-template-columns: repeat(3, 1fr); padding: 14px 18px; gap: 8px; border-bottom: 1px solid var(--border); }
    .mini-stat { text-align: center; }
    .mini-stat-value { font-size: 20px; font-weight: 800; color: var(--text); line-height: 1; }
    .mini-stat-label { font-size: 10.5px; color: var(--text-light); margin-top: 2px; }

    .info-list { padding: 14px 18px; border-bottom: 1px solid var(--border); }
    .info-row { display: flex; align-items: center; gap: 8px; padding: 7px 0; font-size: 12.5px; border-bottom: 1px solid var(--border); }
    .info-row:last-child { border-bottom: none; }
    .info-row svg { width: 13px; height: 13px; color: var(--teal); flex-shrink: 0; }
    .info-label { color: var(--text-light); width: 80px; flex-shrink: 0; font-size: 11.5px; }
    .info-value { color: var(--text); font-weight: 600; font-size: 12.5px; }

    .profile-actions { padding: 14px 18px; display: flex; flex-direction: column; gap: 7px; }
    .btn-action { width: 100%; padding: 9px 12px; border-radius: 10px; font-size: 12.5px; font-weight: 700; cursor: pointer; border: 1.5px solid transparent; font-family: inherit; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 6px; }
    .btn-action svg { width: 14px; height: 14px; }
    .btn-validate { background: #f0fdf4; color: #007A4D; border-color: #bbf7d0; }
    .btn-validate:hover { background: #dcfce7; }
    .btn-suspend { background: #fefce8; color: #a16207; border-color: #fde68a; }
    .btn-suspend:hover { background: #fef9c3; }
    .btn-delete { background: #fef2f2; color: #CE1126; border-color: #fecaca; }
    .btn-delete:hover { background: #fee2e2; }

    /* Formulaire de rejet imbriqué */
    .rejet-box { background: #fff5f5; padding: 12px; border-radius: 10px; border: 1px solid #fecaca; margin-top: 4px; }
    .rejet-label { display: block; font-size: 11px; font-weight: 700; color: #991b1b; margin-bottom: 6px; text-transform: uppercase; }
    .rejet-input { width: 100%; padding: 8px 10px; font-size: 12px; border: 1.5px solid #fecaca; border-radius: 6px; font-family: inherit; margin-bottom: 8px; outline: none; transition: border-color 0.2s; }
    .rejet-input:focus { border-color: #f87171; }

    /* Contenu colonne droite */
    .content-col { display: flex; flex-direction: column; gap: 20px; min-width: 0; }
    .section-card { background: var(--white); border-radius: 16px; border: 1.5px solid var(--border); }
    .section-header { padding: 15px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .section-title { font-size: 13.5px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 7px; }
    .section-title svg { width: 15px; height: 15px; color: var(--teal); }
    .section-badge { font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px; background: var(--teal-light); color: var(--teal); }

    /* Tableau scrollable */
    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-scroll table { width: 100%; border-collapse: collapse; min-width: 480px; }
    .table-scroll th { padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.06em; border-bottom: 1.5px solid var(--border); background: var(--bg); white-space: nowrap; }
    .table-scroll td { padding: 12px 16px; font-size: 13px; color: var(--text); border-bottom: 1px solid var(--border); vertical-align: middle; }
    .table-scroll tr:last-child td { border-bottom: none; }
    .table-scroll tr:hover td { background: var(--teal-light); }
    .empty-row { text-align: center; color: var(--text-light); padding: 30px; font-size: 13px; }
    .cat-badge { font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px; background: #EEF0F8; color: #2E3A6B; white-space: nowrap; }
    .code-mono { font-family: monospace; font-size: 11.5px; color: var(--text-light); white-space: nowrap; }

    /* Signalements */
    .activity-item { display: flex; align-items: flex-start; gap: 12px; padding: 12px 20px; border-bottom: 1px solid var(--border); transition: background 0.15s; }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: var(--teal-light); }
    .activity-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
    .activity-dot.green { background: #4ade80; }
    .activity-dot.yellow { background: #FCD116; }
    .activity-dot.gray { background: #94a3b8; }

    /* Responsive mobile */
    @media (max-width: 768px) {
        .fab-layout { grid-template-columns: 1fr; }
        .profile-card, .content-col { width: 100%; }
    }
</style>
@endsection

@section('content')
@php
    $statut = $fabricant->statut ?? 'en_attente';
    $totalLots = $fabricant->produits->sum(fn($p) => $p->lots->count());
    $totalQR   = $fabricant->produits->sum(fn($p) => $p->lots->sum(fn($l) => $l->qrCodes->count()));
    
    $signalements = \App\Models\Signalement::whereHas('qrCode.lot.produit', function($q) use ($fabricant) {
        $q->where('fabricant_id', $fabricant->id);
    })->with('qrCode.lot.produit')->latest()->take(10)->get();
@endphp

<div class="breadcrumb">
    <a href="{{ route('admin.fabricants.index') }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Fabricants
    </a>
    <span>/</span>
    <strong>{{ $fabricant->nom_entreprise }}</strong>
</div>

{{-- Message Flash de Succès --}}
@if(session('success'))
    <div style="background: #dcfce7; border: 1.5px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

<div class="fab-layout">
    {{-- Profil Card --}}
    <div class="profile-card">
        <div class="profile-banner"></div>
        <div class="profile-header">
            <div class="profile-avatar">{{ strtoupper(substr($fabricant->nom_entreprise ?? 'F', 0, 1)) }}</div>
            <div class="profile-name">{{ $fabricant->nom_entreprise }}</div>
            <div class="profile-email">{{ $fabricant->email }}</div>
            <span class="status-badge {{ $statut }}">
                {{ match($statut) { 'actif' => 'Actif', 'en_attente' => 'En attente', 'rejete' => 'Rejeté', 'suspendu' => 'Suspendu', default => ucfirst($statut) } }}
            </span>
        </div>
        
        <div class="mini-stats">
            <div class="mini-stat"><div class="mini-stat-value">{{ $fabricant->produits->count() }}</div><div class="mini-stat-label">Produits</div></div>
            <div class="mini-stat"><div class="mini-stat-value">{{ $totalLots }}</div><div class="mini-stat-label">Lots</div></div>
            <div class="mini-stat"><div class="mini-stat-value">{{ $totalQR }}</div><div class="mini-stat-label">QR Codes</div></div>
        </div>
        
        <div class="info-list">
            <div class="info-row">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <span class="info-label">Téléphone</span>
                <span class="info-value">{{ $fabricant->telephone ?? '–' }}</span>
            </div>
            <div class="info-row">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="info-label">Ville</span>
                <span class="info-value">{{ $fabricant->ville ?? '–' }}</span>
            </div>
            <div class="info-row">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="info-label">Inscription</span>
                <span class="info-value">{{ $fabricant->created_at->format('d/m/Y') }}</span>
            </div>
    
            <div class="info-row">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                <span class="info-label">ID</span>
                <span class="info-value">#{{ $fabricant->id }}</span>
            </div>

        </div>
        
        <div class="profile-actions">
            @if($statut === 'en_attente')
                {{-- Bouton Valider --}}
                <form method="POST" action="{{ route('admin.fabricants.valider', $fabricant->id) }}">
                    @csrf
                    <button type="submit" class="btn-action btn-validate">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Valider le fabricant
                    </button>
                </form>

                {{-- Formulaire de rejet --}}
                <div class="rejet-box">
                    <form method="POST" action="{{ route('admin.fabricants.rejeter', $fabricant->id) }}">
                        @csrf
                        <label class="rejet-label">Motif de rejet</label>
                        <input type="text" name="motif" class="rejet-input" placeholder="Expliquez la raison du rejet..." required>
                        <button type="submit" class="btn-action btn-delete" style="width:100%;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Rejeter la demande
                        </button>
                    </form>
                </div>
            @endif

            @if($statut === 'actif')
                <form method="POST" action="{{ route('admin.fabricants.suspendre', $fabricant->id) }}">
                    @csrf
                    <button type="submit" class="btn-action btn-suspend">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Suspendre le compte
                    </button>
                </form>
            @endif

            @if($statut === 'suspendu')
                <form method="POST" action="{{ route('admin.fabricants.reactiver', $fabricant->id) }}">
                    @csrf
                    <button type="submit" class="btn-action btn-validate">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Réactiver le compte
                    </button>
                </form>
            @endif

            @if($statut !== 'en_attente')
                <form method="POST" action="{{ route('admin.fabricants.destroy', $fabricant->id) }}" onsubmit="return confirm('Supprimer définitivement ce fabricant et toutes ses données associées ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-action btn-delete">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Supprimer définitivement
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Contenu Principal --}}
    <div class="content-col">
        {{-- Produits --}}
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Produits enregistrés
                </div>
                <span class="section-badge">{{ $fabricant->produits->count() }} produit(s)</span>
            </div>
            
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th>Code</th>
                            <th style="text-align: center;">Lots</th>
                            <th style="text-align: center;">QR Codes</th>
                            <th>Ajouté le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fabricant->produits as $produit)
                        <tr>
                            <td>
                                <div style="font-weight:700;">{{ $produit->nom }}</div>
                                @if($produit->description)
                                    <div style="font-size:11px; color:var(--text-light);">{{ Str::limit($produit->description, 40) }}</div>
                                @endif
                            </td>
                            <td><span class="cat-badge">{{ $produit->categorie }}</span></td>
                            <td><span class="code-mono">{{ $produit->code_produit }}</span></td>
                            <td style="font-weight:700; text-align:center;">{{ $produit->lots->count() }}</td>
                            <td style="font-weight:700; text-align:center;">{{ $produit->lots->sum(fn($l) => $l->qrCodes->count()) }}</td>
                            <td>{{ $produit->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="empty-row">Aucun produit enregistré</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Signalements --}}
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Signalements liés aux produits
                </div>
                <span class="section-badge">{{ $signalements->count() }} signalement(s)</span>
            </div>
            
            @forelse($signalements as $sig)
            <div class="activity-item">
                <div class="activity-dot {{ $sig->statut === 'traite' ? 'green' : ($sig->statut === 'en_cours' ? 'yellow' : 'gray') }}"></div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:12.5px; font-weight:700; color:var(--text);">{{ $sig->qrCode->lot->produit->nom ?? 'Produit inconnu' }}</div>
                    <div style="font-size:11px; color:var(--text-light); margin-top:1px;">{{ Str::limit($sig->description, 60) }}</div>
                </div>
                <div style="text-align:right; flex-shrink:0;">
                    <span class="status-badge {{ $sig->statut }}" style="font-size:10px; padding:2px 7px;">
                        {{ match($sig->statut) { 'en_cours' => 'En cours', 'traite' => 'Traité', 'rejete' => 'Rejeté', default => $sig->statut } }}
                    </span>
                    <div style="font-size:11px; color:var(--text-light); margin-top:3px;">{{ $sig->created_at->format('d/m/Y') }}</div>
                </div>
            </div>
            @empty
            <div class="empty-row">Aucun signalement lié à ce fabricant</div>
            @endforelse
        </div>
    </div>
</div>
@endsection