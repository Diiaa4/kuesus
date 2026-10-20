@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="wrap page">
    <div class="auth-box">
        <a class="logo" href="{{ route('home') }}">{{ $site['name'] }}</a>
        <h2>Buat akun</h2>
        <p class="sub">Daftar gratis, lalu pilih kue favoritmu.</p>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <label for="name">Nama</label>
            <input id="name" name="name" value="{{ old('name') }}" required autofocus>
            @error('name')<p class="err">{{ $message }}</p>@enderror

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            @error('email')<p class="err">{{ $message }}</p>@enderror

            <label for="phone">No. WhatsApp</label>
            <input id="phone" name="phone" value="{{ old('phone') }}" required>
            @error('phone')<p class="err">{{ $message }}</p>@enderror

            <label for="password">Kata sandi (min. 8 karakter)</label>
            <input id="password" type="password" name="password" required>
            @error('password')<p class="err">{{ $message }}</p>@enderror

            <label for="password_confirmation">Ulangi kata sandi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>

            <p><button class="btn block" type="submit">Daftar</button></p>
        </form>
        <p class="muted text-center">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </div>
</div>
@endsection
