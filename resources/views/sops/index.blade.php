@extends('layouts.app')

@section('title', 'SOP & Jadwal')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">SOP & Jadwal</li>
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

    {{-- Main Tabs --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0">
            <ul class="nav nav-tabs card-header-tabs" id="mainTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="sop-tab" data-bs-toggle="tab" data-bs-target="#sop-content" type="button">
                        <i class="ri-file-text-line me-2"></i>Daftar SOP
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="jadwal-tab" data-bs-toggle="tab" data-bs-target="#jadwal-content" type="button">
                        <i class="ri-calendar-line me-2"></i>Jadwal Karyawan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="program-pelatihan-tab" data-bs-toggle="tab" data-bs-target="#program-pelatihan-content" type="button">
                        <i class="ri-book-line me-2"></i>Program Pelatihan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="daftar-hadir-tab" data-bs-toggle="tab" data-bs-target="#daftar-hadir-content" type="button">
                        <i class="ri-file-list-2-line me-2"></i>Daftar Hadir
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="evaluasi-tab" data-bs-toggle="tab" data-bs-target="#evaluasi-content" type="button">
                        <i class="ri-survey-line me-2"></i>Evaluasi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery-content" type="button">
                        <i class="ri-survey-line me-2"></i>Galeri
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="catatan-suhu-tab" data-bs-toggle="tab"
                            data-bs-target="#catatan-suhu-content" type="button">
                        <i class="ri-temp-hot-line me-2"></i>Suhu Ruangan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pengendalian-hama-tab" data-bs-toggle="tab"
                            data-bs-target="#pengendalian-hama-content" type="button">
                        <i class="ri-bug-line me-2"></i>Pengendalian Hama
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="mainTabContent">
                {{-- TAB 1: SOP --}}
                <div class="tab-pane fade show active" id="sop-content" role="tabpanel">
                    @include('sops.partials.sop-list')
                </div>

                {{-- TAB 2: JADWAL KARYAWAN --}}
                <div class="tab-pane fade" id="jadwal-content" role="tabpanel">
                    @include('sops.index.jadwal-karyawan')
                </div>

                {{-- TAB 3: PROGRAM PELATIHAN --}}
                <div class="tab-pane fade" id="program-pelatihan-content" role="tabpanel">
                    @include('sops.index.program-pelatihan')
                </div>

                {{-- TAB 4: DAFTAR HADIR --}}
                <div class="tab-pane fade" id="daftar-hadir-content" role="tabpanel">
                    @include('sops.index.daftar-hadir')
                </div>

                {{-- TAB 5: EVALUASI --}}
                <div class="tab-pane fade" id="evaluasi-content" role="tabpanel">
                    @include('sops.index.evaluasi')
                </div>

                {{-- TAB 6: GALERI --}}
                <div class="tab-pane fade" id="gallery-content" role="tabpanel">
                    @include('sops.index.gallery')
                </div>

                {{-- TAB 7: CATATAN SUHU RUANGAN --}}
                <div class="tab-pane fade" id="catatan-suhu-content" role="tabpanel">
                    @include('sops.index.catatan-suhu')
                </div>

                {{-- TAB 8: PENGENDALIAN HAMA --}}
                <div class="tab-pane fade" id="pengendalian-hama-content" role="tabpanel">
                    @include('sops.index.pengendalian-hama')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 1rem 1.5rem;
    }

    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #0d6efd;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-color: transparent transparent #0d6efd;
    }

    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
        font-size: 0.85rem;
    }

    .avatar-xs {
        height: 2rem;
        width: 2rem;
    }

    .avatar-sm {
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-title {
        align-items: center;
        display: flex;
        font-weight: 600;
        height: 100%;
        justify-content: center;
        width: 100%;
    }

    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.15) !important;
    }

    .bg-purple {
        background-color: #6f42c1;
        color: white;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 0.5rem;
    }

    .dropdown-item i {
        width: 20px;
    }

    .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-dismiss alert
    setTimeout(() => $('.alert').fadeOut('slow'), 4000);

    // Tab change handler
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).data('bs-target');

        // Jika tab jadwal dibuka, refresh calendar
        if (target === '#jadwal-content') {
            if (typeof window.refreshCalendar === 'function') {
                window.refreshCalendar();
            }
        }
    });
});
</script>
@endpush