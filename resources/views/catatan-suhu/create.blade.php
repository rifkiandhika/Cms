@extends('layouts.app')

@section('title', 'Tambah Periode Gudang')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('sops.index') }}">SOP & Jadwal</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Tambah Periode</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex mb-3">
            <h1 class="h3 mb-0 text-gray-600">Tambah Periode Gudang</h1>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="ri-store-2-line me-2"></i>Informasi Periode</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('catatan-suhu.periode.store') }}" method="POST">
                    @csrf
                    @include('catatan-suhu.form-periode')
                    <div class="text-end mt-3">
                        <a href="{{ route('sops.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="ri-arrow-left-line me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ri-save-line me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection