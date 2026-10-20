<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        return view('admin.reviews', ['reviews' => Review::with('user')->latest()->paginate(15)]);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Komentar dihapus.');
    }
}
