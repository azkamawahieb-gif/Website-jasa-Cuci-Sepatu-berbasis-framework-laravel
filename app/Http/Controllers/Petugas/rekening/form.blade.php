@extends('layouts.petugas')
@section('title', $rekening->exists ? 'Ubah Rekening' : 'Tambah Rekening')

@section('content')
<h1 class="h4 mb-4">{{ $rekening->exists ? 'Ubah Rekening' : 'Tambah Rekening' }}</h1>

<div class="card" style="max-width: 640px;">
  <div class="card-body">
    <form method="POST"
          action="{{ $rekening->exists ? route('petugas.rekening.update', $rekening) : route('petugas.rekening.store') }}">
      @csrf
      @if ($rekening->exists) @method('PUT') @endif

      <div class="mb-3">
        <label for="bank_name" class="form-label">Nama bank</label>
        <input type="text" id="bank_name" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
               value="{{ old('bank_name', $rekening->bank_name) }}" placeholder="BCA" maxlength="30" required>
        @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="mb-3">
        <label for="account_number" class="form-label">Nomor rekening</label>
        <input type="text" id="account_number" name="account_number" inputmode="numeric"
               class="form-control @error('account_number') is-invalid @enderror"
               value="{{ old('account_number', $rekening->account_number) }}" placeholder="1234567890" maxlength="40" required>
        @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="mb-3">
        <label for="account_name" class="form-label">Atas nama</label>
        <input type="text" id="account_name" name="account_name" class="form-control @error('account_name') is-invalid @enderror"
               value="{{ old('account_name', $rekening->account_name) }}" placeholder="Cucci.in" maxlength="100" required>
        @error('account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="form-check mb-4">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input"
               @checked(old('is_active', $rekening->is_active ?? true))>
        <label for="is_active" class="form-check-label">Aktif (tampil di halaman pembayaran)</label>
      </div>

      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="{{ route('petugas.rekening.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>
@endsection