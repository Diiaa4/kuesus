@extends('layouts.app')
@section('title', 'Menu - '.$site['name'])

@section('content')
@include('partials.svg-defs')
<div class="wrap page">
    <section class="band mt-0" id="kategori">
        <h2 class="sec-title">Belanja per Kategori</h2>
        <div class="cats">
            <a class="cat {{ $q === '' ? 'on' : '' }}" href="{{ route('menu') }}#produk">
                <svg viewBox="0 0 120 72" aria-hidden="true"><use href="#puff"/></svg>Semua Menu</a>
            <a class="cat {{ $q === 'Vanila' ? 'on' : '' }}" href="{{ route('menu', ['q' => 'Vanila']) }}#produk">
                <svg viewBox="0 0 120 84" aria-hidden="true"><use href="#puff" y="12" width="120" height="72"/><ellipse cx="60" cy="16" rx="34" ry="8" fill="#f4e3c8"/><ellipse cx="60" cy="8" rx="20" ry="7" fill="#ecd3a9"/></svg>Vanila</a>
            <a class="cat {{ $q === 'Cokelat' ? 'on' : '' }}" href="{{ route('menu', ['q' => 'Cokelat']) }}#produk">
                <svg viewBox="0 0 120 84" aria-hidden="true"><use href="#puff" y="12" width="120" height="72"/><path d="M18 34c10-14 30-18 42-18s34 4 42 18c-6 8-12 4-20 10-6-6-12 2-22 0-8 4-14-4-22 0-8-2-12 2-20-10Z" fill="#4a2a1f"/></svg>Cokelat</a>
            <a class="cat {{ $q === 'Keju' ? 'on' : '' }}" href="{{ route('menu', ['q' => 'Keju']) }}#produk">
                <svg viewBox="0 0 120 84" aria-hidden="true"><use href="#puff" y="12" width="120" height="72"/><g fill="#ffd66b"><rect x="30" y="20" width="26" height="4" rx="2" transform="rotate(-12 43 22)"/><rect x="58" y="16" width="26" height="4" rx="2" transform="rotate(10 71 18)"/><rect x="44" y="28" width="26" height="4" rx="2"/><rect x="76" y="28" width="22" height="4" rx="2" transform="rotate(-8 87 30)"/></g></svg>Keju</a>
            <a class="cat {{ $q === 'Box' ? 'on' : '' }}" href="{{ route('menu', ['q' => 'Box']) }}#produk">
                <svg viewBox="0 0 120 84" aria-hidden="true"><use href="#puff" x="6" y="38" width="60" height="36"/><use href="#puff" x="54" y="38" width="60" height="36"/><use href="#puff" x="30" y="16" width="60" height="36"/></svg>Box Hantaran</a>
        </div>
    </section>

    <section id="produk">
        <h2 class="sec-title">{{ $q !== '' ? 'Hasil untuk "'.$q.'"' : 'Semua Menu' }}</h2>
        <form class="menu-search" method="GET" action="{{ route('menu') }}#produk" role="search">
            <input type="search" name="q" value="{{ $q }}" placeholder="Cari kue..." aria-label="Cari kue">
            <button class="btn" type="submit">Cari</button>
            @if ($q !== '')<a class="btn ghost" href="{{ route('menu') }}#produk">Reset</a>@endif
        </form>

        @if ($products->isEmpty())
            <p class="muted text-center">Tidak ada kue yang cocok. <a class="gold" href="{{ route('menu') }}#produk">Lihat semua menu</a>.</p>
        @else
            <div class="grid-products">
                @foreach ($products as $p)
                    @include('partials.product-card', ['p' => $p])
                @endforeach
            </div>
        @endif
    </section>
</div>

@include('partials.chat-widget', ['chat' => $chat])
@endsection
