@extends('layouts.app')
@section('title', 'Profil saya')
@section('content')
<div class="wrap page">
    <h2>Profil saya</h2>
    <p class="muted">Data ini terisi otomatis saat checkout.</p>
    @include('partials.profile-form', ['user' => $user, 'action' => route('profile.update'), 'showAddress' => true])
</div>
@endsection
