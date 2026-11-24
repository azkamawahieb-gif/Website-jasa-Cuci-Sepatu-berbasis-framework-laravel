<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrisCode extends Model
{
    protected $fillable = ['name', 'image', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function gambarUrl(): ?string
    {
        return is_file(public_path('img/qris/'.basename($this->image)))
            ? asset('img/qris/'.rawurlencode(basename($this->image)))
            : null;
    }
}