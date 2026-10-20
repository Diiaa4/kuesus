@extends('layouts.admin')
@section('title', 'Inventori')
@section('content')
<div class="filters">
    <a href="{{ route('admin.inventory.index') }}" class="{{ ! $low ? 'on' : '' }}">Semua produk</a>
    <a href="{{ route('admin.inventory.index', ['low' => 1]) }}" class="{{ $low ? 'on' : '' }}">Stok menipis (&le; {{ $threshold }})</a>
</div>
<div class="table-scroll">
<table class="stack">
    <thead><tr><th></th><th>Produk</th><th>Stok</th><th>Ubah stok</th></tr></thead>
    <tbody>
    @forelse ($products as $p)
        <tr>
            <td><img class="thumb" src="{{ $p->image_url }}" alt=""></td>
            <td data-label="Produk">{{ $p->name }} @unless ($p->is_active)<span class="muted">(disembunyikan)</span>@endunless</td>
            <td data-label="Stok">
                @if ($p->stock === 0)<span class="status cancelled">Habis</span>
                @elseif ($p->stock <= $threshold)<span class="status new">{{ $p->stock }} (menipis)</span>
                @else<span class="status paid">{{ $p->stock }}</span>@endif
            </td>
            <td data-label="Ubah stok">
                <form class="row-form" method="POST" action="{{ route('admin.inventory.update', $p) }}">
                    @csrf @method('PATCH')
                    <input type="number" name="stock" min="0" value="{{ $p->stock }}" aria-label="Stok {{ $p->name }}" required>
                    <button class="btn small alt" type="submit">Simpan</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4" class="muted">{{ $low ? 'Tidak ada stok menipis.' : 'Belum ada produk.' }}</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@endsection
