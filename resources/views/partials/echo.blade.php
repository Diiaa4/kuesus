@if (config('broadcasting.default') === 'reverb')
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
try {
    window.Pusher = Pusher;
    var EchoClass = window.Echo.default || window.Echo;
    window.Echo = new EchoClass({
        broadcaster: 'reverb',
        key: @json(config('broadcasting.connections.reverb.key')),
        wsHost: @json(config('broadcasting.connections.reverb.options.host')),
        wsPort: {{ (int) config('broadcasting.connections.reverb.options.port', 8080) }},
        wssPort: {{ (int) config('broadcasting.connections.reverb.options.port', 443) }},
        forceTLS: @json(config('broadcasting.connections.reverb.options.scheme') === 'https'),
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } }
    });
    window.chatRealtime = true;
} catch (e) {
    console.warn('Realtime nonaktif, polling tetap jalan.', e);
}
</script>
@endif
