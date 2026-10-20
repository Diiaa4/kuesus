<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public const LOW_STOCK = Product::LOW_STOCK;

    private const COLORS = ['#c8964f', '#e0b16a', '#8c6a35', '#5c5347'];

    private const DAYS = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

    public function index(Request $request)
    {
        $range = $request->query('range') === '30' ? 30 : 7;
        $today = now()->startOfDay();
        $from = $today->copy()->subDays($range - 1);
        $prevFrom = $from->copy()->subDays($range);
        $yesterday = $today->copy()->subDay();

        // Pesanan non-batal untuk periode ini dan periode sebelumnya (dihitung di PHP agar cocok untuk MySQL/SQLite).
        $orders = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $prevFrom->copy()->min($yesterday))
            ->get(['id', 'total', 'created_at']);

        $current = $orders->filter(fn ($o) => $o->created_at->gte($from));
        $previous = $orders->filter(fn ($o) => $o->created_at->lt($from) && $o->created_at->gte($prevFrom));
        $salesTotal = (int) $current->sum('total');

        $byDay = $current->groupBy(fn ($o) => $o->created_at->toDateString())->map->sum('total');
        $bars = collect(range(0, $range - 1))->map(function ($i) use ($from, $byDay, $range) {
            $d = $from->copy()->addDays($i);

            return [
                'label' => $range === 7 ? self::DAYS[$d->dayOfWeek] : $d->format('j'),
                'value' => (int) ($byDay[$d->toDateString()] ?? 0),
            ];
        });
        $maxBar = max(1, $bars->max('value'));

        // Kue terlaris
        $rows = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->where('orders.created_at', '>=', $from)
            ->selectRaw('order_items.name as name, SUM(order_items.qty) as qty')
            ->groupBy('order_items.name')
            ->orderByDesc('qty')
            ->get();
        $totalSold = (int) $rows->sum('qty');
        $segments = $rows->take(3)->map(fn ($r) => ['name' => $r->name, 'qty' => (int) $r->qty])->values();
        $rest = (int) $rows->slice(3)->sum('qty');
        if ($rest > 0) {
            $segments->push(['name' => 'Lainnya', 'qty' => $rest]);
        }
        $acc = 0;
        $stops = [];
        $segments = $segments->map(function ($s, $i) use ($totalSold, &$acc, &$stops) {
            $pct = $totalSold ? $s['qty'] / $totalSold * 100 : 0;
            $color = self::COLORS[$i];
            $stops[] = "$color {$acc}% ".($acc + $pct).'%';
            $acc += $pct;

            return $s + ['pct' => (int) round($pct), 'color' => $color];
        });
        $donut = $totalSold ? 'conic-gradient('.implode(', ', $stops).')' : 'conic-gradient(#3a3733 0 100%)';

        $todayOrders = $orders->filter(fn ($o) => $o->created_at->isSameDay($today));
        $yestOrders = $orders->filter(fn ($o) => $o->created_at->isSameDay($yesterday));

        return view('admin.dashboard', [
            'range' => $range,
            'salesTotal' => $salesTotal,
            'salesChange' => $this->change($salesTotal, (int) $previous->sum('total')),
            'bars' => $bars,
            'maxBar' => $maxBar,
            'segments' => $segments,
            'totalSold' => $totalSold,
            'donut' => $donut,
            'todayCount' => $todayOrders->count(),
            'todayCountChange' => $this->change($todayOrders->count(), $yestOrders->count()),
            'todayRevenue' => (int) $todayOrders->sum('total'),
            'todayRevenueChange' => $this->change((int) $todayOrders->sum('total'), (int) $yestOrders->sum('total')),
            'pending' => Order::where('status', 'new')->count(),
            'lowCount' => Product::lowStock()->count(),
            'recent' => Order::latest()->take(5)->get(),
            'lowItems' => Product::lowStock()->orderBy('stock')->take(5)->get(),
            'lowStock' => self::LOW_STOCK,
        ]);
    }

    /** Persen perubahan; null bila periode pembanding kosong. */
    private function change(int $now, int $before): ?float
    {
        return $before > 0 ? round(($now - $before) / $before * 100, 1) : null;
    }

    /**
     * Dipanggil berkala oleh public/js/admin-notify.js.
     * Mengembalikan pesanan yang id-nya lebih besar dari ?after= beserta hitungan badge.
     */
    public function notifications(Request $request)
    {
        $after = (int) $request->query('after', 0);

        $orders = Order::where('id', '>', $after)
            ->orderBy('id')
            ->limit(10)
            ->get(['id', 'code', 'customer_name', 'total', 'payment_method']);

        return response()->json([
            'latest' => $orders->last()?->id ?? 0,
            'orders' => $orders->map(fn (Order $o) => [
                'id' => $o->id,
                'code' => $o->code,
                'name' => $o->customer_name,
                'total' => 'Rp '.number_format($o->total, 0, ',', '.'),
                'method' => $o->payment_method_label,
                'url' => route('admin.orders.show', $o),
            ])->values(),
            'counts' => [
                'orders' => Order::where('status', 'new')->count(),
                'unread' => Message::whereColumn('sender_id', 'customer_id')->whereNull('read_at')->count(),
                'low' => Product::lowStock()->count(),
            ],
        ]);
    }
}
