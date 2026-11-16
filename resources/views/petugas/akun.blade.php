@extends('layouts.petugas')
@section('title', 'Akun Saya')

@section('content')
<h1 class="h4 mb-4">Akun Saya</h1>

<div class="card mb-4" style="max-width: 640px;">
  <div class="card-header">Data Akun</div>
  <div class="card-body">
    <form method="POST" action="{{ route('petugas.akun.update') }}">
      @csrf @method('PUT')
      <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
      </div>
      <div class="mb-3">
        <label for="phone" class="form-label">Nomor HP</label>
        <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}">
      </div>
      <button class="btn btn-primary">Simpan</button>
    </form>
  </div>
</div>

<div class="card" style="max-width: 640px;">
  <div class="card-header">Ubah Password</div>
  <div class="card-body">
    <form method="POST" action="{{ route('petugas.akun.password') }}">
      @csrf @method('PUT')
      <div class="mb-3">
        <label for="current_password" class="form-label">Password lama</label>
        <input type="password" id="current_password" name="current_password" class="form-control" required>
      </div>
      <div class="row">
        <div class="col-sm-6 mb-3">
          <label for="password" class="form-label">Password baru</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="col-sm-6 mb-3">
          <label for="password_confirmation" class="form-label">Ulangi password baru</label>
          <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
        </div>
      </div>
      <button class="btn btn-primary">Ubah Password</button>
    </form>
  </div>
</div>
@endsection