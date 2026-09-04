@extends('layouts.fabricant')

@section('title', __('messages.carte_risques'))

@section('topbar-title')
    {{ __('messages.carte_des') }} <span>{{ __('Cartes des risques') }}</span>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    .carte-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 20px;
        height: calc(100vh - 140px);
    }
    .carte-card {
        background: var(--white);
        border-radius: 16px;
        border: 1.5px solid var(--border);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .carte-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .carte-header-left { display: flex; align-items: center; gap: 10px; }
    .carte-header-left h2 { font-size: 14px; font-weight: 800; color: var(--text); }
    .carte-header-left p { font-size: 12px; color: var(--text-light); margin-top: 1px; }
    .carte-icon { width: 36px; height: 36px; border-radius: 10px; background: var(--teal-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .carte-icon svg { width: 18px; height: 18px; color: var(--teal); }

    .header-search-wrap { position: relative; }
    .header-search-box {
        display: flex; align-items: center; gap: 7px;
        background: var(--bg); border: 1.5px solid var(--border);
        border-radius: 10px; padding: 7px 12px;
        width: 260px;
        transition: border-color 0.2s;
    }
    .header-search-box:has(input:focus) { border-color: var(--teal); }
    .header-search-box svg { width: 14px; height: 14px; color: #9ca3af; flex-shrink: 0; }
    .header-search-box input {
        border: none; outline: none; background: transparent;
        font-size: 12.5px; color: var(--text); width: 100%; font-family: inherit;
    }
    .header-search-dropdown {
        display: none;
        position: absolute; top: calc(100% + 6px); right: 0;
        background: white; width: 300px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        max-height: 300px; overflow-y: auto;
        z-index: 1100;
    }
    #map { flex: 1; min-height: 350px; }
    .filtres-card { background: var(--white); border-radius: 16px; border: 1.5px solid var(--border); display: flex; flex-direction: column; overflow: hidden; }
    .filtres-header { padding: 16px 18px; border-bottom: 1px solid var(--border); font-size: 13px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 8px; }
    .filtres-header svg { width: 15px; height: 15px; color: var(--teal); }
    .filtres-body { padding: 16px 18px; display: flex; flex-direction: column; gap: 16px; flex: 1; overflow-y: auto; }
    .filtre-group label { display: block; font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
    .filtre-select { width: 100%; padding: 8px 12px; border: 1.5px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 13px; color: var(--text); background: var(--bg); outline: none; cursor: pointer; transition: border-color 0.2s; }
    .filtre-select:focus { border-color: var(--teal); }
    .legende-section { padding: 16px 18px; border-top: 1px solid var(--border); }
    .legende-title { font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px; }
    .legende-item { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 12px; color: var(--text); font-weight: 500; }
    .legende-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; border: 2px solid rgba(0,0,0,0.15); }
    .legende-dot.rouge  { background: #CE1126; }
    .legende-dot.orange { background: #f97316; }
    .legende-dot.jaune  { background: #FCD116; }
    .legende-dot.vert   { background: #007A4D; }
    .stats-rapides { padding: 16px 18px; border-top: 1px solid var(--border); display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .stat-mini { background: var(--bg); border-radius: 10px; padding: 10px; text-align: center; }
    .stat-mini-val { font-size: 20px; font-weight: 800; color: var(--teal); }
    .stat-mini-lbl { font-size: 10px; color: var(--text-light); margin-top: 2px; }
    .stat-mini-val.rouge  { color: #CE1126; }
    .stat-mini-val.orange { color: #f97316; }
    .btn-reset-carte { display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 9px; background: var(--teal-light); border: 1.5px solid var(--teal); border-radius: 10px; color: var(--teal); font-size: 12px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s; margin-top: 4px; }
    .btn-reset-carte:hover { background: var(--teal); color: white; }
    .btn-reset-carte svg { width: 14px; height: 14px; }
    .leaflet-popup-content-wrapper { border-radius: 12px !important; box-shadow: 0 8px 24px rgba(0,0,0,0.15) !important; border: none !important; padding: 0 !important; }
    .leaflet-popup-content { margin: 0 !important; min-width: 200px; }
    .popup-content { padding: 14px 16px; }
    .popup-header { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; }
    .popup-badge { font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 20px; }
    .popup-badge.contrefait { background: #fef2f2; color: #CE1126; }
    .popup-badge.suspect    { background: #fefce8; color: #a16207; }
    .popup-badge.en_cours   { background: #fefce8; color: #a16207; }
    .popup-badge.traite     { background: #f0fdf4; color: #007A4D; }
    .popup-badge.authentique{ background: #f0fdf4; color: #007A4D; }
    .popup-produit { font-size: 13px; font-weight: 700; color: #1F2937; }
    .popup-info { font-size: 11px; color: #6b7280; margin-top: 4px; }
    .popup-desc { font-size: 11px; color: #6b7280; margin-top: 4px; font-style: italic; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px; }
    .popup-score { display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding: 6px 8px; background: #f9fafb; border-radius: 8px; }
    .popup-score-label { font-size: 11px; color: #6b7280; font-weight: 500; }
    .popup-score-val { font-size: 13px; font-weight: 800; }
    .carte-loading { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; flex-direction: column; align-items: center; gap: 12px; z-index: 1000; background: white; padding: 24px 32px; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); }
    .spinner { width: 32px; height: 32px; border: 3px solid #e5e7eb; border-top-color: #2E3A6B; border-radius: 50%; animation: spin 0.8s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .map-search-result-item {
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }
    .map-search-result-item:last-child { border-bottom: none; }
    .map-search-result-item:hover { background: #EEF0F8; }
    .map-search-result-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .map-search-result-name { font-weight: 700; color: #1F2937; font-size: 12.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .map-search-result-sub { font-size: 11px; color: #9ca3af; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .map-search-result-desc { font-size: 10.5px; color: #b0b7c3; margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-style: italic; }
    .map-search-tag {
        font-size: 9px; font-weight: 800; padding: 2px 7px;
        border-radius: 8px; flex-shrink: 0; white-space: nowrap;
        text-transform: uppercase; letter-spacing: 0.3px;
    }
    .map-search-tag.signalement { background: #ecfdf5; color: #007A4D; }
    .map-search-tag.lieu        { background: #eff6ff; color: #1d4ed8; }
    .map-search-empty { padding: 14px; color: #9ca3af; font-size: 12px; text-align: center; }
    .map-search-loading { padding: 10px 14px; color: #9ca3af; font-size: 11px; display: flex; align-items: center; gap: 6px; }
    .map-search-spinner-sm {
        width: 11px; height: 11px; border: 2px solid #e5e7eb;
        border-top-color: #2E3A6B; border-radius: 50%;
        animation: spin 0.6s linear infinite; flex-shrink: 0;
    }

    .map-zoom-stack {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        overflow: hidden;
        width: 34px;
    }
    .map-zoom-btn {
        width: 34px; height: 34px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; border: none; background: white;
        font-size: 17px; font-weight: 600; color: #374151;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }
    .map-zoom-btn:hover { background: #EEF0F8; }
    .map-zoom-btn.geoloc { border-bottom: none; color: #2E3A6B; }
    .map-zoom-btn.geoloc svg { width: 15px; height: 15px; }

    .map-layers-panel {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        overflow: hidden;
        font-size: 12px;
    }
    .map-layer-option {
        display: flex; align-items: center; gap: 7px;
        padding: 8px 12px;
        cursor: pointer;
        color: #6b7280; font-weight: 600;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s, color 0.15s;
        white-space: nowrap;
    }
    .map-layer-option:last-child { border-bottom: none; }
    .map-layer-option:hover { background: #f9fafb; }
    .map-layer-option.active { color: #2E3A6B; background: #EEF0F8; }
    .map-layer-option svg { width: 13px; height: 13px; flex-shrink: 0; }

    @media (max-width: 768px) {
        .carte-layout {
            grid-template-columns: 1fr;
            height: auto;
            gap: 12px;
        }
        .carte-card {
            border-radius: 12px;
        }
        .carte-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
        }
        .carte-header > div:last-child {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        .header-search-wrap { width: 100%; }
        .header-search-box  { width: 100%; box-sizing: border-box; }
        .header-search-dropdown { width: 100%; right: auto; left: 0; }
        #map { min-height: 300px; height: 55vw; min-height: 260px; }
        .filtres-card { border-radius: 12px; }
        .stats-rapides { grid-template-columns: 1fr 1fr 1fr 1fr; gap: 6px; }
        .stat-mini-val { font-size: 16px; }
    }
    @media (max-width: 480px) {
        .carte-header-left h2 { font-size: 13px; }
        .carte-header-left p  { display: none; }
        #map { height: 260px; }
        .stats-rapides { grid-template-columns: 1fr 1fr; }
    }
</style>
@endsection

@section('content')

<div id="translations"
    data-contrefait="{{ __('messages.contrefait') }}"
    data-suspect="{{ __('messages.suspect') }}"
    data-authentique="{{ __('messages.authentique') }}"
    data-score-ia="{{ __('messages.score_ia') }}"
    data-donnees-direct="{{ __('messages.donnees_direct') }}"
    data-chargement="{{ __('messages.chargement_carte') }}"
    data-reinitialiser="{{ __('messages.reinitialiser_filtres') }}"
    style="display:none;">
</div>

<div class="carte-layout">

    <div class="carte-card">
        <div class="carte-header">
            <div class="carte-header-left">
                <div class="carte-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <div>
                    <h2>{{ __('messages.carte_cameroun') }}</h2>
                    <p>{{ __('messages.signalements_temps_reel') }}</p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="header-search-wrap">
                    <div class="header-search-box">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/>
                        </svg>
                        <input id="unified-search-input" type="text" placeholder="Rechercher un signalement ou une ville..." />
                    </div>
                    <div id="unified-search-results" class="header-search-dropdown"></div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:8px;height:8px;border-radius:50%;background:#007A4D;animation:pulse-dot 2s infinite;"></div>
                    <span style="font-size:12px;color:#6b7280;font-weight:600;">{{ __('messages.donnees_direct') }}</span>
                </div>
            </div>
        </div>

        <div id="map" style="position:relative;">
            <div class="carte-loading" id="carteLoading">
                <div class="spinner"></div>
                <span style="font-size:13px;color:#6b7280;font-weight:600;">{{ __('messages.chargement_carte') }}</span>
            </div>
        </div>
    </div>

    <div class="filtres-card">

        <div class="filtres-header">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            {{ __('messages.filtres') }}
        </div>

        <div class="filtres-body">
            <div class="filtre-group">
                <label>{{ __('messages.statut_signalement') }}</label>
                <select class="filtre-select" id="filtreStatut" onchange="filtrerCarte()">
                    <option value="tous">{{ __('messages.tous_statuts') }}</option>
                    <option value="en_cours">{{ __('messages.en_cours') }}</option>
                    <option value="traite">{{ __('messages.traite') }}</option>
                </select>
            </div>

            <div class="filtre-group">
                <label>{{ __('messages.secteur_produit') }}</label>
                <select class="filtre-select" id="filtreSecteur" onchange="filtrerCarte()">
                    <option value="tous">{{ __('messages.tous_secteurs') }}</option>
                    <option value="medicament">{{ __('messages.medicament') }}</option>
                    <option value="alimentation">{{ __('messages.alimentation') }}</option>
                    <option value="cosmetique">{{ __('messages.cosmetique') }}</option>
                    <option value="automobile">{{ __('messages.automobile') }}</option>
                    <option value="hygiene">{{ __('messages.hygiene') }}</option>
                </select>
            </div>

            <div class="filtre-group">
                <label>{{ __('messages.region_cameroun') }}</label>
                <select class="filtre-select" id="filtreRegion" onchange="filtrerCarte()">
                    <option value="tous">{{ __('messages.toutes_regions') }}</option>
                    <option value="centre">{{ __('messages.region_centre') }}</option>
                    <option value="littoral">{{ __('messages.region_littoral') }}</option>
                    <option value="ouest">{{ __('messages.region_ouest') }}</option>
                    <option value="nord">{{ __('messages.region_nord') }}</option>
                    <option value="adamaoua">{{ __('messages.region_adamaoua') }}</option>
                    <option value="sud">{{ __('messages.region_sud') }}</option>
                    <option value="est">{{ __('messages.region_est') }}</option>
                    <option value="nordouest">{{ __('messages.region_nordouest') }}</option>
                    <option value="sudouest">{{ __('messages.region_sudouest') }}</option>
                    <option value="extremenord">{{ __('messages.region_extremenord') }}</option>
                </select>
            </div>

            <button class="btn-reset-carte" onclick="resetFiltres()">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ __('messages.reinitialiser_filtres') }}
            </button>
        </div>

        <div class="legende-section">
            <div class="legende-title">{{ __('messages.legende') }}</div>
            <div class="legende-item"><div class="legende-dot rouge"></div>{{ __('messages.legende_contrefait') }}</div>
            <div class="legende-item"><div class="legende-dot orange"></div>{{ __('messages.legende_suspect_eleve') }}</div>
            <div class="legende-item"><div class="legende-dot jaune"></div>{{ __('messages.legende_suspect_faible') }}</div>
            <div class="legende-item"><div class="legende-dot vert"></div>{{ __('messages.legende_authentique') }}</div>
        </div>

        <div class="stats-rapides">
            <div class="stat-mini">
                <div class="stat-mini-val rouge" id="statContrefait">0</div>
                <div class="stat-mini-lbl">{{ __('messages.contrefaits') }}</div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-val orange" id="statSuspect">0</div>
                <div class="stat-mini-lbl">{{ __('messages.suspects') }}</div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-val" id="statTotal">0</div>
                <div class="stat-mini-lbl">{{ __('messages.total_scans') }}</div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-val" id="statRegions">0</div>
                <div class="stat-mini-lbl">{{ __('messages.regions') }}</div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script>

const t = document.getElementById('translations').dataset;

const signalements = @json($signalements);

function getCouleur(score) {
    if (score >= 70) return '#CE1126';
    if (score >= 50) return '#f97316';
    if (score >= 30) return '#FCD116';
    return '#007A4D';
}

function getStatutLabel(statut) {
    const labels = { contrefait: t.contrefait, suspect: t.suspect, authentique: t.authentique, en_cours: t.suspect, traite: t.authentique };
    return labels[statut] || statut;
}

const tuilePlan = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors', maxZoom: 19,
});
const tuileSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: '© Esri, Maxar, Earthstar Geographics', maxZoom: 19,
});
const tuileTopo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenTopoMap contributors', maxZoom: 17,
});

const map = L.map('map', {
    center: [5.5, 12.5], zoom: 6, zoomControl: false, layers: [tuilePlan],
});

const zoomGeoControl = L.control({ position: 'topleft' });
zoomGeoControl.onAdd = function() {
    const div = L.DomUtil.create('div', 'map-zoom-stack');
    div.innerHTML = `
        <button class="map-zoom-btn" id="map-zoom-in" title="Zoomer">+</button>
        <button class="map-zoom-btn" id="map-zoom-out" title="Dézoomer">−</button>
        <button class="map-zoom-btn geoloc" id="map-geoloc-btn" title="Ma position">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <path stroke-linecap="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/>
            </svg>
        </button>
    `;
    L.DomEvent.disableClickPropagation(div);
    return div;
};
zoomGeoControl.addTo(map);

setTimeout(() => {
    document.getElementById('map-zoom-in').addEventListener('click', () => map.zoomIn());
    document.getElementById('map-zoom-out').addEventListener('click', () => map.zoomOut());
    document.getElementById('map-geoloc-btn').addEventListener('click', () => map.locate({ setView: true, maxZoom: 13 }));
}, 400);

map.on('locationfound', function(e) {
    L.circleMarker(e.latlng, { radius: 8, fillColor: '#2E3A6B', color: '#fff', weight: 2, fillOpacity: 1 })
        .addTo(map).bindPopup('Vous êtes ici').openPopup();
});

let coucheActive = 'plan';
const couchesParNom = { plan: tuilePlan, satellite: tuileSatellite, topo: tuileTopo };

const layersControl = L.control({ position: 'topright' });
layersControl.onAdd = function() {
    const div = L.DomUtil.create('div', 'map-layers-panel');
    div.innerHTML = `
        <div class="map-layer-option active" data-couche="plan">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            Plan
        </div>
        <div class="map-layer-option" data-couche="satellite">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Satellite
        </div>
        <div class="map-layer-option" data-couche="topo">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l6 3 6-3 6 3v9l-6-3-6 3-6-3V6z"/></svg>
            Topo
        </div>
    `;
    L.DomEvent.disableClickPropagation(div);
    return div;
};
layersControl.addTo(map);

setTimeout(() => {
    document.querySelectorAll('.map-layer-option').forEach(opt => {
        opt.addEventListener('click', () => {
            const nom = opt.dataset.couche;
            if (nom === coucheActive) return;
            map.removeLayer(couchesParNom[coucheActive]);
            map.addLayer(couchesParNom[nom]);
            coucheActive = nom;
            document.querySelectorAll('.map-layer-option').forEach(o => o.classList.remove('active'));
            opt.classList.add('active');
        });
    });
}, 400);

let unifiedSearchMarker = null;
let nominatimDebounceTimer = null;
window._unifiedResultats = [];

function rechercherSignalementsLocaux(q) {
    return signalements.filter(s =>
        (s.produit      && s.produit.toLowerCase().includes(q)) ||
        (s.lieu         && s.lieu.toLowerCase().includes(q)) ||
        (s.region       && s.region.toLowerCase().includes(q)) ||
        (s.description  && s.description.toLowerCase().includes(q))
    ).slice(0, 5).map(s => ({ ...s, _type: 'signalement' }));
}

function renderUnifiedResults(resultats, loadingVilles) {
    const box = document.getElementById('unified-search-results');
    window._unifiedResultats = resultats;

    if (!resultats.length && !loadingVilles) {
        box.style.display = 'block';
        box.innerHTML = '<div class="map-search-empty">Aucun résultat trouvé</div>';
        return;
    }

    box.style.display = 'block';
    let html = resultats.map((r, i) => {
        if (r._type === 'signalement') {
            const sub = r.lieu && r.lieu !== 'Localisation inconnue' ? r.lieu : (r.region || '—');
            const desc = r.description ? r.description.substring(0, 60) + (r.description.length > 60 ? '…' : '') : '';
            return `
                <div class="map-search-result-item" onclick="selectUnifiedResult(${i})">
                    <div class="map-search-result-top">
                        <span class="map-search-result-name">${r.produit}</span>
                        <span class="map-search-tag signalement">Signalement</span>
                    </div>
                    <div class="map-search-result-sub">📍 ${sub} · score ${r.score}</div>
                    ${desc ? `<div class="map-search-result-desc">${desc}</div>` : ''}
                </div>
            `;
        }
        return `
            <div class="map-search-result-item" onclick="selectUnifiedResult(${i})">
                <div class="map-search-result-top">
                    <span class="map-search-result-name">${r.display_name.split(',')[0]}</span>
                    <span class="map-search-tag lieu">Lieu</span>
                </div>
                <div class="map-search-result-sub">${r.display_name.split(',').slice(1,3).join(',')}</div>
            </div>
        `;
    }).join('');

    if (loadingVilles) {
        html += '<div class="map-search-loading"><span class="map-search-spinner-sm"></span> Recherche de lieux...</div>';
    }

    box.innerHTML = html;
}

function doUnifiedSearch() {
    const raw = document.getElementById('unified-search-input').value.trim();
    const q = raw.toLowerCase();
    const box = document.getElementById('unified-search-results');

    if (!raw) { box.style.display = 'none'; clearTimeout(nominatimDebounceTimer); return; }

    const resultatsSignalements = rechercherSignalementsLocaux(q);
    renderUnifiedResults(resultatsSignalements, true);

    clearTimeout(nominatimDebounceTimer);
    nominatimDebounceTimer = setTimeout(() => {
        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(raw)}&format=json&limit=4&countrycodes=cm`)
            .then(r => r.json())
            .then(villes => {
                renderUnifiedResults([...resultatsSignalements, ...villes.map(v => ({ ...v, _type: 'lieu' }))], false);
            })
            .catch(() => renderUnifiedResults(resultatsSignalements, false));
    }, 350);
}

window.selectUnifiedResult = function(index) {
    const r = window._unifiedResultats[index];
    if (!r) return;

    if (unifiedSearchMarker) { map.removeLayer(unifiedSearchMarker); unifiedSearchMarker = null; }

    if (r._type === 'signalement') {
        map.setView([r.lat, r.lng], 13);
        const m = construireMarqueur(r);
        m.addTo(map).openPopup();
        unifiedSearchMarker = m;
        document.getElementById('unified-search-input').value = r.produit;
    } else {
        const lat = parseFloat(r.lat), lon = parseFloat(r.lon);
        map.setView([lat, lon], 12);
        unifiedSearchMarker = L.marker([lat, lon]).addTo(map).bindPopup(r.display_name).openPopup();
        document.getElementById('unified-search-input').value = r.display_name.split(',')[0];
    }

    document.getElementById('unified-search-results').style.display = 'none';
};

document.addEventListener('click', function(e) {
    if (!e.target.closest('#unified-search-input') && !e.target.closest('#unified-search-results')) {
        const box = document.getElementById('unified-search-results');
        if (box) box.style.display = 'none';
    }
});

setTimeout(() => {
    const inp = document.getElementById('unified-search-input');
    if (inp) inp.addEventListener('input', doUnifiedSearch);
}, 500);

setTimeout(() => {
    document.getElementById('carteLoading').style.display = 'none';
}, 1500);

let heatLayer = null;
let marqueurs = [];

function construireMarqueur(s) {
    const couleur = getCouleur(s.score);
    const icon = L.divIcon({
        className: '',
        html: `<div style="width:32px;height:32px;background:${couleur};border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid white;box-shadow:0 3px 10px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;"><div style="transform:rotate(45deg);color:white;font-size:10px;font-weight:900;font-family:'DM Sans',sans-serif;">${s.score}</div></div>`,
        iconSize: [32, 32], iconAnchor: [16, 32], popupAnchor: [0, -35],
    });

    const descHtml = s.description
        ? `<div class="popup-desc" title="${s.description}">💬 ${s.description.substring(0, 80)}${s.description.length > 80 ? '…' : ''}</div>`
        : '';
    const lotHtml = s.lot
        ? `<div class="popup-info">📦 ${s.lot}</div>`
        : '';

    return L.marker([s.lat, s.lng], { icon }).bindPopup(`
        <div class="popup-content">
            <div class="popup-header">
                <span class="popup-badge ${s.statut}">${getStatutLabel(s.statut)}</span>
                <span style="font-size:11px;color:#6b7280;">${s.date}</span>
            </div>
            <div class="popup-produit">${s.produit}</div>
            <div class="popup-info">📍 ${s.lieu}</div>
            ${lotHtml}
            ${descHtml}
            <div class="popup-score">
                <span class="popup-score-label">${t.scoreIa}</span>
                <span class="popup-score-val" style="color:${couleur};">${s.score}/100</span>
            </div>
        </div>
    `, { maxWidth: 260 });
}

function afficherSignalements(data) {
    marqueurs.forEach(m => map.removeLayer(m));
    marqueurs = [];
    if (heatLayer) { map.removeLayer(heatLayer); heatLayer = null; }

    const heatPoints = [];
    data.forEach(s => {
        const m = construireMarqueur(s);
        m.addTo(map);
        marqueurs.push(m);
        heatPoints.push([s.lat, s.lng, s.score / 100]);
    });

    if (heatPoints.length > 0) {
        heatLayer = L.heatLayer(heatPoints, {
            radius: 35, blur: 25, maxZoom: 10,
            gradient: { 0.0: '#007A4D', 0.3: '#FCD116', 0.5: '#f97316', 1.0: '#CE1126' }
        }).addTo(map);
    }

    majStats(data);
}

function majStats(data) {
    document.getElementById('statContrefait').textContent = data.filter(s => s.score >= 70).length;
    document.getElementById('statSuspect').textContent    = data.filter(s => s.score >= 30 && s.score < 70).length;
    document.getElementById('statTotal').textContent      = data.length;
    document.getElementById('statRegions').textContent    = [...new Set(data.map(s => s.region))].length;
}

function filtrerCarte() {
    const statut  = document.getElementById('filtreStatut').value;
    const secteur = document.getElementById('filtreSecteur').value;
    const region  = document.getElementById('filtreRegion').value;
    let filtre = signalements;
    if (statut  !== 'tous') filtre = filtre.filter(s => s.statut  === statut);
    if (secteur !== 'tous') filtre = filtre.filter(s => s.secteur === secteur);
    if (region  !== 'tous') filtre = filtre.filter(s => s.region  === region);
    afficherSignalements(filtre);
}

function resetFiltres() {
    document.getElementById('filtreStatut').value  = 'tous';
    document.getElementById('filtreSecteur').value = 'tous';
    document.getElementById('filtreRegion').value  = 'tous';
    afficherSignalements(signalements);
    map.setView([5.5, 12.5], 6);
}

setTimeout(function() {
    afficherSignalements(signalements);
}, 300);
</script>

<style>
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.7); }
    }
</style>
@endsection