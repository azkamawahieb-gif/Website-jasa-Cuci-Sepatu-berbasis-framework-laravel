{{-- Panel chat. Variabel: $pesan (koleksi ChatMessage), $milik (fungsi: pesan ini milik pembaca?),
     $urlKirim, $urlSejak, $kosongTeks. Id dan atribut data-* dipakai public/js/chat.js. --}}
<div class="chat" id="chat" data-sejak="{{ $urlSejak }}" data-terakhir="{{ $pesan->last()?->id ?? 0 }}">
  <div class="chat-daftar" id="chat-daftar" tabindex="0" aria-live="polite" aria-label="Percakapan">
    @forelse ($pesan as $m)
      <div class="chat-gelembung {{ $milik($m) ? 'saya' : 'lawan' }}" data-id="{{ $m->id }}">
        <p>{{ $m->body }}</p>
        <small>{{ $m->created_at->format('d M, H:i') }}</small>
      </div>
    @empty
      <p class="chat-kosong" id="chat-kosong">{{ $kosongTeks }}</p>
    @endforelse
  </div>

  <p class="chat-galat chat-sembunyi" id="chat-galat" role="alert"></p>

  <form method="POST" action="{{ $urlKirim }}" id="chat-form" class="chat-form">
    @csrf
    <label for="chat-isi" class="chat-sr">Tulis pesan</label>
    <textarea id="chat-isi" name="body" rows="1" maxlength="1000" required
              class="chat-isi" placeholder="Tulis pesan..."></textarea>
    <button type="submit" class="chat-kirim" aria-label="Kirim pesan">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4z"/></svg>
    </button>
  </form>
</div>
<script src="{{ asset('js/chat.js') }}" defer></script>