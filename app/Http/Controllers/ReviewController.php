<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        if (! $request->user()->hasCompletedOrder()) {
            return redirect()->to(route('home').'#ulasan')
                ->with('error', 'Komentar hanya untuk pelanggan yang pesanannya sudah selesai.');
        }

        $data = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'body' => 'required|string|min:5|max:300',
        ], [
            'rating.required' => 'Pilih jumlah bintang.',
            'body.required' => 'Tulis komentar Anda.',
            'body.min' => 'Komentar minimal 5 karakter.',
        ]);

        Review::create($data + ['user_id' => $request->user()->id]);

        return redirect()->to(route('home').'#ulasan')->with('success', 'Terima kasih, komentar Anda sudah tampil.');
    }
}
