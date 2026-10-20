<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /** Landing page: hero, menu pilihan, cerita, ulasan, lokasi. */
    public function home(Request $request)
    {
        $products = Product::where('is_active', true)->latest()->take(8)->get();
        $reviews = Review::with('user')->latest()->take(4)->get();
        $user = $request->user();

        return view('landing', [
            'products' => $products,
            'reviews' => $reviews,
            'canReview' => $user && ! $user->isAdmin() && $user->hasCompletedOrder(),
            'chat' => $this->chatConfig($request),
        ]);
    }

    /** Halaman menu: kategori + semua produk. */
    public function menu(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $products = Product::where('is_active', true)
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', '%'.$q.'%')
                ->orWhere('description', 'like', '%'.$q.'%')))
            ->latest()->get();

        return view('menu', [
            'products' => $products,
            'q' => $q,
            'chat' => $this->chatConfig($request),
        ]);
    }

    /** Konfigurasi widget chat (hanya untuk customer yang login). */
    private function chatConfig(Request $request): ?array
    {
        $user = $request->user();
        if (! $user || $user->isAdmin()) {
            return null;
        }

        return [
            'customerId' => $user->id,
            'me' => $user->id,
            'messagesUrl' => route('chat.messages'),
            'sendUrl' => route('chat.send'),
            'csrf' => csrf_token(),
        ];
    }
}
