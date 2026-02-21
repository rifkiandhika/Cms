@extends('layouts.app')
@section('title', 'Buat Audit')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('audits.index') }}">Audit</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Buat</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex mb-3">
            <h1 class="h3 mb-0 text-gray-600">Buat Audit Baru</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="ri-file-add-line me-2"></i>Informasi Audit</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Terdapat kesalahan!</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('audits.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Judul Audit <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   placeholder="e.g. Audit CDAKB 2024"
                                   required>
                            <small class="text-muted">Contoh: Audit CDAKB Q1 2024</small>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="audit_date" class="form-label">Tanggal Audit <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('audit_date') is-invalid @enderror" 
                                   id="audit_date" 
                                   name="audit_date" 
                                   value="{{ old('audit_date', date('Y-m-d')) }}" 
                                   required>
                            @error('audit_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="auditor_name" class="form-label">Nama Auditor</label>
                            <input type="text" 
                                   class="form-control @error('auditor_name') is-invalid @enderror" 
                                   id="auditor_name" 
                                   name="auditor_name" 
                                   value="{{ old('auditor_name') }}"
                                   placeholder="e.g. John Doe">
                            @error('auditor_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Catatan tambahan mengenai audit ini...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <i class="ri-information-line me-2"></i>
                        <strong>Info:</strong> Setelah audit dibuat, sistem akan otomatis membuat response kosong untuk semua pertanyaan yang ada. Anda akan diarahkan ke halaman pengisian audit.
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('audits.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Simpan & Mulai Audit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 0.5rem;
    }
</style>
@endpush