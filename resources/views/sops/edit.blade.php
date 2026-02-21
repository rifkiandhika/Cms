@extends('layouts.app')
@section('title', 'Edit SOP')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('sops.index') }}">SOP</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Edit</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex mb-3 justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-600">Edit SOP</h1>
            <div>
                <span class="badge bg-info">{{ $sop->no_sop }}</span>
                <span class="badge bg-secondary">Rev. {{ $sop->revisi }}</span>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('sops.update', $sop->id) }}" method="POST" enctype="multipart/form-data" id="sopForm">
                    @csrf
                    @method('PUT')
                    @include('sops.form')

                    <div class="text-end mt-4">
                        <a href="{{ route('sops.index') }}" class="btn btn-secondary me-2">
                            <i class="ri-arrow-left-line me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Update SOP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection