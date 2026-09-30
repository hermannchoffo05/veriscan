@extends('layouts.fabricant')

@section('title', __('messages.mes_produits'))

@section('topbar-title')
    {{ __('messages.mes_produits') }}
@endsection

@section('styles')
<style>
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .page-header-left h1 { font-size: 22px; font-weight: 800; color: var(--text); }
    .page-header-left p { font-size: 13px; color: var(--text-light); margin-top: 3px; }
    .btn-primary { background: #F5A623; color: #171B3D; border: none; border-radius: 10px; padding: 10px 20px; font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; text-decoration: none; }
    .btn-primary:hover { background: #e0961d; transform: translateY(-1px); }
    .btn-primary svg { width: 15px; height: 15px; }
    .filters-bar { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
    .search-input { display: flex; align-items: center; gap: 8px; background: var(--white); border: 1.5px solid var(--border); border-radius: 10px; padding: 8px 14px; flex: 1; min-width: 200px; }
    .search-input svg { width: 14px; height: 14px; color: var(--text-light); flex-shrink: 0; }
    .search-input input { border: none; outline: none; font-family: inherit; font-size: 13px; color: var(--text); width: 100%; background: transparent; }
    .filter-select { background: var(--white); border: 1.5px solid var(--border); border-radius: 10px; padding: 8px 14px; font-size: 13px; font-family: inherit; color: var(--text); outline: none; cursor: pointer; }
    .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
    .product-card { background: var(--white); border: 1.5px solid var(--border); border-radius: 16px; overflow: hidden; transition: all 0.2s; }
    .product-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); border-color: var(--teal); }
    .product-card-image { width: 100%; height: 130px; overflow: hidden; position: relative; background: transparent; display: flex; align-items: center; justify-content: center; }
    .product-card-image img { width: 100%; height: 100%; object-fit: contain; transition: transform 0.3s; }
    .product-card:hover .product-card-image img { transform: scale(1.04); }
    .product-card-image .no-image-icon { width: 52px; height: 52px; color: var(--teal); opacity: 0.35; }
    .product-card-image .image-badge { position: absolute; top: 10px; left: 10px; font-size: 10.5px; font-weight: 700; padding: 3px 9px; border-radius: 6px; background: rgba(255,255,255,0.92); color: var(--teal); backdrop-filter: blur(4px); }
    .product-card-image .status-overlay { position: absolute; top: 10px; right: 10px; }
    .product-card-body { padding: 14px 18px; }
    .product-card-name { font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
    .product-card-lot { font-size: 11.5px; color: var(--text-light); margin-bottom: 12px; }
    .product-card-stats { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-bottom: 14px; }
    .product-stat { background: var(--bg); border-radius: 8px; padding: 8px; text-align: center; }
    .product-stat-val { font-size: 16px; font-weight: 800; color: var(--teal); }
    .product-stat-lbl { font-size: 10px; color: var(--text-light); }
    .product-card-footer { display: flex; gap: 8px; flex-wrap: wrap; }
    .btn-sm { padding: 7px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; transition: all 0.2s; border: none; }
    .btn-sm svg { width: 12px; height: 12px; }
    .btn-sm.teal { background: var(--teal-light); color: var(--teal); }
    .btn-sm.teal:hover { background: var(--teal); color: white; }
    .btn-sm.gray { background: var(--bg); color: var(--text); }
    .btn-sm.gray:hover { background: var(--border); }
    .btn-sm.red { background: #fef2f2; color: var(--red); }
    .btn-sm.red:hover { background: var(--red); color: white; }
    .status-badge-img { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px; backdrop-filter: blur(4px); }
    .status-badge-img::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
    .status-badge-img.actif { background: rgba(240,253,244,0.92); color: var(--green-ok); }
    .status-badge-img.actif::before { background: var(--green-ok); }
    .status-badge-img.suspect { background: rgba(254,252,232,0.92); color: #a16207; }
    .status-badge-img.suspect::before { background: var(--yellow); }
    .empty-state { text-align: center; padding: 60px 20px; background: var(--white); border-radius: 16px; border: 1.5px dashed var(--border); }
    .empty-state-icon { width: 64px; height: 64px; background: var(--teal-light); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
    .empty-state-icon svg { width: 28px; height: 28px; color: var(--teal); }
    .empty-state h3 { font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 8px; }
    .empty-state p { font-size: 13px; color: var(--text-light); margin-bottom: 20px; }
    @media (max-width: 480px) { .products-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')

    <div class="page-header">
        <div class="page-header-left">
            <h1>{{ __('messages.mes_produits') }}</h1>
            <p>{{ __('messages.produits_desc') }}</p>
        </div>
        <a href="{{ route('fabricant.produits.create') }}" class="btn-primary">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('messages.nouveau_produit') }}
        </a>
    </div>

    <div class="filters-bar">
        <div class="search-input">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" placeholder="{{ __('messages.rechercher_produit') }}">
        </div>
        <select class="filter-select" name="categorie" id="filtreCategorie">
            <option value="">{{ __('messages.tous_secteurs') }}</option>
            <option value="Pharmaceutique">{{ __('messages.pharmaceutique') }}</option>
            <option value="Cosmétique">{{ __('messages.cosmetique') }}</option>
        </select>
        <select class="filter-select">
            <option>{{ __('messages.tous_statuts') }}</option>
            <option>{{ __('messages.authentique') }}</option>
            <option>{{ __('messages.suspect') }}</option>
        </select>
    </div>

    @if($produits->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
            </div>
            <h3>{{ __('messages.aucun_produit') }}</h3>
            <p>{{ __('messages.aucun_produit_desc') }}</p>
            <a href="{{ route('fabricant.produits.create') }}" class="btn-primary">
                + {{ __('messages.nouveau_produit') }}
            </a>
        </div>
    @else
        <div class="products-grid">
            @foreach($produits as $produit)
                <div class="product-card" @if($produit->est_suspect) style="border-color:#fde68a;" @endif>

                    {{-- Image --}}
                    <div class="product-card-image">
                        @if($produit->image)
                            <img src="{{ Storage::url($produit->image) }}" alt="{{ $produit->nom }}">
                        @else
                            <svg class="no-image-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                        @endif
                        <span class="image-badge">{{ $produit->categorie }}</span>
                        <span class="status-overlay">
                            @if(!$produit->estCertifie())
                                <span class="status-badge-img suspect">{{ $produit->libelle_certification }}</span>
                            @elseif($produit->est_suspect)
                                <span class="status-badge-img suspect">{{ __('messages.suspect') }}</span>
                            @else
                                <span class="status-badge-img actif">{{ __('messages.authentique') }}</span>
                            @endif
                        </span>
                    </div>

                    <div class="product-card-body">
                        <div class="product-card-name">{{ $produit->nom }}</div>
                        <div class="product-card-lot">
                            @if($produit->dernier_lot)
                                {{ $produit->dernier_lot->numero_lot }} · {{ __('messages.cree_le') }} {{ $produit->created_at->format('d/m/Y') }}
                            @else
                                {{ __('messages.cree_le') }} {{ $produit->created_at->format('d/m/Y') }}
                            @endif
                        </div>

                        <div class="product-card-stats">
                            <div class="product-stat">
                                <div class="product-stat-val">{{ $produit->total_qr }}</div>
                                <div class="product-stat-lbl">{{ __('messages.qr_generes') }}</div>
                            </div>
                            <div class="product-stat">
                                <div class="product-stat-val">{{ $produit->total_scans }}</div>
                                <div class="product-stat-lbl">{{ __('messages.scans') }}</div>
                            </div>
                            <div class="product-stat">
                                <div class="product-stat-val" @if($produit->total_sigs > 0) style="color:#a16207;" @endif>
                                    {{ $produit->total_sigs }}
                                </div>
                                <div class="product-stat-lbl">{{ __('messages.alertes') }}</div>
                            </div>
                        </div>

                        <div class="product-card-footer">
                            <a href="{{ route('fabricant.produits.show', $produit->id) }}" class="btn-sm teal">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ __('messages.voir') }}
                            </a>
                            <a href="{{ route('fabricant.qrcodes.index') }}" class="btn-sm gray">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg>
                                QR Codes
                            </a>
                            @if($produit->total_sigs > 0)
                                <a href="{{ route('fabricant.signalements.index') }}" class="btn-sm red">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/></svg>
                                    {{ $produit->total_sigs }} {{ __('messages.alerte') }}
                                </a>
                            @endif
                            <a href="{{ route('fabricant.produits.edit', $produit->id) }}" class="btn-sm gray">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                {{ __('messages.modifier') }}
                            </a>
                            <form action="{{ route('fabricant.produits.destroy', $produit->id) }}" method="POST" style="display:inline;" onsubmit="return confirmerSuppression(event, '{{ addslashes($produit->nom) }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-sm red">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    {{ __('messages.supprimer') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($produits->hasPages())
            <div style="margin-top:24px;">
                {{ $produits->links() }}
            </div>
        @endif
    @endif

<script>
function confirmerSuppression(event, nomProduit) {
    event.preventDefault();
    const confirme = confirm("{{ app()->getLocale() === 'en' ? 'Delete' : 'Supprimer' }} \"" + nomProduit + "\" ? " + "{{ app()->getLocale() === 'en' ? 'This action is irreversible.' : 'Cette action est irréversible.' }}");
    if (confirme) {
        event.target.submit();
    }
    return false;
}
</script>

@endsection