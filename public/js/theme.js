(function () {
    var root = document.documentElement;
    function label(btn) {
        var light = root.getAttribute('data-theme') === 'light';
        var text = light ? 'Ganti ke tema gelap' : 'Ganti ke tema terang';
        btn.setAttribute('aria-label', text);
        btn.setAttribute('title', text);
        btn.setAttribute('aria-pressed', light ? 'true' : 'false');
    }
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
        label(btn);
        btn.addEventListener('click', function () {
            var next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            root.setAttribute('data-theme', next);
            try { localStorage.setItem('theme', next); } catch (e) {}
            document.querySelectorAll('[data-theme-toggle]').forEach(label);
        });
    });
})();
