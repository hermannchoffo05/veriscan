<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeriScan – @yield('title', 'Espace Fabricant')</title>
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
        }

        body.dark {
            --bg:          #0f172a;
            --white:       #1e293b;
            --text:        #e2e8f0;
            --text-light:  #94a3b8;
            --border:      #334155;
            --teal-light:  #134e4a;
            --topbar-bg:   #1e293b;
            --card-bg:     #1e293b;
        }

        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            transition: background 0.3s, color 0.3s;
        }

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

        .sidebar {
            width: var(--sidebar-w);
            background: var(--teal-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0; top: 0; bottom: 0;
            z-index: 100;
            overflow: hidden;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1);
        }
        .sidebar::before {
            content: '';
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 20px 20px;
            pointer-events: none;
        }
        .sidebar::after {
            content: '';
            position: absolute;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, rgba(20,184,166,0.18), rgba(6,78,59,0.05));
            bottom: -100px; right: -100px;
            pointer-events: none;
        }
        .sidebar-logo {
            padding: 28px 24px 20px;
            display: flex; align-items: center; gap: 12px;
            position: relative; z-index: 1;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-logo-circle {
            width: 44px; height: 44px;
            border-radius: 50%; background: white;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        }
        .sidebar-logo-circle img { width: 38px; height: 38px; object-fit: contain; }
        .sidebar-brand { color: white; }
        .sidebar-brand strong { display: block; font-size: 16px; font-weight: 800; }
        .sidebar-brand span { font-size: 10.5px; color: rgba(255,255,255,0.5); font-weight: 500; }

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

        .sidebar-nav {
            flex: 1; padding: 16px 12px;
            position: relative; z-index: 1;
            overflow-y: auto;
        }
        .nav-section-label {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: rgba(255,255,255,0.35);
            padding: 12px 12px 6px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 14px; border-radius: 12px;
            color: rgba(255,255,255,1);
            font-size: 13.5px; font-weight: 500;
            cursor: pointer; text-decoration: none;
            transition: all 0.2s; margin-bottom: 2px;
            position: relative;
        }
        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: white; }
        .nav-item.active {
            background: var(--teal); color: white;
            font-weight: 700; box-shadow: 0 4px 12px rgba(15,118,110,0.4);
        }
        .nav-item .badge {
            margin-left: auto; background: var(--yellow);
            color: #1a1a00; font-size: 10px; font-weight: 800;
            padding: 2px 7px; border-radius: 20px;
        }
        .nav-item .badge.red { background: var(--red); color: white; }

        .sidebar-user {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.08);
            position: relative; z-index: 1;
        }
        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            background: rgba(255,255,255,0.07);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--teal);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; color: white;
            flex-shrink: 0; overflow: hidden; padding: 0;
        }
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .user-info { flex: 1; min-width: 0; }
        .user-info strong { display: block; font-size: 12.5px; color: white; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-info span { font-size: 11px; color: rgba(255,255,255,0.45); }
        .logout-btn {
            background: rgba(206,17,38,0.2); border: none; cursor: pointer;
            color: var(--red); padding: 6px; border-radius: 8px;
            display: flex; align-items: center; transition: all 0.2s;
        }
        .logout-btn:hover { background: var(--red); color: white; }
        .logout-btn svg { width: 16px; height: 16px; }

        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .sidebar-overlay.active { opacity: 1; }

        .main {
            margin-left: var(--sidebar-w);
            flex: 1; display: flex;
            flex-direction: column; min-height: 100vh;
        }

        .topbar {
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--border);
            padding: 0 32px; height: 64px;
            display: flex; align-items: center; gap: 16px;
            position: sticky; top: 0; z-index: 50;
            transition: background 0.3s, border-color 0.3s;
        }

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

        .topbar-title { font-size: 17px; font-weight: 800; color: var(--text); flex: 1; }
        .topbar-title span { color: var(--teal); }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }
        .topbar-search {
            display: flex; align-items: center; gap: 8px;
            background: var(--bg); border: 1.5px solid var(--border);
            border-radius: 10px; padding: 7px 14px;
            font-size: 13px; color: var(--text-light); width: 220px;
            transition: background 0.3s, border-color 0.3s;
        }
        .topbar-search svg { width: 14px; height: 14px; color: var(--text-light); flex-shrink: 0; }
        .topbar-search input { border: none; background: none; outline: none; font-family: inherit; font-size: 13px; color: var(--text); width: 100%; }
        .notif-btn {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--bg); border: 1.5px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; position: relative; transition: all 0.2s;
        }
        .notif-btn:hover { border-color: var(--teal); background: var(--teal-light); }
        .notif-btn svg { width: 16px; height: 16px; color: var(--text-light); }
        .notif-dot { position: absolute; top: 6px; right: 6px; width: 8px; height: 8px; border-radius: 50%; background: var(--red); border: 2px solid white; }
        .topbar-date {
            font-size: 12px; color: var(--text-light);
            background: var(--teal-light); border: 1px solid rgba(15,118,110,0.15);
            padding: 5px 12px; border-radius: 8px; font-weight: 600;
        }

        .lang-switcher {
            display: flex; align-items: center; gap: 4px;
            background: var(--bg); border: 1.5px solid var(--border);
            border-radius: 10px; padding: 5px 10px;
            transition: background 0.3s, border-color 0.3s;
        }
        .lang-btn {
            font-size: 12px; font-weight: 700;
            color: var(--text-light); text-decoration: none;
            padding: 2px 4px; border-radius: 6px;
            transition: all 0.2s;
        }
        .lang-btn:hover { color: var(--teal); }
        .lang-btn.active { color: var(--teal); background: var(--teal-light); padding: 2px 6px; }
        .lang-sep { color: var(--border); font-size: 12px; }

        .dark-toggle {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--bg); border: 1.5px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s;
            color: var(--text-light);
        }
        .dark-toggle:hover { border-color: var(--teal); background: var(--teal-light); color: var(--teal); }
        .dark-toggle svg { width: 16px; height: 16px; }
        .icon-moon { display: block; }
        .icon-sun  { display: none; }
        body.dark .icon-moon { display: none; }
        body.dark .icon-sun  { display: block; }

        .warning-banner {
            display: flex; align-items: center; gap: 12px;
            background: #fefce8; border: 1px solid #fde68a;
            border-left: 4px solid #f59e0b; border-radius: 12px;
            padding: 14px 18px; margin-bottom: 24px;
            font-size: 13.5px; color: #92400e;
        }
        .warning-banner svg { width: 18px; height: 18px; color: #f59e0b; flex-shrink: 0; }
        .warning-banner .close-btn {
            margin-left: auto; background: none; border: none;
            cursor: pointer; color: #b45309; padding: 2px; border-radius: 4px;
            display: flex; align-items: center;
        }
        .warning-banner .close-btn svg { width: 14px; height: 14px; }

        .content { padding: 28px 32px; flex: 1; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); z-index: 200; }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block; }
            .hamburger-btn { display: flex; }
            .main { margin-left: 0; }
            .topbar { padding: 0 12px; gap: 8px; }
            .topbar-title { font-size: 14px; }
            .topbar-date { display: none; }
            .topbar-actions-wrap { display: none !important; }
            .content { padding: 16px; }
        }

        /* ================================================================
           CHATBOT ASSISTANT VERISCAN
        ================================================================ */
        #vs-chat-bubble {
            position: fixed;
            bottom: 28px; right: 28px;
            z-index: 9999;
            display: flex; flex-direction: column; align-items: flex-end;
            gap: 12px;
            pointer-events: none;
        }

        #vs-chat-bubble > * {
            pointer-events: all;
        }

        #vs-chat-toggle {
            width: 56px; height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px rgba(15,118,110,0.4);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }
        #vs-chat-toggle:hover { transform: scale(1.08); box-shadow: 0 12px 32px rgba(15,118,110,0.5); }
        #vs-chat-toggle svg { width: 26px; height: 26px; color: white; transition: opacity 0.2s; }
        #vs-chat-toggle .icon-chat { display: flex; }
        #vs-chat-toggle .icon-close-chat { display: none; }
        #vs-chat-toggle.open .icon-chat { display: none; }
        #vs-chat-toggle.open .icon-close-chat { display: flex; }

        /* Pastille notification */
        #vs-chat-notif {
            position: absolute;
            top: -2px; right: -2px;
            width: 18px; height: 18px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid white;
            font-size: 10px; font-weight: 800;
            color: white;
            display: flex; align-items: center; justify-content: center;
        }

        /* Fenêtre du chat */
        #vs-chat-window {
            width: 360px;
            max-height: 520px;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 4px 16px rgba(0,0,0,0.08);
            display: flex; flex-direction: column;
            overflow: hidden;
            transform: scale(0.85) translateY(20px);
            opacity: 0;
            pointer-events: none;
            transition: transform 0.25s cubic-bezier(.4,0,.2,1), opacity 0.25s;
            border: 1px solid var(--border);
        }
        #vs-chat-window.open {
            transform: scale(1) translateY(0);
            opacity: 1;
            pointer-events: all;
        }

        /* Header du chat */
        .vs-chat-header {
            background: linear-gradient(135deg, #0f766e, #0d6560);
            padding: 16px 18px;
            display: flex; align-items: center; gap: 12px;
            flex-shrink: 0;
        }
        .vs-chat-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .vs-chat-avatar svg { width: 22px; height: 22px; color: white; }
        .vs-chat-header-info { flex: 1; }
        .vs-chat-header-info strong { display: block; font-size: 14px; font-weight: 700; color: white; }
        .vs-chat-header-info span { font-size: 11px; color: rgba(255,255,255,0.7); display: flex; align-items: center; gap: 4px; }
        .vs-online-dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; display: inline-block; }

        /* Messages */
        .vs-chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex; flex-direction: column; gap: 12px;
            background: var(--bg);
        }
        .vs-msg {
            max-width: 82%;
            font-size: 13px; line-height: 1.5;
            animation: fadeUp 0.2s ease;
        }
        .vs-msg-bot {
            align-self: flex-start;
        }
        .vs-msg-bot .vs-msg-bubble {
            background: var(--white);
            color: var(--text);
            border-radius: 4px 16px 16px 16px;
            padding: 10px 14px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .vs-msg-user {
            align-self: flex-end;
        }
        .vs-msg-user .vs-msg-bubble {
            background: var(--teal);
            color: white;
            border-radius: 16px 4px 16px 16px;
            padding: 10px 14px;
        }
        .vs-msg-time {
            font-size: 10px; color: var(--text-light);
            margin-top: 4px; padding: 0 4px;
        }
        .vs-msg-bot .vs-msg-time { text-align: left; }
        .vs-msg-user .vs-msg-time { text-align: right; }

        /* Suggestions rapides */
        .vs-suggestions {
            display: flex; flex-wrap: wrap; gap: 6px;
            padding: 0 16px 10px;
            background: var(--bg);
        }
        .vs-suggestion-btn {
            font-size: 11.5px; font-weight: 600;
            color: var(--teal);
            background: var(--teal-light);
            border: 1px solid rgba(15,118,110,0.2);
            border-radius: 20px;
            padding: 5px 12px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .vs-suggestion-btn:hover { background: var(--teal); color: white; border-color: var(--teal); }

        /* Indicateur de frappe */
        .vs-typing {
            display: flex; align-items: center; gap: 4px;
            padding: 10px 14px;
            background: var(--white);
            border-radius: 4px 16px 16px 16px;
            border: 1px solid var(--border);
            width: fit-content;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .vs-typing span {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #9ca3af;
            animation: bounce 1.2s infinite;
        }
        .vs-typing span:nth-child(2) { animation-delay: 0.2s; }
        .vs-typing span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes bounce {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-6px); }
        }

        /* Input zone */
        .vs-chat-input {
            padding: 12px 16px;
            border-top: 1px solid var(--border);
            display: flex; gap: 8px; align-items: center;
            background: var(--white);
            flex-shrink: 0;
        }
        .vs-chat-input input {
            flex: 1;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 9px 14px;
            font-size: 13px;
            font-family: inherit;
            color: var(--text);
            background: var(--bg);
            outline: none;
            transition: border-color 0.2s;
        }
        .vs-chat-input input:focus { border-color: var(--teal); background: var(--white); }
        .vs-chat-input input::placeholder { color: var(--text-light); }
        .vs-send-btn {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: var(--teal);
            border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .vs-send-btn:hover { background: #0d6560; }
        .vs-send-btn svg { width: 16px; height: 16px; color: white; }
        .vs-send-btn:disabled { background: #d1d5db; cursor: not-allowed; }

        @media (max-width: 480px) {
            #vs-chat-window { width: calc(100vw - 32px); }
            #vs-chat-bubble { bottom: 16px; right: 16px; }
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
    <button class="sidebar-close-btn" id="sidebarCloseBtn" onclick="closeSidebar()" aria-label="Fermer">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <div class="sidebar-logo">
        <div class="sidebar-logo-circle">
            <img src="{{ asset('images/logo.png') }}" alt="VeriScan">
        </div>
        <div class="sidebar-brand">
            <strong>VeriScan</strong>
            <span>{{ __('messages.espace_fabricant') }}</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">{{ __('messages.principal') }}</div>

        <a href="{{ route('fabricant.dashboard') }}" class="nav-item {{ request()->routeIs('fabricant.dashboard') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            {{ __('messages.dashboard') }}
        </a>

        <a href="{{ route('fabricant.produits.index') }}" class="nav-item {{ request()->routeIs('fabricant.produits*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
            {{ __('messages.mes_produits') }}
        </a>

        <a href="{{ route('fabricant.qrcodes.index') }}" class="nav-item {{ request()->routeIs('fabricant.qrcodes*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zm12 0h4v4h-4V4zM4 16h4v4H4v-4zm8-12h1m4 4h2m-6 4h-2v4m0-8v1M12 12h4"/></svg>
            {{ __('messages.qr_codes') }}
            <span class="badge">{{ \App\Models\QrCode::whereHas('lot.produit', fn($q) => $q->where('fabricant_id', Auth::guard('fabricant')->id()))->count() }}</span>
        </a>

        <a href="{{ route('fabricant.statistiques.index') }}" class="nav-item {{ request()->routeIs('fabricant.statistiques*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            {{ __('messages.statistiques') }}
        </a>

        <a href="{{ route('fabricant.carte.index') }}" class="nav-item {{ request()->routeIs('fabricant.carte.index') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            {{ __('messages.carte_risques') }}
        </a>

        <div class="nav-section-label" style="margin-top:8px;">{{ __('messages.alertes') }}</div>

        <a href="{{ route('fabricant.signalements.index') }}" class="nav-item {{ request()->routeIs('fabricant.signalements*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ __('messages.signalements') }}
            <span class="badge red">{{ \App\Models\Signalement::where(function($q) { $fabricantId = Auth::guard('fabricant')->id(); $qrcodeIds = \App\Models\QrCode::whereIn('lot_id', \App\Models\Lot::whereIn('produit_id', \App\Models\Produit::where('fabricant_id', $fabricantId)->pluck('id'))->pluck('id'))->pluck('id'); $q->whereIn('qr_code_id', $qrcodeIds)->orWhereNull('qr_code_id'); })->where('statut', 'en_cours')->count() }}</span>
        </a>

        <a href="{{ route('fabricant.rapports.index') }}" class="nav-item {{ request()->routeIs('fabricant.rapports*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            {{ __('messages.rapports') }}
        </a>

        <div class="nav-section-label" style="margin-top:8px;">{{ __('messages.compte') }}</div>

        <a href="{{ route('fabricant.profil') }}" class="nav-item {{ request()->routeIs('fabricant.profil') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            {{ __('messages.mon_profil') }}
        </a>

        <a href="{{ route('fabricant.parametres.index') }}" class="nav-item {{ request()->routeIs('fabricant.parametres*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
            {{ __('messages.parametres') }}
        </a>
    </nav>

    <div class="sidebar-user">
        <div class="user-card">
            <div class="user-avatar">
                @if(Auth::guard('fabricant')->user()->logo)
                    <img src="{{ Storage::url(Auth::guard('fabricant')->user()->logo) }}" alt="Logo">
                @else
                    {{ strtoupper(substr(Auth::guard('fabricant')->user()->nom_entreprise ?? 'F', 0, 1)) }}
                @endif
            </div>
            <div class="user-info">
                <strong>{{ Auth::guard('fabricant')->user()->nom_entreprise ?? 'Fabricant' }}</strong>
                <span>{{ __('messages.fabricant_certifie') }}</span>
            </div>
            <form method="POST" action="{{ route('fabricant.logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="{{ __('messages.deconnexion') }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<main class="main">
    <div class="topbar">
        <button class="hamburger-btn" id="hamburgerBtn" onclick="openSidebar()" aria-label="Ouvrir le menu">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="topbar-title">@yield('topbar-title')</div>

        <div class="topbar-actions-wrap" style="display:flex;align-items:center;gap:10px;">
            @yield('topbar-actions')
        </div>

        <div class="lang-switcher">
            <a href="{{ route('langue.changer', 'fr') }}" class="lang-btn {{ app()->getLocale() === 'fr' ? 'active' : '' }}">FR</a>
            <span class="lang-sep">|</span>
            <a href="{{ route('langue.changer', 'en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
        </div>

        <button class="dark-toggle" id="darkToggle" title="Changer le thème" onclick="toggleDark()">
            <svg class="icon-moon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
            <svg class="icon-sun" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="5"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
            </svg>
        </button>
    </div>

    <div class="content">
        @if(session('warning'))
            <div class="warning-banner" id="warningBanner">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span><strong>Accès refusé –</strong> {{ session('warning') }}</span>
                <button class="close-btn" onclick="document.getElementById('warningBanner').remove()">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
        @yield('content')
    </div>
</main>

{{-- ================================================================
     CHATBOT ASSISTANT VERISCAN
================================================================ --}}
<div id="vs-chat-bubble">
    {{-- Fenêtre --}}
    <div id="vs-chat-window">
        {{-- Header --}}
        <div class="vs-chat-header">
            <div class="vs-chat-avatar">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347a3.76 3.76 0 01-1.05 2.59A4.016 4.016 0 0112 21a4.016 4.016 0 01-2.841-1.163 3.76 3.76 0 01-1.05-2.59l-.347-.347z"/>
                </svg>
            </div>
            <div class="vs-chat-header-info">
                <strong>Assistant VeriScan</strong>
                <span><span class="vs-online-dot"></span> En ligne – Répond instantanément</span>
            </div>
        </div>

        {{-- Messages --}}
        <div class="vs-chat-messages" id="vsChatMessages">
            {{-- Message de bienvenue --}}
            <div class="vs-msg vs-msg-bot">
                <div class="vs-msg-bubble">
                    👋 Bonjour ! Je suis l'assistant VeriScan. Je peux vous aider à utiliser la plateforme.<br><br>
                    Que souhaitez-vous faire ?
                </div>
                <div class="vs-msg-time" id="vsBotWelcomeTime"></div>
            </div>
        </div>

        {{-- Suggestions rapides --}}
        <div class="vs-suggestions" id="vsSuggestions">
            <button class="vs-suggestion-btn" onclick="vsSendSuggestion(this.innerText)">{{ app()->getLocale() === 'fr' ? 'Créer un produit' : 'Create a product' }}</button>
            <button class="vs-suggestion-btn" onclick="vsSendSuggestion(this.innerText)">{{ app()->getLocale() === 'fr' ? 'Générer un QR code' : 'Generate a QR code' }}</button>
            <button class="vs-suggestion-btn" onclick="vsSendSuggestion(this.innerText)">{{ app()->getLocale() === 'fr' ? 'Créer un lot' : 'Create a batch' }}</button>
            <button class="vs-suggestion-btn" onclick="vsSendSuggestion(this.innerText)">{{ app()->getLocale() === 'fr' ? 'Voir mes statistiques' : 'View my statistics' }}</button>
        </div>

        {{-- Input --}}
        <div class="vs-chat-input">
            <input type="text" id="vsChatInput" placeholder="{{ app()->getLocale() === 'fr' ? 'Posez votre question...' : 'Ask your question...' }}" autocomplete="off" onkeydown="if(event.key==='Enter') vsSend()">
            <button class="vs-send-btn" id="vsSendBtn" onclick="vsSend()">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Bouton toggle --}}
    <button id="vs-chat-toggle" onclick="vsToggleChat()" aria-label="Assistant">
        <span id="vs-chat-notif">1</span>
        <span class="icon-chat">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
        </span>
        <span class="icon-close-chat">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </span>
    </button>
</div>

@yield('scripts')

<script>
    // ── Theme & Sidebar ────────────────────────────────────────────
    (function () {
        if (localStorage.getItem('veriscan_theme') === 'dark') {
            document.body.classList.add('dark');
        }
    })();

    function toggleDark() {
        const isDark = document.body.classList.toggle('dark');
        localStorage.setItem('veriscan_theme', isDark ? 'dark' : 'light');
    }

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
        if (window.innerWidth > 768) closeSidebar();
    });

    // ── CHATBOT VERISCAN ──────────────────────────────────────────
    const VS_LOCALE = '{{ app()->getLocale() }}';
    const VS_CSRF   = '{{ csrf_token() }}';
    let vsChatOpen  = false;
    let vsTyping    = false;

    // Heure de bienvenue
    document.getElementById('vsBotWelcomeTime').innerText = vsNow();

    // FAQ locale — réponses instantanées sans API
    const VS_FAQ = {
        fr: [
            {
                patterns: ['créer un produit', 'nouveau produit', 'ajouter produit', 'comment créer'],
                answer: '📦 Pour créer un produit :\n1. Allez dans <b>Mes Produits</b> dans la sidebar\n2. Cliquez sur <b>+ Nouveau produit</b>\n3. Remplissez le nom et la catégorie\n4. La description se génère automatiquement par IA !\n5. Cliquez sur <b>Créer le produit</b>'
            },
            {
                patterns: ['qr code', 'qr-code', 'générer qr', 'créer qr'],
                answer: '🔲 Pour générer des QR codes :\n1. Allez dans <b>QR Codes</b> dans la sidebar\n2. Cliquez sur <b>+ Nouveau QR Code</b>\n3. Sélectionnez le produit et le lot concerné\n4. Choisissez la quantité à générer\n5. Téléchargez vos QR codes en PDF ou PNG'
            },
            {
                patterns: ['lot', 'créer lot', 'nouveau lot', 'ajouter lot'],
                answer: '📋 Pour créer un lot :\n1. Allez dans <b>Mes Produits</b>\n2. Cliquez sur un produit existant\n3. Cliquez sur <b>+ Nouveau lot</b>\n4. Remplissez le numéro, les dates et la quantité\n5. Validez — le lot est créé et prêt pour les QR codes'
            },
            {
                patterns: ['statistique', 'stats', 'analyse', 'rapport'],
                answer: '📊 Vos statistiques sont disponibles dans :\n• <b>Statistiques</b> — graphiques de scans et activité\n• <b>Rapports</b> — téléchargez vos rapports PDF complets\n• <b>Dashboard</b> — vue d\'ensemble en temps réel'
            },
            {
                patterns: ['signalement', 'contrefaçon', 'faux', 'alerte'],
                answer: '🚨 Pour gérer les signalements :\n1. Allez dans <b>Signalements</b> dans la sidebar\n2. Consultez les alertes en cours\n3. Cliquez sur un signalement pour voir les détails\n4. Marquez-le comme traité une fois résolu'
            },
            {
                patterns: ['profil', 'logo', 'entreprise', 'modifier profil'],
                answer: '👤 Pour modifier votre profil :\n1. Cliquez sur <b>Mon Profil</b> dans la sidebar\n2. Mettez à jour vos informations d\'entreprise\n3. Uploadez votre logo (il apparaîtra sur les QR codes)\n4. Changez votre mot de passe si nécessaire'
            },
            {
                patterns: ['paiement', 'abonnement', 'plan', 'tarif', 'souscrire'],
                answer: '💳 Pour gérer votre abonnement :\n1. Allez sur la page <b>Tarifs</b>\n2. Choisissez le plan adapté à vos besoins\n3. Le paiement se fait via <b>CamPay</b> (Mobile Money)\n4. Votre compte est activé immédiatement après paiement'
            },
            {
                patterns: ['carte', 'carte risque', 'localisation', 'géographie'],
                answer: '🗺️ La <b>Carte des risques</b> vous montre :\n• La localisation géographique des scans\n• Les zones à risque de contrefaçon\n• La distribution de vos produits sur la carte'
            },
        ],
        en: [
            {
                patterns: ['create product', 'new product', 'add product'],
                answer: '📦 To create a product:\n1. Go to <b>My Products</b> in the sidebar\n2. Click <b>+ New Product</b>\n3. Fill in the name and category\n4. The description is auto-generated by AI!\n5. Click <b>Create Product</b>'
            },
            {
                patterns: ['qr code', 'generate qr', 'create qr'],
                answer: '🔲 To generate QR codes:\n1. Go to <b>QR Codes</b> in the sidebar\n2. Click <b>+ New QR Code</b>\n3. Select the product and batch\n4. Choose the quantity\n5. Download your QR codes as PDF or PNG'
            },
            {
                patterns: ['batch', 'create batch', 'new batch', 'lot'],
                answer: '📋 To create a batch:\n1. Go to <b>My Products</b>\n2. Click on an existing product\n3. Click <b>+ New Batch</b>\n4. Fill in the number, dates and quantity\n5. Save — the batch is ready for QR codes'
            },
            {
                patterns: ['statistics', 'stats', 'report', 'analysis'],
                answer: '📊 Your statistics are available in:\n• <b>Statistics</b> — scan charts and activity\n• <b>Reports</b> — download full PDF reports\n• <b>Dashboard</b> — real-time overview'
            },
            {
                patterns: ['report', 'counterfeit', 'fake', 'alert', 'signalement'],
                answer: '🚨 To manage reports:\n1. Go to <b>Reports</b> in the sidebar\n2. View ongoing alerts\n3. Click on a report for details\n4. Mark as resolved once handled'
            },
        ]
    };

    function vsNow() {
        return new Date().toLocaleTimeString(VS_LOCALE === 'fr' ? 'fr-FR' : 'en-US', { hour: '2-digit', minute: '2-digit' });
    }

    function vsToggleChat() {
        vsChatOpen = !vsChatOpen;
        const win = document.getElementById('vs-chat-window');
        const btn = document.getElementById('vs-chat-toggle');
        const notif = document.getElementById('vs-chat-notif');
        win.classList.toggle('open', vsChatOpen);
        btn.classList.toggle('open', vsChatOpen);
        if (vsChatOpen) {
            notif.style.display = 'none';
            document.getElementById('vsChatInput').focus();
        }
    }

    function vsAddMessage(text, isUser = false) {
        const msgs = document.getElementById('vsChatMessages');
        const div = document.createElement('div');
        div.className = 'vs-msg ' + (isUser ? 'vs-msg-user' : 'vs-msg-bot');
        // Convertir \n en <br>
        const formatted = text.replace(/\n/g, '<br>');
        div.innerHTML = `<div class="vs-msg-bubble">${formatted}</div><div class="vs-msg-time">${vsNow()}</div>`;
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function vsShowTyping() {
        const msgs = document.getElementById('vsChatMessages');
        const div = document.createElement('div');
        div.className = 'vs-msg vs-msg-bot';
        div.id = 'vsTypingIndicator';
        div.innerHTML = `<div class="vs-typing"><span></span><span></span><span></span></div>`;
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function vsHideTyping() {
        const t = document.getElementById('vsTypingIndicator');
        if (t) t.remove();
    }

    function vsFindFaqAnswer(question) {
        const q = question.toLowerCase();
        const faq = VS_FAQ[VS_LOCALE] || VS_FAQ['fr'];
        for (const item of faq) {
            if (item.patterns.some(p => q.includes(p))) {
                return item.answer;
            }
        }
        return null;
    }

    async function vsAskGemini(question) {
        try {
            const response = await fetch('{{ route("fabricant.chatbot.ask") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': VS_CSRF
                },
                body: JSON.stringify({ question, locale: VS_LOCALE })
            });
            const data = await response.json();
            return data.answer || (VS_LOCALE === 'fr'
                ? "Je n'ai pas pu trouver une réponse précise. Consultez notre documentation ou contactez le support."
                : "I couldn't find a precise answer. Please check our documentation or contact support.");
        } catch(e) {
            return VS_LOCALE === 'fr'
                ? "Je rencontre un problème de connexion. Réessayez dans quelques instants."
                : "I'm having a connection issue. Please try again in a moment.";
        }
    }

    async function vsSend() {
        const input = document.getElementById('vsChatInput');
        const question = input.value.trim();
        if (!question || vsTyping) return;

        // Cacher suggestions
        document.getElementById('vsSuggestions').style.display = 'none';

        // Message utilisateur
        vsAddMessage(question, true);
        input.value = '';
        vsTyping = true;
        document.getElementById('vsSendBtn').disabled = true;

        // Chercher dans FAQ locale d'abord
        vsShowTyping();
        const faqAnswer = vsFindFaqAnswer(question);

        if (faqAnswer) {
            // Réponse instantanée FAQ
            setTimeout(() => {
                vsHideTyping();
                vsAddMessage(faqAnswer, false);
                vsTyping = false;
                document.getElementById('vsSendBtn').disabled = false;
            }, 600);
        } else {
            // Fallback Gemini
            const answer = await vsAskGemini(question);
            vsHideTyping();
            vsAddMessage(answer, false);
            vsTyping = false;
            document.getElementById('vsSendBtn').disabled = false;
        }
    }

    function vsSendSuggestion(text) {
        document.getElementById('vsChatInput').value = text;
        vsSend();
    }
</script>
<script src="{{ asset('js/responsive.js') }}"></script>
</body>
</html>