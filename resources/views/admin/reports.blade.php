@extends('layouts.admin')
@section('title', 'Laporan')
@section('content')
@php($rp = fn ($n) => 'Rp '.number_format($n, 0, ',', '.'))
<form class="row-form a-card" method="GET" action="{{ route('admin.reports.index') }}">
    <div><label for="from">Dari</label><input id="from" type="date" name="from" value="{{ $from->format('Y-m-d') }}"></div>
    <div><label for="to">Sampai</label><input id="to" type="date" name="to" value="{{ $to->format('Y-m-d') }}"></div>
    <button class="btn" type="submit">Tampilkan</button>
    <a class="btn ghost" href="{{ route('admin.reports.export', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}">Unduh CSV</a>
</form>
@error('to')<p class="err">{{ $message }}</p>@enderror
@error('from')<p class="err">{{ $message }}</p>@enderror

<div class="a-grid g4">
    <div class="a-card"><div class="stat-l">Jumlah pesanan</div><div class="stat-v">{{ $orderCount }}</div></div>
    <div class="a-card"><div class="stat-l">Total penjualan</div><div class="stat-v">{{ $rp($revenue) }}</div></div>
    <div class="a-card"><div class="stat-l">Sudah lunas</div><div class="stat-v">{{ $rp($paid) }}</div></div>
    <div class="a-card"><div class="stat-l">Rata-rata per pesanan</div><div class="stat-v">{{ $rp($average) }}</div></div>
</div>
<p class="muted sm">Pesanan yang dibatalkan tidak dihitung.</p>

<div class="a-grid g2">
    <div class="a-card">
        <h3>Penjualan per produk</h3>
        <div class="table-scroll"><table>
            <thead><tr><th>Produk</th><th>Terjual</th><th>Nilai</th></tr></thead>
            <tbody>
            @forelse ($byProduct as $r)
                <tr><td>{{ $r->name }}</td><td>{{ (int) $r->qty }}</td><td>{{ $rp($r->total) }}</td></tr>
            @empty
                <tr><td colspan="3" class="muted">Belum ada penjualan pada periode ini.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
    <div class="a-card">
        <h3>Penjualan per hari</h3>
        <div class="table-scroll"><table>
            <thead><tr><th>Tanggal</th><th>Pesanan</th><th>Total</th></tr></thead>
            <tbody>
            @forelse ($byDay as $date => $d)
                <tr><td>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td><td>{{ $d['count'] }}</td><td>{{ $rp($d['total']) }}</td></tr>
            @empty
                <tr><td colspan="3" class="muted">Belum ada pesanan pada periode ini.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
</div>
@endsection
