<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh voucher. Ubah atau tambah sesuai promo usaha Anda.
        Voucher::updateOrCreate(['code' => 'HEMAT10'], [
            'description' => 'Diskon 10% semua layanan',
            'type' => 'persen', 'value' => 10, 'min_order' => 30000, 'max_discount' => 15000,
        ]);

        Voucher::updateOrCreate(['code' => 'SEPATUBARU'], [
            'description' => 'Potongan untuk pesanan pertama',
            'type' => 'nominal', 'value' => 10000, 'min_order' => 25000,
        ]);
    }
}