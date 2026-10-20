@extends('layouts.app')
@section('title', 'Chat dengan admin')
@section('content')
<div class="wrap page narrow-xl">
    <h2>Chat dengan admin</h2>
    <p class="muted">Pesan terkirim langsung. Admin akan membalas di halaman ini.</p>
    @include('partials.chat-box', ['chat' => $chat])
</div>
@endsection
