<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/fleet.css') }}">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-mark">M</div>
        <div>
            <div class="sidebar-logo-text">MSD<span>.</span>Fleet</div>
            <div class="sidebar-sub">Pengurusan Kenderaan</div>
        </div>
    </div>

    <div class="sidebar-role">
        <div class="avatar">{{ auth()->user()->avatar_initials ?? 'U' }}</div>
        <div style="min-width:0">
            <div class="name">{{ auth()->user()->name }}</div>
            <div class="role-name">{{ auth()->user()->position ?? ucfirst(auth()->user()->roles->first()?->name ?? 'User') }}</div>
        </div>
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
                    <span class="nav-icon"><x-icon :name="$item['icon']" :size="18" /></span>
                    <span class="nav-label">{{ $item['label'] }}</span>
                    @if(!empty($item['badge']))
                        <span class="badge {{ ($item['badgeType'] ?? '') === 'warn' ? 'warn' : '' }}">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        @endforeach
    </nav>

    <div class="sidebar-foot">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-foot-inner">
                <x-icon name="log-out" :size="17" /><span>Log Keluar</span>
            </button>
        </form>
    </div>
</aside>

<!-- SIDEBAR OVERLAY (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- MAIN CONTENT -->
<div class="main">
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:14px;flex:1;min-width:0">
            <button class="hamburger" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
            <div class="topbar-search">
                <x-icon name="search" :size="17" />
                <input placeholder="Cari plat, pemandu, saman...">
            </div>
        </div>
        <div class="topbar-right">
            <a href="{{ route('notifications.index') }}" class="topbar-notif" title="Notifikasi">
                <x-icon name="bell" :size="17" />
                @if(auth()->user()->unreadNotifications()->count() > 0)
                    <div class="notif-dot"></div>
                @endif
            </a>
            <a href="{{ route('reminders.index') }}" class="topbar-notif" title="Peringatan">
                <x-icon name="calendar" :size="17" />
            </a>
            <div class="topbar-user" onclick="document.getElementById('logoutDropdown').classList.toggle('show')">
                <div class="avatar">{{ auth()->user()->avatar_initials ?? 'U' }}</div>
                <div>
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role-label">{{ auth()->user()->position ?? ucfirst($role) }}</div>
                </div>
                <span style="font-size:11px;color:var(--muted);margin-left:4px">▾</span>
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
                <x-icon name="check" :size="16" />
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="margin-bottom:16px">
                <x-icon name="triangle-alert" :size="16" />
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
                <div class="icon"><x-icon :name="$item['icon']" :size="20" /></div>
                <div>{{ $item['label'] }}</div>
            </a>
        @endforeach
    </div>
</nav>

<!-- TOAST -->
<div class="toast" id="toast">
    <x-icon name="check" :size="15" />
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
