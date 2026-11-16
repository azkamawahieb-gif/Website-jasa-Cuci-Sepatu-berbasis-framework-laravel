<?php

namespace App\Enums;

enum OrderStatus: string
{
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case MenungguJemput = 'menunggu_jemput';
    case Diterima = 'diterima';
    case Dicuci = 'dicuci';
    case Siap = 'siap';
    case DalamPengantaran = 'dalam_pengantaran';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::MenungguKonfirmasi => 'Menunggu Konfirmasi',
            self::MenungguJemput => 'Menunggu Jemput',
            self::Diterima => 'Diterima',
            self::Dicuci => 'Sedang Dicuci',
            self::Siap => 'Siap Diambil/Diantar',
            self::DalamPengantaran => 'Dalam Pengantaran',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    // Warna badge (cocok untuk CoreUI "text-bg-*" maupun CSS pengguna "badge-*")
    public function color(): string
    {
        return match ($this) {
            self::MenungguKonfirmasi => 'warning',
            self::MenungguJemput, self::Diterima => 'info',
            self::Dicuci => 'primary',
            self::Siap, self::Selesai => 'success',
            self::DalamPengantaran => 'secondary',
            self::Dibatalkan => 'danger',
        };
    }

    // Pengguna hanya boleh membatalkan pada dua status ini
    public function bisaDibatalkanPengguna(): bool
    {
        return in_array($this, [self::MenungguKonfirmasi, self::MenungguJemput], true);
    }

    // Pesanan masih berjalan (belum selesai atau dibatalkan)
    public function berjalan(): bool
    {
        return ! in_array($this, [self::Selesai, self::Dibatalkan], true);
    }
}