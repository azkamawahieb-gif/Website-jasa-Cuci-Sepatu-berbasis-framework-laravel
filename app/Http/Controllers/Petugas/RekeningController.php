<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RekeningController extends Controller
{
    public function index()
    {
        return view('petugas.rekening.index', [
            'rekening' => PaymentAccount::orderByDesc('is_active')->orderBy('bank_name')->get(),
        ]);
    }

    public function create()
    {
        return view('petugas.rekening.form', ['rekening' => new PaymentAccount()]);
    }

    public function store(Request $request)
    {
        PaymentAccount::create($this->validasi($request));

        return redirect()->route('petugas.rekening.index')
            ->with('success', 'Rekening berhasil ditambahkan.');
    }

    public function edit(PaymentAccount $rekening)
    {
        return view('petugas.rekening.form', compact('rekening'));
    }

    public function update(Request $request, PaymentAccount $rekening)
    {
        $rekening->update($this->validasi($request, $rekening));

        return redirect()->route('petugas.rekening.index')
            ->with('success', 'Rekening berhasil diperbarui.');
    }

    public function toggle(PaymentAccount $rekening)
    {
        $rekening->update(['is_active' => ! $rekening->is_active]);

        return back()->with('success', 'Status rekening diperbarui.');
    }

    public function destroy(PaymentAccount $rekening)
    {
        $rekening->delete();

        return back()->with('success', 'Rekening dihapus.');
    }

    private function validasi(Request $request, ?PaymentAccount $rekening = null): array
    {
        $data = $request->validate([
            'bank_name' => ['required', 'string', 'max:30', Rule::unique('payment_accounts', 'bank_name')->ignore($rekening)],
            'account_number' => ['required', 'string', 'max:40', 'regex:/^[0-9 \-]+$/'],
            'account_name' => ['required', 'string', 'max:100'],
        ], [
            'bank_name.required' => 'Nama bank wajib diisi.',
            'bank_name.unique' => 'Bank ini sudah ada. Ubah rekening yang lama atau pakai nama lain.',
            'account_number.required' => 'Nomor rekening wajib diisi.',
            'account_number.regex' => 'Nomor rekening hanya boleh berisi angka.',
            'account_name.required' => 'Atas nama wajib diisi.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}