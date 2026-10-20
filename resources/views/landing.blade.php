@extends('layouts.app')
@section('title', $site['name'].' - Kue sus segar setiap hari')

@section('bodyclass', 'has-hero')

@section('content')
@include('partials.svg-defs')

{{-- HERO --}}
<section class="hero" style="--hero-img:url('{{ asset('images/bg.jpg') }}')">
    <div class="wrap">
        <div class="hero-copy">
            <p class="eyebrow">DIBUAT DENGAN SEPENUH HATI</p>
            <h1>Kue Sus<br>Segar Setiap Pagi</h1>
            <svg class="brush" width="230" height="14" viewBox="0 0 230 14" aria-hidden="true"><path d="M2 9C50 2 120 2 228 6" fill="none" stroke="#c8964f" stroke-width="4" stroke-linecap="round"/></svg>
            <p class="lead">{{ $site['tagline'] }}</p>
            <div class="cta">
                <a class="btn" href="{{ route('menu') }}">Pesan Sekarang <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
                <a class="btn ghost" href="{{ route('menu') }}#kategori">Lihat Kategori</a>
            </div>
            <div class="perks">
                <div class="perk"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="13" r="8"/><path d="M12 8v5l3 2M9 3h6"/></svg><div><b>Dibuat Segar</b><span>Dipanggang tiap pagi</span></div></div>
                <div class="perk"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M20 3C9 3 4 9 4 15c0 1.6.4 3 1 4 1-4 4-8 9-10-4 3-6 6-7 11 1 .6 2.4 1 4 1 6 0 9-5 9-18Z"/></svg><div><b>Bahan Pilihan</b><span>Tanpa pengawet</span></div></div>
                <div class="perk"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h3v3M21 14v7h-4"/></svg><div><b>Bayar QRIS / COD</b><span>Mudah dan aman</span></div></div>
                <div class="perk"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4 5h16v11H9l-5 4V5Z"/></svg><div><b>Chat Admin</b><span>Tanya kapan saja</span></div></div>
            </div>
        </div>
    </div>
</section>

<div class="wrap">
    {{-- MENU PILIHAN --}}
    <section class="band reveal" id="produk">
        <h2 class="sec-title">Menu Pilihan</h2>
        @if ($products->isEmpty())
            <p class="muted text-center">Belum ada produk. Admin bisa menambahkannya dari dashboard.</p>
        @else
        <div class="rail-wrap">
            <button class="rail-btn prev" type="button" aria-label="Geser ke kiri" data-rail="-1">&#8249;</button>
            <div class="rail" id="rail" tabindex="0" aria-label="Daftar kue">
                @foreach ($products as $p)
                    @include('partials.product-card', ['p' => $p])
                @endforeach
            </div>
            <button class="rail-btn next" type="button" aria-label="Geser ke kanan" data-rail="1">&#8250;</button>
        </div>
        <p class="text-center mt-1 mb-0"><a class="btn alt" href="{{ route('menu') }}">Lihat semua menu &amp; kategori</a></p>
        @endif
    </section>

    {{-- PROMO --}}
    <section class="promo reveal">
        <div>
            <small>UNTUK ACARA SPESIAL</small>
            <h2>Box Hantaran<br>Isi 12 Kue Sus</h2>
            <p>Campuran vanila, cokelat, dan keju dalam satu box. Cocok untuk arisan, ulang tahun, dan oleh-oleh.</p>
            <a class="btn" href="{{ route('menu', ['q' => 'Box']) }}#produk">Lihat Box</a>
        </div>
        <img class="promo-photo" src="{{ asset('images/packing.jpg') }}" width="445" height="689" loading="lazy" alt="Box berisi kue sus hangat dengan alas kertas putih">
    </section>

    {{-- CERITA --}}
    <section class="story reveal" id="cerita">
        <div class="pic">
            <img class="story-photo" src="{{ asset('images/cerita-panggang.jpg') }}" width="1000" height="667" loading="lazy" alt="Kue sus baru matang di dalam oven">
        </div>
        <div class="txt">
            <div>
                <small>CERITA KAMI</small>
                <h2>Dipanggang Cinta,<br>Disajikan Hangat</h2>
                <p>Di {{ $site['name'] }}, setiap kulit sus dipanggang pagi hari dan diisi vla saat dipesan. Kami percaya kue yang enak lahir dari bahan jujur dan proses yang sabar.</p>
                <a class="btn alt small" href="#cara-pesan">Cara Pesan</a>
            </div>
            <div class="values">
                <div class="value"><span class="ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M12 3l2.6 5.6 6 .7-4.5 4.1 1.2 6L12 16.4 6.7 19.4l1.2-6L3.4 9.3l6-.7L12 3Z"/></svg></span><div><b>Kualitas Rumahan</b><span>Resep turun-temurun</span></div></div>
                <div class="value"><span class="ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M20 3C9 3 4 9 4 15c0 1.6.4 3 1 4 1-4 4-8 9-10-4 3-6 6-7 11 1 .6 2.4 1 4 1 6 0 9-5 9-18Z"/></svg></span><div><b>Bahan Segar</b><span>Telur, susu, mentega</span></div></div>
                <div class="value"><span class="ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="12" cy="13" r="8"/><path d="M12 8v5l3 2"/></svg></span><div><b>Dipanggang Harian</b><span>Setiap pagi</span></div></div>
                <div class="value"><span class="ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M12 20s-8-5-8-11a4.5 4.5 0 0 1 8-2.5A4.5 4.5 0 0 1 20 9c0 6-8 11-8 11Z"/></svg></span><div><b>Puas atau Kabari Kami</b><span>Admin siap membantu</span></div></div>
            </div>
        </div>
    </section>

    {{-- CARA PESAN --}}
    <section class="band" id="cara-pesan">
        <h2 class="sec-title">Cara Pesan</h2>
        <div class="quotes">
            <div class="quote"><b class="gold">1. Masuk atau daftar</b><p>Wajib punya akun sebelum membeli.</p></div>
            <div class="quote"><b class="gold">2. Pilih kue</b><p>Tekan Tambah ke keranjang, lalu cek keranjang.</p></div>
            <div class="quote"><b class="gold">3. Isi alamat, pilih bayar</b><p>Bayar dengan QRIS atau COD.</p></div>
            <div class="quote"><b class="gold">4. Tanya lewat chat</b><p>Tombol chat ada di kanan bawah.</p></div>
        </div>
    </section>

    {{-- ULASAN --}}
    <section id="ulasan">
        <h2 class="sec-title">Kata Pelanggan</h2>
        @if ($reviews->isEmpty())
            <p class="muted text-center">Belum ada komentar. Jadilah yang pertama!</p>
        @else
        <div class="quotes">
            @foreach ($reviews as $r)
                <div class="quote solid">
                    <div class="stars" aria-label="{{ $r->rating }} dari 5 bintang">{{ str_repeat('★', $r->rating) }}<span class="muted">{{ str_repeat('★', 5 - $r->rating) }}</span></div>
                    <p>"{{ $r->body }}"</p>
                    <b>{{ $r->user->name ?? 'Pelanggan' }}</b>
                </div>
            @endforeach
        </div>
        @endif

        @unless (auth()->check() && auth()->user()->isAdmin())
        <div class="panel review-form">
            @guest
                <p class="mb-0">Mau berbagi pengalaman? <a class="gold" href="{{ route('login') }}">Masuk</a> atau <a class="gold" href="{{ route('register') }}">daftar</a>. Komentar bisa ditulis setelah pesananmu selesai.</p>
            @elseif (! $canReview)
                <p class="mb-0 muted">Komentar hanya untuk pelanggan yang pesanannya sudah selesai. Pesan dulu, lalu ceritakan pengalamanmu di sini.</p>
            @else
                <h3>Tulis komentar</h3>
                <form method="POST" action="{{ route('reviews.store') }}">
                    @csrf
                    <fieldset class="rate-set">
                        <legend>Nilai Anda</legend>
                        <div class="rate">
                            @for ($i = 5; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" @checked((int) old('rating') === $i) required>
                                <label for="star{{ $i }}" title="{{ $i }} bintang">&#9733;</label>
                            @endfor
                        </div>
                    </fieldset>
                    @error('rating')<p class="err">{{ $message }}</p>@enderror
                    <label for="body">Komentar</label>
                    <textarea id="body" name="body" rows="3" maxlength="300" required placeholder="Ceritakan rasa kue sus favoritmu...">{{ old('body') }}</textarea>
                    @error('body')<p class="err">{{ $message }}</p>@enderror
                    <div class="form-actions"><button class="btn" type="submit">Kirim komentar</button></div>
                </form>
            @endguest
        </div>
        @endunless
    </section>

    {{-- LOKASI --}}
    <section class="band" id="lokasi">
        <h2 class="sec-title">Lokasi Kami</h2>
        <div class="loc">
            <div class="loc-info">
                <h3>{{ $site['name'] }}</h3>
                <p class="muted">{{ $site['address'] }}</p>
                <p class="muted">{{ $site['hours'] }}</p>
                <p class="muted">{{ $site['phone'] }}</p>
                <div class="cta">
                    <a class="btn" href="{{ $site['map_url'] }}" target="_blank" rel="noopener">Buka di Google Maps</a>
                    <a class="btn ghost" href="{{ $site['dir_url'] }}" target="_blank" rel="noopener">Petunjuk arah</a>
                </div>
            </div>
            <iframe class="map" src="{{ $site['map_embed'] }}" title="Peta lokasi {{ $site['name'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        </div>
    </section>

    {{-- CTA CHAT --}}
    <section class="chat-cta">
        <svg viewBox="0 0 300 180" role="img" aria-label="Secangkir kopi dan kue sus">
            <ellipse class="s-shadow" cx="150" cy="150" rx="130" ry="20" fill="#000" opacity=".4"/>
            <path class="s-cup" d="M80 70h120c0 50-14 78-60 78S80 120 80 70Z" fill="#26221e" stroke="#c8964f" stroke-width="1.5"/>
            <ellipse cx="140" cy="70" rx="60" ry="14" fill="#8a5a2c"/><path d="M140 80c-16-8-16-18-6-18 4 0 6 3 6 3s2-3 6-3c10 0 10 10-6 18Z" fill="#f4e3c8"/>
            <path d="M200 84c28-4 28 32-2 34" fill="none" stroke="#c8964f" stroke-width="6" stroke-linecap="round"/>
            <use href="#puff" x="214" y="112" width="62" height="38"/>
        </svg>
        <div>
            <h2>Ada Pertanyaan?</h2>
            <p>Tanya stok, pesanan besar, atau rasa favoritmu langsung ke admin lewat chat.</p>
            <button class="btn" type="button" id="open-chat">Chat dengan kami</button>
        </div>
    </section>
</div>

@include('partials.chat-widget', ['chat' => $chat])
@endsection

@push('scripts')
<script src="{{ asset('js/landing.js') }}"></script>
@endpush
