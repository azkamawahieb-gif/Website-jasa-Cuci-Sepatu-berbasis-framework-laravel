<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        return view('petugas.zona.index', [
            'zona' => DeliveryZone::orderByDesc('is_active')->orderBy('fee')->get(),
        ]);
    }

    public function create()
    {
        return view('petugas.zona.form', ['zone' => new DeliveryZone()]);
    }

    public function store(Request $request)
    {
        DeliveryZone::create($this->validasi($request));

        return redirect()->route('petugas.zona.index')
            ->with('success', 'Zona berhasil ditambahkan.');
    }

    public function edit(DeliveryZone $zone)
    {
        return view('petugas.zona.form', compact('zone'));
    }

    public function update(Request $request, DeliveryZone $zone)
    {
        $zone->update($this->validasi($request));

        return redirect()->route('petugas.zona.index')
            ->with('success', 'Zona berhasil diperbarui.');
    }

    public function toggle(DeliveryZone $zone)
    {
        $zone->update(['is_active' => ! $zone->is_active]);

        return back()->with('success', 'Status zona diperbarui.');
    }

    public function destroy(DeliveryZone $zone)
    {
        if (Delivery::where('delivery_zone_id', $zone->id)->exists()) {
            return back()->withErrors([
                'hapus' => 'Zona ini sudah dipakai di pengiriman, jadi tidak bisa dihapus. Nonaktifkan saja.',
            ]);
        }

        $zone->delete();

        return back()->with('success', 'Zona dihapus.');
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'fee' => ['required', 'integer', 'min:0', 'max:1000000'],
        ], [
            'name.required' => 'Nama zona wajib diisi.',
            'fee.required' => 'Biaya wajib diisi.',
            'fee.integer' => 'Biaya harus berupa angka bulat.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}