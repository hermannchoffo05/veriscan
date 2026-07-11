@extends('layouts.admin')
@section('title', 'Carte des risques')
@section('topbar-title')
    Carte des <span>risques</span>
@endsection
@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    .carte-layout { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }
    .map-card { background: var(--white); border-radius: 16px; border: 1.5px solid var(--border); overflow: hidden; }
    .map-header { padding: 15px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .map-title { font-size: 14px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 7px; flex-shrink: 0; }
    .map-title svg { width: 15px; height: 15px; color: var(--teal); }
    #map { height: 540px; width: 100%; }
    .side-card { background: var(--white); border-radius: 16px; border: 1.5px solid var(--border); overflow: hidden; margin-bottom: 16px; }
    .side-card-header { padding: 14px 16px; border-bottom: 1px solid var(--border); font-size: 13px; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 6px; }
    .side-card-header svg { width: 14px; height: 14px; color: var(--teal); }
    .side-card-body { padding: 14px 16px; }

    /* Recherche dans le header */
    .header-search-wrap { position: relative; }
    .header-search-box {
        display: flex; align-items: center; gap: 7px;
        background: var(--bg); border: 1.5px solid var(--border);
        border-radius: 10px; padding: 7px 12px;
        width: 260px; transition: border-color 0.2s;
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
        background: white; width: 320px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        max-height: 320px; overflow-y: auto;
        z-index: 1100;
    }

    /* Résultats dropdown */
    .map-search-result-item { padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f3f4f6; transition: background 0.15s; }
    .map-search-result-item:last-child { border-bottom: none; }
    .map-search-result-item:hover { background: #EEF0F8; }
    .map-search-result-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .map-search-result-name { font-weight: 700; color: #1F2937; font-size: 12.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .map-search-result-sub { font-size: 11px; color: #9ca3af; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .map-search-result-desc { font-size: 10.5px; color: #b0b7c3; margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-style: italic; }
    .map-search-tag { font-size: 9px; font-weight: 800; padding: 2px 7px; border-radius: 8px; flex-shrink: 0; white-space: nowrap; text-transform: uppercase; letter-spacing: 0.3px; }
    .map-search-tag.ia          { background: #eff6ff; color: #1d4ed8; }
    .map-search-tag.gps         { background: #fdf4ff; color: #7e22ce; }
    .map-search-tag.lieu        { background: #ecfdf5; color: #007A4D; }
    .map-search-empty { padding: 14px; color: #9ca3af; font-size: 12px; text-align: center; }
    .map-search-loading { padding: 10px 14px; color: #9ca3af; font-size: 11px; display: flex; align-items: center; gap: 6px; }
    .map-search-spinner-sm { width: 11px; height: 11px; border: 2px solid #e5e7eb; border-top-color: #2E3A6B; border-radius: 50%; animation: spin 0.6s linear infinite; flex-shrink: 0; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Contrôles carte custom */
    .map-zoom-stack { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.15); overflow: hidden; width: 34px; }
    .map-zoom-btn { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; background: white; font-size: 17px; font-weight: 600; color: #374151; border-bottom: 1px solid #f3f4f6; transition: background 0.15s; }
    .map-zoom-btn:hover { background: #EEF0F8; }
    .map-zoom-btn.geoloc { border-bottom: none; color: #2E3A6B; }
    .map-zoom-btn.geoloc svg { width: 15px; height: 15px; }
    .map-layers-panel { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.15); overflow: hidden; font-size: 12px; }
    .map-layer-option { display: flex; align-items: center; gap: 7px; padding: 8px 12px; cursor: pointer; color: #6b7280; font-weight: 600; border-bottom: 1px solid #f3f4f6; transition: background 0.15s, color 0.15s; white-space: nowrap; }
    .map-layer-option:last-child { border-bottom: none; }
    .map-layer-option:hover { background: #f9fafb; }
    .map-layer-option.active { color: #2E3A6B; background: #EEF0F8; }
    .map-layer-option svg { width: 13px; height: 13px; flex-shrink: 0; }

    /* Légende */
    .legend-item { display: flex; align-items: center; gap: 10px; padding: 7px 0; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
    .legend-item:last-child { border-bottom: none; }
    .legend-dot { width: 13px; height: 13px; border-radius: 50%; flex-shrink: 0; }
    .legend-label { font-weight: 600; color: var(--text); flex: 1; }
    .legend-range { font-size: 11px; color: var(--text-light); }

    /* Scores IA */
    .score-item { display: flex; align-items: center; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid #f3f4f6; }
    .score-item:last-child { border-bottom: none; }
    .score-produit { font-size: 12px; font-weight: 600; color: var(--text); max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .score-fabricant { font-size: 11px; color: var(--text-light); margin-top: 2px; }
    .score-badge { font-size: 11px; font-weight: 800; padding: 3px 9px; border-radius: 20px; }
    .score-bar-wrap { margin-top: 4px; height: 4px; background: #f3f4f6; border-radius: 2px; width: 100%; }
    .score-bar { height: 4px; border-radius: 2px; }

    /* Stats résumé */
    .stat-mini { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
    .stat-mini:last-child { border-bottom: none; }
    .stat-mini-label { color: var(--text-light); font-weight: 500; }
    .stat-mini-val { font-weight: 800; color: var(--text); }

    /* Toggle couches */
    .layer-toggle { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .layer-btn { display: flex; align-items: center; gap: 5px; padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 2px solid transparent; cursor: pointer; transition: all 0.2s; }
    .layer-btn.active-ia    { background: #eff6ff; border-color: #3b82f6; color: #1d4ed8; }
    .layer-btn.active-gps   { background: #fdf4ff; border-color: #a855f7; color: #7e22ce; }
    .layer-btn.inactive     { background: #f3f4f6; border-color: #e5e7eb; color: #9ca3af; }
    .layer-dot-ia  { width: 10px; height: 10px; border-radius: 50%; background: #3b82f6; }
    .layer-dot-gps { width: 10px; height: 10px; background: #a855f7; transform: rotate(45deg); }

    /* Popup Leaflet */
    .leaflet-popup-content-wrapper { border-radius: 12px !important; box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important; border: none !important; }
    .leaflet-popup-content { margin: 0 !important; padding: 0 !important; min-width: 200px; }
    .popup-inner { padding: 14px 16px; }
    .popup-title { font-size: 13px; font-weight: 800; color: #111827; margin-bottom: 4px; }
    .popup-fab { font-size: 11px; color: #6b7280; margin-bottom: 10px; }
    .popup-score { font-size: 22px; font-weight: 900; margin-bottom: 2px; }
    .popup-niveau { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
    .popup-row { display: flex; justify-content: space-between; font-size: 11px; color: #6b7280; padding: 3px 0; }
    .popup-row span:last-child { font-weight: 600; color: #111827; }
    .popup-footer { background: #f9fafb; border-top: 1px solid #f3f4f6; padding: 8px 16px; font-size: 11px; color: #9ca3af; }
    .popup-footer-gps { background: #fdf4ff; border-top: 1px solid #f3e8ff; padding: 8px 16px; font-size: 11px; color: #a855f7; }
    .popup-tag-gps { display: inline-block; background: #fdf4ff; color: #7e22ce; font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 10px; margin-bottom: 8px; border: 1px solid #e9d5ff; }

    /* ── Responsive mobile ────────────────────────────────────────────────────── */
    @media (max-width: 768px) {
        .carte-layout {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .map-card { border-radius: 12px; }
        .map-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
        }
        .map-header > div {
            width: 100%;
        }
        .header-search-wrap { width: 100%; order: 2; }
        .header-search-box  { width: 100%; box-sizing: border-box; }
        .header-search-dropdown { width: 100%; right: auto; left: 0; }
        .layer-toggle { flex-wrap: wrap; }
        #map { height: 55vw; min-height: 260px; }
        .side-card { border-radius: 12px; margin-bottom: 10px; }
    }
    @media (max-width: 480px) {
        .map-title { font-size: 13px; }
        #map { height: 260px; }
        .map-header > div:last-child { flex-direction: column; align-items: flex-start; gap: 6px; }
    }
</style>
@endsection

@section('content')
<div class="carte-layout">

    {{-- CARTE LEAFLET --}}
    <div class="map-card">
        <div class="map-header">
            <div class="map-title">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                Carte des risques · Cameroun
            </div>

            {{-- Recherche unifiée dans le header --}}
            <div class="header-search-wrap">
                <div class="header-search-box">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/>
                    </svg>
                    <input id="unified-search-input" type="text" placeholder="Rechercher produit, fabricant ou ville..." />
                </div>
                <div id="unified-search-results" class="header-search-dropdown"></div>
            </div>

            <div style="display:flex;align-items:center;gap:12px;">
                <div class="layer-toggle">
                    <button class="layer-btn active-ia" id="btn-ia" onclick="toggleLayer('ia')">
                        <span class="layer-dot-ia"></span> Scores IA ({{ $totalScores }})
                    </button>
                    <button class="layer-btn active-gps" id="btn-gps" onclick="toggleLayer('gps')">
                        <span class="layer-dot-gps"></span> GPS mobile ({{ $totalSignalementsGps }})
                    </button>
                </div>
                <div style="font-size:11px;color:var(--text-light);">
                    Mis à jour : {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
        <div id="map"></div>
    </div>

    {{-- SIDEBAR DROITE --}}
    <div>
        {{-- Légende IA --}}
        <div class="side-card">
            <div class="side-card-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Légende
            </div>
            <div class="side-card-body">
                <div style="font-size:11px;font-weight:700;color:var(--text-light);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;">● Cercles — Score IA</div>
                <div class="legend-item"><div class="legend-dot" style="background:#10b981;"></div><div class="legend-label">Faible</div><div class="legend-range">Score 0–30</div></div>
                <div class="legend-item"><div class="legend-dot" style="background:#f59e0b;"></div><div class="legend-label">Modéré</div><div class="legend-range">Score 31–60</div></div>
                <div class="legend-item"><div class="legend-dot" style="background:#ef4444;"></div><div class="legend-label">Élevé</div><div class="legend-range">Score 61–80</div></div>
                <div class="legend-item" style="border-bottom:none;margin-bottom:10px;"><div class="legend-dot" style="background:#7f1d1d;"></div><div class="legend-label">Critique</div><div class="legend-range">Score 81–100</div></div>
                <div style="font-size:11px;font-weight:700;color:var(--text-light);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;">◆ Losanges — Signalements GPS</div>
                <div class="legend-item"><div style="width:13px;height:13px;background:#a855f7;transform:rotate(45deg);flex-shrink:0;"></div><div class="legend-label">Signalement mobile</div><div class="legend-range">GPS réel</div></div>
            </div>
        </div>

        {{-- Résumé statistiques --}}
        <div class="side-card">
            <div class="side-card-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Résumé
            </div>
            <div class="side-card-body">
                <div class="stat-mini"><span class="stat-mini-label">Produits analysés IA</span><span class="stat-mini-val">{{ $totalScores }}</span></div>
                <div class="stat-mini"><span class="stat-mini-label">Score moyen</span><span class="stat-mini-val">{{ $scoreMoyen }}/100</span></div>
                <div class="stat-mini"><span class="stat-mini-label">Signalements GPS</span><span class="stat-mini-val" style="color:#a855f7;">{{ $totalSignalementsGps }}</span></div>
                <div class="stat-mini"><span class="stat-mini-label" style="color:#065f46;">● Faibles</span><span class="stat-mini-val">{{ $niveaux['faible'] ?? 0 }}</span></div>
                <div class="stat-mini"><span class="stat-mini-label" style="color:#92400e;">● Modérés</span><span class="stat-mini-val">{{ $niveaux['modere'] ?? 0 }}</span></div>
                <div class="stat-mini"><span class="stat-mini-label" style="color:#991b1b;">● Élevés</span><span class="stat-mini-val">{{ $niveaux['eleve'] ?? 0 }}</span></div>
                <div class="stat-mini"><span class="stat-mini-label" style="color:#7f1d1d;">● Critiques</span><span class="stat-mini-val">{{ $niveaux['critique'] ?? 0 }}</span></div>
            </div>
        </div>

        {{-- Top scores IA --}}
        <div class="side-card">
            <div class="side-card-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Produits à surveiller
            </div>
            <div class="side-card-body">
                @forelse($topRisques as $rs)
                @php
                    $color = match($rs->niveau) {
                        'critique' => ['bg'=>'#7f1d1d','text'=>'#fff','bar'=>'#7f1d1d'],
                        'eleve'    => ['bg'=>'#fee2e2','text'=>'#991b1b','bar'=>'#ef4444'],
                        'modere'   => ['bg'=>'#fef3c7','text'=>'#92400e','bar'=>'#f59e0b'],
                        default    => ['bg'=>'#d1fae5','text'=>'#065f46','bar'=>'#10b981'],
                    };
                @endphp
                <div class="score-item">
                    <div style="flex:1;min-width:0;">
                        <div class="score-produit">{{ $rs->produit->nom ?? 'Produit #'.$rs->produit_id }}</div>
                        <div class="score-fabricant">{{ $rs->produit->fabricant->nom_entreprise ?? '' }}</div>
                        <div class="score-bar-wrap"><div class="score-bar" style="width:{{ $rs->score }}%;background:{{ $color['bar'] }};"></div></div>
                    </div>
                    <div style="margin-left:10px;text-align:right;">
                        <div style="font-size:16px;font-weight:900;color:{{ $color['bar'] }};">{{ round($rs->score) }}</div>
                        <div class="score-badge" style="background:{{ $color['bg'] }};color:{{ $color['text'] }};">{{ ucfirst($rs->niveau) }}</div>
                    </div>
                </div>
                @empty
                <p style="font-size:12px;color:var(--text-light);text-align:center;padding:10px 0;">Aucun score calculé</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const marqueurs       = @json($marqueurs);
const signalementsGps = @json($signalementsGps);

// ── Couches de tuiles ──────────────────────────────────────────────────────────
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
    center: [4.5, 11.5], zoom: 6, zoomControl: false, layers: [tuilePlan],
});

// ── Zoom + Géolocalisation regroupés (haut gauche) ────────────────────────────
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

// ── Sélecteur Plan / Satellite / Topo custom (haut droite) ───────────────────
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

// ── Fonctions utilitaires ─────────────────────────────────────────────────────
function getCouleur(niveau) {
    switch(niveau) {
        case 'critique': return '#7f1d1d';
        case 'eleve':    return '#ef4444';
        case 'modere':   return '#f59e0b';
        default:         return '#10b981';
    }
}

function getRayon(score) {
    if (score >= 80) return 28;
    if (score >= 60) return 22;
    if (score >= 30) return 16;
    return 12;
}

function iconGps(statut) {
    const fill = statut === 'traite' ? '#10b981' : '#a855f7';
    return L.divIcon({
        className: '',
        html: `<div style="width:16px;height:16px;background:${fill};transform:rotate(45deg);border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.25);"></div>`,
        iconSize: [16,16], iconAnchor: [8,8], popupAnchor: [0,-10],
    });
}

// ── Recherche unifiée (dans le header) ────────────────────────────────────────
let unifiedSearchMarker = null;
let nominatimDebounceTimer = null;
window._unifiedResultats = [];

function rechercherDonnees(q) {
    const resIA = marqueurs.filter(m =>
        (m.produit   && m.produit.toLowerCase().includes(q)) ||
        (m.fabricant && m.fabricant.toLowerCase().includes(q)) ||
        (m.categorie && m.categorie.toLowerCase().includes(q))
    ).slice(0, 3).map(m => ({ ...m, _type: 'ia' }));

    const resGps = signalementsGps.filter(s =>
        (s.produit      && s.produit.toLowerCase().includes(q)) ||
        (s.fabricant    && s.fabricant.toLowerCase().includes(q)) ||
        (s.region       && s.region.toLowerCase().includes(q)) ||
        (s.localisation && s.localisation.toLowerCase().includes(q)) ||
        (s.description  && s.description.toLowerCase().includes(q))
    ).slice(0, 3).map(s => ({ ...s, _type: 'gps' }));

    return [...resIA, ...resGps];
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
        if (r._type === 'ia') {
            const couleur = getCouleur(r.niveau);
            return `
                <div class="map-search-result-item" onclick="selectUnifiedResult(${i})">
                    <div class="map-search-result-top">
                        <span class="map-search-result-name">${r.produit}</span>
                        <span class="map-search-tag ia">Score IA</span>
                    </div>
                    <div class="map-search-result-sub">${r.fabricant || '—'} · <span style="color:${couleur};font-weight:700;">${Math.round(r.score)}</span></div>
                </div>
            `;
        }
        if (r._type === 'gps') {
            const sub = r.localisation || r.region || '—';
            const desc = r.description ? r.description.substring(0, 55) + (r.description.length > 55 ? '…' : '') : '';
            return `
                <div class="map-search-result-item" onclick="selectUnifiedResult(${i})">
                    <div class="map-search-result-top">
                        <span class="map-search-result-name">${r.produit}</span>
                        <span class="map-search-tag gps">GPS mobile</span>
                    </div>
                    <div class="map-search-result-sub">📍 ${sub}</div>
                    ${desc ? `<div class="map-search-result-desc">${desc}</div>` : ''}
                </div>
            `;
        }
        // lieu Nominatim
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

    const resultatsLocaux = rechercherDonnees(q);
    renderUnifiedResults(resultatsLocaux, true);

    clearTimeout(nominatimDebounceTimer);
    nominatimDebounceTimer = setTimeout(() => {
        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(raw)}&format=json&limit=3&countrycodes=cm`)
            .then(r => r.json())
            .then(villes => {
                renderUnifiedResults([...resultatsLocaux, ...villes.map(v => ({ ...v, _type: 'lieu' }))], false);
            })
            .catch(() => renderUnifiedResults(resultatsLocaux, false));
    }, 350);
}

window.selectUnifiedResult = function(index) {
    const r = window._unifiedResultats[index];
    if (!r) return;

    if (unifiedSearchMarker) { map.removeLayer(unifiedSearchMarker); unifiedSearchMarker = null; }

    if (r._type === 'ia') {
        if (!r.lat || !r.lng) return;
        map.setView([r.lat, r.lng], 13);
        const couleur = getCouleur(r.niveau);
        unifiedSearchMarker = L.circleMarker([r.lat, r.lng], {
            radius: getRayon(r.score), fillColor: couleur, color: '#fff', weight: 2, opacity: 1, fillOpacity: 0.9,
        }).addTo(map).bindPopup(`
            <div class="popup-inner">
                <div class="popup-title">${r.produit}</div>
                <div class="popup-fab">${r.fabricant}</div>
                <div class="popup-score" style="color:${couleur};">${Math.round(r.score)}<span style="font-size:13px;font-weight:500;color:#6b7280;">/100</span></div>
                <div class="popup-niveau" style="color:${couleur};">${r.niveau.toUpperCase()}</div>
            </div>
        `, { maxWidth: 240 }).openPopup();
        document.getElementById('unified-search-input').value = r.produit;

    } else if (r._type === 'gps') {
        if (!r.lat || !r.lng) return;
        map.setView([r.lat, r.lng], 13);
        const couleur = getCouleur(r.niveau);
        unifiedSearchMarker = L.marker([r.lat, r.lng], { icon: iconGps(r.statut) }).addTo(map).bindPopup(`
            <div class="popup-inner">
                <div class="popup-tag-gps">◆ Signalement GPS mobile</div>
                <div class="popup-title">${r.produit}</div>
                <div class="popup-fab">${r.fabricant || ''} · ${r.region || 'Région inconnue'}</div>
                <div class="popup-score" style="color:${couleur};">${Math.round(r.score)}<span style="font-size:13px;font-weight:500;color:#6b7280;">/100</span></div>
                <div class="popup-row"><span>Localisation</span><span>${r.localisation || '—'}</span></div>
                ${r.description ? `<div class="popup-row"><span>Description</span><span style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${r.description}">${r.description.substring(0,40)}…</span></div>` : ''}
            </div>
        `, { maxWidth: 260 }).openPopup();
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

// ── Groupes de couches ────────────────────────────────────────────────────────
const layerIA  = L.layerGroup().addTo(map);
const layerGPS = L.layerGroup().addTo(map);
let iaVisible  = true;
let gpsVisible = true;

// ── Marqueurs Scores IA ───────────────────────────────────────────────────────
marqueurs.forEach(function(m) {
    if (!m.lat || !m.lng) return;
    const couleur = getCouleur(m.niveau);
    const rayon   = getRayon(m.score);

    const cercle = L.circleMarker([m.lat, m.lng], {
        radius: rayon, fillColor: couleur, color: '#fff', weight: 2, opacity: 1, fillOpacity: 0.82,
    });

    cercle.bindPopup(`
        <div class="popup-inner">
            <div class="popup-title">${m.produit}</div>
            <div class="popup-fab">${m.fabricant}</div>
            <div class="popup-score" style="color:${couleur};">${Math.round(m.score)}<span style="font-size:13px;font-weight:500;color:#6b7280;">/100</span></div>
            <div class="popup-niveau" style="color:${couleur};">${m.niveau.toUpperCase()}</div>
            <div class="popup-row"><span>Signalements</span><span>${m.signalements}</span></div>
            <div class="popup-row"><span>QR Codes actifs</span><span>${m.qrcodes}</span></div>
            <div class="popup-row"><span>Catégorie</span><span>${m.categorie}</span></div>
        </div>
        <div class="popup-footer">Score IA — AIRiskScoringService</div>
    `, { maxWidth: 240 });

    cercle.on('mouseover', function() { this.openPopup(); });
    layerIA.addLayer(cercle);
});

// ── Marqueurs GPS réels ───────────────────────────────────────────────────────
signalementsGps.forEach(function(s) {
    if (!s.lat || !s.lng) return;
    const couleur = getCouleur(s.niveau);

    const marker = L.marker([s.lat, s.lng], { icon: iconGps(s.statut) });

    const statutBadge = s.statut === 'traite'
        ? '<span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:800;">Traité</span>'
        : '<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:800;">En cours</span>';

    marker.bindPopup(`
        <div class="popup-inner">
            <div class="popup-tag-gps">◆ Signalement GPS mobile</div>
            <div class="popup-title">${s.produit}</div>
            <div class="popup-fab">${s.fabricant} · ${s.region || 'Région inconnue'}</div>
            <div class="popup-score" style="color:${couleur};">${Math.round(s.score)}<span style="font-size:13px;font-weight:500;color:#6b7280;">/100</span></div>
            <div class="popup-niveau" style="color:${couleur};">${s.niveau.toUpperCase()}</div>
            <div class="popup-row"><span>Localisation</span><span>${s.localisation || '—'}</span></div>
            <div class="popup-row"><span>Description</span><span style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${s.description || '—'}</span></div>
            <div class="popup-row"><span>Date</span><span>${s.date}</span></div>
            <div class="popup-row"><span>Statut</span><span>${statutBadge}</span></div>
        </div>
        <div class="popup-footer-gps">Coordonnées GPS réelles · lat:${parseFloat(s.lat).toFixed(4)} lng:${parseFloat(s.lng).toFixed(4)}</div>
    `, { maxWidth: 260 });

    marker.on('mouseover', function() { this.openPopup(); });
    layerGPS.addLayer(marker);
});

// ── Toggle couches ────────────────────────────────────────────────────────────
function toggleLayer(type) {
    if (type === 'ia') {
        iaVisible = !iaVisible;
        const btn = document.getElementById('btn-ia');
        if (iaVisible) { map.addLayer(layerIA); btn.className = 'layer-btn active-ia'; }
        else           { map.removeLayer(layerIA); btn.className = 'layer-btn inactive'; }
    } else {
        gpsVisible = !gpsVisible;
        const btn = document.getElementById('btn-gps');
        if (gpsVisible) { map.addLayer(layerGPS); btn.className = 'layer-btn active-gps'; }
        else            { map.removeLayer(layerGPS); btn.className = 'layer-btn inactive'; }
    }
}

if (marqueurs.length === 0 && signalementsGps.length === 0) {
    const info = L.control({ position: 'topright' });
    info.onAdd = function() {
        const div = L.DomUtil.create('div');
        div.innerHTML = '<div style="background:white;padding:10px 14px;border-radius:10px;font-size:12px;color:#6b7280;box-shadow:0 2px 8px rgba(0,0,0,0.1);">Aucune donnée disponible.</div>';
        return div;
    };
    info.addTo(map);
}
</script>
@endsection