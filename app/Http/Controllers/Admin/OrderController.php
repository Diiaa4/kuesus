<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $payment = $request->query('payment');
        $like = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $q).'%';

        $orders = Order::query()
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->whereRaw("code like ? escape '!'", [$like])
                ->orWhereRaw("customer_name like ? escape '!'", [$like])
                ->orWhereRaw("phone like ? escape '!'", [$like])))
            ->when(in_array($status, array_keys(Order::STATUSES), true), fn ($query) => $query->where('status', $status))
            ->when(in_array($payment, array_keys(Order::PAYMENT_STATUSES), true), fn ($query) => $query->where('payment_status', $payment))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $waiting = Order::where('payment_status', 'waiting_verification')
            ->where('status', '!=', 'cancelled')
            ->count();

        return view('admin.orders.index', compact('orders', 'q', 'status', 'payment', 'waiting'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'user');

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Order::STATUSES)),
            'payment_status' => 'required|in:'.implode(',', array_keys(Order::PAYMENT_STATUSES)),
        ]);

        // COD: uang diterima saat kue sampai.
        if ($order->payment_method === 'cod' && $data['status'] === 'delivered') {
            $data['payment_status'] = 'paid';
        }

        if ($order->payment_method === 'qris' && $data['payment_status'] === 'paid' && ! $order->payment_proof) {
            return back()->with('error', 'Pesanan QRIS belum punya bukti bayar, belum bisa ditandai Lunas.');
        }

        if ($data['status'] === 'delivered' && $data['payment_status'] !== 'paid') {
            return back()->with('error', 'Pesanan QRIS belum lunas, belum bisa ditandai Selesai. Verifikasi pembayaran dulu.');
        }

        // Baris dikunci agar klik ganda "Dibatalkan" tidak mengembalikan stok dua kali.
        $changed = DB::transaction(function () use ($order, $data) {
            $locked = Order::with('items')->lockForUpdate()->findOrFail($order->id);

            if ($locked->status === 'cancelled') {
                return false;
            }

            if ($data['status'] === 'cancelled') {
                foreach ($locked->items as $item) {
                    if ($item->product_id) {
                        Product::whereKey($item->product_id)->increment('stock', $item->qty);
                    }
                }
            }
            $locked->update($data);

            return true;
        });

        if (! $changed) {
            return back()->with('error', 'Pesanan yang dibatalkan tidak bisa diubah.');
        }

        return back()->with('success', 'Pesanan diperbarui.');
    }
}
