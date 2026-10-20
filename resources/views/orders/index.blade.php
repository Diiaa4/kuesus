@extends('layouts.app')
@section('title', 'Pesanan saya')
@section('content')
<div class="wrap page">
    <h2>Pesanan saya</h2>
    @if ($orders->isEmpty())
        <p class="muted">Belum ada pesanan. <a href="{{ route('home') }}#produk">Mulai belanja</a>.</p>
    @else
    <div class="table-scroll">
    <table class="stack">
        <thead><tr><th>Kode</th><th>Tanggal</th><th>Total</th><th>Bayar</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach ($orders as $o)
            <tr>
                <td data-label="Kode">{{ $o->code }}</td>
                <td data-label="Tanggal">{{ $o->created_at->format('d M Y H:i') }}</td>
                <td data-label="Total">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
                <td data-label="Bayar"><span class="status {{ $o->payment_status }}">{{ $o->payment_status_label }}</span></td>
                <td data-label="Status"><span class="status {{ $o->status }}">{{ $o->status_label }}</span></td>
                <td><a href="{{ route('orders.show', $o) }}">Detail</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    @endif
</div>
@endsection
