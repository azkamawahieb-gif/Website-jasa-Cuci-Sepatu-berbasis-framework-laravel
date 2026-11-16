@extends('layouts.petugas')
@section('title', $service->exists ? 'Ubah Layanan' : 'Tambah Layanan')

@section('content')
<h1 class="h4 mb-4">{{ $service->exists ? 'Ubah Layanan' : 'Tambah Layanan' }}</h1>

<div class="card" style="max-width: 640px;">
  <div class="card-body">
    <form method="POST"
          action="{{ $service->exists ? route('petugas.layanan.update', $service) : route('petugas.layanan.store') }}">
      @csrf
      @if ($service->exists) @method('PUT') @endif

      <div class="mb-3">
        <label for="name" class="form-label">Nama layanan</label>
        <input type="text" id="name" name="name" class="form-control"
               value="{{ old('name', $service->name) }}" required>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea id="description" name="description" rows="3" class="form-control"
                  placeholder="Contoh: Cocok untuk sneakers kanvas">{{ old('description', $service->description) }}</textarea>
      </div>

      <div class="row">
        <div class="col-sm-6 mb-3">
          <label for="price" class="form-label">Harga (Rp)</label>
          <input type="number" id="price" name="price" class="form-control" min="0" step="500"
                 value="{{ old('price', $service->price) }}" required>
        </div>
        <div class="col-sm-6 mb-3">
          <label for="estimated_days" class="form-label">Estimasi pengerjaan (hari)</label>
          <input type="number" id="estimated_days" name="estimated_days" class="form-control" min="1" max="30"
                 value="{{ old('estimated_days', $service->estimated_days ?? 3) }}" required>
        </div>
      </div>

      <div class="form-check mb-4">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input"
               @checked(old('is_active', $service->is_active ?? true))>
        <label for="is_active" class="form-check-label">Aktif (tampil di beranda dan bisa dipesan)</label>
      </div>

      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="{{ route('petugas.layanan.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>
@endsection