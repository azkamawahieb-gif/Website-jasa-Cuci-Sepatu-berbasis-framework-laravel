<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'photo',
        'price',
        'estimated_days',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean'
        ];
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    // Nama layanan versi URL: "Fast Clean" menjadi "fast-clean"
    public function slug(): string
    {
        return Str::slug($this->name);
    }

    // Mencari foto di public/img/layanan
    // Nama file tidak peduli huruf besar-kecil,
    // spasi, atau ekstensi ganda.
    // Boleh lebih pendek di depan atau salah satu huruf.
    public function fotoUrl(): ?string
    {
        // Foto yang diunggah lewat dashboard petugas didahulukan
        if ($this->photo && is_file(public_path('img/layanan/'.$this->photo))) {
            return asset('img/layanan/'.rawurlencode($this->photo));
        }

        static $daftar = null;

        if ($daftar === null) {
            $daftar = [];

            $dir = public_path('img/layanan');

            if (is_dir($dir)) {
                foreach (scandir($dir) as $berkas) {
                    if (! preg_match('/\.(webp|jpe?g|png)$/i', $berkas)) {
                        continue;
                    }

                    $nama = preg_replace('/(\.(webp|jpe?g|png))+$/i', '', $berkas);

                    $daftar[Str::slug($nama)] = asset(
                        'img/layanan/'.rawurlencode($berkas)
                    );
                }
            }
        }

        $slug = $this->slug();

        if (isset($daftar[$slug])) {
            return $daftar[$slug];
        }

        foreach ($daftar as $kunci => $url) {
            if ($kunci === '') {
                continue;
            }

            if (
                str_starts_with($slug, $kunci.'-') ||
                levenshtein($kunci, $slug) <= 1
            ) {
                return $url;
            }
        }

        return null;
    }
}
