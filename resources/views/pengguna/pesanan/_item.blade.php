{{-- Satu kotak sepatu. Atribut data-item, data-nomor, data-hapus dan class js-layanan
     dipakai JavaScript di buat.blade.php: jangan diganti namanya. --}}
<div class="item-box" data-item>
  <div class="item-head">
    <strong class="item-judul">Sepatu <span class="item-no" data-nomor></span></strong>
    <button type="button" class="link-btn teks-bahaya" data-hapus>Hapus</button>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="shoe_name_{{ $i }}">Nama / merek sepatu</label>
      <input type="text" id="shoe_name_{{ $i }}" name="items[{{ $i }}][shoe_name]"
             class="form-control @error('items.' . $i . '.shoe_name') salah @enderror"
             value="{{ $row['shoe_name'] ?? '' }}" placeholder="Contoh: Nike Air Force 1" required>
      @error('items.' . $i . '.shoe_name')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
    </div>
    <div class="form-group">
      <label for="shoe_color_{{ $i }}">Warna</label>
      <input type="text" id="shoe_color_{{ $i }}" name="items[{{ $i }}][shoe_color]" class="form-control"
             value="{{ $row['shoe_color'] ?? '' }}" placeholder="Putih">
    </div>
  </div>
  
  <div class="form-group">
    <label for="shoe_size_{{ $i }}">Ukuran sepatu</label>
    <input type="text" id="shoe_size_{{ $i }}" name="items[{{ $i }}][shoe_size]"
           class="form-control @error('items.' . $i . '.shoe_size') salah @enderror"
           value="{{ $row['shoe_size'] ?? '' }}" placeholder="Contoh: 42" maxlength="10" inputmode="decimal">
    @error('items.' . $i . '.shoe_size')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
  </div>

  <div class="form-group">
    <label for="service_{{ $i }}">Layanan</label>
    <select id="service_{{ $i }}" name="items[{{ $i }}][service_id]"
            class="form-control js-layanan @error('items.' . $i . '.service_id') salah @enderror" required>
      <option value="" data-price="0">- Pilih layanan -</option>
      @foreach ($layanan as $s)
        <option value="{{ $s->id }}" data-price="{{ $s->price }}"
                @selected(($row['service_id'] ?? null) == $s->id)>
          {{ $s->name }} - Rp {{ number_format($s->price, 0, ',', '.') }} (estimasi {{ $s->estimated_days }} hari)
        </option>
      @endforeach
    </select>
    @error('items.' . $i . '.service_id')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
  </div>

  <div class="form-group">
    <label for="kondisi_{{ $i }}">Kondisi sepatu (noda, sobek, sol lepas, dan sebagainya)</label>
    <textarea id="kondisi_{{ $i }}" name="items[{{ $i }}][condition_notes]" rows="2" class="form-control">{{ $row['condition_notes'] ?? '' }}</textarea>
  </div>

  <div class="form-group" style="margin-bottom:0">
    <label for="foto_{{ $i }}">Foto kondisi awal (opsional, maks 2 MB)</label>
    <input type="file" id="foto_{{ $i }}" name="items[{{ $i }}][before_photo]" accept="image/*"
           class="form-control @error('items.' . $i . '.before_photo') salah @enderror">
    @error('items.' . $i . '.before_photo')<p class="pesan-salah" role="alert">{{ $message }}</p>@enderror
  </div>
</div>