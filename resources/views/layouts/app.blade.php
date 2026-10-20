<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', $site['name'])</title>
</head>
<body class="@yield('bodyclass')">
@php($isAdmin = auth()->check() && auth()->user()->isAdmin())
<header class="site-head">
    <div class="wrap">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-text"><span class="logo">{{ $site['name'] }}</span><small>KUE SUS</small></span>
        </a>

        <nav class="nav" id="main-nav" aria-label="Menu utama">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'on' : '' }}">Beranda</a>
            <a href="{{ route('menu') }}" class="{{ request()->routeIs('menu') ? 'on' : '' }}">Menu</a>
            <a href="{{ route('home') }}#cerita">Tentang Kami</a>
            <a href="{{ route('home') }}#cara-pesan">Cara Pesan</a>
            <a href="{{ route('home') }}#lokasi">Lokasi</a>
            @auth
                @unless ($isAdmin)
                    <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'on' : '' }}">Pesanan Saya</a>
                @endunless
            @endauth
        </nav>

        <div class="head-actions">
            @include('partials.theme-toggle')
            <button class="icon-btn" type="button" id="search-toggle" aria-label="Cari kue" aria-expanded="false" aria-controls="head-search">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg>
            </button>
            @auth
                @unless ($isAdmin)
                    <a class="cart-link" href="{{ route('cart.index') }}" aria-label="Keranjang">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 4h2l2.4 11.2a1 1 0 0 0 1 .8h8.8a1 1 0 0 0 1-.8L20 8H6"/><circle cx="9.5" cy="20" r="1.3"/><circle cx="17" cy="20" r="1.3"/></svg>
                        <span class="lbl">Keranjang</span> <span class="badge">{{ collect(session('cart', []))->sum() }}</span>
                    </a>
                @endunless
                <details class="dd">
                    <summary class="avatar" aria-label="Menu akun {{ auth()->user()->name }}">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</summary>
                    <div class="dd-menu">
                        <p>{{ auth()->user()->name }}<br>{{ auth()->user()->email }}</p>
                        @if ($isAdmin)
                            <a href="{{ route('admin.dashboard') }}">Dashboard admin</a>
                            <a href="{{ route('admin.profile.edit') }}">Profil</a>
                        @else
                            <a href="{{ route('profile.edit') }}">Profil</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Keluar</button></form>
                    </div>
                </details>
            @else
                <a class="icon-btn show-sm" href="{{ route('login') }}" aria-label="Masuk">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1-5 5-7 8-7s7 2 8 7"/></svg>
                </a>
                <a class="linklike hide-sm" href="{{ route('login') }}">Masuk</a>
                <a class="btn small hide-sm" href="{{ route('register') }}">Daftar</a>
            @endauth
        </div>

        <button class="icon-btn nav-toggle" type="button" id="nav-toggle" aria-label="Buka menu" aria-expanded="false" aria-controls="main-nav">
            <svg class="i-open" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            <svg class="i-close" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>

        <form class="head-search" id="head-search" method="GET" action="{{ route('menu') }}#produk" role="search" hidden>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari kue sus, misalnya cokelat..." aria-label="Cari kue">
            <button class="btn" type="submit">Cari</button>
        </form>
    </div>
</header>

<main>
    <div class="wrap">@include('partials.flash')</div>
    @yield('content')
</main>

<footer class="foot">
    <div class="wrap">
        <div class="foot-grid">
            <div>
                <span class="brand foot-brand"><span class="logo">{{ $site['name'] }}</span></span>
                <p class="about">Kue sus buatan rumah, dipanggang setiap pagi untuk momen manismu.</p>
            </div>
            <div>
                <h4>Toko</h4>
                <ul>
                    <li><a href="{{ route('menu') }}">Semua Menu</a></li>
                    <li><a href="{{ route('menu') }}#kategori">Kategori</a></li>
                    <li><a href="{{ route('home') }}#cara-pesan">Cara Pesan</a></li>
                    <li><a href="{{ route('login') }}">Masuk</a></li>
                </ul>
            </div>
            <div>
                <h4>Bantuan</h4>
                <ul>
                    <li><a href="{{ route('home') }}#cara-pesan">Cara pesan</a></li>
                    <li>Bayar: QRIS atau COD</li>
                </ul>
            </div>
            <div>
                <h4>Kunjungi Kami</h4>
                <ul>
                    <li><a href="{{ $site['map_url'] }}" target="_blank" rel="noopener">{{ $site['address'] }}</a></li>
                    <li>{{ $site['phone'] }}</li>
                    <li>{{ $site['email'] }}</li>
                    <li>{{ $site['hours'] }}</li>
                </ul>
            </div>
        </div>
        <div class="copy">
            <span>&copy; {{ date('Y') }} {{ $site['name'] }}. Hak cipta dilindungi.</span>
            <span class="pay-chips"><span>QRIS</span><span>COD</span></span>
        </div>
    </div>
</footer>
<script src="{{ asset('js/theme.js') }}"></script>
<script src="{{ asset('js/site.js') }}"></script>
@stack('scripts')
</body>
</html>
