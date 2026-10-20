{{-- $p: Product --}}
<article class="card">
    <img src="{{ $p->image_url }}" alt="{{ $p->name }}" loading="lazy">
    <div class="body">
        <h3>{{ $p->name }}</h3>
        <p class="desc">{{ $p->description }}</p>
        <div class="price">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
        @if ($p->stock > 0)
            <span class="tag">Stok {{ $p->stock }}</span>
        @else
            <span class="tag out">Habis</span>
        @endif

        @guest
            <a class="btn small" href="{{ route('login') }}">Masuk untuk beli</a>
        @else
            @unless (auth()->user()->isAdmin())
                <form method="POST" action="{{ route('cart.add', $p) }}">
                    @csrf
                    <button class="btn small" type="submit" @disabled($p->stock < 1)>{{ $p->stock < 1 ? 'Stok habis' : 'Tambah ke keranjang' }}</button>
                </form>
            @endunless
        @endguest
    </div>
</article>
