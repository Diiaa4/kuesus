@extends('layouts.admin')
@section('title', 'Pengaturan toko')
@section('content')
@unless ($ready)
    <div class="flash bad" role="alert">Tabel pengaturan belum ada. Jalankan <code>php artisan migrate</code>, lalu muat ulang halaman ini.</div>
@endunless
<form class="a-card narrow-lg" method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
    @csrf @method('PUT')
    <h3>Informasi toko</h3>
    <label for="name">Nama toko</label>
    <input id="name" name="name" value="{{ old('name', $site['name']) }}" required>
    @error('name')<p class="err">{{ $message }}</p>@enderror

    <label for="tagline">Deskripsi singkat (tampil di halaman depan)</label>
    <textarea id="tagline" name="tagline" rows="3">{{ old('tagline', $site['tagline']) }}</textarea>
    @error('tagline')<p class="err">{{ $message }}</p>@enderror

    <label for="address">Alamat</label>
    <input id="address" name="address" value="{{ old('address', $site['address']) }}">
    @error('address')<p class="err">{{ $message }}</p>@enderror

    <label for="phone">Telepon / WhatsApp</label>
    <input id="phone" name="phone" value="{{ old('phone', $site['phone']) }}">
    @error('phone')<p class="err">{{ $message }}</p>@enderror

    <label for="email">Email toko</label>
    <input id="email" type="email" name="email" value="{{ old('email', $site['email']) }}">
    @error('email')<p class="err">{{ $message }}</p>@enderror

    <label for="hours">Jam buka</label>
    <input id="hours" name="hours" value="{{ old('hours', $site['hours']) }}">
    @error('hours')<p class="err">{{ $message }}</p>@enderror

    <hr>
    <h3>Titik lokasi Google Maps</h3>
    <p class="muted sm">Buka Google Maps, klik kanan pada titik toko, lalu klik angka koordinat untuk menyalin. Pakai titik (bukan koma) sebagai desimal, contoh -6.98700.</p>
    <div class="row-form">
        <div><label for="lat">Latitude</label>
            <input id="lat" name="lat" inputmode="decimal" value="{{ old('lat', $site['lat']) }}" placeholder="-6.98700">
            @error('lat')<p class="err">{{ $message }}</p>@enderror</div>
        <div><label for="lng">Longitude</label>
            <input id="lng" name="lng" inputmode="decimal" value="{{ old('lng', $site['lng']) }}" placeholder="106.55000">
            @error('lng')<p class="err">{{ $message }}</p>@enderror</div>
    </div>
    <p class="sm"><a class="gold" href="{{ $site['map_url'] }}" target="_blank" rel="noopener">Cek titik tersimpan di Google Maps</a>
        <span class="muted">(kosong = pakai alamat)</span></p>

    <hr>
    <h3>Kode QRIS pembayaran</h3>
    @if ($site['qris_image'])
        <img class="qr" src="{{ asset('media/'.$site['qris_image']) }}" alt="QRIS saat ini">
    @else
        <p class="muted sm">Belum ada QRIS. Pelanggan melihat gambar contoh.</p>
    @endif
    <label for="qris">Unggah QRIS baru (gambar, maks 2 MB)</label>
    <input id="qris" type="file" name="qris" accept="image/*">
    @error('qris')<p class="err">{{ $message }}</p>@enderror

    <div class="form-actions">
        <button class="btn" type="submit">Simpan pengaturan</button>
    </div>
</form>
@endsection
