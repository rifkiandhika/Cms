@extends('layouts.app')
@section('title', 'Daftar Hadir Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Daftar Hadir Pelatihan</li>
@endsection

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
    <div id="alertContainer">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <div class="d-sm-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-600">Formulir Daftar Hadir Pelatihan</h1>
        <a href="{{ route('attendance-forms.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i>Tambah Daftar Hadir
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div>
                <table class="table table-responsive table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 25%">Topik Pelatihan</th>
                            <th style="width: 12%">Tanggal</th>
                            <th style="width: 15%">Tempat</th>
                            <th style="width: 15%">Instruktur</th>
                            <th style="width: 10%">Jumlah Peserta</th>
                            <th style="width: 10%">Dibuat</th>
                            <th style="width: 8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceForms as $index => $form)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $form->topik_pelatihan }}</strong>
                                </td>
                                <td>
                                    @if($form->tanggal)
                                        {{ $form->tanggal->format('d M Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $form->tempat ?? '-' }}</td>
                                <td>{{ $form->instruktur ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="ri-user-line"></i> {{ $form->participants->count() }} Peserta
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $form->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('attendance-forms.show', $form->id) }}">
                                                    <i class="ri-eye-line"></i> Detail
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('attendance-forms.edit', $form->id) }}">
                                                    <i class="ri-edit-line"></i> Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('attendance-forms.destroy', $form->id) }}" method="POST" 
                                                      onsubmit="return confirm('Yakin ingin menghapus daftar hadir ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="ri-delete-bin-line"></i> Hapus
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="ri-inbox-line fs-1 text-muted d-block mb-3"></i>
                                    <p class="text-muted">Belum ada data daftar hadir pelatihan</p>
                                    <a href="{{ route('attendance-forms.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ri-add-line me-1"></i>Tambah Daftar Hadir Pertama
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    setTimeout(() => $('.alert').fadeOut('slow'), 4000);
});
</script>
@endpush