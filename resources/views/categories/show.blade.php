@extends('layouts.app')
@section('title', 'Detail Kategori')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('categories.index') }}">Kategori</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Detail</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0 text-gray-600">Detail Kategori</h1>
            <div>
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm">
                    <i class="ri-pencil-line me-1"></i>Edit
                </a>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Category Information -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="ri-file-list-3-line me-2"></i>Informasi Kategori</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="text-muted small">Nomor Kategori</label>
                            <p class="fw-bold mb-0">
                                <span class="badge bg-primary fs-6">{{ $category->number }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small">Nama Kategori</label>
                            <p class="fw-bold mb-0">{{ $category->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="text-muted small">Urutan</label>
                            <p class="fw-bold mb-0">
                                <span class="badge bg-secondary">{{ $category->order }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub Categories -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ri-folder-line me-2"></i>Sub Kategori & Pertanyaan</h5>
                <span class="badge bg-light text-dark">{{ $category->subCategories->count() }} Sub Kategori</span>
            </div>
            <div class="card-body">
                @if($category->subCategories->count() > 0)
                    @foreach($category->subCategories as $index => $subCategory)
                        <div class="card mb-3 border">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="ri-folder-2-line me-2"></i>
                                        <span class="badge bg-info">{{ $subCategory->label }}</span>
                                        {{ $subCategory->name }}
                                    </h6>
                                    <span class="badge bg-secondary">Urutan: {{ $subCategory->order }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($subCategory->questions->count() > 0)
                                    <h6 class="mb-3">
                                        <i class="ri-question-line me-2"></i>Pertanyaan 
                                        <span class="badge bg-info">{{ $subCategory->questions->count() }}</span>
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="80">Nomor</th>
                                                    <th>Pertanyaan</th>
                                                    <th width="80" class="text-center">Urutan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($subCategory->questions as $question)
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-primary">{{ $question->number }}</span>
                                                        </td>
                                                        <td>{{ $question->question }}</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-secondary">{{ $question->order }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        <i class="ri-error-warning-line me-2"></i>Belum ada pertanyaan untuk sub kategori ini.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info mb-0">
                        <i class="ri-information-line me-2"></i>Belum ada sub kategori untuk kategori ini.
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="ri-folder-line fs-1 text-success mb-2"></i>
                        <h3 class="mb-0">{{ $category->subCategories->count() }}</h3>
                        <p class="text-muted mb-0">Total Sub Kategori</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="ri-question-line fs-1 text-info mb-2"></i>
                        <h3 class="mb-0">{{ $category->questions->count() }}</h3>
                        <p class="text-muted mb-0">Total Pertanyaan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="ri-calendar-line fs-1 text-primary mb-2"></i>
                        <h6 class="mb-0">{{ $category->created_at->format('d/m/Y') }}</h6>
                        <p class="text-muted mb-0">Tanggal Dibuat</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .table {
        border: 1px solid #ced4da;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .card {
        border-radius: 0.5rem;
    }
</style>
@endpush