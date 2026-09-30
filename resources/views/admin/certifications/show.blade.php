@extends('layouts.admin')

@section('title', 'Examen de certification')

@section('topbar-title')
    Examen de <span>certification</span>
@endsection

@section('topbar-actions')
    <a href="{{ route('admin.certifications.index') }}"
       style="font-size:12.5px;font-weight:700;color:var(--teal);text-decoration:none;">← Retour à la liste</a>
@endsection

@section('styles')
<style>
    .grid { display:grid; grid-template-columns: 1fr 1.2fr; gap:20px; align-items:start; }
    @media (max-width: 960px) { .grid { grid-template-columns: 1fr; } }

    .card { background:var(--white); border-radius:16px; border:1.5px solid var(--border); padding:22px 24px; margin-bottom:20px; }
    .card h3 { font-size:15px; font-weight:800; margin-bottom:14px; }
    .row { display:flex; justify-content:space-between; gap:16px; padding:9px 0; border-bottom:1px solid var(--border); font-size:13px; }
    .row:last-child { border-bottom:none; }
    .row .k { color:var(--text-light); font-weight:600; flex-shrink:0; }
    .row .v { font-weight:600; text-align:right; word-break:break-word; }
    .long { font-size:13px; line-height:1.6; background:var(--bg); border-radius:10px; padding:12px 14px; margin-top:6px; white-space:pre-wrap; }

    .badge { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .badge.soumis   { background:#fefce8; color:#a16207; }
    .badge.certifie { background:#f0fdf4; color:#007A4D; }
    .badge.rejete   { background:#fef2f2; color:#CE1126; }
    .badge.revoque  { background:#f3f4f6; color:#4b5563; }

    .viewer { width:100%; height:520px; border:1.5px solid var(--border); border-radius:12px; background:var(--bg); }
    .viewer-img { max-width:100%; border-radius:12px; border:1.5px solid var(--border); }
    .missing { padding:14px; border-radius:10px; background:#fffbeb; color:#92400e; font-size:13px; }

    .decision textarea {
        width:100%; padding:10px 12px; border:1.5px solid var(--border); border-radius:10px;
        font-family:inherit; font-size:13px; min-height:80px; outline:none; margin-bottom:10px;
    }
    .decision textarea:focus { border-color:var(--teal); }
    .actions { display:flex; gap:10px; flex-wrap:wrap; }
    .btn { padding:10px 18px; border-radius:10px; border:none; font-weight:800; font-size:13px; cursor:pointer; font-family:inherit; }
    .btn.ok     { background:#007A4D; color:#fff; }
    .btn.ok:hover { background:#006340; }
    .btn.reject { background:#fef2f2; color:#CE1126; }
    .btn.reject:hover { background:#fee2e2; }
    .btn.revoke { background:#CE1126; color:#fff; }
    .err { color:#CE1126; font-size:12px; margin:-4px 0 10px; }
</style>
@endsection

@section('content')

<div class="grid">
    <div>
        <div class="card">
            <h3>Produit</h3>
            <div class="row"><span class="k">Nom</span><span class="v">{{ $produit->nom }}</span></div>
            <div class="row"><span class="k">Code</span><span class="v">{{ $produit->code_produit }}</span></div>
            <div class="row"><span class="k">Secteur</span><span class="v">{{ $produit->categorie }}</span></div>
            <div class="row"><span class="k">Fabricant</span>
                <span class="v">
                    <a href="{{ route('admin.fabricants.show', $produit->fabricant_id) }}" style="color:var(--teal);text-decoration:none;">
                        {{ $produit->fabricant->nom_entreprise ?? '–' }}
                    </a>
                </span>
            </div>
            <div class="row"><span class="k">Soumis le</span><span class="v">{{ $produit->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="row"><span class="k">Statut</span>
                <span class="v"><span class="badge {{ $produit->statut_certification }}">{{ $produit->libelle_certification }}</span></span>
            </div>
            @if($produit->numero_certificat)
                <div class="row"><span class="k">N° de certificat</span><span class="v">{{ $produit->numero_certificat }}</span></div>
            @endif
            @if($produit->certifie_le)
                <div class="row"><span class="k">Certifié le</span><span class="v">{{ $produit->certifie_le->format('d/m/Y') }}</span></div>
            @endif
            @if($produit->motif_decision)
                <div class="row"><span class="k">Motif</span><span class="v">{{ $produit->motif_decision }}</span></div>
            @endif
            @if($produit->description)
                <div class="long">{{ $produit->description }}</div>
            @endif
        </div>

        <div class="card">
            <h3>Informations déclarées par le fabricant</h3>
            @php $c = $produit->certification; @endphp
            @if(!$c)
                <div class="missing">Aucune information réglementaire renseignée.</div>
            @elseif($produit->categorie === 'Pharmaceutique')
                <div class="row"><span class="k">N° d'AMM</span><span class="v">{{ $c->numero_amm }}</span></div>
                <div class="row"><span class="k">Laboratoire</span><span class="v">{{ $c->laboratoire_fabricant ?? '–' }}</span></div>
                <div class="row"><span class="k">Date de l'AMM</span><span class="v">{{ $c->date_amm?->format('d/m/Y') ?? '–' }}</span></div>
            @else
                <div class="row"><span class="k">Réf. certificat</span><span class="v">{{ $c->certificat_conformite ?? '–' }}</span></div>
                <div class="row"><span class="k">Date</span><span class="v">{{ $c->date_certification?->format('d/m/Y') ?? '–' }}</span></div>
                <div style="margin-top:8px;font-size:12px;font-weight:700;color:var(--text-light);">Liste INCI</div>
                <div class="long">{{ $c->liste_inci }}</div>
            @endif
        </div>

        <div class="card decision">
            <h3>Décision de l'autorité</h3>

            @if($errors->any())
                <div class="err">{{ $errors->first() }}</div>
            @endif

            @if($produit->statut_certification === 'soumis' || $produit->statut_certification === 'rejete')
                <div class="actions" style="margin-bottom:14px;">
                    <form method="POST" action="{{ route('admin.certifications.certifier', $produit->id) }}"
                          onsubmit="return confirm('Certifier ce produit ? Le fabricant pourra générer des QR codes.')">
                        @csrf
                        <button type="submit" class="btn ok">✓ Certifier ce produit</button>
                    </form>
                </div>
            @endif

            @if($produit->statut_certification === 'soumis')
                <form method="POST" action="{{ route('admin.certifications.rejeter', $produit->id) }}">
                    @csrf
                    <textarea name="motif" placeholder="Motif du rejet (obligatoire, visible par le fabricant)…" required></textarea>
                    <button type="submit" class="btn reject">✕ Rejeter</button>
                </form>
            @endif

            @if($produit->statut_certification === 'certifie')
                <form method="POST" action="{{ route('admin.certifications.revoquer', $produit->id) }}"
                      onsubmit="return confirm('Révoquer la certification ? Tous les QR codes du produit seront révoqués.')">
                    @csrf
                    <textarea name="motif" placeholder="Motif de la révocation (obligatoire)…" required></textarea>
                    <button type="submit" class="btn revoke">Révoquer la certification</button>
                </form>
            @endif

            @if($produit->statut_certification === 'rejete')
                <div class="missing" style="margin-top:10px;">Produit rejeté : en attente d'un nouveau justificatif du fabricant.</div>
            @endif
            @if($produit->statut_certification === 'revoque')
                <div class="missing">Certification révoquée. Le fabricant peut déposer un nouveau justificatif pour être réexaminé.</div>
            @endif
        </div>
    </div>

    <div class="card">
        <h3>Justificatif déposé</h3>
        @if(!$justificatifExiste)
            <div class="missing">Fichier introuvable sur le serveur.</div>
        @elseif($extension === 'pdf')
            <iframe class="viewer" src="{{ route('admin.certifications.justificatif', $produit->id) }}"></iframe>
        @else
            <img class="viewer-img" src="{{ route('admin.certifications.justificatif', $produit->id) }}" alt="Justificatif">
        @endif
        @if($justificatifExiste)
            <p style="margin-top:10px;">
                <a href="{{ route('admin.certifications.justificatif', $produit->id) }}" target="_blank"
                   style="font-size:12.5px;font-weight:700;color:var(--teal);text-decoration:none;">Ouvrir dans un nouvel onglet ↗</a>
            </p>
        @endif
    </div>
</div>

@endsection
