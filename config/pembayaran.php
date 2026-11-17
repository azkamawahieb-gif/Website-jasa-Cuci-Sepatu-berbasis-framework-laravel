<?php

/*
 | Pengaturan pembayaran untuk halaman pesanan.
 | GANTI nomor rekening dan nama pemilik di bawah dengan data usaha Anda.
 | Untuk QRIS: simpan gambar QRIS toko di public/images/qris.png (atau ubah 'gambar').
 */
return [
    'metode' => [
        'cod' => [
            'nama' => 'COD',
            'ket' => 'Bayar tunai saat sepatu diterima kembali',
        ],
        'transfer' => [
            'nama' => 'Transfer Bank',
            'ket' => 'Transfer ke rekening toko',
        ],
        'qris' => [
            'nama' => 'QRIS',
            'ket' => 'Dana, GoPay, OVO, BCA, dan lainnya',
        ],
    ],

    'bank' => [
        ['nama' => 'BCA',     'rekening' => '1234567890',    'atas_nama' => 'Cucci.in'],
        ['nama' => 'BRI',     'rekening' => '098765432101',  'atas_nama' => 'Cucci.in'],
        ['nama' => 'Mandiri', 'rekening' => '1230009876543', 'atas_nama' => 'Cucci.in'],
        ['nama' => 'BNI',     'rekening' => '0123456789',    'atas_nama' => 'Cucci.in'],
    ],

    'qris' => [
        'merchant' => 'Cucci.in',
        'gambar' => 'images/qris.png',
        'aplikasi' => ['Dana', 'GoPay', 'OVO', 'ShopeePay', 'LinkAja', 'BCA Mobile', 'Livin\' Mandiri', 'Lainnya'],
    ],
];