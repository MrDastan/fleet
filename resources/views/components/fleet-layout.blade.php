<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/fleet.css') }}">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('images/logo.png') }}" alt="MSD" style="height:38px;width:auto;display:block;margin-bottom:4px;" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <div style="display:none;font-size:20px;font-weight:800;letter-spacing:-1px;color:#1a1a1a">MSD<span style="color:#e85d00">.</span></div>
        <div class="sidebar-sub">Sistem Pengurusan Kenderaan</div>
    </div>

    <div class="sidebar-role">
        <div style="font-size:11px;color:rgba(255,255,255,0.4);margin-bottom:3px">Log masuk sebagai</div>
        <div style="color:#fff;font-size:12px;font-weight:600">{{ auth()->user()->roles->first()?->name ? ucfirst(auth()->user()->roles->first()->name) : 'User' }}</div>
    </div>

    <nav id="sideNav">
        @php
            $role = auth()->user()->roles->first()?->name ?? 'staff';
            $navConfigs = config('fleet.nav');
            $sections = $navConfigs[$role] ?? $navConfigs['staff'];
        @endphp

        @foreach($sections as $section)
            <div class="nav-section">{{ $section['section'] }}</div>
            @foreach($section['items'] as $item)
                <a href="{{ route($item['route']) }}"
                   class="nav-item {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                    <span>{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                    @if(!empty($item['badge']))
                        <span class="badge {{ ($item['badgeType'] ?? '') === 'warn' ? 'warn' : '' }}">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        @endforeach
    </nav>
</aside>

<!-- SIDEBAR OVERLAY (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- MAIN CONTENT -->
<div class="main">
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:10px">
            <button class="hamburger" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
            <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
        </div>
        <div class="topbar-right">
            <a href="{{ route('reminders.index') }}" class="topbar-notif" title="Peringatan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                <div class="notif-dot"></div>
            </a>
            <div class="topbar-user" onclick="document.getElementById('logoutDropdown').classList.toggle('show')">
                <div class="avatar">{{ auth()->user()->avatar_initials ?? 'U' }}</div>
                <div>
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role-label">{{ auth()->user()->position ?? ucfirst($role) }}</div>
                </div>
                <span style="font-size:11px;color:#636e72;margin-left:4px">▾</span>
            </div>
            <div id="logoutDropdown" class="logout-dropdown">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">↩ Log Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-info" style="margin-bottom:16px">
                <span>✅</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="margin-bottom:16px">
                <span>🔴</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        {{ $slot }}
    </div>
</div>

<!-- BOTTOM NAV (mobile) -->
<nav class="bottom-nav" id="bottomNav">
    <div class="bottom-nav-inner">
        @php
            $bottomNavConfigs = config('fleet.bottom_nav');
            $bottomItems = $bottomNavConfigs[$role] ?? $bottomNavConfigs['staff'];
        @endphp
        @foreach($bottomItems as $item)
            <a href="{{ route($item['route']) }}"
               class="bottom-nav-item {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
                @if(!empty($item['badge']))
                    <div class="bnav-dot"></div>
                @endif
                <div class="icon">{{ $item['icon'] }}</div>
                <div>{{ $item['label'] }}</div>
            </a>
        @endforeach
    </div>
</nav>

<!-- TOAST -->
<div class="toast" id="toast">
    <span>✅</span>
    <span id="toastMsg"></span>
</div>

<script>
function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    const btn = document.getElementById('hamburgerBtn');
    const ov = document.getElementById('sidebarOverlay');
    const isOpen = sb.classList.toggle('open');
    btn.classList.toggle('open', isOpen);
    ov.classList.toggle('show', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
}

function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('hamburgerBtn').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

document.addEventListener('click', function(e) {
    const dd = document.getElementById('logoutDropdown');
    const tu = document.querySelector('.topbar-user');
    if (dd && !tu.contains(e.target)) dd.classList.remove('show');
});

function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}

@if(session('success'))
    showToast('{{ session('success') }}');
@endif
</script>
</body>
</html>
