<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->latest()->get();

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $products = $this->cartProducts();
        if ($products->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $total = $products->sum(fn ($p) => $p->price * $p->qty);

        return view('checkout', compact('products', 'total'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'note' => 'nullable|string|max:255',
            'payment_method' => 'required|in:qris,cod',
        ]);

        $cart = session('cart', []);
        if (! $cart) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $order = DB::transaction(function () use ($data, $cart, $request) {
            $products = Product::whereIn('id', array_keys($cart))
                ->where('is_active', true)->lockForUpdate()->get();

            if ($products->isEmpty() || $products->count() !== count($cart)) {
                throw ValidationException::withMessages(['cart' => 'Ada produk di keranjang yang sudah tidak tersedia. Hapus dari keranjang lalu coba lagi.']);
            }

            $total = 0;
            foreach ($products as $p) {
                $qty = $cart[$p->id];
                if ($p->stock < $qty) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$p->name} tinggal {$p->stock}. Ubah jumlah di keranjang.",
                    ]);
                }
                $total += $p->price * $qty;
            }

            do {
                $code = 'SUS-'.strtoupper(Str::random(6));
            } while (Order::where('code', $code)->exists());

            $order = Order::create($data + [
                'user_id' => $request->user()->id,
                'code' => $code,
                'total' => $total,
                'payment_status' => 'unpaid',
                'status' => 'new',
            ]);

            foreach ($products as $p) {
                $order->items()->create([
                    'product_id' => $p->id,
                    'name' => $p->name,
                    'price' => $p->price,
                    'qty' => $cart[$p->id],
                ]);
                $p->decrement('stock', $cart[$p->id]);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('orders.show', $order)->with('success', 'Pesanan dibuat.');
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $order->load('items');

        return view('orders.show', compact('order'));
    }

    public function uploadProof(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($order->payment_method === 'qris' && $order->payment_status !== 'paid', 403);
        abort_if(in_array($order->status, ['cancelled', 'delivered'], true), 403);

        $request->validate(['proof' => 'required|image|max:2048']);

        $old = $order->payment_proof;

        $order->update([
            'payment_proof' => $request->file('proof')->store('proofs', 'public'),
            'payment_status' => 'waiting_verification',
        ]);

        if ($old) {
            Storage::disk('public')->delete($old);
        }

        return back()->with('success', 'Bukti bayar terkirim. Admin akan memeriksa.');
    }
}
