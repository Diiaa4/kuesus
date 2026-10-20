<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Collection;

abstract class Controller
{
    /**
     * Produk di keranjang beserta ->qty. Produk yang sudah dihapus atau dinonaktifkan
     * dibuang dari sesi, supaya pelanggan tidak tersangkut di checkout oleh item yang
     * tidak tampil dan tidak bisa dihapus.
     */
    protected function cartProducts(): Collection
    {
        $cart = session('cart', []);

        $products = Product::whereIn('id', array_keys($cart))->where('is_active', true)->get();

        if ($products->count() !== count($cart)) {
            $cart = array_intersect_key($cart, $products->keyBy('id')->all());
            session(['cart' => $cart]);
            session()->now('error', 'Sebagian produk di keranjang sudah tidak tersedia dan dihapus.');
        }

        return $products->each(fn (Product $p) => $p->qty = $cart[$p->id]);
    }
}
