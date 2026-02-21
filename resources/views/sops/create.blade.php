@extends('layouts.app')
@section('title', 'Tambah SOP')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('sops.index') }}">SOP</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Tambah</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex mb-3">
            <h1 class="h3 mb-0 text-gray-600">Tambah SOP Baru</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('sops.store') }}" method="POST" enctype="multipart/form-data" id="sopForm">
                    @csrf
                    @include('sops.form')

                    <div class="text-end mt-4">
                        <a href="{{ route('sops.index') }}" class="btn btn-secondary me-2">
                            <i class="ri-arrow-left-line me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Simpan SOP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection