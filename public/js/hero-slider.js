/* Slider foto di hero beranda: geser (swipe/scroll), tombol, titik, dan otomatis berganti. */
(function () {
  var root = document.querySelector('[data-slider]');
  if (!root) return;

  var track = root.querySelector('.hs-track');
  var slides = root.querySelectorAll('.hs-slide');
  var dots = root.querySelectorAll('[data-dot]');
  var jumlah = slides.length;
  if (jumlah < 2) return;

  var INTERVAL = 4500;
  var kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var aktif = 0;
  var timer = null;
  var ditahan = false;

  function lebar() { return track.clientWidth || 1; }

  function tandai(i) {
    aktif = i;
    for (var d = 0; d < dots.length; d++) {
      dots[d].setAttribute('aria-current', d === i ? 'true' : 'false');
    }
  }

  function ke(i, halus) {
    i = (i + jumlah) % jumlah;
    track.scrollTo({ left: i * lebar(), behavior: halus === false || kurangiGerak ? 'auto' : 'smooth' });
    tandai(i);
  }

  // Sinkronkan titik saat pengguna menggeser dengan jari/trackpad
  var menunggu = null;
  track.addEventListener('scroll', function () {
    clearTimeout(menunggu);
    menunggu = setTimeout(function () {
      var i = Math.round(track.scrollLeft / lebar());
      if (i !== aktif) tandai(Math.max(0, Math.min(jumlah - 1, i)));
    }, 60);
  }, { passive: true });

  root.querySelector('[data-prev]').addEventListener('click', function () { ke(aktif - 1); mulaiLagi(); });
  root.querySelector('[data-next]').addEventListener('click', function () { ke(aktif + 1); mulaiLagi(); });
  dots.forEach(function (dot) {
    dot.addEventListener('click', function () { ke(parseInt(dot.getAttribute('data-dot'), 10)); mulaiLagi(); });
  });

  track.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowLeft') { e.preventDefault(); ke(aktif - 1); mulaiLagi(); }
    if (e.key === 'ArrowRight') { e.preventDefault(); ke(aktif + 1); mulaiLagi(); }
  });

  function henti() { clearInterval(timer); timer = null; }
  function mulai() {
    if (kurangiGerak || ditahan || timer) return;
    timer = setInterval(function () { ke(aktif + 1); }, INTERVAL);
  }
  function mulaiLagi() { henti(); mulai(); }

  // Berhenti saat kursor di atas slider, fokus ada di dalamnya, atau sedang disentuh
  root.addEventListener('mouseenter', function () { ditahan = true; henti(); });
  root.addEventListener('mouseleave', function () { ditahan = false; mulai(); });
  root.addEventListener('focusin', function () { ditahan = true; henti(); });
  root.addEventListener('focusout', function () { ditahan = false; mulai(); });
  root.addEventListener('touchstart', function () { henti(); }, { passive: true });
  root.addEventListener('touchend', function () { setTimeout(mulai, INTERVAL); }, { passive: true });

  // Hemat tenaga: jeda saat tab tidak terlihat
  document.addEventListener('visibilitychange', function () { document.hidden ? henti() : mulai(); });

  // Tetap di foto yang sama saat ukuran jendela berubah
  window.addEventListener('resize', function () { ke(aktif, false); });

  tandai(0);
  mulai();
})();