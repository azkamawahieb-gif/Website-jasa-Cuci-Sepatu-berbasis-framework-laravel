@extends('layouts.petugas')
@section('title', $voucher->exists ? 'Ubah Voucher' : 'Tambah Voucher')

@section('content')
@php
  $tipe = old('type', $voucher->type ?? 'nominal');
  $fmt = fn ($t) => $t ? $t->format('Y-m-d\TH:i') : '';
@endphp
<h1 class="h4 mb-4">{{ $voucher->exists ? 'Ubah Voucher' : 'Tambah Voucher' }}</h1>

<div class="card" style="max-width: 640px;">
  <div class="card-body">
    <form method="POST"
          action="{{ $voucher->exists ? route('petugas.voucher.update', $voucher) : route('petugas.voucher.store') }}">
      @csrf
      @if ($voucher->exists) @method('PUT') @endif

      <div class="mb-3">
        <label for="code" class="form-label">Kode voucher</label>
        <input type="text" id="code" name="code" maxlength="30"
               class="form-control text-uppercase @error('code') is-invalid @enderror"
               value="{{ old('code', $voucher->code) }}" placeholder="HEMAT10" required>
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">Kode yang diketik pelanggan saat memesan. Huruf dan angka, tanpa spasi.</div>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Nama voucher</label>
        <input type="text" id="description" name="description" maxlength="255"
               class="form-control @error('description') is-invalid @enderror"
               value="{{ old('description', $voucher->description) }}" placeholder="Promo Hari Sepatu Nasional" required>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="row">
        <div class="col-sm-6 mb-3">
          <label for="type" class="form-label">Jenis potongan</label>
          <select id="type" name="type" class="form-select">
            <option value="nominal" @selected($tipe === 'nominal')>Nominal (Rp)</option>
            <option value="persen" @selected($tipe === 'persen')>Persen (%)</option>
          </select>
        </div>
        <div class="col-sm-6 mb-3">
          <label for="value" class="form-label"><span id="label-value">Potongan (Rp)</span></label>
          <input type="number" id="value" name="value" min="1"
                 class="form-control @error('value') is-invalid @enderror"
                 value="{{ old('value', $voucher->value) }}" required>
          @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6 mb-3">
          <label for="min_order" class="form-label">Minimal belanja layanan (Rp)</label>
          <input type="number" id="min_order" name="min_order" min="0" step="500"
                 class="form-control @error('min_order') is-invalid @enderror"
                 value="{{ old('min_order', $voucher->min_order ?? 0) }}">
          @error('min_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-sm-6 mb-3" id="kolom-maks">
          <label for="max_discount" class="form-label">Potongan maksimal (Rp)</label>
          <input type="number" id="max_discount" name="max_discount" min="1"
                 class="form-control @error('max_discount') is-invalid @enderror"
                 value="{{ old('max_discount', $voucher->max_discount) }}" placeholder="Kosongkan = tanpa batas">
          @error('max_discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
          <div class="form-text">Hanya untuk potongan persen.</div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6 mb-3">
          <label for="starts_at" class="form-label">Berlaku mulai</label>
          <input type="datetime-local" id="starts_at" name="starts_at"
                 class="form-control @error('starts_at') is-invalid @enderror"
                 value="{{ old('starts_at', $fmt($voucher->starts_at)) }}">
          @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
          <div class="form-text">Kosongkan = langsung berlaku.</div>
        </div>
        <div class="col-sm-6 mb-3">
          <label for="ends_at" class="form-label">Berlaku sampai</label>
          <input type="datetime-local" id="ends_at" name="ends_at"
                 class="form-control @error('ends_at') is-invalid @enderror"
                 value="{{ old('ends_at', $fmt($voucher->ends_at)) }}">
          @error('ends_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
          <div class="form-text">Kosongkan = tanpa batas waktu.</div>
        </div>
      </div>

      <div class="mb-3">
        <label for="quota" class="form-label">Kuota klaim</label>
        <input type="number" id="quota" name="quota" min="1"
               class="form-control @error('quota') is-invalid @enderror"
               value="{{ old('quota', $voucher->quota) }}" placeholder="Kosongkan = tanpa batas">
        @error('quota') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">
          Berapa kali voucher bisa dipakai secara total.
          @if ($voucher->exists) Sudah terpakai {{ $voucher->used_count }} kali. @endif
        </div>
      </div>

      <div class="form-check mb-4">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input"
               @checked(old('is_active', $voucher->is_active ?? true))>
        <label for="is_active" class="form-check-label">Aktif (bisa dipakai pelanggan)</label>
      </div>

      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="{{ route('petugas.voucher.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>

<script>
  (function () {
    var tipe = document.getElementById('type');
    function atur() {
      var persen = tipe.value === 'persen';
      document.getElementById('label-value').textContent = persen ? 'Potongan (%)' : 'Potongan (Rp)';
      document.getElementById('value').max = persen ? 100 : 10000000;
      document.getElementById('kolom-maks').style.display = persen ? '' : 'none';
    }
    tipe.addEventListener('change', atur);
    atur();
  })();
</script>
@endsection