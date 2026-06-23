<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan Admin – @yield('title', 'Administration')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --teal:        #0F766E;
            --teal-dark:   #064e3b;
            --teal-light:  #F0FDFA;
            --teal-mid:    #14b8a6;
            --red:         #CE1126;
            --yellow:      #FCD116;
            --green-ok:    #007A4D;
            --text:        #1F2937;
            --text-light:  #6b7280;
            --border:      #e5e7eb;
            --bg:          #f4f8f8;
            --white:       #ffffff;
            --sidebar-w:   260px;
            --topbar-bg:   #ffffff;
            --card-bg:     #ffffff;
            --sb-bg:       #064e3b;
            --sb-bg2:      #065f46;
            --sb-text:     rgba(255,255,255,1);
            --sb-text-act: #ffffff;
            --sb-active:   rgba(255,255,255,0.12);
            --sb-hover:    rgba(255,255,255,0.06);
            --sb-border:   rgba(255,255,255,0.08);
            --sb-label:    rgba(255,255,255,0.3);
        }

        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* Croix fermeture sidebar mobile */
        .sidebar-close-btn {
            display: none;
            position: absolute;
            top: 12px; right: 12px;
            z-index: 10;
            background: rgba(255,255,255,0.15);
            border: none; cursor: pointer;
            color: white;
            width: 32px; height: 32px;
            border-radius: 8px;
            align-items: center; justify-content: center;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .sidebar-close-btn:hover { background: rgba(255,255,255,0.25); }
        .sidebar-close-btn svg { width: 16px; height: 16px; }

        @media (max-width: 768px) {
            .sidebar.open .sidebar-close-btn { display: flex; }
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sb-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            z-index: 200;
            overflow: hidden;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1);
        }
        .sidebar::before {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            bottom: 80px; right: -60px;
            pointer-events: none;
        }
        .sidebar::after {
            content: '';
            position: absolute;
            width: 120px; height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            bottom: 200px; right: 20px;
            pointer-events: none;
        }

        /* Bouton croix fermeture — mobile uniquement */
        .sidebar-close {
            display: none;
            position: absolute;
            top: 16px; right: 16px;
            z-index: 10;
            background: rgba(255,255,255,0.1);
            border: none; cursor: pointer;
            color: white;
            width: 32px; height: 32px;
            border-radius: 8px;
            align-items: center; justify-content: center;
            transition: background 0.2s;
        }
        .sidebar-close:hover { background: rgba(255,255,255,0.2); }
        .sidebar-close svg { width: 18px; height: 18px; }

        /* Logo */
        .sidebar-logo {
            padding: 20px 20px 18px;
            border-bottom: 1px solid var(--sb-border);
            display: flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
        }
        .sidebar-logo-img {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: white;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
            flex-shrink: 0;
            overflow: hidden;
        }
        .sidebar-logo-img img { width: 34px; height: 34px; object-fit: contain; }
        .sidebar-logo-text {
            font-size: 17px;
            font-weight: 800;
            color: white;
            line-height: 1;
        }
        .sidebar-logo-badge {
            font-size: 9px;
            font-weight: 700;
            color: #5eead4;
            background: rgba(94,234,212,0.12);
            border: 1px solid rgba(94,234,212,0.25);
            border-radius: 4px;
            padding: 2px 6px;
            letter-spacing: 0.08em;
            margin-top: 4px;
            display: inline-block;
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            padding: 12px 10px;
            display: flex;
            flex-direction: column;
            gap: 1px;
            overflow-y: auto;
        }
        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--sb-label);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 14px 10px 6px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--sb-text);
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            position: relative;
        }
        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; }
        .nav-item:hover { background: var(--sb-hover); color: white; }
        .nav-item.active {
            background: var(--sb-active);
            color: white;
            font-weight: 700;
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: #5eead4;
            border-radius: 0 3px 3px 0;
        }
        .nav-badge {
            margin-left: auto;
            background: var(--red);
            color: white;
            font-size: 10px;
            font-weight: 800;
            padding: 1px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }
        .nav-badge.yellow { background: var(--yellow); color: #78350f; }

        /* Footer */
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid var(--sb-border);
        }
        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
        }
        .admin-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 800; color: white;
            flex-shrink: 0;
        }
        .admin-name { font-size: 13px; font-weight: 700; color: white; line-height: 1.2; }
        .admin-role { font-size: 10px; color: #5eead4; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .logout-btn {
            background: rgba(206,17,38,0.2); border: none; cursor: pointer;
            color: #fca5a5; padding: 6px; border-radius: 8px;
            display: flex; align-items: center; transition: all 0.2s;
            margin-left: auto; flex-shrink: 0;
        }
        .logout-btn:hover { background: var(--red); color: white; }
        .logout-btn svg { width: 16px; height: 16px; }

        /* OVERLAY mobile */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 199;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .sidebar-overlay.active { opacity: 1; }

        /* MAIN */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* TOPBAR */
        .topbar {
            height: 64px;
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            gap: 12px;
        }

        /* Bouton hamburger (mobile) */
        .hamburger-btn {
            display: none;
            background: none; border: none; cursor: pointer;
            color: var(--text); padding: 6px;
            border-radius: 8px; align-items: center; justify-content: center;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .hamburger-btn:hover { background: var(--teal-light); color: var(--teal); }
        .hamburger-btn svg { width: 22px; height: 22px; }

        .topbar-title { font-size: 18px; font-weight: 800; color: var(--text); flex: 1; }
        .topbar-title span { color: var(--teal); }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-actions { display: flex; align-items: center; gap: 8px; }
        .topbar-btn {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: var(--bg);
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--text-light);
            position: relative;
            transition: all 0.2s;
            text-decoration: none;
        }
        .topbar-btn:hover { background: var(--teal-light); color: var(--teal); border-color: rgba(15,118,110,0.2); }
        .topbar-btn svg { width: 16px; height: 16px; }
        .topbar-notif-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--red);
            border: 1.5px solid white;
        }

        /* CONTENT */
        .content { flex: 1; padding: 24px 28px; }

        /* BANNERS */
        .warning-banner {
            display: flex; align-items: center; gap: 12px;
            background: #fffbeb; border: 1px solid #fde68a;
            border-left: 4px solid var(--yellow);
            border-radius: 10px; padding: 12px 16px;
            margin-bottom: 20px; font-size: 13px;
            color: #92400e; font-weight: 500;
        }
        .warning-banner svg { width: 18px; height: 18px; flex-shrink: 0; }
        .close-btn { margin-left: auto; background: none; border: none; color: #92400e; cursor: pointer; font-size: 16px; opacity: 0.6; }
        .close-btn:hover { opacity: 1; }

        /* CARDS */
        .card { background: var(--card-bg); border: 1.5px solid var(--border); border-radius: 16px; overflow: hidden; }
        .card-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid var(--border); }
        .card-title { font-size: 14px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 8px; }
        .card-title svg { width: 16px; height: 16px; color: var(--teal); }

        .stat-card { background: var(--card-bg); border: 1.5px solid var(--border); border-radius: 16px; padding: 20px; transition: all 0.25s; }
        .stat-card:hover { border-color: rgba(15,118,110,0.3); box-shadow: 0 4px 20px rgba(15,118,110,0.08); transform: translateY(-2px); }
        .stat-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
        .stat-icon svg { width: 20px; height: 20px; }
        .stat-icon.teal   { background: var(--teal-light); color: var(--teal); }
        .stat-icon.red    { background: #fef2f2; color: #dc2626; }
        .stat-icon.yellow { background: #fffbeb; color: #d97706; }
        .stat-icon.blue   { background: #eff6ff; color: #2563eb; }
        .stat-value { font-size: 30px; font-weight: 900; color: var(--text); letter-spacing: -0.5px; }
        .stat-label { font-size: 12px; color: var(--text-light); margin-top: 4px; font-weight: 500; }
        .stat-change { display: inline-flex; align-items: center; gap: 3px; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px; margin-top: 8px; }
        .stat-change.up   { background: #f0fdf4; color: #16a34a; }
        .stat-change.down { background: #fef2f2; color: #dc2626; }
        .stat-change svg  { width: 10px; height: 10px; }

        .btn-primary { padding: 8px 16px; background: var(--teal); color: white; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; box-shadow: 0 2px 8px rgba(15,118,110,0.25); }
        .btn-primary:hover { background: var(--teal-dark); }
        .btn-primary svg { width: 14px; height: 14px; }
        .btn-secondary { padding: 8px 16px; background: var(--bg); color: var(--text-light); border: 1.5px solid var(--border); border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
        .btn-secondary:hover { color: var(--teal); border-color: rgba(15,118,110,0.3); background: var(--teal-light); }
        .btn-secondary svg { width: 14px; height: 14px; }

        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .badge-green  { background: #f0fdf4; color: #16a34a; }
        .badge-red    { background: #fef2f2; color: #dc2626; }
        .badge-yellow { background: #fffbeb; color: #d97706; }
        .badge-gray   { background: #f9fafb; color: #6b7280; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1.5px solid var(--border); background: var(--bg); }
        td { padding: 12px 14px; font-size: 13px; color: var(--text); border-bottom: 1px solid var(--border); }
        tr:hover td { background: var(--teal-light); }
        tr:last-child td { border-bottom: none; }

        .refresh-dot { width: 6px; height: 6px; border-radius: 50%; background: #16a34a; display: inline-block; margin-right: 4px; animation: blink 2s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }

        /* ── RESPONSIVE MOBILE ── */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay {
                display: block;
            }
            .hamburger-btn {
                display: flex;
            }
            .main {
                margin-left: 0;
            }
            .topbar {
                padding: 0 12px;
                gap: 8px;
            }
            .topbar-title {
                font-size: 15px;
            }
            .topbar-actions-wrap { display: none !important; }
            .content {
                padding: 16px;
            }
        }
    </style>
    @yield('styles')
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>

{{-- OVERLAY --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">

    {{-- Croix fermeture mobile --}}
    <button class="sidebar-close-btn" id="sidebarCloseBtn" onclick="closeSidebar()" aria-label="Fermer">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
        <div class="sidebar-logo-img">
            <img src="{{ asset('images/logo.png') }}" alt="VeriScan">
        </div>
        <div>
            <div class="sidebar-logo-text">VeriScan</div>
            <div class="sidebar-logo-badge">ADMIN</div>
        </div>
    </a>

    <nav class="sidebar-nav">

        <div class="nav-section-label">Principal</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Tableau de bord
        </a>

        <a href="{{ route('admin.fabricants.index') }}"
           class="nav-item {{ request()->routeIs('admin.fabricants.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Fabricants
            @php $enAttente = \App\Models\Fabricant::where('statut','en_attente')->count(); @endphp
            @if($enAttente > 0)
            <span class="nav-badge yellow">{{ $enAttente }}</span>
            @endif
        </a>

        <a href="{{ route('admin.signalements.index') }}"
           class="nav-item {{ request()->routeIs('admin.signalements.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Signalements
            @php $enCours = \App\Models\Signalement::where('statut','en_cours')->count(); @endphp
            @if($enCours > 0)
            <span class="nav-badge">{{ $enCours }}</span>
            @endif
        </a>

        <div class="nav-section-label">Analyse</div>

        <a href="{{ route('admin.carte') }}"
           class="nav-item {{ request()->routeIs('admin.carte') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            Carte des risques
        </a>

        <a href="{{ route('admin.rapports.index') }}"
           class="nav-item {{ request()->routeIs('admin.rapports.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Rapports
        </a>

        <div class="nav-section-label">Système</div>

        <a href="{{ route('admin.parametres.index') }}"
           class="nav-item {{ request()->routeIs('admin.parametres.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Paramètres
        </a>

    </nav>

    <div class="sidebar-footer">
        <div class="admin-info">
            @php $adminUser = Auth::guard('admin')->user(); @endphp
            @if($adminUser->photo)
                <img src="{{ asset('storage/' . $adminUser->photo) }}"
                     style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid rgba(255,255,255,0.2);"
                     alt="Photo admin">
            @else
                <div class="admin-avatar">
                    {{ strtoupper(substr($adminUser->nom ?? 'A', 0, 1)) }}
                </div>
            @endif
            <div style="flex:1;min-width:0;">
                <div class="admin-name">{{ $adminUser->nom ?? 'Admin' }}</div>
                <div class="admin-role">{{ $adminUser->role ?? 'super_admin' }}</div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Déconnexion">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

</aside>

{{-- MAIN --}}
<div class="main">

    <header class="topbar">
        {{-- Bouton hamburger (mobile) --}}
        <button class="hamburger-btn" id="hamburgerBtn" onclick="openSidebar()" aria-label="Ouvrir le menu">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="topbar-title">@yield('topbar-title', 'Administration')</div>
        <div class="topbar-right">
            <div class="topbar-actions-wrap topbar-actions">@yield('topbar-actions')</div>
            <a href="{{ route('admin.signalements.index') }}" class="topbar-btn" title="Signalements">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                @if($enCours ?? 0 > 0)<span class="topbar-notif-dot"></span>@endif
            </a>
        </div>
    </header>

    <main class="content">

        @if(session('warning'))
        <div class="warning-banner">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ session('warning') }}
            <button class="close-btn" onclick="this.parentElement.remove()">✕</button>
        </div>
        @endif

        @if(session('success'))
        <div class="warning-banner" style="background:#f0fdf4;border-color:#bbf7d0;border-left-color:#16a34a;color:#15803d;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
            <button class="close-btn" style="color:#15803d" onclick="this.parentElement.remove()">✕</button>
        </div>
        @endif

        @yield('content')
    </main>
</div>

@yield('scripts')

<script>
    const iconHamburger = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>`;
    const iconClose = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`;

    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('hamburgerBtn').innerHTML = iconClose;
        const overlay = document.getElementById('sidebarOverlay');
        overlay.style.display = 'block';
        requestAnimationFrame(() => overlay.classList.add('active'));
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('hamburgerBtn').innerHTML = iconHamburger;
        const overlay = document.getElementById('sidebarOverlay');
        overlay.classList.remove('active');
        setTimeout(() => { overlay.style.display = 'none'; }, 300);
        document.body.style.overflow = '';
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });
</script>
<script src="{{ asset('js/responsive.js') }}"></script>
</body>
</html>