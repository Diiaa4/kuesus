@extends('layouts.admin')
@section('title', 'Pesanan '.$order->code)
@section('content')

<div class="two-col">
    <div class="panel">
        <p><strong>{{ $order->customer_name }}</strong> &middot; {{ $order->phone }}<br>{{ $order->address }}
            @if ($order->note)<br><em>Catatan: {{ $order->note }}</em>@endif</p>
        <div class="table-scroll">
        <table>
            <thead><tr><th>Produk</th><th>Harga</th><th>Jml</th><th>Subtotal</th></tr></thead>
            <tbody>
            @foreach ($order->items as $i)
                <tr><td>{{ $i->name }}</td><td>Rp {{ number_format($i->price, 0, ',', '.') }}</td><td>{{ $i->qty }}</td>
                    <td>Rp {{ number_format($i->price * $i->qty, 0, ',', '.') }}</td></tr>
            @endforeach
            </tbody>
        </table>
        </div>
        <p><strong>Total: Rp {{ number_format($order->total, 0, ',', '.') }}</strong> ({{ $order->payment_method_label }})</p>
        <p><a href="{{ route('admin.chat', $order->user_id) }}">Chat pelanggan ini</a>
            &middot; <a href="{{ $order->whatsapp_url }}" target="_blank" rel="noopener">WhatsApp {{ $order->phone }}</a></p>
    </div>

    <div class="panel">
        <h3>Kelola</h3>
        @if ($order->payment_proof)
            <p>Bukti bayar:</p>
            <a href="{{ asset('media/'.$order->payment_proof) }}" target="_blank" rel="noopener">
                <img src="{{ asset('media/'.$order->payment_proof) }}" alt="Bukti bayar" class="proof-img">
            </a>
        @elseif ($order->payment_method === 'qris')
            <p class="muted">Belum ada bukti bayar.</p>
        @endif

        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
            @csrf @method('PATCH')
            <label for="payment_status">Status bayar</label>
            <select id="payment_status" name="payment_status">
                @foreach (\App\Models\Order::PAYMENT_STATUSES as $k => $label)
                    <option value="{{ $k }}" @selected($order->payment_status === $k)>{{ $label }}</option>
                @endforeach
            </select>
            <label for="status">Status pesanan</label>
            <select id="status" name="status">
                @foreach (\App\Models\Order::STATUSES as $k => $label)
                    <option value="{{ $k }}" @selected($order->status === $k)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="muted">Membatalkan pesanan mengembalikan stok. COD otomatis lunas saat Selesai.</p>
            <p><button class="btn" type="submit" @disabled($order->status === 'cancelled')>Simpan</button></p>
        </form>
    </div>
</div>
@endsection
