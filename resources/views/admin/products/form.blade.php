@extends('layouts.admin')
@section('title', $product ? 'Ubah produk' : 'Tambah produk')
@section('content')
<form class="panel narrow" method="POST" enctype="multipart/form-data"
      action="{{ $product ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if ($product) @method('PUT') @endif

    <label for="name">Nama</label>
    <input id="name" name="name" value="{{ old('name', $product?->name) }}" required>
    @error('name')<p class="err">{{ $message }}</p>@enderror

    <label for="description">Deskripsi</label>
    <textarea id="description" name="description" rows="3">{{ old('description', $product?->description) }}</textarea>
    @error('description')<p class="err">{{ $message }}</p>@enderror

    <label for="price">Harga (Rp)</label>
    <input id="price" type="number" name="price" min="0" value="{{ old('price', $product?->price) }}" required>
    @error('price')<p class="err">{{ $message }}</p>@enderror

    <label for="stock">Stok</label>
    <input id="stock" type="number" name="stock" min="0" value="{{ old('stock', $product?->stock ?? 0) }}" required>
    @error('stock')<p class="err">{{ $message }}</p>@enderror

    <label for="image">Foto (maks 2 MB)</label>
    @if ($product?->image)<img class="thumb" src="{{ $product->image_url }}" alt="Foto saat ini">@endif
    <input id="image" type="file" name="image" accept="image/*">
    @error('image')<p class="err">{{ $message }}</p>@enderror

    <label class="check"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $product?->is_active ?? true) ? 'checked' : '' }}> Tampilkan di toko</label>

    <div class="form-actions">
        <a class="btn ghost" href="{{ route('admin.products.index') }}">Batal</a>
        <button class="btn" type="submit">Simpan</button>
    </div>
</form>
@endsection
