@extends('layouts.admin')
@section('title', 'Profil admin')
@section('content')
@include('partials.profile-form', ['user' => $user, 'action' => route('admin.profile.update'), 'showAddress' => false])
@endsection
