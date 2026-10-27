@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h4>Selamat datang, {{ auth()->user()->name }}!</h4>
        <p class="mb-0">Role Anda: <strong>{{ auth()->user()->role }}</strong></p>
    </div>
</div>
@endsection