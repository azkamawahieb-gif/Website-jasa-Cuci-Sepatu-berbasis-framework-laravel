// Interaksi kecil untuk halaman login dan daftar.
(function () {
  // Tampilkan / sembunyikan password
  document.querySelectorAll('[data-pw-toggle]').forEach(function (tombol) {
    var kotak = tombol.closest('.pw');
    var input = kotak ? kotak.querySelector('input') : null;
    if (!input) return;

    tombol.addEventListener('click', function () {
      var tampil = input.type === 'password';
      input.type = tampil ? 'text' : 'password';
      tombol.setAttribute('aria-pressed', tampil ? 'true' : 'false');
      tombol.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
    });
  });

  // Cegah form terkirim dua kali
  document.querySelectorAll('form[data-auth-form]').forEach(function (form) {
    form.addEventListener('submit', function () {
      var tombol = form.querySelector('button[type="submit"]');
      if (!tombol) return;
      setTimeout(function () {
        tombol.dataset.teks = tombol.textContent;
        tombol.textContent = tombol.dataset.loading || 'Memproses...';
        tombol.disabled = true;
      }, 0);
    });
  });

  // Tombol Kembali di browser: pulihkan tombol yang tadi dinonaktifkan
  window.addEventListener('pageshow', function (e) {
    if (!e.persisted) return;
    document.querySelectorAll('button[type="submit"][disabled]').forEach(function (t) {
      t.disabled = false;
      if (t.dataset.teks) t.textContent = t.dataset.teks;
    });
  });

  // Panel kanan: sorot cahaya dan gerak halus mengikuti kursor.
  // Hanya di perangkat dengan mouse dan bila gerakan tidak dikurangi.
  var panel = document.querySelector('.auth-panel');
  if (panel && window.matchMedia) {
    var kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var adaMouse = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    if (!kurangiGerak && adaMouse) {
      var menunggu = false, px = 0, py = 0, mx = 0, my = 0;

      var perbarui = function () {
        menunggu = false;
        panel.style.setProperty('--mx', mx + 'px');
        panel.style.setProperty('--my', my + 'px');
        panel.style.setProperty('--px', px + 'px');
        panel.style.setProperty('--py', py + 'px');
      };

      panel.addEventListener('pointerenter', function () { panel.classList.add('sorot'); });

      panel.addEventListener('pointermove', function (e) {
        var r = panel.getBoundingClientRect();
        mx = e.clientX - r.left;
        my = e.clientY - r.top;
        px = -((mx / r.width) - 0.5) * 16;   // teks bergeser berlawanan arah, maks 8px
        py = -((my / r.height) - 0.5) * 16;
        if (!menunggu) { menunggu = true; requestAnimationFrame(perbarui); }
      });

      panel.addEventListener('pointerleave', function () {
        panel.classList.remove('sorot');
        px = 0; py = 0;
        if (!menunggu) { menunggu = true; requestAnimationFrame(perbarui); }
      });
    }
  }
})();