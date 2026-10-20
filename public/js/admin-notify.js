(function () {
    'use strict';
    var admin = document.querySelector('.admin');
    var box = document.getElementById('toasts');
    if (!admin || !box || !admin.dataset.notifyUrl || !window.fetch) return;

    var url = admin.dataset.notifyUrl;
    var last = parseInt(admin.dataset.latestOrder || '0', 10);
    var INTERVAL = 10000;   // cek tiap 10 detik
    var SHOW_MS = 12000;    // toast hilang sendiri setelah 12 detik
    var busy = false;
    var audioCtx = null;

    function el(tag, cls, text) {
        var n = document.createElement(tag);
        if (cls) n.className = cls;
        if (text != null) n.textContent = text;
        return n;
    }

    // Bunyi singkat. Hanya jalan setelah admin pernah menyentuh halaman (aturan browser).
    function beep() {
        try {
            var AC = window.AudioContext || window.webkitAudioContext;
            if (!AC) return;
            audioCtx = audioCtx || new AC();
            if (audioCtx.state === 'suspended') return;
            var o = audioCtx.createOscillator(), g = audioCtx.createGain();
            o.type = 'sine'; o.frequency.value = 880;
            g.gain.setValueAtTime(0.0001, audioCtx.currentTime);
            g.gain.exponentialRampToValueAtTime(0.15, audioCtx.currentTime + 0.02);
            g.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.35);
            o.connect(g); g.connect(audioCtx.destination);
            o.start(); o.stop(audioCtx.currentTime + 0.4);
        } catch (e) {}
    }
    document.addEventListener('pointerdown', function () {
        try {
            var AC = window.AudioContext || window.webkitAudioContext;
            if (AC) { audioCtx = audioCtx || new AC(); if (audioCtx.state === 'suspended') audioCtx.resume(); }
        } catch (e) {}
    }, { once: true });

    function dismiss(t) {
        if (!t.parentNode) return;
        t.classList.add('out');
        setTimeout(function () { if (t.parentNode) t.parentNode.removeChild(t); }, 250);
    }

    function toast(o) {
        var t = el('div', 'toast');
        var ico = el('span', 'toast-ico');
        ico.innerHTML = '<svg class="ico-s lg" viewBox="0 0 24 24"><path d="M6 7h12l1 13H5L6 7Z"/><path d="M9 7a3 3 0 0 1 6 0"/></svg>';
        var body = el('div', 'toast-body');
        body.appendChild(el('strong', null, 'Pesanan baru ' + o.code));
        body.appendChild(el('span', null, o.name + ' - ' + o.total));
        body.appendChild(el('small', null, o.method));
        var go = el('a', 'btn small', 'Lihat');
        go.href = o.url;
        var x = el('button', 'toast-x', '\u00d7');
        x.type = 'button'; x.setAttribute('aria-label', 'Tutup notifikasi');
        x.addEventListener('click', function () { dismiss(t); });
        t.appendChild(ico); t.appendChild(body); t.appendChild(go); t.appendChild(x);
        box.appendChild(t);
        while (box.children.length > 4) box.removeChild(box.firstChild);

        var timer = setTimeout(function () { dismiss(t); }, SHOW_MS);
        t.addEventListener('mouseenter', function () { clearTimeout(timer); });
        t.addEventListener('mouseleave', function () { timer = setTimeout(function () { dismiss(t); }, 4000); });
    }

    function setCounts(c) {
        if (!c) return;
        document.querySelectorAll('[data-badge]').forEach(function (b) {
            var n = c[b.dataset.badge];
            if (n == null) return;
            b.textContent = n;
            b.hidden = !n;
        });
        document.querySelectorAll('[data-count]').forEach(function (b) {
            if (c[b.dataset.count] != null) b.textContent = c[b.dataset.count];
        });
        var bell = document.getElementById('bell');
        if (bell) {
            var low = parseInt((document.querySelector('[data-count="low"]') || {}).textContent || '0', 10);
            bell.classList.toggle('dot', (c.orders || 0) + (c.unread || 0) + low > 0);
        }
    }

    function check() {
        if (busy || document.hidden) return;
        busy = true;
        fetch(url + '?after=' + last, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        }).then(function (r) {
            if (!r.ok) throw new Error(r.status);
            return r.json();
        }).then(function (d) {
            (d.orders || []).forEach(toast);
            if (d.orders && d.orders.length) beep();
            if (d.latest > last) last = d.latest;
            setCounts(d.counts);
        }).catch(function () { /* diam: coba lagi di putaran berikut */ })
          .then(function () { busy = false; });
    }

    setInterval(check, INTERVAL);
    document.addEventListener('visibilitychange', function () { if (!document.hidden) check(); });
})();
