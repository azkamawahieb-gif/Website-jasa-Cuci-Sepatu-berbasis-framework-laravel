{{-- Ringkasan pesanan di atas chat. Variabel: $order (items.service sudah dimuat), $urlDetail. --}}
<div class="chat-pesanan">
  <div class="chat-pesanan-atas">
    <div>
      <span class="chat-pesanan-label">Chat tentang pesanan</span>
      <a href="{{ $urlDetail }}" class="chat-pesanan-kode">{{ $order->code }}</a>
    </div>
    <span class="chat-pesanan-status chat-status-{{ $order->status->color() }}">{{ $order->statusLabel() }}</span>
  </div>

  <ul class="chat-pesanan-item">
    @foreach ($order->items as $item)
      <li>
        <strong>{{ $item->shoe_name ?: 'Sepatu' }}</strong>
        @if ($item->shoe_color) <span>{{ $item->shoe_color }}</span> @endif
        @if ($item->service) <span class="chat-pesanan-layanan">{{ $item->service->name }}</span> @endif
      </li>
    @endforeach
  </ul>
</div>