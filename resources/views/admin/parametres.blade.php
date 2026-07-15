@extends('layouts.admin')

@section('title', 'Paramètres')

@section('topbar-title')
    <span>Paramètres</span> du compte
@endsection

@section('styles')
<style>
    .params-layout { display: grid; grid-template-columns: 240px 1fr; gap: 20px; align-items: start; }

    .nav-card { background: var(--white); border-radius: 16px; border: 1.5px solid var(--border); overflow: hidden; position: sticky; top: 80px; }
    .nav-card-title { padding: 14px 18px; border-bottom: 1px solid var(--border); font-size: 12px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.06em; }
    .nav-link { display: flex; align-items: center; gap: 9px; padding: 11px 18px; font-size: 13px; font-weight: 500; color: var(--text-light); text-decoration: none; border-bottom: 1px solid var(--border); transition: all 0.2s; }
    .nav-link:last-child { border-bottom: none; }
    .nav-link svg { width: 15px; height: 15px; flex-shrink: 0; }
    .nav-link:hover, .nav-link.active { background: var(--teal-light); color: var(--teal); }

    .content-col { display: flex; flex-direction: column; gap: 20px; }
    .section-card { background: var(--white); border-radius: 16px; border: 1.5px solid var(--border); overflow: hidden; }
    .section-header { padding: 16px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; }
    .section-title { font-size: 14px; font-weight: 800; color: var(--text); }
    .section-header svg { width: 15px; height: 15px; color: var(--teal); }
    .section-body { padding: 22px; }

    .photo-section { display: flex; align-items: center; gap: 24px; padding: 20px 22px; background: var(--bg); border-radius: 12px; margin-bottom: 20px; }
    .photo-wrap { position: relative; flex-shrink: 0; }
    .photo-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 4px 16px rgba(0,0,0,0.12); }
    .photo-initials { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #2E3A6B, #4A5899); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 800; color: white; border: 3px solid white; box-shadow: 0 4px 16px rgba(0,0,0,0.12); }
    .photo-overlay { position: absolute; inset: 0; border-radius: 50%; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s; cursor: pointer; }
    .photo-wrap:hover .photo-overlay { opacity: 1; }
    .photo-overlay svg { width: 20px; height: 20px; color: white; }
    .photo-info strong { display: block; font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 3px; }
    .photo-info span { font-size: 12px; color: var(--text-light); }
    .photo-info .role-badge { display: inline-block; font-size: 10px; font-weight: 700; color: #8B93D1; background: rgba(139,147,209,0.1); border: 1px solid rgba(139,147,209,0.2); border-radius: 4px; padding: 2px 7px; letter-spacing: 0.05em; margin-top: 5px; }

    .upload-zone { border: 2px dashed var(--border); border-radius: 12px; padding: 24px; text-align: center; cursor: pointer; transition: all 0.2s; position: relative; }
    .upload-zone:hover, .upload-zone.dragover { border-color: var(--teal); background: var(--teal-light); }
    .upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
    .upload-zone svg { width: 32px; height: 32px; color: var(--teal); margin: 0 auto 8px; display: block; }
    .upload-zone p { font-size: 13px; font-weight: 600; color: var(--text); }
    .upload-zone span { font-size: 11.5px; color: var(--text-light); }
    #preview-wrap { display: none; margin-top: 14px; }
    #preview-img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--teal); }

    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 12px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
    .form-control { width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--white); font-size: 13.5px; font-family: inherit; color: var(--text); outline: none; transition: border 0.2s; }
    .form-control:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(46,58,107,0.08); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    .btn-save { display: inline-flex; align-items: center; gap: 7px; background: var(--teal); color: white; border: none; border-radius: 10px; padding: 10px 22px; font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s; }
    .btn-save:hover { background: var(--teal-dark); transform: translateY(-1px); }
    .btn-save svg { width: 15px; height: 15px; }

    .danger-zone { border: 1.5px solid #fecaca; border-radius: 16px; overflow: hidden; }
    .danger-header { padding: 14px 22px; background: #fef2f2; border-bottom: 1px solid #fecaca; font-size: 13px; font-weight: 800; color: #CE1126; display: flex; align-items: center; gap: 7px; }
    .danger-header svg { width: 15px; height: 15px; }
    .danger-body { padding: 18px 22px; font-size: 13px; color: var(--text-light); background: var(--white); }

    /* Système IA */
    .param-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; border-bottom: 1px solid #f3f4f6; gap: 20px; }
    .param-row:last-child { border-bottom: none; }
    .param-info { flex: 1; }
    .param-label { font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 3px; }
    .param-desc { font-size: 11.5px; color: var(--text-light); }
    .param-input { width: 100px; padding: 8px 12px; border-radius: 8px; border: 1.5px solid var(--border); font-size: 13px; font-family: inherit; color: var(--text); outline: none; text-align: center; font-weight: 700; transition: border 0.2s; }
    .param-input:focus { border-color: var(--teal); }
    .param-toggle { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
    .param-toggle input { opacity: 0; width: 0; height: 0; }
    .param-toggle-slider { position: absolute; inset: 0; background: #d1d5db; border-radius: 24px; cursor: pointer; transition: 0.3s; }
    .param-toggle-slider::before { content: ''; position: absolute; width: 18px; height: 18px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.3s; }
    .param-toggle input:checked + .param-toggle-slider { background: var(--teal); }
    .param-toggle input:checked + .param-toggle-slider::before { transform: translateX(20px); }
    .seuil-bar { display: flex; gap: 8px; margin-top: 12px; }
    .seuil-segment { height: 8px; border-radius: 4px; flex: 1; }

    /* Catégories */
    .categories-wrap { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; min-height: 40px; padding: 8px; background: var(--bg); border-radius: 10px; border: 1.5px solid var(--border); }
    .category-tag { display: inline-flex; align-items: center; gap: 5px; background: white; color: var(--teal); border: 1.5px solid rgba(46,58,107,0.25); border-radius: 20px; padding: 5px 12px; font-size: 12px; font-weight: 600; }
    .cat-remove { background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 16px; line-height: 1; padding: 0 0 0 2px; font-weight: 700; }
    .cat-remove:hover { color: #CE1126; }
    .add-category { display: flex; gap: 8px; margin-top: 10px; }
    .cat-input { flex: 1; padding: 9px 14px; border-radius: 10px; border: 1.5px solid var(--border); font-size: 13px; font-family: inherit; outline: none; transition: border 0.2s; background: white; }
    .cat-input:focus { border-color: var(--teal); }
    .btn-add-cat { background: var(--teal); color: white; border: none; border-radius: 10px; padding: 9px 18px; font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; white-space: nowrap; transition: all 0.2s; }
    .btn-add-cat:hover { background: var(--teal-dark); }
    .ia-badge { display: inline-flex; align-items: center; gap: 5px; background: rgba(46,58,107,0.08); color: var(--teal); border-radius: 6px; padding: 3px 8px; font-size: 11px; font-weight: 700; margin-left: 8px; }
</style>
@endsection

@section('content')

@php
$categoriesJson = $params['categories_produits'] ?? '["Médicament","Alimentaire","Cosmétique","Automobile","Hygiène","Autre"]';
$categoriesList = json_decode($categoriesJson, true) ?? [];
@endphp

<div class="params-layout">

    {{-- Nav latérale --}}
    <div class="nav-card">
        <div class="nav-card-title">Navigation</div>
        <a href="#photo" class="nav-link active">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Photo de profil
        </a>
        <a href="#profil" class="nav-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profil admin
        </a>
        <a href="#securite" class="nav-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Sécurité
        </a>
        <a href="#systeme" class="nav-link">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Système IA
        </a>
       
    </div>

    {{-- Contenu --}}
    <div class="content-col">

        {{-- Photo de profil --}}
        <div class="section-card" id="photo">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="section-title">Photo de profil</span>
            </div>
            <div class="section-body">
                <div class="photo-section">
                    <div class="photo-wrap">
                        @if($admin->photo)
                            <img src="{{ asset('storage/' . $admin->photo) }}" class="photo-avatar" alt="Photo admin" id="current-photo">
                        @else
                            <div class="photo-initials" id="current-photo">
                                {{ strtoupper(substr($admin->nom ?? 'A', 0, 1)) }}
                            </div>
                        @endif
                        <label for="photoInput" class="photo-overlay">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </label>
                    </div>
                    <div class="photo-info">
                        <strong>{{ $admin->nom }}</strong>
                        <span>{{ $admin->email }}</span>
                        <div class="role-badge">{{ strtoupper($admin->role ?? 'SUPER_ADMIN') }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.parametres.photo') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="upload-zone" id="uploadZone">
                        <input type="file" name="photo" id="photoInput" accept="image/png,image/jpg,image/jpeg,image/webp">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p>Cliquez ou glissez une photo ici</p>
                        <span>PNG, JPG, WEBP · Max 2 Mo</span>
                        <div id="preview-wrap">
                            <img id="preview-img" src="" alt="Aperçu">
                            <p style="margin-top:8px;font-size:12px;color:var(--teal);font-weight:600;">Photo sélectionnée ✓</p>
                        </div>
                    </div>
                    <div style="margin-top:14px;">
                        <button type="submit" class="btn-save">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Mettre à jour la photo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Profil --}}
        <div class="section-card" id="profil">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="section-title">Informations du profil</span>
            </div>
            <div class="section-body">
                <form method="POST" action="{{ route('admin.parametres.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom complet</label>
                            <input type="text" name="nom" class="form-control" value="{{ old('nom', $admin->nom) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Adresse email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-save">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer les modifications
                    </button>
                </form>
            </div>
        </div>

        {{-- Sécurité --}}
        <div class="section-card" id="securite">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span class="section-title">Changer le mot de passe</span>
            </div>
            <div class="section-body">
                <form method="POST" action="{{ route('admin.parametres.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="nom" value="{{ $admin->nom }}">
                    <input type="hidden" name="email" value="{{ $admin->email }}">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nouveau mot de passe</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" autocomplete="new-password">
                        </div>
                        <div class="form-group">
                            <label>Confirmer</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                        </div>
                    </div>
                    <button type="submit" class="btn-save">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Mettre à jour le mot de passe
                    </button>
                </form>
            </div>
        </div>

        {{-- Système IA --}}
        <div class="section-card" id="systeme">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="section-title">Paramètres Système</span>
                <span class="ia-badge">IA</span>
            </div>
            <div class="section-body">
                <form method="POST" action="{{ route('admin.parametres.systeme') }}">
                    @csrf

                    {{-- Seuils --}}
                    <div style="margin-bottom:24px;">
                        <div style="font-size:13px;font-weight:800;color:var(--text);margin-bottom:4px;">Seuils d'alerte IA</div>
                        <div style="font-size:12px;color:var(--text-light);margin-bottom:14px;">Ces seuils déterminent à quel score un produit passe au niveau supérieur sur la carte des risques.</div>
                        <div class="seuil-bar">
                            <div class="seuil-segment" style="background:#10b981;"></div>
                            <div class="seuil-segment" style="background:#f59e0b;"></div>
                            <div class="seuil-segment" style="background:#ef4444;"></div>
                            <div class="seuil-segment" style="background:#7f1d1d;"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;margin-top:6px;margin-bottom:16px;">
                            <span style="font-size:10px;font-weight:700;color:#10b981;">Faible (0)</span>
                            <span style="font-size:10px;font-weight:700;color:#f59e0b;">Modéré ({{ $params['seuil_alerte_modere'] ?? 30 }})</span>
                            <span style="font-size:10px;font-weight:700;color:#ef4444;">Élevé ({{ $params['seuil_alerte_eleve'] ?? 50 }})</span>
                            <span style="font-size:10px;font-weight:700;color:#7f1d1d;">Critique ({{ $params['seuil_alerte_critique'] ?? 70 }})</span>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Seuil Modéré</label>
                                <input type="number" name="seuil_alerte_modere" class="form-control" value="{{ $params['seuil_alerte_modere'] ?? 30 }}" min="1" max="99">
                            </div>
                            <div class="form-group">
                                <label>Seuil Élevé</label>
                                <input type="number" name="seuil_alerte_eleve" class="form-control" value="{{ $params['seuil_alerte_eleve'] ?? 50 }}" min="1" max="99">
                            </div>
                            <div class="form-group">
                                <label>Seuil Critique</label>
                                <input type="number" name="seuil_alerte_critique" class="form-control" value="{{ $params['seuil_alerte_critique'] ?? 70 }}" min="1" max="99">
                            </div>
                            <div class="form-group">
                                <label>Intervalle scoring (h)</label>
                                <input type="number" name="intervalle_scoring" class="form-control" value="{{ $params['intervalle_scoring'] ?? 6 }}" min="1" max="24">
                            </div>
                        </div>
                    </div>

                    {{-- Toggle alertes --}}
                    <div class="param-row">
                        <div class="param-info">
                            <div class="param-label">Alertes automatiques</div>
                            <div class="param-desc">Activer les alertes quand un produit dépasse les seuils définis</div>
                        </div>
                        <label class="param-toggle">
                            <input type="checkbox" name="alertes_actives" value="1" {{ ($params['alertes_actives'] ?? '1') == '1' ? 'checked' : '' }}>
                            <span class="param-toggle-slider"></span>
                        </label>
                    </div>

                    {{-- Scans anormaux --}}
                    <div class="param-row">
                        <div class="param-info">
                            <div class="param-label">Scans anormaux — seuil d'alerte</div>
                            <div class="param-desc">Nombre de scans d'un même QR code déclenchant une alerte de copie suspecte</div>
                        </div>
                        <input type="number" name="nb_scans_max_avant_alerte" class="param-input" value="{{ $params['nb_scans_max_avant_alerte'] ?? 100 }}" min="10" max="9999">
                    </div>

                    {{-- Catégories --}}
                    <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6;">
                        <div style="font-size:13px;font-weight:800;color:var(--text);margin-bottom:4px;">Catégories de produits</div>
                        <div style="font-size:12px;color:var(--text-light);margin-bottom:10px;">Ces catégories sont disponibles lors de la création d'un produit par un fabricant.</div>

                        <div class="categories-wrap" id="categoriesWrap">
                            @foreach($categoriesList as $cat)
                                <div class="category-tag" data-cat="{{ $cat }}">
                                    {{ $cat }}
                                    <button type="button" class="cat-remove">×</button>
                                </div>
                            @endforeach
                        </div>

                        <div class="add-category">
                            <input type="text" id="catInput" class="cat-input" placeholder="Ex: Électronique...">
                            <button type="button" class="btn-add-cat" id="btnAjouter">+ Ajouter</button>
                        </div>

                        <input type="hidden" name="categories_produits" id="catsHidden">
                    </div>

                    <div style="margin-top:22px;">
                        <button type="submit" class="btn-save">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Enregistrer les paramètres système
                        </button>
                    </div>
                </form>
            </div>
        </div>


    </div>
</div>

@endsection

@section('scripts')
<script>
(function() {
    // ── Catégories ────────────────────────────────────────────────────────

    var wrap   = document.getElementById('categoriesWrap');
    var hidden = document.getElementById('catsHidden');

    function syncHidden() {
        var tags = wrap.querySelectorAll('.category-tag');
        var list = [];
        for (var i = 0; i < tags.length; i++) {
            list.push(tags[i].getAttribute('data-cat'));
        }
        hidden.value = JSON.stringify(list);
    }

    function bindRemove(tag) {
        var btn = tag.querySelector('.cat-remove');
        btn.addEventListener('click', function() {
            tag.parentNode.removeChild(tag);
            syncHidden();
        });
    }

    // Lier les boutons × existants
    var existing = wrap.querySelectorAll('.category-tag');
    for (var i = 0; i < existing.length; i++) {
        bindRemove(existing[i]);
    }

    // Init champ hidden
    syncHidden();

    // Bouton Ajouter
    document.getElementById('btnAjouter').addEventListener('click', function() {
        var input = document.getElementById('catInput');
        var val   = input.value.trim();
        if (val === '') return;

        // Vérifier doublon
        var tags = wrap.querySelectorAll('.category-tag');
        for (var i = 0; i < tags.length; i++) {
            if (tags[i].getAttribute('data-cat') === val) {
                input.value = '';
                return;
            }
        }

        // Créer tag
        var div = document.createElement('div');
        div.className = 'category-tag';
        div.setAttribute('data-cat', val);
        div.innerHTML = val + ' <button type="button" class="cat-remove">×</button>';
        bindRemove(div);
        wrap.appendChild(div);
        input.value = '';
        syncHidden();
    });

    // Touche Entrée
    document.getElementById('catInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnAjouter').click();
        }
    });

    // ── Photo ─────────────────────────────────────────────────────────────

    var photoInput = document.getElementById('photoInput');
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('preview-wrap').style.display = 'block';
                };
                reader.readAsDataURL(photoInput.files[0]);
            }
        });
    }

    var zone = document.getElementById('uploadZone');
    if (zone) {
        zone.addEventListener('dragover', function(e) { e.preventDefault(); zone.classList.add('dragover'); });
        zone.addEventListener('dragleave', function() { zone.classList.remove('dragover'); });
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            zone.classList.remove('dragover');
            if (e.dataTransfer.files[0] && photoInput) {
                photoInput.files = e.dataTransfer.files;
                photoInput.dispatchEvent(new Event('change'));
            }
        });
    }

})();
</script>
@endsection