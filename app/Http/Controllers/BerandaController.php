<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use App\Models\Service;

class BerandaController extends Controller
{
    public function index()
    {
        return view('pengguna.beranda', [
            'layanan' => Service::aktif()->orderBy('price')->get(),
            'zona' => DeliveryZone::aktif()->orderBy('fee')->get(),
        ]);
    }

    public function layanan(string $slug)
    {
        $semua = Service::aktif()->orderBy('price')->get();
        $layanan = $semua->first(fn (Service $s) => $s->slug() === $slug);

        abort_if(! $layanan, 404);

        return view('pengguna.layanan.detail', [
            'layanan' => $layanan,
            'detail' => config('layanan_detail.'.$slug),
            'lainnya' => $semua->where('id', '!=', $layanan->id)->values(),
        ]);
    }
}