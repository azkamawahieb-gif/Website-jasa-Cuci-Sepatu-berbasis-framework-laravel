<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\DeliveryZone;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun petugas (hanya bisa dibuat lewat seeder, bukan form daftar)
        User::create([
            'name' => 'Petugas Utama',
            'email' => 'petugas@cucisepatu.test',
            'phone' => '081200000001',
            'password' => Hash::make('password'),
            'role' => Role::Petugas,
        ]);

        // Satu akun pengguna contoh untuk uji coba
        User::create([
            'name' => 'Pengguna Contoh',
            'email' => 'pengguna@cucisepatu.test',
            'phone' => '081200000002',
            'address' => 'Jl. Contoh No. 1, Jakarta',
            'password' => Hash::make('password'),
            'role' => Role::Pengguna,
        ]);

        // Layanan (sesuaikan nama, harga, dan estimasi dengan usaha Anda)
        $layanan = [
            ['Fast Clean', 'Cuci bagian luar dan sol. Cocok untuk sepatu yang tidak terlalu kotor.', 25000, 2],
            ['Deep Clean', 'Cuci menyeluruh luar dan dalam. Cocok untuk sneakers kanvas/knit.', 40000, 3],
            ['Suede Treatment', 'Perawatan khusus bahan suede dan nubuck.', 60000, 4],
            ['Unyellowing', 'Mengurangi sol yang menguning.', 50000, 4],
        ];
        foreach ($layanan as [$nama, $deskripsi, $harga, $hari]) {
            Service::create([
                'name' => $nama,
                'description' => $deskripsi,
                'price' => $harga,
                'estimated_days' => $hari,
            ]);
        }

        // Zona antar/jemput (biaya per sekali jemput atau antar)
        $zona = [
            ['Zona 1', 'Radius sampai 3 km', 5000],
            ['Zona 2', 'Radius 3 sampai 7 km', 10000],
            ['Zona 3', 'Radius 7 sampai 12 km', 15000],
        ];
        foreach ($zona as [$nama, $deskripsi, $biaya]) {
            DeliveryZone::create([
                'name' => $nama,
                'description' => $deskripsi,
                'fee' => $biaya,
            ]);
        }
    }
}