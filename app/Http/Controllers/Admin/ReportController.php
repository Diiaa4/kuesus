<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->period($request);
        $orders = $this->orders($from, $to);

        $revenue = (int) $orders->sum('total');

        return view('admin.reports', [
            'from' => $from,
            'to' => $to,
            'orderCount' => $orders->count(),
            'revenue' => $revenue,
            'paid' => (int) $orders->where('payment_status', 'paid')->sum('total'),
            'average' => $orders->count() ? (int) round($revenue / $orders->count()) : 0,
            'byDay' => $orders->groupBy(fn ($o) => $o->created_at->format('Y-m-d'))
                ->map(fn ($g) => ['count' => $g->count(), 'total' => (int) $g->sum('total')])
                ->sortKeysDesc(),
            'byProduct' => OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', '!=', 'cancelled')
                ->whereBetween('orders.created_at', [$from, $to])
                ->selectRaw('order_items.name as name, SUM(order_items.qty) as qty, SUM(order_items.qty * order_items.price) as total')
                ->groupBy('order_items.name')
                ->orderByDesc('qty')
                ->get(),
        ]);
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->period($request);
        $orders = $this->orders($from, $to);

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
            fputcsv($out, ['Kode', 'Tanggal', 'Pemesan', 'Telepon', 'Metode', 'Status bayar', 'Status', 'Total']);
            foreach ($orders as $o) {
                fputcsv($out, array_map($this->safeCell(...), [$o->code, $o->created_at->format('Y-m-d H:i'),
                    $o->customer_name, $o->phone, strtoupper($o->payment_method),
                    $o->payment_status_label, $o->status_label, $o->total]));
            }
            fclose($out);
        }, 'laporan-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }

    /** Mencegah teks buatan pelanggan dibaca Excel sebagai rumus (=, +, -, @). */
    private function safeCell(mixed $value): mixed
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        $looksLikeFormula = preg_match('/^[=+\-@\t\r]/', $value) && ! preg_match('/^\+?[\d\s().-]+$/', $value);

        return $looksLikeFormula ? "'".$value : $value;
    }

    private function period(Request $request): array
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => ['nullable', 'date', Rule::when($request->filled('from'), 'after_or_equal:from')],
        ]);

        $to = $request->filled('to') ? now()->parse($request->query('to'))->endOfDay() : now()->endOfDay();
        $from = $request->filled('from') ? now()->parse($request->query('from'))->startOfDay() : $to->copy()->subDays(29)->startOfDay();

        return [$from, $to];
    }

    private function orders($from, $to)
    {
        return Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->get();
    }
}
