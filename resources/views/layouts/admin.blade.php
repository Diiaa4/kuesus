<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Admin') - {{ $site['name'] }}</title>
</head>
<body>
<div class="admin" data-notify-url="{{ route('admin.notifications') }}" data-latest-order="{{ $latestOrderId }}">
    <div class="scrim" data-side-close></div>
    <aside class="side" id="admin-side" aria-label="Menu admin">
        <a class="brand" href="{{ route('admin.dashboard') }}">
            <span class="brand-text"><span class="logo">{{ $site['name'] }}</span><small>MANAJEMEN</small></span>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'on' : '' }}">
            <svg class="ico-s" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>Dashboard</a>
        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'on' : '' }}">
            <svg class="ico-s" viewBox="0 0 24 24"><path d="M6 7h12l1 13H5L6 7Z"/><path d="M9 7a3 3 0 0 1 6 0"/></svg>Pesanan
            <span class="badge" data-badge="orders" @if (! $newOrderCount) hidden @endif>{{ $newOrderCount }}</span></a>
        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'on' : '' }}">
            <svg class="ico-s" viewBox="0 0 24 24"><path d="M21 8l-9-5-9 5v8l9 5 9-5V8Z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>Produk</a>
        <a href="{{ route('admin.inventory.index') }}" class="{{ request()->routeIs('admin.inventory.*') ? 'on' : '' }}">
            <svg class="ico-s" viewBox="0 0 24 24"><rect x="6" y="4" width="12" height="17" rx="2"/><path d="M9 4h6v3H9zM9 14l2 2 4-4"/></svg>Inventori</a>
        <a href="{{ route('admin.chat') }}" class="{{ request()->routeIs('admin.chat*') ? 'on' : '' }}">
            <svg class="ico-s" viewBox="0 0 24 24"><path d="M4 5h16v11H9l-5 4V5Z"/></svg>Chat
            <span class="badge" data-badge="unread" @if (! $unreadCount) hidden @endif>{{ $unreadCount }}</span></a>
        <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'on' : '' }}">
            <svg class="ico-s" viewBox="0 0 24 24"><path d="M12 3l2.6 5.6 6 .7-4.5 4.1 1.2 6L12 16.4 6.7 19.4l1.2-6L3.4 9.3l6-.7L12 3Z"/></svg>Ulasan</a>
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'on' : '' }}">
            <svg class="ico-s" viewBox="0 0 24 24"><path d="M5 20V10M12 20V4M19 20v-7"/></svg>Laporan</a>
        <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'on' : '' }}">
            <svg class="ico-s" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19 12a7 7 0 0 0-.1-1.2l2-1.5-2-3.4-2.3 1a7 7 0 0 0-2.1-1.2L14 3h-4l-.5 2.7a7 7 0 0 0-2.1 1.2l-2.3-1-2 3.4 2 1.5A7 7 0 0 0 5 12c0 .4 0 .8.1 1.2l-2 1.5 2 3.4 2.3-1a7 7 0 0 0 2.1 1.2L10 21h4l.5-2.7a7 7 0 0 0 2.1-1.2l2.3 1 2-3.4-2-1.5c.1-.4.1-.8.1-1.2Z"/></svg>Pengaturan</a>
        <a class="push" href="{{ route('home') }}">
            <svg class="ico-s" viewBox="0 0 24 24"><path d="M4 9l1-5h14l1 5M4 9v11h16V9M4 9h16"/></svg>Lihat toko</a>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="linklike out"><svg class="ico-s" viewBox="0 0 24 24"><path d="M9 4H5v16h4M16 8l4 4-4 4M20 12H9"/></svg>Keluar</button></form>
    </aside>

    <section class="main">
        <header class="topbar">
            <button class="icon-btn side-toggle" type="button" id="side-toggle" aria-label="Buka menu admin" aria-expanded="false" aria-controls="admin-side">
                <svg class="ico-s lg" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
            <h1>@yield('heading', trim($__env->yieldContent('title', 'Dashboard')))</h1>
            <form class="tsearch" method="GET" action="{{ route('admin.orders.index') }}" role="search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg>
                <input type="search" name="q" value="{{ request()->routeIs('admin.orders.index') ? request('q') : '' }}" placeholder="Cari pesanan, nama, atau kode..." aria-label="Cari pesanan">
            </form>
            <div class="tools">
                @yield('tools')
                @include('partials.theme-toggle')
                <details class="dd">
                    <summary class="icon-btn {{ $alerts ? 'dot' : '' }}" id="bell" aria-label="Notifikasi">
                        <svg class="ico-s lg" viewBox="0 0 24 24"><path d="M6 16V11a6 6 0 0 1 12 0v5l2 2H4l2-2ZM10 20a2 2 0 0 0 4 0"/></svg>
                    </summary>
                    <div class="dd-menu">
                        <a href="{{ route('admin.orders.index', ['status' => 'new']) }}"><span>Pesanan baru</span><b data-count="orders">{{ $newOrderCount }}</b></a>
                        <a href="{{ route('admin.chat') }}"><span>Pesan belum dibaca</span><b data-count="unread">{{ $unreadCount }}</b></a>
                        <a href="{{ route('admin.inventory.index', ['low' => 1]) }}"><span>Stok menipis</span><b data-count="low">{{ $lowCount }}</b></a>
                    </div>
                </details>
                <a class="icon-btn hide-sm" href="{{ route('home') }}" aria-label="Lihat toko" title="Lihat toko">
                    <svg class="ico-s lg" viewBox="0 0 24 24"><path d="M4 9l1-5h14l1 5M4 9v11h16V9M4 9h16"/></svg>
                </a>
                <details class="dd">
                    <summary class="avatar" aria-label="Akun">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</summary>
                    <div class="dd-menu">
                        <p>{{ auth()->user()->name }}<br>{{ auth()->user()->email }}</p>
                        <a href="{{ route('admin.profile.edit') }}">Profil saya</a>
                        <a href="{{ route('admin.settings.index') }}">Pengaturan toko</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button>Keluar</button></form>
                    </div>
                </details>
            </div>
        </header>
        <div class="content">
            @include('partials.flash')
            @yield('content')
        </div>
    </section>
</div>
<div class="toasts" id="toasts" role="status" aria-live="polite"></div>
<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/site.js') }}"></script>
<script src="{{ asset('js/admin-notify.js') }}?v={{ @filemtime(public_path('js/admin-notify.js')) }}"></script>
@stack('scripts')
</body>
</html>
