@extends('layouts.fabricant')
@section('title', __('messages.modifier') . ' ' . $produit->nom)
@section('topbar-title'){{ __('messages.modifier') }} <span>{{ __('messages.produit') }}</span>@endsection
@section('topbar-actions')@endsection
@section('styles')
<style>
.breadcrumb{display:flex;align-items:center;gap:6px;margin-bottom:20px;font-size:13px;}
.breadcrumb a{color:#6b7280;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:5px;transition:color 0.2s;}
.breadcrumb a:hover{color:#2E3A6B;}
.breadcrumb a svg{width:13px;height:13px;}
.breadcrumb span{color:#d1d5db;}
.breadcrumb strong{color:#1f2937;font-weight:600;}
.form-card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:32px;max-width:720px;margin:0 auto;}
.form-title{font-size:18px;font-weight:700;color:#1f2937;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid #f3f4f6;}
.form-group{margin-bottom:20px;}
.form-label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
.form-label .required{color:#ef4444;}
.form-control{width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#1f2937;background:#f9fafb;transition:.2s;box-sizing:border-box;}
.form-control:focus{outline:none;border-color:#2E3A6B;box-shadow:0 0 0 3px rgba(46,58,107,.1);background:#fff;}
.form-control.is-invalid{border-color:#ef4444;}
.invalid-feedback{font-size:12px;color:#ef4444;margin-top:4px;}
select.form-control{appearance:none;cursor:pointer;}
textarea.form-control{resize:vertical;min-height:100px;}
.current-image{display:flex;align-items:center;gap:12px;margin-bottom:10px;padding:10px 14px;background:#EEF0F8;border-radius:10px;border:1px solid #c7cce6;}
.current-image img{width:50px;height:50px;border-radius:8px;object-fit:cover;}
.current-image span{font-size:13px;color:#2E3A6B;font-weight:500;}
.image-preview{width:100%;height:120px;border:2px dashed #d1d5db;border-radius:10px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;background:#f9fafb;color:#9ca3af;font-size:13px;gap:8px;transition:.2s;}
.image-preview:hover{border-color:#2E3A6B;color:#2E3A6B;}
.image-preview svg{width:28px;height:28px;}
.form-actions{display:flex;gap:12px;justify-content:flex-end;margin-top:28px;padding-top:20px;border-top:1px solid #f3f4f6;}
.btn-primary{padding:10px 24px;background:#F5A623;color:#171B3D;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;}
.btn-primary:hover{background:#e0961d;}
.btn-secondary{padding:10px 24px;background:#f3f4f6;color:#374151;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;}
.btn-secondary:hover{background:#e5e7eb;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;}
.alert-error ul{margin:6px 0 0 16px;}
.certif-section{border:1px solid #e5e7eb;border-radius:12px;padding:18px 20px;margin-top:6px;background:#fafbfc;}
.certif-section .form-label{margin-top:14px;}
.certif-section .form-label:first-child{margin-top:0;}
.certif-hint{font-size:12px;color:#6b7280;margin-bottom:14px;}
</style>
@endsection
@section('content')
<div class="breadcrumb">
    <a href="{{ route('fabricant.produits.index') }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        {{ __('messages.mes_produits') }}
    </a>
    <span>/</span>
    <a href="{{ route('fabricant.produits.show', $produit->id) }}">{{ $produit->nom }}</a>
    <span>/</span>
    <strong>{{ __('messages.modifier') }}</strong>
</div>
<div class="form-card">
    <div class="form-title">{{ __('messages.modifier') }} : {{ $produit->nom }}</div>
    @if($errors->any())
    <div class="alert-error"><strong>{{ __('messages.erreurs_validation') }}</strong><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('fabricant.produits.update', $produit->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- NOM --}}
        <div class="form-group">
            <label class="form-label">{{ __('messages.nom_produit') }} <span class="required">*</span></label>
            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $produit->nom) }}">
            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- CATEGORIE : restreinte aux deux secteurs couverts par VeriScan --}}
        <div class="form-group">
            <label class="form-label">{{ __('messages.categorie') }} <span class="required">*</span></label>
            <select id="categorieProduit" name="categorie" class="form-control @error('categorie') is-invalid @enderror">
                @foreach(['Pharmaceutique'=>__('Pharmaceutique'),'Cosmétique'=>__('Cosmétique')] as $val=>$label)
                    <option value="{{ $val }}" {{ old('categorie',$produit->categorie)==$val?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('categorie') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- CHAMPS DE CERTIFICATION — un bloc par secteur, pré-rempli si déjà enregistré --}}
        @php $certif = $produit->certification; @endphp
        <div class="form-group" id="blocsCertification">

            <div id="champsPharmaceutique" class="certif-section certif-fields" style="display:none">
                <p class="certif-hint">Informations réglementaires propres au secteur pharmaceutique.</p>
                <label class="form-label">Numéro d'AMM (Autorisation de Mise sur le Marché) <span class="required">*</span></label>
                <input type="text" name="numero_amm" class="form-control @error('numero_amm') is-invalid @enderror"
                       value="{{ old('numero_amm', $produit->categorie === 'Pharmaceutique' ? $certif?->numero_amm : null) }}">
                @error('numero_amm') <div class="invalid-feedback">{{ $message }}</div> @enderror

                <label class="form-label">Laboratoire fabricant</label>
                <input type="text" name="laboratoire_fabricant" class="form-control"
                       value="{{ old('laboratoire_fabricant', $produit->categorie === 'Pharmaceutique' ? $certif?->laboratoire_fabricant : null) }}">

                <label class="form-label">Date de l'AMM</label>
                <input type="date" name="date_amm" class="form-control"
                       value="{{ old('date_amm', $produit->categorie === 'Pharmaceutique' && $certif?->date_amm ? $certif->date_amm->format('Y-m-d') : null) }}">
            </div>

            <div id="champsCosmetique" class="certif-section certif-fields" style="display:none">
                <p class="certif-hint">Informations réglementaires propres au secteur cosmétique.</p>
                <label class="form-label">Liste INCI (ingrédients) <span class="required">*</span></label>
                <textarea name="liste_inci" class="form-control @error('liste_inci') is-invalid @enderror" placeholder="Ex : Aqua, Glycerin, Hydroquinone...">{{ old('liste_inci', $produit->categorie === 'Cosmétique' ? $certif?->liste_inci : null) }}</textarea>
                @error('liste_inci') <div class="invalid-feedback">{{ $message }}</div> @enderror

                <label class="form-label">Certificat de conformité (référence)</label>
                <input type="text" name="certificat_conformite" class="form-control"
                       value="{{ old('certificat_conformite', $produit->categorie === 'Cosmétique' ? $certif?->certificat_conformite : null) }}">

                <label class="form-label">Date de certification</label>
                <input type="date" name="date_certification" class="form-control"
                       value="{{ old('date_certification', $produit->categorie === 'Cosmétique' && $certif?->date_certification ? $certif->date_certification->format('Y-m-d') : null) }}">
            </div>

        </div>

        {{-- JUSTIFICATIF DE CERTIFICATION --}}
        <div class="form-group">
            <label class="form-label">Justificatif (AMM, certificat de conformité…)</label>
            <div class="certif-hint" style="margin-bottom:8px;">
                Statut actuel : <strong>{{ $produit->libelle_certification }}</strong>
                @if($produit->motif_decision && in_array($produit->statut_certification, ['rejete','revoque']))
                    — motif : {{ $produit->motif_decision }}
                @endif
            </div>
            <input type="file" name="justificatif" accept=".pdf,.jpg,.jpeg,.png"
                   class="form-control @error('justificatif') is-invalid @enderror">
            <div class="certif-hint" style="margin-top:6px;">
                Laisser vide pour conserver le justificatif actuel. Déposer un nouveau fichier renvoie le produit en validation.
            </div>
            @error('justificatif') <div class="invalid-feedback" style="display:block">{{ $message }}</div> @enderror
        </div>

        {{-- DESCRIPTION --}}
        <div class="form-group">
            <label class="form-label">{{ __('messages.description') }}</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $produit->description) }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- IMAGE --}}
        <div class="form-group">
            <label class="form-label">{{ __('messages.image_produit') }}</label>
            @if($produit->image)
            <div class="current-image">
                <img src="{{ asset('storage/'.$produit->image) }}" alt="{{ __('messages.image_actuelle') }}">
                <span>{{ __('messages.image_actuelle_hint') }}</span>
            </div>
            @endif
            <div class="image-preview" id="imagePreview" onclick="document.getElementById('imageInput').click()">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>{{ __('messages.cliquer_nouvelle_image') }}</span>
            </div>
            <input type="file" id="imageInput" name="image" accept="image/*" style="display:none" onchange="previewImage(this)">
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('fabricant.produits.show', $produit->id) }}" class="btn-secondary">{{ __('messages.annuler') }}</a>
            <button type="submit" class="btn-primary">{{ __('messages.enregistrer_modifications') }}</button>
        </div>
    </form>
</div>
<script>
function previewImage(input){
    const preview=document.getElementById('imagePreview');
    if(input.files&&input.files[0]){
        const reader=new FileReader();
        reader.onload=e=>{preview.innerHTML=`<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:8px">`;};
        reader.readAsDataURL(input.files[0]);
    }
}

function afficherChampsCertification(categorie) {
    document.querySelectorAll('.certif-fields').forEach(el => el.style.display = 'none');
    const map = { 'Pharmaceutique': 'champsPharmaceutique', 'Cosmétique': 'champsCosmetique' };
    const id = map[categorie];
    if (id) document.getElementById(id).style.display = 'block';
}

document.getElementById('categorieProduit').addEventListener('change', function() {
    afficherChampsCertification(this.value);
});

// Affiche le bon bloc dès le chargement de la page (catégorie actuelle du produit)
afficherChampsCertification(document.getElementById('categorieProduit').value);
</script>
@endsection