@extends('layouts.petugas')
@section('title', $qris->exists ? 'Ubah QRIS' : 'Tambah QRIS')

@section('content')
<h1 class="h4 mb-4">{{ $qris->exists ? 'Ubah QRIS' : 'Tambah QRIS' }}</h1>

<div class="card" style="max-width: 640px;">
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data"
          action="{{ $qris->exists ? route('petugas.qris.update', $qris) : route('petugas.qris.store') }}">
      @csrf
      @if ($qris->exists) @method('PUT') @endif

      <div class="mb-3">
        <label for="name" class="form-label">Nama QRIS / merchant</label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $qris->name) }}" placeholder="Cucci.in" maxlength="100" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="mb-3">
        <label for="image" class="form-label">Gambar QRIS</label>
        @if ($qris->exists && ($url = $qris->gambarUrl()))
          <div class="mb-2">
            <img src="{{ $url }}" alt="{{ $qris->name }}" class="rounded border"
                 style="width: 180px; height: 180px; object-fit: contain; background: #fff;">
          </div>
        @endif
        <img id="qris-preview" alt="Pratinjau QRIS" class="rounded border mb-2 d-none"
             style="width: 180px; height: 180px; object-fit: contain; background: #fff;">
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
               class="form-control @error('image') is-invalid @enderror" {{ $qris->exists ? '' : 'required' }}>
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">
          JPG, PNG, atau WEBP, maksimal 2 MB.
          @if ($qris->exists) Kosongkan jika tidak ingin mengganti gambar. @endif
        </div>
      </div>

      <div class="form-check mb-4">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input"
               @checked(old('is_active', $qris->is_active ?? false))>
        <label for="is_active" class="form-check-label">Aktif (QRIS lain otomatis dinonaktifkan)</label>
      </div>

      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="{{ route('petugas.qris.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>

<script>
  document.getElementById('image').addEventListener('change', function () {
    var img = document.getElementById('qris-preview');
    var f = this.files && this.files[0];
    if (!f) { img.classList.add('d-none'); return; }
    img.src = URL.createObjectURL(f);
    img.classList.remove('d-none');
  });
</script>
@endsection