{{-- Pasang tema tersimpan sebelum halaman digambar agar tidak berkedip. Default: gelap. --}}
<script>
(function () {
    var t = 'dark';
    try { var s = localStorage.getItem('theme'); if (s === 'light' || s === 'dark') t = s; } catch (e) {}
    document.documentElement.setAttribute('data-theme', t);
    document.documentElement.classList.add('js');
})();
</script>
