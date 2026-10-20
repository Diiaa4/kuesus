@extends('layouts.admin')
@section('title', 'Pesanan')
@section('content')
<form class="filter-bar" method="GET" action="{{ route('admin.orders.index') }}" data-autosubmit>
    @if ($q !== '')<input type="hidden" name="q" value="{{ $q }}">@endif
    <div>
        <label class="sr-only" for="f-status">Status pesanan</label>
        <select id="f-status" name="status">
            <option value="">Semua status</option>
            @foreach (\App\Models\Order::STATUSES as $k => $label)
                <option value="{{ $k }}" @selected($status === $k)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="sr-only" for="f-payment">Status pembayaran</label>
        <select id="f-payment" name="payment">
            <option value="">Semua pembayaran</option>
            @foreach (\App\Models\Order::PAYMENT_STATUSES as $k => $label)
                <option value="{{ $k }}" @selected($payment === $k)>{{ $label }}@if ($k === 'waiting_verification' && $waiting) ({{ $waiting }})@endif</option>
            @endforeach
        </select>
    </div>
    <noscript><button class="btn small" type="submit">Terapkan</button></noscript>
    @if ($status || $payment)
        <a class="btn small ghost" href="{{ route('admin.orders.index', array_filter(['q' => $q])) }}">Reset filter</a>
    @endif
</form>
@if ($q !== '')<p class="muted">Hasil pencarian untuk "{{ $q }}". <a class="gold" href="{{ route('admin.orders.index', array_filter(['status' => $status, 'payment' => $payment])) }}">Hapus</a></p>@endif
<div class="table-scroll">
<table class="stack">
    <thead><tr><th>Kode</th><th>Tanggal</th><th>Pemesan</th><th>Total</th><th>Metode</th><th>Bayar</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($orders as $o)
        <tr>
            <td data-label="Kode">{{ $o->code }}</td>
            <td data-label="Tanggal">{{ $o->created_at->format('d M H:i') }}</td>
            <td data-label="Pemesan">{{ $o->customer_name }}</td>
            <td data-label="Total">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
            <td data-label="Metode">{{ strtoupper($o->payment_method) }}</td>
            <td data-label="Bayar"><span class="status {{ $o->payment_status }}">{{ $o->payment_status_label }}</span></td>
            <td data-label="Status"><span class="status {{ $o->status }}">{{ $o->status_label }}</span></td>
            <td><a href="{{ route('admin.orders.show', $o) }}">Buka</a></td>
        </tr>
    @empty
        <tr><td colspan="8" class="muted">Tidak ada pesanan.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@if ($orders->hasPages())
<div class="pager">
    @if ($orders->previousPageUrl())<a class="btn small ghost" href="{{ $orders->previousPageUrl() }}">Sebelumnya</a>@endif
    <span class="muted">Halaman {{ $orders->currentPage() }} dari {{ $orders->lastPage() }}</span>
    @if ($orders->nextPageUrl())<a class="btn small ghost" href="{{ $orders->nextPageUrl() }}">Berikutnya</a>@endif
</div>
@endif
@endsection
