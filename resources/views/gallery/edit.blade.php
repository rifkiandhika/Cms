@extends('layouts.app')
@section('title', 'Edit Gallery')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('gallery.index') }}">Gallery</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Edit</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex mb-3">
            <h1 class="h3 mb-0 text-gray-600">Edit Gallery</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('gallery.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('gallery.form')

                    <div class="text-end mt-3">
                        <a href="{{ route('gallery.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection