<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Produksi: hanya membuat akun admin dari ADMIN_EMAIL dan ADMIN_PASSWORD di .env.
     * Lokal: ditambah pelanggan dan produk contoh. Aman dijalankan berulang.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@kuesus.id');
        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            if (app()->isProduction()) {
                $this->command?->error('ADMIN_PASSWORD belum diisi di .env. Admin tidak dibuat.');

                return;
            }
            $password = 'password';
        }

        User::firstOrCreate(
            ['email' => $email],
            ['name' => 'Admin Toko', 'password' => $password, 'role' => 'admin']
        );

        if (app()->isProduction()) {
            return;
        }

        User::firstOrCreate(
            ['email' => 'customer@kuesus.id'],
            [
                'name' => 'Pelanggan Contoh', 'password' => 'password', 'role' => 'customer',
                'phone' => '081234567890', 'address' => 'Jl. Contoh No. 1, Pelabuhanratu',
            ]
        );

        $products = [
            ['Kue Sus Vla Vanila', 'Kulit sus renyah, isi vla vanila lembut. Dibuat pagi hari.', 3500, 60],
            ['Kue Sus Cokelat', 'Isi vla cokelat pekat dengan topping cokelat leleh.', 4000, 50],
            ['Kue Sus Keju', 'Vla gurih ditambah taburan keju parut.', 4500, 40],
            ['Box Sus Mix isi 12', 'Campuran vanila, cokelat, dan keju dalam satu box. Cocok untuk hantaran.', 45000, 20],
        ];

        foreach ($products as [$name, $desc, $price, $stock]) {
            Product::firstOrCreate(
                ['name' => $name],
                ['description' => $desc, 'price' => $price, 'stock' => $stock, 'is_active' => true]
            );
        }
    }
}
