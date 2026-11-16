@extends('layouts.petugas')
@section('title', 'Chat')

@section('content')
<h1 class="h4 mb-3">Chat dengan Pengguna</h1>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>Pengguna</th>
          <th>Pesan terakhir</th>
          <th>Waktu</th>
          <th class="text-center">Belum dibaca</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($ringkas as $r)
          @php
            $u = $users[$r->user_id] ?? null;
            $t = $terakhir[$r->terakhir_id] ?? null;
          @endphp
          @if ($u && $t)
            <tr>
              <td class="fw-semibold">{{ $u->name }}<div class="small text-body-secondary fw-normal">{{ $u->phone }}</div></td>
              <td>{{ $t->sender_id === $u->id ? '' : 'Anda: ' }}{{ \Illuminate\Support\Str::limit($t->body, 60) }}</td>
              <td class="small text-body-secondary">{{ $t->created_at->format('d M, H:i') }}</td>
              <td class="text-center">
                @if ($r->belum > 0)
                  <span class="badge text-bg-danger">{{ $r->belum }}</span>
                @else
                  <span class="text-body-secondary">-</span>
                @endif
              </td>
              <td class="text-end"><a href="{{ route('petugas.chat.show', $u) }}" class="btn btn-sm btn-primary">Buka</a></td>
            </tr>
          @endif
        @empty
          <tr><td colspan="5" class="text-center text-body-secondary py-4">Belum ada percakapan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection