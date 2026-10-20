@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="wrap page">
    <h2>Checkout</h2>
    <form method="POST" action="{{ route('checkout.store') }}" class="two-col">
        @csrf
        <div class="panel">
            <h3>Data pengiriman</h3>
            <label for="customer_name">Nama penerima</label>
            <input id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required>
            @error('customer_name')<p class="err">{{ $message }}</p>@enderror

            <label for="phone">No. WhatsApp</label>
            <input id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
            @error('phone')<p class="err">{{ $message }}</p>@enderror

            <label for="address">Alamat lengkap</label>
            <textarea id="address" name="address" rows="3" required>{{ old('address', auth()->user()->address) }}</textarea>
            @error('address')<p class="err">{{ $message }}</p>@enderror

            <label for="note">Catatan (opsional)</label>
            <input id="note" name="note" value="{{ old('note') }}" maxlength="255">
        </div>

        <div class="panel">
            <h3>Ringkasan</h3>
            @foreach ($products as $p)
                <div class="summary-row">
                    <span>{{ $p->qty }} &times; {{ $p->name }}</span>
                    <span>Rp {{ number_format($p->price * $p->qty, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <hr>
            <p><strong>Total: Rp {{ number_format($total, 0, ',', '.') }}</strong></p>

            <h3>Pembayaran</h3>
            <label class="pay-opt"><input type="radio" name="payment_method" value="qris" {{ old('payment_method', 'qris') === 'qris' ? 'checked' : '' }}>
                <span>QRIS<br><small class="muted">Scan kode, lalu unggah bukti bayar.</small></span></label>
            <label class="pay-opt"><input type="radio" name="payment_method" value="cod" {{ old('payment_method') === 'cod' ? 'checked' : '' }}>
                <span>COD, bayar di tempat<br><small class="muted">Bayar tunai saat kue sampai.</small></span></label>
            @error('payment_method')<p class="err">{{ $message }}</p>@enderror

            <div class="form-actions">
                <a class="btn ghost" href="{{ route('cart.index') }}">Kembali</a>
                <button class="btn" type="submit">Buat pesanan</button>
            </div>
        </div>
    </form>
</div>
@endsection
