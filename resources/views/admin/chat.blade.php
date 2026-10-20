@extends('layouts.admin')
@section('title', 'Chat')
@section('content')

<div class="chat-layout">
    <div class="threads" id="threads">
        @forelse ($threads as $t)
            <a href="{{ route('admin.chat', $t->id) }}" class="{{ $customer && $customer->id === $t->id ? 'on' : '' }}">
                <span>{{ $t->name }}</span>
                @if ($t->unread)<span class="badge">{{ $t->unread }}</span>@endif
            </a>
        @empty
            <p class="muted">Belum ada percakapan.</p>
        @endforelse
    </div>

    <div>
        @if ($customer)
            <h3>{{ $customer->name }} <small class="muted">{{ $customer->phone }}</small></h3>
            @include('partials.chat-box', ['chat' => $chat])
        @else
            <p class="muted">Pilih pelanggan di kiri untuk membuka percakapan.</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var box = document.getElementById('threads');
    var activeId = {{ $customer?->id ?? 'null' }};
    function render(rows) {
        box.textContent = '';
        if (!rows.length) { var p = document.createElement('p'); p.className = 'muted'; p.textContent = 'Belum ada percakapan.'; box.appendChild(p); return; }
        rows.forEach(function (t) {
            var a = document.createElement('a');
            a.href = t.url;
            if (t.id === activeId) a.className = 'on';
            var n = document.createElement('span'); n.textContent = t.name; a.appendChild(n);
            if (t.unread && t.id !== activeId) { var b = document.createElement('span'); b.className = 'badge'; b.textContent = t.unread; a.appendChild(b); }
            box.appendChild(a);
        });
    }
    function refresh() {
        fetch('{{ route('admin.chat.threads') }}', { headers: { Accept: 'application/json' } })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (rows) { if (rows) render(rows); })
            .catch(function () {});
    }
    setInterval(refresh, 5000);
})();
</script>
@endpush
