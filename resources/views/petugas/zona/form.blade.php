@extends('layouts.petugas')
@section('title', $zone->exists ? 'Ubah Zona' : 'Tambah Zona')

@section('content')
<h1 class="h4 mb-4">{{ $zone->exists ? 'Ubah Zona' : 'Tambah Zona' }}</h1>

<div class="card" style="max-width: 640px;">
  <div class="card-body">
    <form method="POST"
          action="{{ $zone->exists ? route('petugas.zona.update', $zone) : route('petugas.zona.store') }}">
      @csrf
      @if ($zone->exists) @method('PUT') @endif

      <div class="mb-3">
        <label for="name" class="form-label">Nama zona</label>
        <input type="text" id="name" name="name" class="form-control"
               value="{{ old('name', $zone->name) }}" placeholder="Zona 1" required>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Area / keterangan</label>
        <input type="text" id="description" name="description" class="form-control"
               value="{{ old('description', $zone->description) }}" placeholder="Radius sampai 3 km">
      </div>

      <div class="mb-3">
        <label for="fee" class="form-label">Biaya (Rp) per sekali jemput atau antar</label>
        <input type="number" id="fee" name="fee" class="form-control" min="0" step="500"
               value="{{ old('fee', $zone->fee) }}" required>
      </div>

      <div class="form-check mb-4">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input"
               @checked(old('is_active', $zone->is_active ?? true))>
        <label for="is_active" class="form-check-label">Aktif</label>
      </div>

      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="{{ route('petugas.zona.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>
@endsection