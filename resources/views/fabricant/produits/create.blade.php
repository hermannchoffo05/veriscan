@extends('layouts.fabricant')
@section('title', __('messages.nouveau_produit'))
@section('topbar-title'){{ __('messages.nouveau_produit') }}@endsection
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
.form-label span.required{color:#ef4444;}
.form-control{width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#1f2937;background:#f9fafb;transition:border-color .2s,box-shadow .2s;box-sizing:border-box;}
.form-control:focus{outline:none;border-color:#2E3A6B;box-shadow:0 0 0 3px rgba(46,58,107,.1);background:#fff;}
.form-control.is-invalid{border-color:#ef4444;}
.invalid-feedback{font-size:12px;color:#ef4444;margin-top:4px;}
select.form-control{appearance:none;cursor:pointer;}
textarea.form-control{resize:vertical;min-height:100px;}
.image-preview{width:100%;height:160px;border:2px dashed #d1d5db;border-radius:10px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:border-color .2s;background:#f9fafb;color:#9ca3af;font-size:13px;gap:8px;}
.image-preview:hover{border-color:#2E3A6B;color:#2E3A6B;}
.image-preview svg{width:32px;height:32px;}
.form-actions{display:flex;gap:12px;justify-content:flex-end;margin-top:28px;padding-top:20px;border-top:1px solid #f3f4f6;}
.btn-primary{padding:10px 24px;background:#F5A623;color:#171B3D;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;}
.btn-primary:hover{background:#e0961d;}
.btn-secondary{padding:10px 24px;background:#f3f4f6;color:#374151;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;}
.btn-secondary:hover{background:#e5e7eb;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;}
.alert-error ul{margin:6px 0 0 16px;}
.ai-badge{display:none;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#2E3A6B;background:#EEF0F8;border:1px solid #c7cce6;border-radius:6px;padding:4px 10px;margin-top:6px;width:fit-content;}
.ai-badge svg{width:13px;height:13px;}
.ai-badge.visible{display:flex;}
.ai-badge-desc{margin-top:0;}
#aiGeneratedBadge.visible{display:inline-flex;margin-left:8px;}
.ai-loading{display:none;align-items:center;gap:8px;font-size:12px;color:#6b7280;margin-top:6px;}
.ai-loading.visible{display:flex;}
.ai-loading .spinner{width:14px;height:14px;border:2px solid #e5e7eb;border-top-color:#2E3A6B;border-radius:50%;animation:spin .7s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}
.desc-wrapper{position:relative;}
.desc-regen-btn{position:absolute;bottom:10px;right:10px;background:#2E3A6B;color:#fff;border:none;border-radius:7px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;display:none;align-items:center;gap:5px;transition:background .2s;}
.desc-regen-btn:hover{background:#212a52;}
.desc-regen-btn.visible{display:flex;}
.desc-regen-btn svg{width:12px;height:12px;}
.ai-error{display:none;font-size:12px;color:#dc2626;background:#fef2f2;border:1px solid #fecaca;border-radius:7px;padding:8px 12px;margin-top:6px;}
.ai-error.visible{display:block;}
.ai-quota{display:none;font-size:12px;color:#92400e;background:#fffbeb;border:1px solid #fde68a;border-radius:7px;padding:8px 12px;margin-top:6px;}
.ai-quota.visible{display:block;}
.ai-warning{display:none;align-items:center;justify-content:space-between;gap:10px;font-size:12px;color:#92400e;background:#fffbeb;border:1px solid #fde68a;border-radius:7px;padding:8px 12px;margin-top:6px;}
.ai-warning.visible{display:flex;}
.ai-warning button{background:#2E3A6B;color:#fff;border:none;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:600;cursor:pointer;white-space:nowrap;}
.ai-warning button:hover{background:#212a52;}
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
    <strong>{{ __('messages.nouveau_produit') }}</strong>
</div>
<div class="form-card">
    <div class="form-title">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20" style="vertical-align:middle;margin-right:8px;color:#2E3A6B"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
        {{ __('messages.infos_produit') }}
    </div>
    @if($errors->any())
    <div class="alert-error"><strong>{{ __('messages.erreurs_validation') }}</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('fabricant.produits.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- NOM --}}
        <div class="form-group">
            <label class="form-label">{{ __('messages.nom_produit') }} <span class="required">*</span></label>
            <input type="text" id="nomProduit" name="nom"
                   class="form-control @error('nom') is-invalid @enderror"
                   value="{{ old('nom') }}"
                   placeholder="{{ __('messages.placeholder_nom_produit') }}"
                   autocomplete="off">
            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- CATEGORIE : restreinte aux deux secteurs couverts par VeriScan --}}
        <div class="form-group">
            <label class="form-label">
                {{ __('messages.categorie') }} <span class="required">*</span>
            </label>
            <select id="categorieProduit" name="categorie" class="form-control @error('categorie') is-invalid @enderror">
                <option value="">-- {{ __('messages.selectionner_categorie') }} --</option>
                @foreach(['Pharmaceutique'=>__('Pharmaceutique'),'Cosmétique'=>__('Cosmétique')] as $val=>$label)
                    <option value="{{ $val }}" {{ old('categorie')==$val?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="ai-loading" id="catLoading">
                <div class="spinner"></div>
                <span>Détection automatique de la catégorie...</span>
            </div>
            <span id="catAiBadge" class="ai-badge">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347a3.76 3.76 0 01-1.05 2.59A4.016 4.016 0 0112 21a4.016 4.016 0 01-2.841-1.163 3.76 3.76 0 01-1.05-2.59l-.347-.347z"/></svg>
                Catégorie suggérée par IA — modifiable
            </span>
            <div id="catError" class="ai-error"></div>
            <div id="catQuota" class="ai-quota"></div>
            <div id="catCoherenceWarning" class="ai-warning"></div>
            <div id="catHorsPerimetre" class="ai-warning"></div>
            @error('categorie') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- CHAMPS DE CERTIFICATION — un bloc par secteur, un seul visible à la fois --}}
        <div class="form-group" id="blocsCertification">

            <div id="champsPharmaceutique" class="certif-section certif-fields" style="display:none">
                <p class="certif-hint">Informations réglementaires propres au secteur pharmaceutique.</p>
                <label class="form-label">Numéro d'AMM (Autorisation de Mise sur le Marché) <span class="required">*</span></label>
                <input type="text" name="numero_amm" class="form-control @error('numero_amm') is-invalid @enderror" value="{{ old('numero_amm') }}">
                @error('numero_amm') <div class="invalid-feedback">{{ $message }}</div> @enderror

                <label class="form-label">Laboratoire fabricant</label>
                <input type="text" name="laboratoire_fabricant" class="form-control" value="{{ old('laboratoire_fabricant') }}">

                <label class="form-label">Date de l'AMM</label>
                <input type="date" name="date_amm" class="form-control" value="{{ old('date_amm') }}">
            </div>

            <div id="champsCosmetique" class="certif-section certif-fields" style="display:none">
                <p class="certif-hint">Informations réglementaires propres au secteur cosmétique.</p>
                <label class="form-label">Liste INCI (ingrédients) <span class="required">*</span></label>
                <textarea name="liste_inci" class="form-control @error('liste_inci') is-invalid @enderror" placeholder="Ex : Aqua, Glycerin, Hydroquinone...">{{ old('liste_inci') }}</textarea>
                @error('liste_inci') <div class="invalid-feedback">{{ $message }}</div> @enderror

                <label class="form-label">Certificat de conformité (référence)</label>
                <input type="text" name="certificat_conformite" class="form-control" value="{{ old('certificat_conformite') }}">

                <label class="form-label">Date de certification</label>
                <input type="date" name="date_certification" class="form-control" value="{{ old('date_certification') }}">
            </div>

        </div>

        {{-- JUSTIFICATIF DE CERTIFICATION --}}
        <div class="form-group">
            <label class="form-label">Justificatif (AMM, certificat de conformité…) <span class="required">*</span></label>
            <input type="file" name="justificatif" accept=".pdf,.jpg,.jpeg,.png"
                   class="form-control @error('justificatif') is-invalid @enderror">
            <div class="certif-hint" style="margin-top:6px;">
                PDF, JPG ou PNG, 5&nbsp;Mo max. Ce document sera examiné par l'autorité de certification
                avant que vous puissiez générer des QR codes pour ce produit.
            </div>
            @error('justificatif') <div class="invalid-feedback" style="display:block">{{ $message }}</div> @enderror
        </div>

        {{-- DESCRIPTION --}}
        <div class="form-group">
            <label class="form-label">
                {{ __('messages.description') }}
                <span id="aiGeneratedBadge" class="ai-badge ai-badge-desc">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347a3.76 3.76 0 01-1.05 2.59A4.016 4.016 0 0112 21a4.016 4.016 0 01-2.841-1.163 3.76 3.76 0 01-1.05-2.59l-.347-.347z"/></svg>
                    Générée par IA — modifiable
                </span>
            </label>
            <div class="desc-wrapper">
                <textarea id="descriptionProduit" name="description"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="{{ __('messages.placeholder_description') }}">{{ old('description') }}</textarea>
                <button type="button" class="desc-regen-btn" id="regenBtn" onclick="genererDescription()">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Regénérer
                </button>
            </div>
            <div class="ai-loading" id="descLoading">
                <div class="spinner"></div>
                <span>Génération de la description en cours...</span>
            </div>
            <div id="descError" class="ai-error"></div>
            <div id="descQuota" class="ai-quota"></div>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- IMAGE --}}
        <div class="form-group">
            <label class="form-label">{{ __('messages.image_produit') }}</label>
            <div class="image-preview" id="imagePreview" onclick="document.getElementById('imageInput').click()">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>{{ __('messages.cliquer_image') }}</span>
            </div>
            <input type="file" id="imageInput" name="image" accept="image/*" style="display:none" onchange="previewImage(this)">
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('fabricant.produits.index') }}" class="btn-secondary">{{ __('messages.annuler') }}</a>
            <button type="submit" class="btn-primary">{{ __('messages.creer_produit') }}</button>
        </div>
    </form>
</div>

<script>
const CSRF_TOKEN     = '{{ csrf_token() }}';
const ROUTE_CLASSIFY = '{{ route("fabricant.produits.classify-category") }}';
const ROUTE_DESC     = '{{ route("fabricant.produits.generate-description") }}';

// La carte de normalisation ne couvre plus que les deux secteurs
// autorisés. Toute autre formulation (auto, électronique, textile,
// alimentaire...) n'a volontairement plus de correspondance : elle
// retombe sur "Hors périmètre".
const CAT_MAP = {
    'medicament': 'Pharmaceutique', 'médicaments': 'Pharmaceutique', 'medicaments': 'Pharmaceutique', 'pharmaceut': 'Pharmaceutique',
    'cosmet': 'Cosmétique', 'cosmét': 'Cosmétique', 'beaut': 'Cosmétique', 'hygiene': 'Cosmétique', 'hygiène': 'Cosmétique',
};

function normalizeCategory(raw) {
    if (!raw) return null;
    const lower = raw.toLowerCase().trim();
    if (lower.includes('hors') || lower.includes('perimetre') || lower.includes('périmètre')) return 'HORS_PERIMETRE';
    for (const [key, val] of Object.entries(CAT_MAP)) {
        if (lower.includes(key)) return val;
    }
    return null;
}

function afficherChampsCertification(categorie) {
    document.querySelectorAll('.certif-fields').forEach(el => el.style.display = 'none');
    const map = { 'Pharmaceutique': 'champsPharmaceutique', 'Cosmétique': 'champsCosmetique' };
    const id = map[categorie];
    if (id) document.getElementById(id).style.display = 'block';
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" alt="apercu" style="width:100%;height:100%;object-fit:cover;border-radius:8px">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function afficherMessage(container, message) {
    container.textContent = message;
    container.classList.add('visible');
}

function masquerMessage(container) {
    container.classList.remove('visible');
    container.textContent = '';
}

async function classifierCategorie(nom) {
    const catSelect     = document.getElementById('categorieProduit');
    const catLoading    = document.getElementById('catLoading');
    const catBadge      = document.getElementById('catAiBadge');
    const catError      = document.getElementById('catError');
    const catQuota      = document.getElementById('catQuota');
    const catHorsPerim  = document.getElementById('catHorsPerimetre');

    catLoading.classList.add('visible');
    catBadge.classList.remove('visible');
    masquerMessage(catError);
    masquerMessage(catQuota);
    masquerMessage(catHorsPerim);

    try {
        const res  = await fetch(ROUTE_CLASSIFY, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ nom })
        });

        if (res.status === 429) {
            const data = await res.json();
            afficherMessage(catQuota, data.message || "Quota IA mensuel atteint.");
            return;
        }
        if (!res.ok) {
            afficherMessage(catError, "Une erreur est survenue, réessayez.");
            return;
        }

        const data = await res.json();

        if (data.category) {
            const normalized = normalizeCategory(data.category);
            if (normalized === 'HORS_PERIMETRE') {
                afficherMessage(catHorsPerim, "⚠️ Ce produit ne semble appartenir à aucun des deux secteurs couverts par VeriScan (Pharmaceutique, Cosmétique). Sélectionnez la catégorie la plus proche ou vérifiez le nom saisi.");
            } else if (normalized) {
                catSelect.value = normalized;
                afficherChampsCertification(normalized);
                catBadge.classList.add('visible');
                await genererDescription();
            }
        }
    } catch(e) {
        console.error('Erreur classification:', e);
        afficherMessage(catError, "Une erreur est survenue, réessayez.");
    } finally {
        catLoading.classList.remove('visible');
    }
}

async function genererDescription() {
    const nom       = document.getElementById('nomProduit').value.trim();
    const categorie = document.getElementById('categorieProduit').value;
    const descTA    = document.getElementById('descriptionProduit');
    const loading   = document.getElementById('descLoading');
    const badge     = document.getElementById('aiGeneratedBadge');
    const regenBtn  = document.getElementById('regenBtn');
    const descError = document.getElementById('descError');
    const descQuota = document.getElementById('descQuota');

    if (!nom || !categorie) return;

    loading.classList.add('visible');
    badge.classList.remove('visible');
    regenBtn.classList.remove('visible');
    masquerMessage(descError);
    masquerMessage(descQuota);
    descTA.disabled = true;

    try {
        const res  = await fetch(ROUTE_DESC, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ nom, categorie })
        });

        if (res.status === 429) {
            const data = await res.json();
            afficherMessage(descQuota, data.message || "Quota IA mensuel atteint.");
            return;
        }
        if (!res.ok) {
            afficherMessage(descError, "Une erreur est survenue lors de la génération, réessayez.");
            return;
        }

        const data = await res.json();

        if (data.description) {
            descTA.value = data.description;
            badge.classList.add('visible');
            regenBtn.classList.add('visible');
        }
    } catch(e) {
        console.error('Erreur description:', e);
        afficherMessage(descError, "Une erreur est survenue lors de la génération, réessayez.");
    } finally {
        loading.classList.remove('visible');
        descTA.disabled = false;
    }
}

async function verifierCoherenceCategorie() {
    const nom          = document.getElementById('nomProduit').value.trim();
    const categorieSel = document.getElementById('categorieProduit').value;
    const warningBox   = document.getElementById('catCoherenceWarning');

    if (!nom || !categorieSel) { warningBox.classList.remove('visible'); return; }

    try {
        const res = await fetch(ROUTE_CLASSIFY, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ nom })
        });

        if (!res.ok) { warningBox.classList.remove('visible'); return; }

        const data = await res.json();
        const suggestion = normalizeCategory(data.category);

        if (suggestion === 'HORS_PERIMETRE') {
            warningBox.innerHTML = `⚠️ "${nom}" ne semble appartenir à aucun des deux secteurs couverts par VeriScan.`;
            warningBox.classList.add('visible');
        } else if (suggestion && suggestion !== categorieSel) {
            warningBox.innerHTML = `⚠️ "${nom}" correspond généralement à la catégorie <strong>${suggestion}</strong>, pas ${categorieSel}. <button type="button" onclick="corrigerCategorie('${suggestion}')">Corriger</button>`;
            warningBox.classList.add('visible');
        } else {
            warningBox.classList.remove('visible');
        }
    } catch(e) {
        console.error('Erreur vérification cohérence:', e);
    }
}

function corrigerCategorie(categorie) {
    document.getElementById('categorieProduit').value = categorie;
    afficherChampsCertification(categorie);
    document.getElementById('catCoherenceWarning').classList.remove('visible');
    genererDescription();
}

document.getElementById('nomProduit').addEventListener('blur', function() {
    const nom       = this.value.trim();
    const categorie = document.getElementById('categorieProduit').value;
    if (!nom) return;
    if (categorie) {
        genererDescription();
        verifierCoherenceCategorie();
    } else {
        classifierCategorie(nom);
    }
});

document.getElementById('categorieProduit').addEventListener('change', function() {
    afficherChampsCertification(this.value);
    const nom = document.getElementById('nomProduit').value.trim();
    if (nom && this.value) {
        genererDescription();
        verifierCoherenceCategorie();
    }
});

// Cas d'un rechargement de page après erreur de validation (old('categorie'))
afficherChampsCertification(document.getElementById('categorieProduit').value);
</script>
@endsection