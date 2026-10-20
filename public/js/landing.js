(function () {
    'use strict';

    // Rail produk: tombol geser kiri/kanan.
    var rail = document.getElementById('rail');
    if (rail) {
        document.querySelectorAll('[data-rail]').forEach(function (b) {
            b.addEventListener('click', function () {
                rail.scrollBy({ left: rail.clientWidth * 0.8 * Number(b.dataset.rail), behavior: 'smooth' });
            });
        });
    }

    // Animasi muncul saat scroll.
    var els = document.querySelectorAll('.reveal');
    if (!('IntersectionObserver' in window)) {
        els.forEach(function (e) { e.classList.add('in'); });
    } else {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
            });
        }, { threshold: 0.12 });
        els.forEach(function (e) { io.observe(e); });
    }

    // Tombol "Chat dengan kami" membuka widget.
    var open = document.getElementById('open-chat');
    var fab = document.getElementById('cw-fab');
    var panel = document.getElementById('cw-panel');
    if (open && fab && panel) {
        open.addEventListener('click', function () { if (panel.hidden) fab.click(); });
    }
})();
