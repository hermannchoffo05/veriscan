@extends('layouts.fabricant')
@section('title', $produit->nom)
@section('topbar-title')
    {{ __('messages.detail') }} <span>{{ __('messages.produit') }}</span>
@endsection
@section('topbar-actions')
    <a href="{{ route('fabricant.lots.create', $produit->id) }}" class="btn-topbar btn-topbar-primary">
        + {{ __('messages.nouveau_lot') }}
    </a>
    <a href="{{ route('fabricant.produits.edit', $produit->id) }}" class="btn-topbar">
        {{ __('messages.modifier') }}
    </a>
    <a href="{{ route('fabricant.produits.index') }}" class="btn-topbar">
        ← {{ __('messages.retour') }}
    </a>
@endsection

@section('styles')
<style>
/* ── BOUTONS MOBILE SOUS TOPBAR ── */
.page-actions-mobile {
    display: none;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.page-actions-mobile a {
    flex: 1;
    min-width: 90px;
    text-align: center;
    padding: 10px 10px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: 1.5px solid #e5e7eb;
    color: #1f2937;
    background: #fff;
    transition: all 0.2s;
}
.page-actions-mobile a.primary {
    background: #F5A623;
    color: #171B3D;
    border-color: #F5A623;
}

.product-header {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    padding: 28px;
    display: flex;
    gap: 24px;
    align-items: flex-start;
    margin-bottom: 24px;
}
.product-image {
    width: 120px; height: 120px;
    border-radius: 12px;
    flex-shrink: 0; background: #EEF0F8;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
}
.product-image img { width: 120px; height: 120px; border-radius: 12px; object-fit: cover; }
.product-image svg { width: 48px; height: 48px; color: #2E3A6B; }
.product-meta { flex: 1; min-width: 0; }
.product-name { font-size: 22px; font-weight: 700; color: #1f2937; margin-bottom: 6px; }

.code-produit-box {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #EEF0F8;
    border: 1.5px solid rgba(46,58,107,0.3);
    border-radius: 10px;
    padding: 8px 14px;
    margin-bottom: 10px;
    max-width: 100%;
}
.code-produit-label { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap; }
.code-produit-value { font-family: monospace; font-size: 16px; font-weight: 800; color: #2E3A6B; letter-spacing: 0.08em; word-break: break-all; }
.code-produit-copy { background: none; border: none; cursor: pointer; color: #9ca3af; padding: 2px; display: flex; align-items: center; transition: color 0.2s; border-radius: 4px; flex-shrink: 0; }
.code-produit-copy:hover { color: #2E3A6B; }
.code-produit-copy svg { width: 14px; height: 14px; }
.code-produit-hint { font-size: 11px; color: #9ca3af; margin-bottom: 12px; display: flex; align-items: flex-start; gap: 4px; line-height: 1.5; }
.code-produit-hint svg { width: 12px; height: 12px; flex-shrink: 0; margin-top: 2px; }
.product-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #EEF0F8; color: #2E3A6B; border: 1px solid #c7cce6; }
.product-desc { font-size: 14px; color: #6b7280; margin-top: 10px; line-height: 1.6; }

.stats-row {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 16px; margin-bottom: 24px;
}
.stat-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; padding: 20px; text-align: center; }
.stat-value { font-size: 28px; font-weight: 700; color: #2E3A6B; }
.stat-label { font-size: 12px; color: #6b7280; margin-top: 4px; }

.lots-section { background: #fff; border-radius: 16px; border: 1px solid #e5e7eb; overflow: hidden; }
.section-header { padding: 20px 24px; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
.section-title { font-size: 16px; font-weight: 700; color: #1f2937; }
.table-scroll-hint { display: none; font-size: 11px; color: #9ca3af; padding: 6px 16px 0; text-align: right; }
.table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
table { width: 100%; border-collapse: collapse; min-width: 520px; }
th { background: #f9fafb; padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }
td { padding: 14px 16px; border-top: 1px solid #f3f4f6; font-size: 14px; color: #1f2937; vertical-align: middle; }
tr:hover td { background: #f9fafb; }
.actions-cell { display: flex; gap: 6px; align-items: center; }
.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-green { background: #dcfce7; color: #16a34a; }
.badge-red   { background: #fee2e2; color: #dc2626; }
.badge-gray  { background: #f3f4f6; color: #6b7280; }
.btn-sm { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: .2s; white-space: nowrap; display: inline-flex; align-items: center; }
.btn-sm-teal { background: #EEF0F8; color: #2E3A6B; }
.btn-sm-teal:hover { background: #dde1f0; }
.btn-sm-edit { background: #eff6ff; color: #2563eb; }
.btn-sm-edit:hover { background: #dbeafe; }
.btn-sm-red { background: #fef2f2; color: #dc2626; }
.btn-sm-red:hover { background: #fee2e2; }
.empty-state { text-align: center; padding: 48px 24px; color: #6b7280; }
.empty-state svg { width: 48px; height: 48px; margin: 0 auto 12px; display: block; color: #d1d5db; }
.btn-topbar-primary { background: #F5A623; color: #171B3D !important; border-radius: 8px; padding: 8px 16px; font-size: 13px; font-weight: 700; text-decoration: none; }

.copy-toast { position: fixed; bottom: 24px; right: 24px; background: #171B3D; color: white; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; box-shadow: 0 4px 16px rgba(0,0,0,0.15); opacity: 0; transform: translateY(8px); transition: all 0.3s; pointer-events: none; z-index: 9999; }
.copy-toast.show { opacity: 1; transform: translateY(0); }

/* ── RESPONSIVE MOBILE ── */
@media (max-width: 768px) {
    .page-actions-mobile { display: flex; }

    .product-header { flex-direction: column; align-items: center; text-align: center; padding: 20px 16px; gap: 14px; }
    .product-image { width: 80px; height: 80px; }
    .product-image img { width: 80px; height: 80px; }
    .product-image svg { width: 32px; height: 32px; }
    .product-name { font-size: 18px; }
    .code-produit-box { justify-content: center; }
    .code-produit-hint { justify-content: center; text-align: center; }

    .stats-row { grid-template-columns: repeat(3, 1fr) !important; gap: 8px; }
    .stat-card { padding: 12px 4px; }
    .stat-value { font-size: 18px; }
    .stat-label { font-size: 10px; line-height: 1.3; }

    .section-header { padding: 14px 16px; }
    .table-scroll-hint { display: block; }
    .copy-toast { bottom: 16px; right: 16px; left: 16px; text-align: center; }
}
</style>
@endsection

@section('content')

{{-- Boutons d'action mobile --}}
<div class="page-actions-mobile">
    <a href="{{ route('fabricant.lots.create', $produit->id) }}" class="primary">
        + {{ __('messages.nouveau_lot') }}
    </a>
    <a href="{{ route('fabricant.produits.edit', $produit->id) }}">
        {{ __('messages.modifier') }}
    </a>
    <a href="{{ route('fabricant.produits.index') }}">
        ← {{ __('messages.retour') }}
    </a>
</div>

{{-- Header produit --}}
<div class="product-header">
    <div class="product-image">
        @if($produit->image)
            <img src="{{ asset('storage/'.$produit->image) }}" alt="{{ $produit->nom }}">
        @else
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
        @endif
    </div>
    <div class="product-meta">
        <div class="product-name">{{ $produit->nom }}</div>
        <div class="code-produit-box">
            <span class="code-produit-label">Code produit</span>
            <span class="code-produit-value" id="codeProduit">{{ $produit->code_produit }}</span>
            <button class="code-produit-copy" onclick="copierCode()" title="Copier le code">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </button>
        </div>
        <div class="code-produit-hint">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Ce code est imprimé sous le QR code sur l'emballage — les consommateurs peuvent le saisir manuellement sur VeriScan
        </div>
        <span class="product-badge">{{ $produit->categorie }}</span>
        @if($produit->description)
            <div class="product-desc">{{ $produit->description }}</div>
        @endif
    </div>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-value">{{ $produit->lots->count() }}</div>
        <div class="stat-label">{{ __('messages.lots_production') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $produit->lots->sum(fn($l) => $l->qrcodes->count()) }}</div>
        <div class="stat-label">{{ __('messages.qr_generes') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $produit->lots->sum(fn($l) => $l->qrcodes->sum('nb_scans')) }}</div>
        <div class="stat-label">{{ __('messages.scans_total') }}</div>
    </div>
</div>

{{-- Lots --}}
<div class="lots-section">
    <div class="section-header">
        <span class="section-title">{{ __('messages.lots_production') }}</span>
        <a href="{{ route('fabricant.lots.create', $produit->id) }}" class="btn-sm btn-sm-teal">
            + {{ __('messages.nouveau_lot') }}
        </a>
    </div>

    @if($produit->lots->isEmpty())
    <div class="empty-state">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
        <p>{{ __('messages.aucun_lot_cree') }}</p>
        <a href="{{ route('fabricant.lots.create', $produit->id) }}" class="btn-sm btn-sm-teal" style="margin-top:12px;">
            {{ __('messages.creer_premier_lot') }}
        </a>
    </div>
    @else
    <span class="table-scroll-hint">← défiler →</span>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.numero_lot') }}</th>
                    <th>{{ __('messages.date_fabrication') }}</th>
                    <th>{{ __('messages.date_expiration') }}</th>
                    <th>{{ __('messages.quantite') }}</th>
                    <th>QR</th>
                    <th>{{ __('messages.statut') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produit->lots as $lot)
                <tr>
                    <td><strong>{{ $lot->numero_lot }}</strong></td>
                    <td>{{ $lot->date_fabrication->format('d/m/Y') }}</td>
                    <td>
                        @if($lot->date_expiration->isPast())
                            <span style="color:#dc2626">{{ $lot->date_expiration->format('d/m/Y') }}</span>
                        @else
                            {{ $lot->date_expiration->format('d/m/Y') }}
                        @endif
                    </td>
                    <td>{{ number_format($lot->quantite) }}</td>
                    <td>{{ $lot->qrcodes->count() }}/{{ $lot->quantite }}</td>
                    <td>
                        @if($lot->date_expiration->isPast())
                            <span class="badge badge-red">{{ __('messages.expire') }}</span>
                        @else
                            <span class="badge badge-green">{{ __('messages.actif') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions-cell">
                            <a href="{{ route('fabricant.qrcodes.create') }}?lot_id={{ $lot->id }}"
                               class="btn-sm btn-sm-teal">{{ __('messages.generer_qr') }}</a>
                            <a href="{{ route('fabricant.lots.download-pdf', $lot->id) }}" class="btn-sm btn-sm-teal">PDF lot</a>
                            <a href="{{ route('fabricant.lots.edit', [$produit->id, $lot->id]) }}"
                               class="btn-sm btn-sm-edit">{{ __('messages.modifier') }}</a>
                            <form action="{{ route('fabricant.lots.destroy', [$produit->id, $lot->id]) }}"
                                  method="POST"
                                  onsubmit="return confirm('{{ __('messages.confirmer_suppression_lot') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-sm-red">{{ __('messages.supprimer') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<div class="copy-toast" id="copyToast">✓ Code copié dans le presse-papiers</div>

@endsection

@section('scripts')
<script>
function copierCode() {
    const code = document.getElementById('codeProduit').textContent;
    navigator.clipboard.writeText(code).then(() => {
        const toast = document.getElementById('copyToast');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2500);
    });
}
</script>
@endsection