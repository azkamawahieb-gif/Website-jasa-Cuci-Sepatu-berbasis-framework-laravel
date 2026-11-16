<?php

// Isi halaman detail tiap layanan.
// Kunci = nama layanan huruf kecil, spasi menjadi "-". Contoh: "Fast Clean" => 'fast-clean'.
// Kunci bagian yang dikenal gambar sepatu: upper, tali, midsole, outsole, dalam.
// Hanya Fast Clean yang teksnya dari pemilik. Lainnya DRAF, silakan sesuaikan.
return [

    'fast-clean' => [
        'ringkasan' => 'Pembersihan cepat bagian luar dan sol, cocok untuk sepatu yang tidak terlalu kotor.',
        'bagian' => [
            ['kunci' => 'upper', 'nama' => 'Upper', 'teks' => 'Bagian atas dan samping badan sepatu, termasuk bahan kanvas, mesh, atau kulit luar.'],
            ['kunci' => 'midsole', 'nama' => 'Midsole', 'teks' => 'Bagian tengah atau dinding alas sepatu bagian samping.'],
            ['kunci' => 'outsole', 'nama' => 'Outsole', 'teks' => 'Bagian telapak sepatu yang menyentuh tanah.'],
            ['kunci' => 'tali', 'nama' => 'Tali sepatu', 'teks' => 'Biasanya ikut dibersihkan sekilas tanpa dilepas dari sepatunya.'],
        ],
        'cocok' => ['Sepatu yang tidak terlalu kotor', 'Perawatan rutin sepatu harian'],
        'catatan' => ['Bagian dalam sepatu tidak termasuk. Pilih Deep Clean bila ingin dibersihkan menyeluruh.'],
    ],

    'deep-clean' => [
        'ringkasan' => 'Pembersihan menyeluruh bagian luar dan dalam sepatu.',
        'bagian' => [
            ['kunci' => 'upper', 'nama' => 'Upper', 'teks' => 'Seluruh badan sepatu bagian luar dibersihkan menyeluruh.'],
            ['kunci' => 'dalam', 'nama' => 'Bagian dalam', 'teks' => 'Insole dan lapisan dalam sepatu ikut dibersihkan.'],
            ['kunci' => 'midsole', 'nama' => 'Midsole', 'teks' => 'Dinding alas sepatu dibersihkan hingga ke sela-selanya.'],
            ['kunci' => 'outsole', 'nama' => 'Outsole', 'teks' => 'Telapak sepatu dibersihkan dari kotoran yang menempel.'],
            ['kunci' => 'tali', 'nama' => 'Tali sepatu', 'teks' => 'Tali sepatu dibersihkan bersama bagian luar.'],
        ],
        'cocok' => ['Sneakers kanvas atau knit', 'Sepatu yang cukup kotor atau berbau'],
        'catatan' => ['Estimasi pengerjaan lebih lama dari Fast Clean karena bagian dalam ikut dibersihkan.'],
    ],

    'suede-treatment' => [
        'ringkasan' => 'Perawatan khusus untuk bahan suede dan nubuck yang butuh penanganan lebih lembut.',
        'bagian' => [
            ['kunci' => 'upper', 'nama' => 'Upper suede/nubuck', 'teks' => 'Bahan suede atau nubuck dirawat dengan cara khusus yang lebih lembut dibanding kanvas atau kulit.'],
            ['kunci' => 'tali', 'nama' => 'Tali sepatu', 'teks' => 'Tali sepatu dibersihkan bersama bagian luar.'],
            ['kunci' => 'midsole', 'nama' => 'Midsole', 'teks' => 'Dinding alas sepatu ikut dibersihkan.'],
            ['kunci' => 'outsole', 'nama' => 'Outsole', 'teks' => 'Telapak sepatu ikut dibersihkan.'],
        ],
        'cocok' => ['Sepatu berbahan suede', 'Sepatu berbahan nubuck'],
        'catatan' => ['Bahan suede dan nubuck lebih sensitif, jadi pengerjaannya dilakukan lebih hati-hati.'],
    ],

    'unyellowing' => [
        'ringkasan' => 'Perawatan untuk mengurangi sol sepatu yang menguning.',
        'bagian' => [
            ['kunci' => 'midsole', 'nama' => 'Midsole', 'teks' => 'Dinding sol yang menguning menjadi fokus utama layanan ini.'],
            ['kunci' => 'outsole', 'nama' => 'Outsole', 'teks' => 'Telapak sol ikut dirawat agar tampilan sol lebih seragam.'],
        ],
        'cocok' => ['Sepatu dengan sol putih atau krem yang menguning'],
        'catatan' => ['Hasil dapat berbeda pada tiap sepatu, tergantung tingkat kekuningan dan bahan sol.'],
    ],

];