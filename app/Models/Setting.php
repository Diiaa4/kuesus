<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    private static ?array $site = null;

    public static function defaults(): array
    {
        return [
            'name' => config('app.name'),
            'tagline' => 'Kue sus isi vla buatan rumah dari telur, susu, dan mentega pilihan. Dipanggang pagi ini, siap diantar hari ini.',
            'address' => 'Jl. Contoh No. 1, Pelabuhanratu',
            'phone' => '+62 812-3456-7890',
            'email' => 'halo@kuesus.test',
            'hours' => 'Setiap hari, 07.00 - 21.00',
            'qris_image' => '',
            'lat' => '',
            'lng' => '',
        ];
    }

    /** Info toko: nilai tersimpan menimpa default. Aman dipanggil sebelum migrasi. */
    public static function site(): array
    {
        if (self::$site !== null) {
            return self::$site;
        }

        $stored = [];
        try {
            if (Schema::hasTable('settings')) {
                $stored = static::pluck('value', 'key')->all();
            }
        } catch (\Throwable $e) {
            // tabel belum ada: pakai default
        }

        // Nilai tersimpan menimpa default, termasuk yang sengaja dikosongkan admin.
        $site = array_merge(
            self::defaults(),
            array_map(fn ($v) => (string) ($v ?? ''), $stored)
        );

        // Titik koordinat dipakai bila diisi; kalau kosong, Google Maps mencari berdasarkan alamat.
        $place = ($site['lat'] !== '' && $site['lng'] !== '') ? $site['lat'].','.$site['lng'] : $site['address'];
        $site['logo_url'] = asset('images/logo.png'); // logo tetap, tidak bisa diubah dari admin
        $site['map_url'] = 'https://www.google.com/maps/search/?api=1&query='.urlencode($place);
        $site['dir_url'] = 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($place);
        $site['map_embed'] = 'https://maps.google.com/maps?q='.urlencode($place).'&z=16&output=embed';

        return self::$site = $site;
    }

    /** Kosongkan cache info toko (dipakai test agar tiap test mulai bersih). */
    public static function flush(): void
    {
        self::$site = null;
    }

    public static function put(array $data): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        self::flush();
    }
}
