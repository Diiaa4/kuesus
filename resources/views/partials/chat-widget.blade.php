{{-- Chat mengambang kanan bawah. Skrip chat dimuat saat panel pertama kali dibuka. --}}
@unless (auth()->check() && auth()->user()->isAdmin())
<button class="cw-fab" id="cw-fab" type="button" aria-label="Chat dengan admin" aria-expanded="false" aria-controls="cw-panel">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 4h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-5 4v-4H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/></svg><span class="lbl">Chat dengan kami</span>
</button>

<section class="cw-panel" id="cw-panel" hidden aria-label="Chat dengan admin">
    <div class="cw-head">
        <strong>Chat dengan admin</strong>
        <button type="button" id="cw-close" aria-label="Tutup chat">&times;</button>
    </div>
    @if ($chat)
        @include('partials.echo')
        @include('partials.chat-box', ['chat' => $chat, 'lazy' => true])
    @else
        <div class="cw-guest">
            <p>Masuk dulu untuk chat langsung dengan admin toko.</p>
            <a class="btn" href="{{ route('login') }}">Masuk</a>
            <a class="btn ghost" href="{{ route('register') }}">Daftar</a>
        </div>
    @endif
</section>

<script>
(function () {
    var fab = document.getElementById('cw-fab');
    var panel = document.getElementById('cw-panel');
    var closeBtn = document.getElementById('cw-close');
    var hasChat = {{ $chat ? 'true' : 'false' }};
    var loaded = false;

    function toggle(open) {
        panel.hidden = !open;
        fab.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (!open) return;
        if (hasChat && !loaded) {
            loaded = true;
            var s = document.createElement('script');
            s.src = @json(asset('js/chat.js'));
            document.body.appendChild(s);
        }
        var input = panel.querySelector('input[name=body]');
        if (input) input.focus();
    }

    fab.addEventListener('click', function () { toggle(panel.hidden); });
    closeBtn.addEventListener('click', function () { toggle(false); fab.focus(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !panel.hidden) { toggle(false); fab.focus(); }
    });
})();
</script>
@endunless
