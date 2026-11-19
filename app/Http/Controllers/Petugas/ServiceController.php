<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

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
        $data = $this->validasi($request);
        $data['photo'] = $request->hasFile('photo') ? $this->simpanFoto($request->file('photo')) : null;

        Service::create($data);

        return redirect()->route('petugas.layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Service $service)
    {
        return view('petugas.layanan.form', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('photo')) {
            $this->hapusFoto($service->photo);
            $data['photo'] = $this->simpanFoto($request->file('photo'));
        } elseif ($request->boolean('hapus_foto')) {
            $this->hapusFoto($service->photo);
            $data['photo'] = null;
        } else {
            unset($data['photo']);
        }

        $service->update($data);

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

        $this->hapusFoto($service->photo);
        $service->delete();

        return back()->with('success', 'Layanan dihapus.');
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'price' => ['required', 'integer', 'min:0', 'max:10000000'],
            'estimated_days' => ['required', 'integer', 'min:1', 'max:30'],
        ], [
            'name.required' => 'Nama layanan wajib diisi.',
            'photo.image' => 'Berkas harus berupa gambar.',
            'photo.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
            'photo.uploaded' => 'Foto gagal diunggah. Pastikan ukurannya maksimal 2 MB.',
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka bulat.',
            'estimated_days.required' => 'Estimasi hari wajib diisi.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    // Foto disimpan di public/img/layanan, folder yang sama dengan foto bawaan,
    // jadi tidak perlu php artisan storage:link.
    private function simpanFoto(UploadedFile $file): string
    {
        $nama = 'layanan-'.Str::lower(Str::random(12)).'.'.$file->guessExtension();
        $file->move(public_path('img/layanan'), $nama);

        return $nama;
    }

    private function hapusFoto(?string $nama): void
    {
        if ($nama && is_file($path = public_path('img/layanan/'.basename($nama)))) {
            @unlink($path);
        }
    }
}