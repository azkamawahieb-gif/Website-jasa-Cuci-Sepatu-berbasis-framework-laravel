<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\QrisCode;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class QrisController extends Controller
{
    public function index()
    {
        return view('petugas.qris.index', [
            'daftar' => QrisCode::orderByDesc('is_active')->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('petugas.qris.form', ['qris' => new QrisCode()]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request, true);
        $data['image'] = $this->simpanGambar($request->file('image'));

        $qris = QrisCode::create($data);
        $this->jagaSatuAktif($qris);

        return redirect()->route('petugas.qris.index')
            ->with('success', 'QRIS berhasil ditambahkan.');
    }

    public function edit(QrisCode $qris)
    {
        return view('petugas.qris.form', compact('qris'));
    }

    public function update(Request $request, QrisCode $qris)
    {
        $data = $this->validasi($request, false);

        if ($request->hasFile('image')) {
            $this->hapusGambar($qris->image);
            $data['image'] = $this->simpanGambar($request->file('image'));
        }

        $qris->update($data);
        $this->jagaSatuAktif($qris);

        return redirect()->route('petugas.qris.index')
            ->with('success', 'QRIS berhasil diperbarui.');
    }

    public function toggle(QrisCode $qris)
    {
        $qris->update(['is_active' => ! $qris->is_active]);
        $this->jagaSatuAktif($qris);

        return back()->with('success', 'Status QRIS diperbarui.');
    }

    public function destroy(QrisCode $qris)
    {
        $this->hapusGambar($qris->image);
        $qris->delete();

        return back()->with('success', 'QRIS dihapus.');
    }

    private function validasi(Request $request, bool $wajibGambar): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'image' => [$wajibGambar ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama QRIS wajib diisi.',
            'image.required' => 'Gambar QRIS wajib diunggah.',
            'image.image' => 'Berkas harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
            'image.uploaded' => 'Gambar gagal diunggah. Pastikan ukurannya maksimal 2 MB.',
        ]);

        unset($data['image']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    // Hanya satu QRIS yang boleh aktif: yang lain dinonaktifkan.
    private function jagaSatuAktif(QrisCode $qris): void
    {
        if ($qris->is_active) {
            QrisCode::where('id', '!=', $qris->id)->update(['is_active' => false]);
        }
    }

    private function simpanGambar(UploadedFile $file): string
    {
        $nama = 'qris-'.Str::lower(Str::random(12)).'.'.$file->guessExtension();
        $file->move(public_path('img/qris'), $nama);

        return $nama;
    }

    private function hapusGambar(?string $nama): void
    {
        if ($nama && is_file($path = public_path('img/qris/'.basename($nama)))) {
            @unlink($path);
        }
    }
}