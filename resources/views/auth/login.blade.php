@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="wrap page">
    <div class="auth-box">
        <a class="logo" href="{{ route('home') }}">{{ $site['name'] }}</a>
        <h2>Masuk</h2>
        <p class="sub">Masuk untuk memesan kue dan chat dengan admin.</p>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')<p class="err">{{ $message }}</p>@enderror

            <label for="password">Kata sandi</label>
            <input id="password" type="password" name="password" required>

            <label class="check"><input type="checkbox" name="remember"> Ingat saya</label>
            <p><button class="btn block" type="submit">Masuk</button></p>
        </form>
        <p class="muted text-center">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
    </div>
</div>
@endsection
