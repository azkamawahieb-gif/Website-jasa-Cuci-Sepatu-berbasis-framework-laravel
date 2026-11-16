// Menu navbar untuk layar kecil.
(function () {
  var tombol = document.querySelector('.nav-toggle');
  var menu = document.getElementById('menu-utama');
  if (!tombol || !menu) return;

  function atur(buka) {
    menu.classList.toggle('buka', buka);
    tombol.setAttribute('aria-expanded', buka ? 'true' : 'false');
    tombol.setAttribute('aria-label', buka ? 'Tutup menu' : 'Buka menu');
  }

  tombol.addEventListener('click', function () {
    atur(!menu.classList.contains('buka'));
  });
  menu.addEventListener('click', function (e) {
    if (e.target.closest('a')) atur(false);
  });
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.nav')) atur(false);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') atur(false);
  });
})();

// Memunculkan elemen ber-class "reveal" saat masuk layar.
(function () {
  var elemen = document.querySelectorAll('.reveal');
  if (!elemen.length) return;

  var kurangiGerak = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!('IntersectionObserver' in window) || kurangiGerak) {
    elemen.forEach(function (e) { e.classList.add('terlihat'); });
    return;
  }

  var pengamat = new IntersectionObserver(function (daftar) {
    daftar.forEach(function (item) {
      if (item.isIntersecting) {
        item.target.classList.add('terlihat');
        pengamat.unobserve(item.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  elemen.forEach(function (e) { pengamat.observe(e); });
})();

// Efek interaktif khusus beranda: progres scroll, paralaks hero, sorot kursor,
// kartu miring, penanda menu aktif, dan tombol kembali ke atas.
(function () {
  var hero = document.querySelector('.hero-utama');
  if (!hero) return;

  var mm = window.matchMedia ? window.matchMedia.bind(window) : null;
  var kurangiGerak = mm && mm('(prefers-reduced-motion: reduce)').matches;
  var adaMouse = mm && mm('(hover: hover) and (pointer: fine)').matches;
  var header = document.querySelector('.header');

  // Bilah progres scroll di paling atas
  var bar = document.createElement('div');
  bar.className = 'progres-gulir';
  bar.setAttribute('aria-hidden', 'true');
  document.body.appendChild(bar);

  // Tombol kembali ke atas
  var atas = document.createElement('button');
  atas.type = 'button';
  atas.className = 'ke-atas';
  atas.setAttribute('aria-label', 'Kembali ke atas');
  atas.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>';
  atas.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: kurangiGerak ? 'auto' : 'smooth' });
  });
  document.body.appendChild(atas);

  // Satu penangan scroll, dibatasi requestAnimationFrame
  var menunggu = false;
  function hitung() {
    menunggu = false;
    var y = window.pageYOffset || document.documentElement.scrollTop;
    var total = document.documentElement.scrollHeight - window.innerHeight;
    bar.style.setProperty('--prog', total > 0 ? Math.min(1, y / total).toFixed(4) : 0);
    if (header) header.classList.toggle('bergulir', y > 24);
    atas.classList.toggle('tampil', y > 700);
    if (!kurangiGerak) {
      var p = Math.min(1, Math.max(0, y / (hero.offsetHeight || 1)));
      hero.style.setProperty('--p', p.toFixed(3));
    }
  }
  function jadwalkan() {
    if (!menunggu) { menunggu = true; requestAnimationFrame(hitung); }
  }
  window.addEventListener('scroll', jadwalkan, { passive: true });
  window.addEventListener('resize', jadwalkan);
  hitung();

  // Sorot cahaya di hero mengikuti kursor
  if (!kurangiGerak && adaMouse) {
    var hm = false, hx = 0, hy = 0;
    hero.addEventListener('pointermove', function (e) {
      var r = hero.getBoundingClientRect();
      hx = e.clientX - r.left; hy = e.clientY - r.top;
      hero.classList.add('sorot');
      if (!hm) {
        hm = true;
        requestAnimationFrame(function () {
          hm = false;
          hero.style.setProperty('--hx', hx + 'px');
          hero.style.setProperty('--hy', hy + 'px');
        });
      }
    });
    hero.addEventListener('pointerleave', function () { hero.classList.remove('sorot'); });
  }

  // Kartu: sorot cahaya mengikuti kursor, dan miring tipis (maks sekitar 4 derajat)
  if (!kurangiGerak && adaMouse) {
    document.querySelectorAll('.kartu-layanan, .fitur, .zona-kartu, .langkah-item').forEach(function (k) {
      var bisaMiring = k.matches('.kartu-layanan, .fitur');
      var sibuk = false, rx = 0, ry = 0, mx = 50, my = 50;

      k.addEventListener('pointermove', function (e) {
        var r = k.getBoundingClientRect();
        var x = (e.clientX - r.left) / r.width;
        var y = (e.clientY - r.top) / r.height;
        mx = x * 100; my = y * 100;
        ry = (x - 0.5) * 8; rx = -(y - 0.5) * 8;
        if (bisaMiring) k.classList.add('miring');
        if (!sibuk) {
          sibuk = true;
          requestAnimationFrame(function () {
            sibuk = false;
            k.style.setProperty('--mx', mx.toFixed(1) + '%');
            k.style.setProperty('--my', my.toFixed(1) + '%');
            if (bisaMiring) {
              k.style.setProperty('--rx', rx.toFixed(2) + 'deg');
              k.style.setProperty('--ry', ry.toFixed(2) + 'deg');
            }
          });
        }
      });

      k.addEventListener('pointerleave', function () {
        k.classList.remove('miring');
        k.style.setProperty('--rx', '0deg');
        k.style.setProperty('--ry', '0deg');
      });
    });
  }

  // Penanda menu aktif sesuai bagian yang sedang dilihat
  if ('IntersectionObserver' in window) {
    var peta = {};
    document.querySelectorAll('.nav-tengah a').forEach(function (a) {
      var h = a.getAttribute('href') || '';
      var i = h.indexOf('#');
      peta[i > -1 ? h.slice(i + 1) : 'beranda'] = a;
    });

    var spy = new IntersectionObserver(function (daftar) {
      daftar.forEach(function (item) {
        if (!item.isIntersecting) return;
        var a = peta[item.target.id];
        if (!a) return;
        Object.keys(peta).forEach(function (k) {
          peta[k].classList.remove('aktif');
          peta[k].removeAttribute('aria-current');
        });
        a.classList.add('aktif');
        a.setAttribute('aria-current', 'true');
      });
    }, { rootMargin: '-45% 0px -50% 0px' });

    Object.keys(peta).forEach(function (id) {
      var el = document.getElementById(id);
      if (el) spy.observe(el);
    });
  }
})();