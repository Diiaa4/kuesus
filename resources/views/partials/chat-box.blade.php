<div id="chat" class="chat" data-config="{{ json_encode($chat) }}">
    <div class="chat-list" aria-live="polite"></div>
    <form>
        <input type="text" name="body" placeholder="Tulis pesan..." maxlength="1000" autocomplete="off" aria-label="Pesan">
        <button class="btn" type="submit">Kirim</button>
    </form>
</div>
@unless ($lazy ?? false)
    @push('scripts')
        @include('partials.echo')
        <script src="{{ asset('js/chat.js') }}"></script>
    @endpush
@endunless
