(function () {
    'use strict';
    var doc = document;

    // Header toko: transparan di atas hero, solid setelah scroll.
    var head = doc.querySelector('.site-head');
    if (head) {
        var onScroll = function () { head.classList.toggle('scrolled', window.scrollY > 40); };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    function closeMenus(except) {
        doc.querySelectorAll('details.dd[open]').forEach(function (d) {
            if (d !== except) d.removeAttribute('open');
        });
    }

    // Menu navigasi mobile.
    var navBtn = doc.getElementById('nav-toggle');
    function setNav(open) {
        if (!head || !navBtn) return;
        head.classList.toggle('nav-open', open);
        navBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        navBtn.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
    }
    if (navBtn) {
        navBtn.addEventListener('click', function () {
            var open = !head.classList.contains('nav-open');
            setNav(open);
            if (open) setSearch(false);
        });
        doc.querySelectorAll('.nav a').forEach(function (a) {
            a.addEventListener('click', function () { setNav(false); });
        });
    }

    // Kolom cari di header.
    var searchBtn = doc.getElementById('search-toggle');
    var searchBox = doc.getElementById('head-search');
    function setSearch(open) {
        if (!searchBtn || !searchBox) return;
        searchBox.hidden = !open;
        searchBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) searchBox.querySelector('input').focus();
    }
    if (searchBtn && searchBox) {
        searchBtn.addEventListener('click', function () {
            var open = searchBox.hidden;
            setSearch(open);
            if (open) setNav(false);
        });
        var q = searchBox.querySelector('input');
        if (q && q.value) { searchBox.hidden = false; searchBtn.setAttribute('aria-expanded', 'true'); }
    }

    // Sidebar admin (laci di layar kecil).
    var admin = doc.querySelector('.admin');
    var sideBtn = doc.getElementById('side-toggle');
    function setSide(open) {
        if (!admin) return;
        admin.classList.toggle('side-open', open);
        if (sideBtn) sideBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    if (admin && sideBtn) {
        sideBtn.addEventListener('click', function () { setSide(!admin.classList.contains('side-open')); });
        doc.querySelectorAll('[data-side-close]').forEach(function (el) {
            el.addEventListener('click', function () { setSide(false); });
        });
        doc.querySelectorAll('.side a').forEach(function (a) {
            a.addEventListener('click', function () { setSide(false); });
        });
    }

    // Dropdown akun/notifikasi: tutup saat klik di luar atau tekan Esc.
    doc.addEventListener('click', function (e) {
        var inside = e.target.closest ? e.target.closest('details.dd') : null;
        closeMenus(inside);
    });
    doc.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        closeMenus(null);
        setNav(false);
        setSide(false);
        if (searchBox && !searchBox.hidden && !searchBox.querySelector('input').value) setSearch(false);
    });

    // Formulir filter: kirim otomatis saat pilihan berubah.
    doc.querySelectorAll('form[data-autosubmit]').forEach(function (f) {
        f.addEventListener('change', function () { f.submit(); });
    });

    // Pindah ke layar lebar: pastikan panel mobile tertutup.
    window.matchMedia('(min-width:1101px)').addEventListener('change', function (m) { if (m.matches) setNav(false); });
    window.matchMedia('(min-width:1001px)').addEventListener('change', function (m) { if (m.matches) setSide(false); });
})();
