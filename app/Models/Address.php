<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'recipient_name', 'phone', 'address', 'detail',
        'province', 'regency', 'district', 'city', 'postal_code',
        'latitude', 'longitude', 'maps_url', 'is_default',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_default' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Contoh: "Senapelan, Kota Pekanbaru, Riau 28151"
    public function wilayah(): string
    {
        $bagian = array_filter([
            $this->district,
            $this->regency ?: $this->city,
            $this->province,
        ]);

        $teks = implode(', ', $bagian);

        return trim($teks . ($this->postal_code ? ' ' . $this->postal_code : ''));
    }

    // Teks satu paragraf, disalin ke kolom alamat di form pesanan.
    public function lengkap(): string
    {
        $jalan = $this->address . ($this->detail ? ', ' . $this->detail : '');
        $wilayah = $this->wilayah();

        return $this->recipient_name . ' (' . $this->phone . '), '
            . $jalan
            . ($wilayah !== '' ? ', ' . $wilayah : '');
    }

    public function petaUrl(): ?string
    {
        if ($this->maps_url) {
            return $this->maps_url;
        }

        if ($this->latitude !== null && $this->longitude !== null) {
            return 'https://www.google.com/maps/search/?api=1&query=' . $this->latitude . ',' . $this->longitude;
        }

        return null;
    }
}