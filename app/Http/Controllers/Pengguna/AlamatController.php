<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AlamatController extends Controller
{
    private const MAKS = 10;

    public function index(Request $request)
    {
        $alamat = $request->user()->addresses()->get();

        return view('pengguna.alamat.index', compact('alamat'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->addresses()->count() >= self::MAKS) {
            throw ValidationException::withMessages([
                'address' => 'Maksimal ' . self::MAKS . ' alamat tersimpan. Hapus salah satu dulu.',
            ]);
        }

        $data = $this->validasi($request);
        $jadiUtama = $request->boolean('is_default') || ! $user->addresses()->exists();

        $alamat = $user->addresses()->create($data + ['is_default' => false]);

        if ($jadiUtama) {
            $this->jadikanUtama($alamat);
        }

        if ($request->expectsJson()) {
            return response()->json(['alamat' => $this->ringkas($alamat->fresh())], 201);
        }

        return redirect()->route('alamat.index')->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function update(Request $request, Address $alamat)
    {
        $this->pastikanMilik($request, $alamat);

        $alamat->update($this->validasi($request));

        if ($request->boolean('is_default')) {
            $this->jadikanUtama($alamat);
        }

        if ($request->expectsJson()) {
            return response()->json(['alamat' => $this->ringkas($alamat->fresh())]);
        }

        return redirect()->route('alamat.index')->with('success', 'Alamat diperbarui.');
    }

    public function utama(Request $request, Address $alamat)
    {
        $this->pastikanMilik($request, $alamat);
        $this->jadikanUtama($alamat);

        return redirect()->route('alamat.index')->with('success', 'Alamat utama diganti.');
    }

    public function destroy(Request $request, Address $alamat)
    {
        $this->pastikanMilik($request, $alamat);

        $adalahUtama = $alamat->is_default;
        $alamat->delete();

        if ($adalahUtama) {
            $pengganti = $request->user()->addresses()->latest('id')->first();

            if ($pengganti) {
                $this->jadikanUtama($pengganti);
            }
        }

        return redirect()->route('alamat.index')->with('success', 'Alamat dihapus.');
    }

    private function pastikanMilik(Request $request, Address $alamat): void
    {
        abort_unless($alamat->user_id === $request->user()->id, 404);
    }

    private function jadikanUtama(Address $alamat): void
    {
        Address::where('user_id', $alamat->user_id)->update(['is_default' => false]);

        $alamat->update(['is_default' => true]);
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'province' => ['required', 'string', 'max:100'],
            'regency' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'regex:/^[0-9]{5}$/'],
            'address' => ['required', 'string', 'max:500'],
            'detail' => ['nullable', 'string', 'max:200'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'label' => ['nullable', Rule::in(['Kantor', 'Rumah'])],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'maps_url' => ['nullable', 'url:http,https', 'max:500'],
        ], [
            'province.required' => 'Provinsi wajib diisi.',
            'regency.required' => 'Kabupaten/Kota wajib diisi.',
            'district.required' => 'Kecamatan wajib diisi.',
            'postal_code.required' => 'Kode pos wajib diisi.',
            'postal_code.regex' => 'Kode pos harus 5 angka.',
            'address.required' => 'Nama jalan, gedung, atau nomor rumah wajib diisi.',
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Nomor telepon tidak valid. Contoh: 081234567890.',
            'label.in' => 'Pilih Kantor atau Rumah.',
            'maps_url.url' => 'Tautan Google Maps harus diawali http:// atau https://.',
        ]);

        // Tombol Kantor/Rumah boleh tidak dipilih: kosongkan label lama.
        $data['label'] = $data['label'] ?? null;

        return $data;
    }

    // Data untuk daftar pilihan di form pesanan (balasan JSON).
    private function ringkas(Address $a): array
    {
        return $a->ringkas();
    }
}
