@extends('layouts.app')

@section('title', 'Tambah Program Pelatihan')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="ri-add-circle-line me-2"></i>Tambah Program Pelatihan</h2>
                    <p class="text-muted mb-0">Buat program pelatihan karyawan baru</p>
                </div>
                <a href="{{ route('training-programs.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('training-programs.store') }}" method="POST" enctype="multipart/form-data" id="trainingProgramForm">
        @csrf
        
        @include('training_programs.form')

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('training-programs.index') }}" class="btn btn-light">
                        <i class="ri-close-line me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Simpan Program
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection