/* Angka berhitung naik dari 0 (Tahap 3). Tanpa library.
   Angka akhir sudah ada di HTML, jadi tanpa JS atau saat gerak dikurangi tetap benar. */
(function () {
  'use strict';

  var kurangiGerak = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (kurangiGerak || !window.requestAnimationFrame) return;

  var angka = document.querySelectorAll('[data-count]');
  var format = new Intl.NumberFormat('id-ID');
  var DURASI = 900;

  function tulis(el, nilai) {
    var awalan = el.hasAttribute('data-rupiah') ? 'Rp ' : '';
    el.textContent = awalan + format.format(Math.round(nilai));
  }

  Array.prototype.forEach.call(angka, function (el, urutan) {
    var target = parseInt(el.getAttribute('data-count'), 10);
    if (isNaN(target) || target <= 0) return;

    tulis(el, 0);
    setTimeout(function () {
      var mulai = null;
      function langkah(waktu) {
        if (mulai === null) mulai = waktu;
        var t = Math.min((waktu - mulai) / DURASI, 1);
        var halus = 1 - Math.pow(1 - t, 3); // easing keluar
        tulis(el, target * halus);
        if (t < 1) {
          window.requestAnimationFrame(langkah);
        } else {
          tulis(el, target);
        }
      }
      window.requestAnimationFrame(langkah);
    }, 150 + urutan * 90);
  });
})();