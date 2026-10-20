<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $low = $request->boolean('low');

        $products = Product::query()
            ->when($low, fn ($q) => $q->lowStock())
            ->orderBy('stock')
            ->orderBy('name')
            ->get();

        return view('admin.inventory', [
            'products' => $products,
            'low' => $low,
            'threshold' => Product::LOW_STOCK,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(['stock' => 'required|integer|min:0|max:100000']);

        $product->update($data);

        return back()->with('success', 'Stok '.$product->name.' diperbarui.');
    }
}
