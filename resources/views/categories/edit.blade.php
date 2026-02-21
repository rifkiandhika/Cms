@extends('layouts.app')
@section('title', 'Edit Kategori')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('categories.index') }}">Kategori</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Edit</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex mb-3">
            <h1 class="h3 mb-0 text-gray-600">Edit Kategori: {{ $category->name }}</h1>
        </div>

        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            @include('categories.form')

            <div class="text-end mt-4">
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line me-1"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i>Update
                </button>
            </div>
        </form>
    </div>
</section>
@endsection