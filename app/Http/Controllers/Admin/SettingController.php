<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings', ['ready' => Schema::hasTable('settings')]);
    }

    public function update(Request $request)
    {
        if (! Schema::hasTable('settings')) {
            return back()->with('error', 'Tabel pengaturan belum ada. Jalankan: php artisan migrate');
        }

        $data = $request->validate([
            'name' => 'required|string|max:60',
            'tagline' => 'nullable|string|max:250',
            'address' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:120',
            'hours' => 'nullable|string|max:100',
            'lat' => 'nullable|required_with:lng|numeric|between:-90,90',
            'lng' => 'nullable|required_with:lat|numeric|between:-180,180',
            'qris' => 'nullable|image|max:2048',
        ]);

        // File disimpan sebagai path (qris_image); objek unggahan tidak ikut ke tabel.
        unset($data['qris']);

        if ($request->hasFile('qris')) {
            $old = Setting::site()['qris_image'];
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $data['qris_image'] = $request->file('qris')->store('settings', 'public');
        }

        Setting::put($data);

        return back()->with('success', 'Pengaturan toko disimpan.');
    }
}
