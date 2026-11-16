<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return view('petugas.layanan.index', [
            'layanan' => Service::orderByDesc('is_active')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('petugas.layanan.form', ['service' => new Service()]);
    }

    public function store(Request $request)
    {
        Service::create($this->validasi($request));

        return redirect()->route('petugas.layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Service $service)
    {
        return view('petugas.layanan.form', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $service->update($this->validasi($request));

        return redirect()->route('petugas.layanan.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function toggle(Service $service)
    {
        $service->update(['is_active' => ! $service->is_active]);

        return back()->with('success', 'Status layanan diperbarui.');
    }

    public function destroy(Service $service)
    {
        // Layanan yang sudah pernah dipesan tidak boleh dihapus
        if (OrderItem::where('service_id', $service->id)->exists()) {
            return back()->withErrors([
                'hapus' => 'Layanan ini sudah dipakai di pesanan, jadi tidak bisa dihapus. Nonaktifkan saja.',
            ]);
        }

        $service->delete();

        return back()->with('success', 'Layanan dihapus.');
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'integer', 'min:0', 'max:10000000'],
            'estimated_days' => ['required', 'integer', 'min:1', 'max:30'],
        ], [
            'name.required' => 'Nama layanan wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka bulat.',
            'estimated_days.required' => 'Estimasi hari wajib diisi.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}