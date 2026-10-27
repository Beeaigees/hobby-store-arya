@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Tambah Kategori</h4>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    @include('categories._form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection