<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'description', 'type', 'value', 'min_order', 'max_discount',
        'quota', 'used_count', 'starts_at', 'ends_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public static function cari(string $kode): ?self
    {
        return static::where('code', strtoupper(trim($kode)))->first();
    }

    // Pesan galat bila voucher tidak bisa dipakai, atau null bila layak.
    public function galat(int $subtotal): ?string
    {
        if (! $this->is_active) {
            return 'Voucher ini sudah tidak aktif.';
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'Voucher ini belum bisa dipakai.';
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return 'Voucher ini sudah kedaluwarsa.';
        }

        if ($this->quota !== null && $this->used_count >= $this->quota) {
            return 'Kuota voucher ini sudah habis.';
        }

        if ($subtotal < $this->min_order) {
            return 'Minimal belanja layanan Rp ' . number_format($this->min_order, 0, ',', '.') . ' untuk voucher ini.';
        }

        return null;
    }

    // Besar potongan (rupiah) dari subtotal layanan. Tidak pernah melebihi subtotal.
    public function potongan(int $subtotal): int
    {
        $potongan = $this->type === 'persen'
            ? (int) floor($subtotal * min($this->value, 100) / 100)
            : (int) $this->value;

        if ($this->type === 'persen' && $this->max_discount !== null) {
            $potongan = min($potongan, (int) $this->max_discount);
        }

        return max(0, min($potongan, $subtotal));
    }

    // Teks singkat: "Diskon 10% (maks. Rp 15.000)"
    public function ringkasan(): string
    {
        if ($this->type === 'persen') {
            $teks = 'Diskon ' . $this->value . '%';

            if ($this->max_discount !== null) {
                $teks .= ' (maks. Rp ' . number_format($this->max_discount, 0, ',', '.') . ')';
            }

            return $teks;
        }

        return 'Potongan Rp ' . number_format($this->value, 0, ',', '.');
    }
}