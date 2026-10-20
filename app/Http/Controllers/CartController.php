<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $products = $this->cartProducts();

        $total = $products->sum(fn ($p) => $p->price * $p->qty);

        return view('cart', compact('products', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        if ($product->stock < 1) {
            return redirect()->to(url()->previous().'#produk')->with('error', $product->name.' sedang habis.');
        }

        $cart = session('cart', []);
        $qty = ($cart[$product->id] ?? 0) + max(1, (int) $request->input('qty', 1));
        $cart[$product->id] = min($qty, $product->stock);
        session(['cart' => $cart]);

        return redirect()->to(url()->previous().'#produk')->with('success', $product->name.' masuk keranjang.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(['qty' => 'required|integer|min:1']);

        $cart = session('cart', []);
        if (isset($cart[$product->id])) {
            $cart[$product->id] = min($data['qty'], max(1, $product->stock));
            session(['cart' => $cart]);
        }

        return back();
    }

    public function remove(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return back();
    }
}
