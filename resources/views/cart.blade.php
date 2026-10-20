@extends('layouts.app')
@section('title', 'Keranjang')
@section('content')
<div class="wrap page">
    <h2>Keranjang</h2>
    @if ($products->isEmpty())
        <p class="muted">Keranjang kosong. <a href="{{ route('home') }}#produk">Pilih kue dulu</a>.</p>
    @else
        <div class="table-scroll">
        <table class="stack">
            <thead><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr></thead>
            <tbody>
            @foreach ($products as $p)
                <tr>
                    <td data-label="Produk">{{ $p->name }}</td>
                    <td data-label="Harga">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                    <td data-label="Jumlah">
                        <form class="inline" method="POST" action="{{ route('cart.update', $p) }}">
                            @csrf @method('PATCH')
                            <input type="number" name="qty" value="{{ $p->qty }}" min="1" max="{{ $p->stock }}" aria-label="Jumlah {{ $p->name }}">
                            <button class="btn small alt">Ubah</button>
                        </form>
                    </td>
                    <td data-label="Subtotal">Rp {{ number_format($p->price * $p->qty, 0, ',', '.') }}</td>
                    <td>
                        <form method="POST" action="{{ route('cart.remove', $p) }}">
                            @csrf @method('DELETE')
                            <button class="linklike">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        <p class="cart-total"><strong>Total: Rp {{ number_format($total, 0, ',', '.') }}</strong></p>
        <div class="form-actions">
            <a class="btn ghost" href="{{ route('menu') }}">Lanjut belanja</a>
            <a class="btn" href="{{ route('checkout') }}">Lanjut ke checkout</a>
        </div>
    @endif
</div>
@endsection
