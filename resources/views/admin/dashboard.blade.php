@extends('layouts.admin')
@section('title', 'Dashboard')

@section('tools')
<form method="GET" action="{{ route('admin.dashboard') }}">
    <label class="sr-only" for="range">Periode</label>
    <select id="range" name="range" onchange="this.form.submit()">
        <option value="7" @selected($range === 7)>7 hari terakhir</option>
        <option value="30" @selected($range === 30)>30 hari terakhir</option>
    </select>
</form>
@endsection

@php
    $pct = function ($v) {
        if ($v === null) return '<span class="muted">Baru</span>';
        return '<span class="'.($v >= 0 ? 'pos' : 'neg').'">'.($v >= 0 ? '+' : '').$v.'%</span>';
    };
    $rp = fn ($n) => 'Rp '.number_format($n, 0, ',', '.');
@endphp

@section('content')
<div class="a-grid g2">
    <div class="a-card">
        <h3>Ringkasan penjualan</h3>
        <div class="big">{{ $rp($salesTotal) }}</div>
        <div class="muted sm">{{ $range }} hari terakhir {!! $pct($salesChange) !!}</div>
        <div class="bars" role="img" aria-label="Grafik penjualan {{ $range }} hari terakhir">
            @foreach ($bars as $b)
                <div class="bar {{ $b['value'] > 0 && $b['value'] === $maxBar ? 'peak' : '' }}" title="{{ $b['label'] }}: {{ $rp($b['value']) }}">
                    <i style="height:{{ max(2, round($b['value'] / $maxBar * 100)) }}%"></i>
                    <span>{{ $b['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="a-card">
        <h3>Kue terlaris</h3>
        <div class="donut-row">
            <div class="donut" style="background:{{ $donut }}">
                <div><b>{{ $totalSold }}</b><span class="muted sm">Total terjual</span></div>
            </div>
        </div>
        @if ($segments->isEmpty())
            <p class="muted sm text-center">Belum ada penjualan pada periode ini.</p>
        @else
            <div class="legend">
                @foreach ($segments as $s)
                    <div><i style="background:{{ $s['color'] }}"></i>{{ $s['name'] }}<em>{{ $s['pct'] }}%</em></div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="a-grid g4">
    <div class="a-card"><div class="stat-l">Pesanan hari ini</div><div class="stat-v">{{ $todayCount }}</div><div class="sm">{!! $pct($todayCountChange) !!} <span class="muted">vs kemarin</span></div></div>
    <div class="a-card"><div class="stat-l">Pendapatan hari ini</div><div class="stat-v">{{ $rp($todayRevenue) }}</div><div class="sm">{!! $pct($todayRevenueChange) !!} <span class="muted">vs kemarin</span></div></div>
    <a class="a-card" href="{{ route('admin.orders.index', ['status' => 'new']) }}"><div class="stat-l">Pesanan baru</div><div class="stat-v">{{ $pending }}</div><div class="sm {{ $pending ? 'amber' : 'muted' }}">{{ $pending ? 'Perlu diproses' : 'Semua sudah diproses' }}</div></a>
    <a class="a-card" href="{{ route('admin.inventory.index', ['low' => 1]) }}"><div class="stat-l">Stok menipis</div><div class="stat-v">{{ $lowCount }}</div><div class="sm {{ $lowCount ? 'neg' : 'muted' }}">{{ $lowCount ? 'Perlu tindakan' : 'Stok aman' }}</div></a>
</div>

<div class="a-grid g21">
    <div class="a-card">
        <h3>Pesanan terbaru</h3>
        <div class="table-scroll">
        <table>
            <thead><tr><th>Kode</th><th>Pemesan</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($recent as $o)
                <tr>
                    <td>{{ $o->code }}</td><td>{{ $o->customer_name }}</td>
                    <td>{{ $rp($o->total) }}</td>
                    <td><span class="status {{ $o->status }}">{{ $o->status_label }}</span></td>
                    <td><a href="{{ route('admin.orders.show', $o) }}">Buka</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Belum ada pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        <p class="card-link"><a class="gold" href="{{ route('admin.orders.index') }}">Lihat semua pesanan</a></p>
    </div>

    <div class="a-card">
        <h3>Peringatan stok</h3>
        @forelse ($lowItems as $p)
            @php($w = min(100, max(4, round($p->stock / $lowStock * 100))))
            <div class="inv">
                <div class="row"><span>{{ $p->name }}</span><span class="muted">{{ $p->stock }} tersisa</span></div>
                <div class="meter"><i style="width:{{ $w }}%;background:{{ $p->stock <= $lowStock / 2 ? 'var(--bad)' : 'var(--warn)' }}"></i></div>
            </div>
        @empty
            <p class="muted sm">Semua stok di atas {{ $lowStock }}.</p>
        @endforelse
        <p class="card-link"><a class="gold" href="{{ route('admin.inventory.index') }}">Kelola inventori</a></p>
    </div>
</div>
@endsection
