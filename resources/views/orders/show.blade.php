@extends('layouts.app')
@section('title', 'Pesanan '.$order->code)
@section('content')
<div class="wrap page">
    <h2>Pesanan {{ $order->code }}</h2>
    <div class="two-col">
        <div class="panel">
            <p>
                <span class="status {{ $order->status }}">{{ $order->status_label }}</span>
                <span class="status {{ $order->payment_status }}">{{ $order->payment_status_label }}</span>
            </p>
            <div class="table-scroll">
            <table>
                <thead><tr><th>Produk</th><th>Jml</th><th>Subtotal</th></tr></thead>
                <tbody>
                @foreach ($order->items as $i)
                    <tr><td>{{ $i->name }}</td><td>{{ $i->qty }}</td><td>Rp {{ number_format($i->price * $i->qty, 0, ',', '.') }}</td></tr>
                @endforeach
                </tbody>
            </table>
            </div>
            <p><strong>Total: Rp {{ number_format($order->total, 0, ',', '.') }}</strong></p>
            <p class="muted">Dikirim ke {{ $order->customer_name }} ({{ $order->phone }})<br>{{ $order->address }}
                @if ($order->note)<br>Catatan: {{ $order->note }}@endif</p>
        </div>

        <div class="panel">
            <h3>Pembayaran: {{ $order->payment_method_label }}</h3>
            @if ($order->status === 'cancelled')
                <p>Pesanan dibatalkan.</p>
            @elseif ($order->payment_method === 'cod')
                <p>Siapkan uang pas sebesar <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>. Bayar ke kurir saat kue sampai.</p>
            @elseif ($order->payment_status === 'paid')
                <p>Pembayaran diterima. Terima kasih.</p>
            @else
                <p>Scan QRIS, bayar <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>, lalu unggah bukti.</p>
                <img class="qr" src="{{ $site['qris_image'] ? asset('media/'.$site['qris_image']) : asset(file_exists(public_path('images/qris.png')) ? 'images/qris.png' : 'images/qris.svg') }}" alt="Kode QRIS toko">
                @if ($order->payment_status === 'waiting_verification')
                    <p class="flash ok">Bukti sudah dikirim. Menunggu verifikasi admin. Kirim ulang bila salah.</p>
                @endif
                <form method="POST" action="{{ route('orders.proof', $order) }}" enctype="multipart/form-data">
                    @csrf
                    <label for="proof">Bukti bayar (gambar, maks 2 MB)</label>
                    <input id="proof" type="file" name="proof" accept="image/*" required>
                    @error('proof')<p class="err">{{ $message }}</p>@enderror
                    <div class="form-actions"><button class="btn" type="submit">Kirim bukti</button></div>
                </form>
            @endif
            <p><a href="{{ route('chat') }}">Tanya admin lewat chat</a></p>
        </div>
    </div>
</div>
@endsection
