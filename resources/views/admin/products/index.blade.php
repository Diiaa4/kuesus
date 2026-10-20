@extends('layouts.admin')
@section('title', 'Produk')
@section('content')
<div class="page-head">
    <p class="muted sm">{{ $products->count() }} produk terdaftar</p>
    <a class="btn" href="{{ route('admin.products.create') }}">Tambah produk</a>
</div>
<div class="table-scroll">
<table class="stack">
    <thead><tr><th></th><th>Nama</th><th>Harga</th><th>Stok</th><th>Tampil</th><th></th></tr></thead>
    <tbody>
    @forelse ($products as $p)
        <tr>
            <td><img class="thumb" src="{{ $p->image_url }}" alt=""></td>
            <td data-label="Nama">{{ $p->name }}</td>
            <td data-label="Harga">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
            <td data-label="Stok">{{ $p->stock }}</td>
            <td data-label="Tampil">{{ $p->is_active ? 'Ya' : 'Tidak' }}</td>
            <td><div class="actions">
                <a class="btn small alt" href="{{ route('admin.products.edit', $p) }}">Ubah</a>
                <form method="POST" action="{{ route('admin.products.destroy', $p) }}"
                      onsubmit="return confirm('Hapus {{ addslashes($p->name) }}? Tidak bisa dibatalkan.')">
                    @csrf @method('DELETE')
                    <button class="btn small danger">Hapus</button>
                </form>
            </div></td>
        </tr>
    @empty
        <tr><td colspan="6" class="muted">Belum ada produk. Tekan Tambah produk.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@endsection
