@extends('layouts.app')
@section('title', 'Preview SOP')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('sops.index') }}">SOP</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Preview & Edit</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-0 text-gray-600">Preview & Edit SOP</h1>
                <p class="text-muted mb-0">{{ $sop->nama_sop }}</p>
            </div>
            <div class="d-flex gap-2 flex-wrap justify-content-end">
                <a href="{{ route('sops.index') }}" class="btn btn-secondary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>Kembali
                </a>

                {{-- Tab Visibility Button --}}
                <div class="dropdown">
                    <button type="button" class="btn btn-warning btn-sm dropdown-toggle" id="tabVisibilityBtn"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="ri-layout-column-line me-1"></i>Tampilkan Tab
                    </button>
                    <div class="dropdown-menu p-3 shadow" style="min-width: 240px;">
                        <h6 class="dropdown-header px-0 pb-2 border-bottom mb-2">Pilih Tab yang Ditampilkan</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input tab-visibility-check" type="checkbox" id="chk_jadwal" value="jadwal" checked>
                            <label class="form-check-label" for="chk_jadwal">
                                <i class="ri-calendar-line me-1 text-success"></i>Jadwal Karyawan
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input tab-visibility-check" type="checkbox" id="chk_program" value="program" checked>
                            <label class="form-check-label" for="chk_program">
                                <i class="ri-book-line me-1 text-purple"></i>Program Pelatihan
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input tab-visibility-check" type="checkbox" id="chk_daftar_hadir" value="daftar_hadir" checked>
                            <label class="form-check-label" for="chk_daftar_hadir">
                                <i class="ri-file-list-2-line me-1 text-warning"></i>Daftar Hadir
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input tab-visibility-check" type="checkbox" id="chk_evaluasi" value="evaluasi" checked>
                            <label class="form-check-label" for="chk_evaluasi">
                                <i class="ri-survey-line me-1 text-danger"></i>Evaluasi
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input tab-visibility-check" type="checkbox" id="chk_gallery" value="gallery" checked>
                            <label class="form-check-label" for="chk_gallery">
                                <i class="ri-image-line me-1 text-pink"></i>Galeri
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input tab-visibility-check" type="checkbox" id="chk_suhu" value="suhu" checked>
                            <label class="form-check-label" for="chk_suhu">
                                <i class="ri-temp-hot-line me-1 text-orange"></i>Suhu Ruangan
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input tab-visibility-check" type="checkbox" id="chk_hama" value="hama" checked>
                            <label class="form-check-label" for="chk_hama">
                                <i class="ri-bug-line me-1 text-dark"></i>Pengendalian Hama
                            </label>
                        </div>
                        <div class="border-top pt-2 mt-2 d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-primary flex-grow-1" id="applyTabVisibility">
                                <i class="ri-check-line me-1"></i>Terapkan
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="resetTabVisibility">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#fontSettingsModal">
                    <i class="ri-font-size me-1"></i>Font Settings
                </button>
                <button type="button" class="btn btn-success btn-sm" id="downloadPDF">
                    <i class="ri-download-line me-1"></i>Download PDF
                </button>
            </div>
        </div>

        {{-- Alert Messages --}}
        <div id="alertContainer"></div>

        {{-- TAB UTAMA --}}
        <ul class="nav nav-tabs mb-3" id="mainTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-4" id="main-preview-tab" data-bs-toggle="tab"
                        data-bs-target="#main-preview" type="button" role="tab">
                    <i class="ri-file-text-line me-1"></i>Preview & Edit
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4" id="main-data-tab" data-bs-toggle="tab"
                        data-bs-target="#main-data" type="button" role="tab">
                    <i class="ri-database-2-line me-1"></i>Data Tambahan
                </button>
            </li>
        </ul>

        <div class="tab-content" id="mainTabContent">

            {{-- ================================================
                 TAB 1: PREVIEW & EDIT
                 ================================================ --}}
            <div class="tab-pane fade show active" id="main-preview" role="tabpanel">
                <div class="row">

                    {{-- ── KOLOM KIRI: Live Preview dengan Navigasi ── --}}
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow-sm sticky-top" style="top: 20px;">

                            {{-- Card Header: Judul + Zoom --}}
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="ri-file-text-line me-2"></i>Live Preview PDF</h6>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light btn-sm" id="zoomOut" title="Zoom Out">
                                        <i class="ri-zoom-out-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm" id="zoomIn" title="Zoom In">
                                        <i class="ri-zoom-in-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm" id="resetZoom" title="Reset">
                                        <i class="ri-refresh-line"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- ══ NAVIGASI HALAMAN ══ --}}
                            <div id="pageNavBar"
                                 class="d-flex align-items-center justify-content-between px-3 py-2 gap-2 flex-wrap"
                                 style="background: #3a3d40; border-bottom: 1px solid #555;">

                                {{-- Kiri: Toggle header/footer (hanya tampil di halaman non-SOP) --}}
                                <div style="min-width: 230px;">
                                    <div id="pageToggleControls" style="display: none;">
                                        <div class="d-flex gap-3">
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" id="toggleHeader" checked>
                                                <label class="form-check-label text-white" for="toggleHeader"
                                                       style="font-size: 0.78rem; white-space: nowrap; cursor: pointer;">
                                                    <i class="ri-layout-top-line me-1"></i>Header SOP
                                                </label>
                                            </div>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" id="toggleFooter" checked>
                                                <label class="form-check-label text-white" for="toggleFooter"
                                                       style="font-size: 0.78rem; white-space: nowrap; cursor: pointer;">
                                                    <i class="ri-layout-bottom-line me-1"></i>Footer
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="pageTogglePlaceholder" class="text-muted" style="font-size: 0.78rem;">
                                        <i class="ri-file-text-line me-1"></i>Halaman Utama SOP
                                    </div>
                                </div>

                                {{-- Tengah: Prev / Counter / Next --}}
                                <div class="d-flex align-items-center gap-2">
                                    <button class="btn btn-sm btn-outline-light" id="btnPrevPage" disabled>
                                        <i class="ri-arrow-left-s-line"></i> Prev
                                    </button>
                                    <span class="text-white fw-bold" id="pageCounter"
                                          style="min-width: 55px; text-align: center; font-size: 0.85rem;">
                                        – / –
                                    </span>
                                    <button class="btn btn-sm btn-outline-light" id="btnNextPage" disabled>
                                        Next <i class="ri-arrow-right-s-line"></i>
                                    </button>
                                </div>

                                {{-- Kanan: Label halaman aktif --}}
                                <div class="text-end" style="min-width: 150px;">
                                    <span id="pageLabel" class="text-white"
                                          style="font-size: 0.78rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">
                                        <i class="ri-pages-line me-1"></i>Memuat...
                                    </span>
                                </div>
                            </div>
                            {{-- ══ END NAVIGASI ══ --}}

                            {{-- Preview Area --}}
                            <div class="card-body p-0" style="background: #525659;">
                                <div id="pdfPreviewContainer" style="overflow: auto; max-height: 85vh;">
                                    <div id="pdfPreview"
                                         style="background: white;
                                                margin: 20px auto;
                                                box-shadow: 0 0 10px rgba(0,0,0,0.3);
                                                transform-origin: top center;
                                                width: 210mm;
                                                min-height: 297mm;
                                                box-sizing: border-box;
                                                overflow: hidden;">
                                        {{-- Konten halaman SOP diload via AJAX saat init --}}
                                        <div class="d-flex align-items-center justify-content-center" style="min-height: 400px;">
                                            <div class="text-center text-muted">
                                                <div class="spinner-border text-primary mb-3" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mb-0">Memuat preview...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- ── END KOLOM KIRI ── --}}

                    {{-- ── KOLOM KANAN: Edit Form ── --}}
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-white p-0">
                                <ul class="nav nav-tabs card-header-tabs flex-nowrap" id="editTabs" role="tablist"
                                    style="white-space: nowrap; padding: 0 0.75rem;">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="header-tab" data-bs-toggle="tab"
                                                data-bs-target="#header-content" type="button">
                                            <i class="ri-layout-top-line me-1"></i>Header
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="sections-tab" data-bs-toggle="tab"
                                                data-bs-target="#sections-content" type="button">
                                            <i class="ri-list-check-2 me-1"></i>Konten
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="approvals-tab" data-bs-toggle="tab"
                                                data-bs-target="#approvals-content" type="button">
                                            <i class="ri-checkbox-circle-line me-1"></i>Persetujuan
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body" style="max-height: 85vh; overflow-y: auto;">
                                <div class="tab-content" id="editTabContent">

                                    {{-- TAB HEADER --}}
                                    <div class="tab-pane fade show active" id="header-content" role="tabpanel">
                                        <form id="headerForm">
                                            @csrf
                                            <input type="hidden" name="section_type" value="header">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nama SOP</label>
                                                <input type="text" name="nama_sop" class="form-control" value="{{ $sop->nama_sop }}">
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">No. SOP</label>
                                                    <input type="text" name="no_sop" class="form-control" value="{{ $sop->no_sop }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Revisi</label>
                                                    <input type="text" name="revisi" class="form-control" value="{{ $sop->revisi }}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Tanggal Dibuat</label>
                                                    <input type="date" name="tanggal_dibuat" class="form-control" value="{{ $sop->tanggal_dibuat->format('Y-m-d') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Tanggal Efektif</label>
                                                    <input type="date" name="tanggal_efektif" class="form-control" value="{{ $sop->tanggal_efektif->format('Y-m-d') }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Judul Header</label>
                                                <input type="text" name="judul_header" class="form-control" value="{{ $sop->judul_header }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Logo Perusahaan</label>
                                                <input type="file" name="logo" class="form-control" accept="image/*">
                                                @if($sop->logo_path)
                                                    <small class="text-muted d-block mt-1">
                                                        Logo saat ini: <a href="{{ Storage::url($sop->logo_path) }}" target="_blank">Lihat</a>
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="draft"     {{ $sop->status == 'draft'    ? 'selected' : '' }}>Draft</option>
                                                    <option value="active"    {{ $sop->status == 'active'   ? 'selected' : '' }}>Aktif</option>
                                                    <option value="archived"  {{ $sop->status == 'archived' ? 'selected' : '' }}>Arsip</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="ri-save-line me-1"></i>Simpan Header
                                            </button>
                                        </form>
                                    </div>

                                    {{-- TAB SECTIONS --}}
                                    <div class="tab-pane fade" id="sections-content" role="tabpanel">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Kelola Sections</h6>
                                            <button type="button" class="btn btn-sm btn-success" id="addNewSection">
                                                <i class="ri-add-line me-1"></i>Tambah Section
                                            </button>
                                        </div>
                                        <div id="sectionsAccordion" class="accordion">
                                            @foreach($sop->sections as $index => $section)
                                                <div class="accordion-item section-accordion-item" data-section-id="{{ $section->id }}">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#section{{ $section->id }}">
                                                            <strong>{{ $section->section_code }}. {{ $section->section_title }}</strong>
                                                        </button>
                                                    </h2>
                                                    <div id="section{{ $section->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}">
                                                        <div class="accordion-body">
                                                            <form class="sectionForm" data-section-id="{{ $section->id }}">
                                                                @csrf
                                                                <input type="hidden" name="section_id" value="{{ $section->id }}">
                                                                <div class="row mb-3">
                                                                    <div class="col-3">
                                                                        <label class="form-label fw-bold">Kode</label>
                                                                        <input type="text" name="section_code" class="form-control" value="{{ $section->section_code }}">
                                                                    </div>
                                                                    <div class="col-9">
                                                                        <label class="form-label fw-bold">Judul</label>
                                                                        <input type="text" name="section_title" class="form-control" value="{{ $section->section_title }}">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Items/Poin:</label>
                                                                    <div class="items-list" data-section-id="{{ $section->id }}">
                                                                        @foreach($section->items as $itemIndex => $item)
                                                                            <div class="input-group mb-2 item-group" data-item-id="{{ $item->id }}">
                                                                                <span class="input-group-text">{{ $itemIndex + 1 }}.</span>
                                                                                <input type="text" name="items[{{ $item->id }}]" class="form-control item-input" value="{{ $item->content }}">
                                                                                <button type="button" class="btn btn-outline-danger btn-sm deleteItem" data-item-id="{{ $item->id }}">
                                                                                    <i class="ri-delete-bin-line"></i>
                                                                                </button>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <button type="button" class="btn btn-sm btn-outline-primary addItemBtn mt-2" data-section-id="{{ $section->id }}">
                                                                        <i class="ri-add-line me-1"></i>Tambah Item
                                                                    </button>
                                                                </div>
                                                                <div class="d-flex gap-2">
                                                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                                                        <i class="ri-save-line me-1"></i>Simpan
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger deleteSection" data-section-id="{{ $section->id }}">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- TAB APPROVALS --}}
                                    <div class="tab-pane fade" id="approvals-content" role="tabpanel">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Kelola Persetujuan</h6>
                                            <button type="button" class="btn btn-sm btn-success" id="addNewApproval">
                                                <i class="ri-add-line me-1"></i>Tambah Approval
                                            </button>
                                        </div>
                                        <div id="approvalsContainer">
                                            @foreach($sop->approvals as $approval)
                                                <div class="card mb-3 approval-card" data-approval-id="{{ $approval->id }}">
                                                    <div class="card-body">
                                                        <form class="approvalForm" data-approval-id="{{ $approval->id }}">
                                                            @csrf
                                                            <input type="hidden" name="approval_id" value="{{ $approval->id }}">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Keterangan</label>
                                                                <input type="text" name="keterangan" class="form-control" value="{{ $approval->keterangan }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Nama</label>
                                                                <input type="text" name="nama" class="form-control" value="{{ $approval->nama }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Jabatan</label>
                                                                <input type="text" name="jabatan" class="form-control" value="{{ $approval->jabatan }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Tanggal Tanda Tangan</label>
                                                                <input type="date" name="tanda_tangan" class="form-control"
                                                                       value="{{ $approval->tanda_tangan ? $approval->tanda_tangan->format('Y-m-d') : '' }}">
                                                            </div>
                                                            <div class="d-flex gap-2">
                                                                <button type="submit" class="btn btn-primary flex-grow-1">
                                                                    <i class="ri-save-line me-1"></i>Simpan
                                                                </button>
                                                                <button type="button" class="btn btn-danger deleteApproval" data-approval-id="{{ $approval->id }}">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ── END KOLOM KANAN ── --}}

                </div>{{-- end .row --}}
            </div>{{-- end TAB 1 --}}

            {{-- ================================================
                 TAB 2: DATA TAMBAHAN
                 ================================================ --}}
            <div class="tab-pane fade" id="main-data" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white p-0">
                        <ul class="nav nav-tabs card-header-tabs flex-nowrap" id="optionalTabs" role="tablist"
                            style="white-space: nowrap; padding: 0 0.75rem;">
                            <li class="nav-item tab-opt" id="tab_li_jadwal" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-jadwal" type="button">
                                    <i class="ri-calendar-line me-1 text-success"></i>Jadwal Karyawan
                                </button>
                            </li>
                            <li class="nav-item tab-opt" id="tab_li_program" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-program" type="button">
                                    <i class="ri-book-line me-1 text-purple"></i>Program Pelatihan
                                </button>
                            </li>
                            <li class="nav-item tab-opt" id="tab_li_daftar_hadir" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-daftar-hadir" type="button">
                                    <i class="ri-file-list-2-line me-1 text-warning"></i>Daftar Hadir
                                </button>
                            </li>
                            <li class="nav-item tab-opt" id="tab_li_evaluasi" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-evaluasi" type="button">
                                    <i class="ri-survey-line me-1 text-danger"></i>Evaluasi
                                </button>
                            </li>
                            <li class="nav-item tab-opt" id="tab_li_gallery" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-gallery" type="button">
                                    <i class="ri-image-line me-1 text-pink"></i>Galeri
                                </button>
                            </li>
                            <li class="nav-item tab-opt" id="tab_li_suhu" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-suhu" type="button">
                                    <i class="ri-temp-hot-line me-1 text-orange"></i>Suhu Ruangan
                                </button>
                            </li>
                            <li class="nav-item tab-opt" id="tab_li_hama" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-hama" type="button">
                                    <i class="ri-bug-line me-1 text-dark"></i>Pengendalian Hama
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="optionalTabContent">
                            <div class="tab-pane fade show active" id="tab-jadwal" role="tabpanel">
                                @include('sops.partials.jadwal-karyawan', ['sop' => $sop])
                            </div>
                            <div class="tab-pane fade" id="tab-program" role="tabpanel">
                                @include('sops.partials.program-pelatihan', ['sop' => $sop])
                            </div>
                            <div class="tab-pane fade" id="tab-daftar-hadir" role="tabpanel">
                                @include('sops.partials.daftar-hadir', ['sop' => $sop])
                            </div>
                            <div class="tab-pane fade" id="tab-evaluasi" role="tabpanel">
                                @include('sops.partials.evaluasi', ['sop' => $sop])
                            </div>
                            <div class="tab-pane fade" id="tab-gallery" role="tabpanel">
                                @include('sops.partials.gallery', ['sop' => $sop])
                            </div>
                            <div class="tab-pane fade" id="tab-suhu" role="tabpanel">
                                @include('sops.partials.catatan-suhu', ['sop' => $sop])
                            </div>
                            <div class="tab-pane fade" id="tab-hama" role="tabpanel">
                                @include('sops.partials.pengendalian-hama', ['sop' => $sop])
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- end TAB 2 --}}

        </div>{{-- end mainTabContent --}}
    </div>
</section>

{{-- Font Settings Modal --}}
<div class="modal fade" id="fontSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ri-font-size me-2"></i>Font & Style Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Font Family</label>
                        <select id="fontFamily" class="form-select">
                            <option value="Arial, sans-serif">Arial</option>
                            <option value="'Times New Roman', serif">Times New Roman</option>
                            <option value="'Courier New', monospace">Courier New</option>
                            <option value="Georgia, serif">Georgia</option>
                            <option value="Verdana, sans-serif">Verdana</option>
                            <option value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
                            <option value="'Calibri', sans-serif">Calibri</option>
                            <option value="Tahoma, sans-serif">Tahoma</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Base Font Size</label>
                        <div class="input-group">
                            <input type="number" id="baseFontSize" class="form-control" min="8" max="16" step="0.5" value="11">
                            <span class="input-group-text">pt</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Header Table Font Size</label>
                        <div class="input-group">
                            <input type="number" id="headerFontSize" class="form-control" min="7" max="14" step="0.5" value="10">
                            <span class="input-group-text">pt</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Section Title Font Size</label>
                        <div class="input-group">
                            <input type="number" id="sectionTitleSize" class="form-control" min="9" max="16" step="0.5" value="11">
                            <span class="input-group-text">pt</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Content Font Size</label>
                        <div class="input-group">
                            <input type="number" id="contentFontSize" class="form-control" min="8" max="14" step="0.5" value="11">
                            <span class="input-group-text">pt</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Line Height</label>
                        <div class="input-group">
                            <input type="number" id="lineHeight" class="form-control" min="1" max="2.5" step="0.1" value="1.6">
                            <span class="input-group-text">x</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Margin Top</label>
                        <div class="input-group">
                            <input type="number" id="marginTop" class="form-control" min="10" max="50" step="1" value="20">
                            <span class="input-group-text">mm</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Margin Bottom</label>
                        <div class="input-group">
                            <input type="number" id="marginBottom" class="form-control" min="10" max="50" step="1" value="20">
                            <span class="input-group-text">mm</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Margin Left</label>
                        <div class="input-group">
                            <input type="number" id="marginLeft" class="form-control" min="10" max="50" step="1" value="20">
                            <span class="input-group-text">mm</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Margin Right</label>
                        <div class="input-group">
                            <input type="number" id="marginRight" class="form-control" min="10" max="50" step="1" value="20">
                            <span class="input-group-text">mm</span>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    <strong>Catatan:</strong> Pengaturan ini akan disimpan di browser Anda dan diterapkan pada preview dan PDF yang didownload.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="resetFontSettings">
                    <i class="ri-refresh-line me-1"></i>Reset Default
                </button>
                <button type="button" class="btn btn-primary" id="applyFontSettings">
                    <i class="ri-check-line me-1"></i>Terapkan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #mainTabs .nav-link {
        color: #6c757d;
        font-size: 0.95rem;
        border-radius: 8px 8px 0 0;
    }
    #mainTabs .nav-link.active {
        color: #fff;
        background-color: #0d6efd;
        border-color: #0d6efd #0d6efd #fff;
        font-weight: 600;
    }

    .sticky-top { position: sticky; z-index: 100; }

    #pdfPreview {
        transition: transform 0.2s ease;
        transform-origin: top center;
        width: 210mm;
        min-height: 297mm;
        box-sizing: border-box;
        flex-shrink: 0;
        /* Selalu lebar penuh A4, tidak pernah dikecilkan otomatis */
    }

    #pdfPreviewContainer {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 85vh;
        /* Pastikan container cukup lebar untuk menampung A4 */
        min-width: 0;
        display: flex;
        justify-content: center;
    }

    #pdfPreviewContainer::-webkit-scrollbar { width: 8px; height: 8px; }
    #pdfPreviewContainer::-webkit-scrollbar-track { background: #f1f1f1; }
    #pdfPreviewContainer::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }

    #optionalTabs, #editTabs { scrollbar-width: thin; }
    #optionalTabs::-webkit-scrollbar,
    #editTabs::-webkit-scrollbar { height: 3px; }
    #optionalTabs::-webkit-scrollbar-thumb,
    #editTabs::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 3px; }

    .nav-tabs .nav-link { color: #6c757d; font-size: 0.875rem; }
    .nav-tabs .nav-link.active { color: #0d6efd; font-weight: 600; }
    #mainTabs .nav-link.active { color: #fff; }

    .accordion-button:not(.collapsed) { background-color: #e7f3ff; color: #0d6efd; }

    .item-group { animation: fadeIn 0.3s ease; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .approval-card { border-left: 3px solid #0d6efd; }
    .tab-hidden { display: none !important; }

    .text-purple { color: #6f42c1; }
    .text-pink   { color: #e83e8c; }
    .text-orange { color: #fd7e14; }

    .btn-group-sm > .btn { padding: 0.25rem 0.5rem; font-size: 0.875rem; }

    #pageNavBar .form-check-input { cursor: pointer; }
    #btnPrevPage, #btnNextPage { min-width: 70px; font-size: 0.8rem; }

    @media print {
        #pdfPreview { width: 210mm; min-height: 297mm; margin: 0; padding: 0; box-shadow: none; }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    const sopId = {{ $sop->id }};
    let zoomLevel = 1;

    // ============================================================
    // FONT SETTINGS
    // ============================================================
    const FontSettings = {
        defaults: {
            fontFamily: 'Arial, sans-serif',
            baseFontSize: 11, headerFontSize: 10,
            sectionTitleSize: 11, contentFontSize: 11,
            lineHeight: 1.6,
            marginTop: 20, marginBottom: 20, marginLeft: 20, marginRight: 20
        },
        load() {
            const saved = localStorage.getItem('sop_font_settings');
            return saved ? JSON.parse(saved) : this.defaults;
        },
        save(s) { localStorage.setItem('sop_font_settings', JSON.stringify(s)); },
        apply(s) {
            const $doc = $('#pdfPreview').find('.pdf-document');
            $doc.css({
                'font-family' : s.fontFamily,
                'font-size'   : s.baseFontSize + 'pt',
                'line-height' : s.lineHeight,
            });
            $doc.find('.header-table').css('font-size', s.headerFontSize + 'pt');
            $doc.find('.section-title').css('font-size', s.sectionTitleSize + 'pt');
            $doc.find('.section-content').css('font-size', s.contentFontSize + 'pt');
        },
        loadToModal(s) {
            $('#fontFamily').val(s.fontFamily);
            $('#baseFontSize').val(s.baseFontSize);
            $('#headerFontSize').val(s.headerFontSize);
            $('#sectionTitleSize').val(s.sectionTitleSize);
            $('#contentFontSize').val(s.contentFontSize);
            $('#lineHeight').val(s.lineHeight);
            $('#marginTop').val(s.marginTop);
            $('#marginBottom').val(s.marginBottom);
            $('#marginLeft').val(s.marginLeft);
            $('#marginRight').val(s.marginRight);
        },
        getFromModal() {
            return {
                fontFamily       : $('#fontFamily').val(),
                baseFontSize     : parseFloat($('#baseFontSize').val()),
                headerFontSize   : parseFloat($('#headerFontSize').val()),
                sectionTitleSize : parseFloat($('#sectionTitleSize').val()),
                contentFontSize  : parseFloat($('#contentFontSize').val()),
                lineHeight       : parseFloat($('#lineHeight').val()),
                marginTop        : parseInt($('#marginTop').val()),
                marginBottom     : parseInt($('#marginBottom').val()),
                marginLeft       : parseInt($('#marginLeft').val()),
                marginRight      : parseInt($('#marginRight').val()),
            };
        }
    };

    $('#fontSettingsModal').on('show.bs.modal', function () {
        FontSettings.loadToModal(FontSettings.load());
    });
    $('#applyFontSettings').click(function () {
        const s = FontSettings.getFromModal();
        FontSettings.save(s); FontSettings.apply(s);
        $('#fontSettingsModal').modal('hide');
        showAlert('success', 'Pengaturan font berhasil diterapkan');
    });
    $('#resetFontSettings').click(function () {
        Swal.fire({
            title: 'Reset Font?', text: 'Pengaturan font akan dikembalikan ke default', icon: 'question',
            showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset!', cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                FontSettings.save(FontSettings.defaults);
                FontSettings.loadToModal(FontSettings.defaults);
                FontSettings.apply(FontSettings.defaults);
                showAlert('info', 'Pengaturan font direset ke default');
            }
        });
    });

    // ============================================================
    // TAB VISIBILITY
    // ============================================================
    const TAB_KEY = 'sop_tab_visibility_' + sopId;
    const TabVisibility = {
        defaults: { jadwal: true, program: true, daftar_hadir: true, evaluasi: true, gallery: true, suhu: true, hama: true },
        tabMap: {
            jadwal: 'tab_li_jadwal', program: 'tab_li_program', daftar_hadir: 'tab_li_daftar_hadir',
            evaluasi: 'tab_li_evaluasi', gallery: 'tab_li_gallery', suhu: 'tab_li_suhu', hama: 'tab_li_hama'
        },
        load() { const s = localStorage.getItem(TAB_KEY); return s ? { ...this.defaults, ...JSON.parse(s) } : { ...this.defaults }; },
        save(s) { localStorage.setItem(TAB_KEY, JSON.stringify(s)); },
        apply(settings) {
            let anyVisible = false;
            Object.entries(this.tabMap).forEach(([key, liId]) => {
                const $li = $('#' + liId);
                if (settings[key] === false) {
                    if ($li.find('.nav-link').hasClass('active')) {
                        const first = Object.entries(this.tabMap).find(([k]) => settings[k] !== false && k !== key);
                        if (first) $('#' + first[1]).find('.nav-link').tab('show');
                    }
                    $li.addClass('tab-hidden');
                } else {
                    $li.removeClass('tab-hidden');
                    anyVisible = true;
                }
            });
            $('#main-data-tab').toggle(anyVisible);
        },
        loadToCheckboxes(s) {
            Object.entries(s).forEach(([key, val]) => {
                const $c = $('#chk_' + key);
                if ($c.length) $c.prop('checked', val !== false);
            });
        },
        getFromCheckboxes() {
            const r = { ...this.defaults };
            $('.tab-visibility-check').each(function () { r[$(this).val()] = $(this).is(':checked'); });
            return r;
        }
    };

    const initTabSettings = TabVisibility.load();
    TabVisibility.apply(initTabSettings);
    TabVisibility.loadToCheckboxes(initTabSettings);

    $('#applyTabVisibility').click(function () {
        const s = TabVisibility.getFromCheckboxes();
        TabVisibility.save(s); TabVisibility.apply(s);
        $('#tabVisibilityBtn').dropdown('hide');
        showAlert('success', 'Visibilitas tab berhasil diperbarui');
    });
    $('#resetTabVisibility').click(function () {
        Swal.fire({
            title: 'Reset Tab?', text: 'Semua tab akan ditampilkan kembali ke default', icon: 'question',
            showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset!', cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                TabVisibility.save(TabVisibility.defaults);
                TabVisibility.loadToCheckboxes(TabVisibility.defaults);
                TabVisibility.apply(TabVisibility.defaults);
                $('#tabVisibilityBtn').dropdown('hide');
                showAlert('info', 'Visibilitas tab direset ke default');
            }
        });
    });

    // ============================================================
    // ZOOM CONTROLS
    // ============================================================
    $('#zoomIn').click(function ()    { zoomLevel = Math.min(zoomLevel + 0.1, 2);   applyZoom(); });
    $('#zoomOut').click(function ()   { zoomLevel = Math.max(zoomLevel - 0.1, 0.3); applyZoom(); });
    $('#resetZoom').click(function () { zoomLevel = 1; applyZoom(); });

    function applyZoom() {
        $('#pdfPreview').css('transform', `scale(${zoomLevel})`);
    }

    // ============================================================
    // HELPER: Set ukuran #pdfPreview — selalu A4 penuh untuk semua halaman
    // ============================================================
        function setPreviewSize(type) {
            const s = FontSettings.load();
            if (type === 'sop') {
                // Halaman SOP utama: portrait A4, padding dari .pdf-document blade
                $('#pdfPreview').css({
                    'width'      : '210mm',
                    'min-height' : '297mm',
                    'padding'    : '0',
                });
            } else {
                // Halaman lain: landscape A4
                $('#pdfPreview').css({
                    'width'          : '255mm',
                    'min-height'     : '297mm',
                    'padding-top'    : s.marginTop    + 'mm',
                    'padding-bottom' : s.marginBottom + 'mm',
                    'padding-left'   : s.marginLeft   + 'mm',
                    'padding-right'  : s.marginRight  + 'mm',
                });
            }
        }

    // ============================================================
    // PAGE NAVIGATOR
    // ============================================================
    const PageNav = {
        pages        : [],
        currentIndex : 0,
        toggleState  : {},

        init() {
            this.bindEvents();
            this.loadPages();
        },

        // ─── FIX 1: loadPages() sekarang langsung fetchPage() halaman pertama ───
        loadPages() {
            $.get(`/sops/${sopId}/pages`)
                .done((res) => {
                    if (!res.success || !res.pages.length) {
                        $('#pdfPreview').html('<div class="alert alert-warning m-4">Tidak ada halaman ditemukan.</div>');
                        return;
                    }
                    this.pages = res.pages;
                    this.currentIndex = 0;
                    this.updateNavUI();
                    this.fetchPage();  // ← FIX: selalu load via AJAX, bukan server-side render
                })
                .fail(() => {
                    $('#pdfPreview').html('<div class="alert alert-danger m-4"><i class="ri-wifi-off-line me-2"></i>Gagal memuat daftar halaman.</div>');
                });
        },

        bindEvents() {
            const self = this;

            $('#btnPrevPage').on('click', function () {
                if (self.currentIndex > 0) {
                    self.currentIndex--;
                    self.updateNavUI();
                    self.restoreToggleState();
                    self.fetchPage();
                }
            });

            $('#btnNextPage').on('click', function () {
                if (self.currentIndex < self.pages.length - 1) {
                    self.currentIndex++;
                    self.updateNavUI();
                    self.restoreToggleState();
                    self.fetchPage();
                }
            });

            // $('#toggleHeader, #toggleFooter').on('change', function () {
            //     const page = self.pages[self.currentIndex];
            //     if (!page) return;
            //     const key = `${page.type}_${page.id}`;
            //     self.toggleState[key] = {
            //         header : $('#toggleHeader').is(':checked'),
            //         footer : $('#toggleFooter').is(':checked'),
            //     };
            //     self.fetchPage();
            // });
        },

        updateNavUI() {
            const total = this.pages.length;
            const idx   = this.currentIndex;
            const page  = this.pages[idx];
            if (!page) return;

            $('#pageCounter').text(`${idx + 1} / ${total}`);
            $('#pageLabel').html(`<i class="ri-pages-line me-1"></i>${page.label}`);
            $('#btnPrevPage').prop('disabled', idx === 0);
            $('#btnNextPage').prop('disabled', idx === total - 1);

            const isSop = page.type === 'sop';
            $('#pageToggleControls').toggle(!isSop);
            $('#pageTogglePlaceholder').toggle(isSop);
        },

        restoreToggleState() {
            const page = this.pages[this.currentIndex];
            if (!page || page.type === 'sop') return;
            const key   = `${page.type}_${page.id}`;
            const state = this.toggleState[key] ?? { header: true, footer: true };
            $('#toggleHeader').prop('checked', state.header);
            $('#toggleFooter').prop('checked', state.footer);
        },

        fetchPage() {
            const page = this.pages[this.currentIndex];
            if (!page) return;

            const isSop      = page.type === 'sop';
            const showHeader = isSop ? 0 : ($('#toggleHeader').is(':checked') ? 1 : 0);
            const showFooter = isSop ? 0 : ($('#toggleFooter').is(':checked') ? 1 : 0);

            // ─── FIX 2: setPreviewSize dipanggil SEBELUM loading ───
            setPreviewSize(page.type);

            $('#pdfPreview').html(`
                <div class="d-flex align-items-center justify-content-center" style="min-height: 400px;">
                    <div class="text-center text-muted">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mb-0">Memuat <strong>${page.label}</strong>...</p>
                    </div>
                </div>
            `);

            $.get(`/sops/${sopId}/page-preview`, {
                type        : page.type,
                id          : page.id,
                show_header : showHeader,
                show_footer : showFooter,
            })
            .done((res) => {
                if (res.success) {
                    // ─── FIX 3: setPreviewSize dipanggil ULANG setelah konten masuk ───
                    setPreviewSize(page.type);
                    $('#pdfPreview').html(res.html);
                    FontSettings.apply(FontSettings.load());

                    // Reset zoom ke 1 setiap ganti halaman — tidak ada auto scale-down
                    zoomLevel = 1;
                    applyZoom();
                } else {
                    setPreviewSize('sop'); // fallback ke ukuran normal
                    $('#pdfPreview').html(`
                        <div class="alert alert-danger m-4">
                            <i class="ri-error-warning-line me-2"></i>${res.message || 'Gagal memuat halaman'}
                        </div>
                    `);
                }
            })
            .fail((xhr) => {
                const msg = xhr.responseJSON?.message || 'Koneksi gagal, coba lagi.';
                setPreviewSize('sop');
                $('#pdfPreview').html(`
                    <div class="alert alert-danger m-4">
                        <i class="ri-wifi-off-line me-2"></i>${msg}
                    </div>
                `);
            });
        },
    };

    PageNav.init();

    // ============================================================
    // UPDATE PREVIEW setelah save
    // ─── FIX 4: updatePreview() tidak lagi pakai route /preview
    //            yang mungkin tidak ada — sekarang selalu pakai fetchPage()
    // ============================================================
    function updatePreview() {
        PageNav.fetchPage();
    }

    // ============================================================
    // HEADER FORM
    // ============================================================
    $('#headerForm').submit(function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: `/sops/${sopId}/update-header`, method: 'POST',
            data: formData, processData: false, contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success(res) {
                showAlert('success', res.message);
                updatePreview();
            },
            error(xhr) {
                showAlert('danger', xhr.responseJSON?.message || 'Gagal menyimpan header');
            },
        });
    });

    // ============================================================
    // SECTION FORMS
    // ============================================================
    $(document).on('submit', '.sectionForm', function (e) {
        e.preventDefault();
        const sectionId = $(this).data('section-id');
        $.ajax({
            url: `/sops/${sopId}/update-section/${sectionId}`, method: 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success(res) { showAlert('success', res.message); updatePreview(); },
            error(xhr)   { showAlert('danger', xhr.responseJSON?.message || 'Gagal menyimpan section'); },
        });
    });

    // ============================================================
    // APPROVAL FORMS
    // ============================================================
    $(document).on('submit', '.approvalForm', function (e) {
        e.preventDefault();
        const approvalId = $(this).data('approval-id');
        $.ajax({
            url: `/sops/${sopId}/update-approval/${approvalId}`, method: 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success(res) { showAlert('success', res.message); updatePreview(); },
            error(xhr)   { showAlert('danger', xhr.responseJSON?.message || 'Gagal menyimpan approval'); },
        });
    });

    // ============================================================
    // ADD / DELETE ITEMS
    // ============================================================
    $(document).on('click', '.addItemBtn', function () {
        const sectionId = $(this).data('section-id');
        const itemsList = $(`.items-list[data-section-id="${sectionId}"]`);
        const itemCount = itemsList.find('.item-group').length + 1;
        const newItem   = $(`
            <div class="input-group mb-2 item-group" data-item-id="new">
                <span class="input-group-text">${itemCount}.</span>
                <input type="text" name="items[new_${Date.now()}]"
                       class="form-control item-input" placeholder="Masukkan poin...">
                <button type="button" class="btn btn-outline-danger btn-sm deleteItem" data-item-id="new">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>`);
        itemsList.append(newItem);
        updateItemNumbers(itemsList);
    });

    $(document).on('click', '.deleteItem', function () {
        const itemId    = $(this).data('item-id');
        const itemGroup = $(this).closest('.item-group');
        const itemsList = $(this).closest('.items-list');

        if (itemId === 'new') {
            itemGroup.remove();
            updateItemNumbers(itemsList);
            return;
        }

        Swal.fire({
            title: 'Hapus Item?', text: 'Item ini akan dihapus dari section', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.ajax({
                    url: `/sops/${sopId}/delete-item/${itemId}`, method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success(res) {
                        itemGroup.fadeOut(300, function () { $(this).remove(); updateItemNumbers(itemsList); });
                        showAlert('success', res.message);
                        updatePreview();
                    },
                    error(xhr) { showAlert('danger', xhr.responseJSON?.message || 'Gagal menghapus item'); },
                });
            }
        });
    });

    // ============================================================
    // DELETE SECTION / APPROVAL
    // ============================================================
    $(document).on('click', '.deleteSection', function () {
        const sectionId = $(this).data('section-id');
        Swal.fire({
            title: 'Hapus Section?', text: 'Section dan semua item di dalamnya akan dihapus', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.ajax({
                    url: `/sops/${sopId}/delete-section/${sectionId}`, method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success(res) {
                        $(`.section-accordion-item[data-section-id="${sectionId}"]`).fadeOut(300, function () { $(this).remove(); });
                        showAlert('success', res.message);
                        updatePreview();
                    },
                    error(xhr) { showAlert('danger', xhr.responseJSON?.message || 'Gagal menghapus section'); },
                });
            }
        });
    });

    $(document).on('click', '.deleteApproval', function () {
        const approvalId = $(this).data('approval-id');
        Swal.fire({
            title: 'Hapus Approval?', text: 'Data approval akan dihapus', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.ajax({
                    url: `/sops/${sopId}/delete-approval/${approvalId}`, method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success(res) {
                        $(`.approval-card[data-approval-id="${approvalId}"]`).fadeOut(300, function () { $(this).remove(); });
                        showAlert('success', res.message);
                        updatePreview();
                    },
                    error(xhr) { showAlert('danger', xhr.responseJSON?.message || 'Gagal menghapus approval'); },
                });
            }
        });
    });

    // ============================================================
    // ADD NEW SECTION / APPROVAL
    // ============================================================
    $('#addNewSection').click(function () {
        Swal.fire({
            title: 'Tambah Section Baru',
            html: `<input id="swal-section-code"  class="swal2-input" placeholder="Kode (e.g. A)">
                   <input id="swal-section-title" class="swal2-input" placeholder="Judul Section">`,
            showCancelButton: true, confirmButtonText: 'Tambah', cancelButtonText: 'Batal',
            preConfirm: () => ({
                section_code  : document.getElementById('swal-section-code').value,
                section_title : document.getElementById('swal-section-title').value,
            }),
        }).then(r => {
            if (r.isConfirmed && r.value.section_code && r.value.section_title) {
                $.ajax({
                    url: `/sops/${sopId}/add-section`, method: 'POST', data: r.value,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success(res) { showAlert('success', res.message); location.reload(); },
                    error(xhr)   { showAlert('danger',  xhr.responseJSON?.message || 'Gagal menambah section'); },
                });
            }
        });
    });

    $('#addNewApproval').click(function () {
        Swal.fire({
            title: 'Tambah Approval Baru',
            html: `<input id="swal-keterangan" class="swal2-input" placeholder="Keterangan (e.g. Dibuat Oleh)">
                   <input id="swal-nama"        class="swal2-input" placeholder="Nama">
                   <input id="swal-jabatan"     class="swal2-input" placeholder="Jabatan">`,
            showCancelButton: true, confirmButtonText: 'Tambah', cancelButtonText: 'Batal',
            preConfirm: () => ({
                keterangan : document.getElementById('swal-keterangan').value,
                nama       : document.getElementById('swal-nama').value,
                jabatan    : document.getElementById('swal-jabatan').value,
            }),
        }).then(r => {
            if (r.isConfirmed && r.value.keterangan) {
                $.ajax({
                    url: `/sops/${sopId}/add-approval`, method: 'POST', data: r.value,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success(res) { showAlert('success', res.message); location.reload(); },
                    error(xhr)   { showAlert('danger',  xhr.responseJSON?.message || 'Gagal menambah approval'); },
                });
            }
        });
    });

    // ============================================================
    // DOWNLOAD PDF
    // ============================================================
    $('#downloadPDF').click(function () {
        const settings     = FontSettings.load();
        const settingsParam = encodeURIComponent(JSON.stringify(settings));
        window.open(`/sops/${sopId}/download-pdf?font_settings=${settingsParam}`, '_blank');
    });

    // ============================================================
    // HELPERS
    // ============================================================
    function updateItemNumbers(itemsList) {
        itemsList.find('.item-group').each(function (index) {
            $(this).find('.input-group-text').text(`${index + 1}.`);
        });
    }

    function showAlert(type, message) {
        const iconMap = { success: 'success', danger: 'error', info: 'info', warning: 'warning' };
        Swal.fire({
            icon             : iconMap[type] || 'info',
            title            : type === 'danger' ? 'Gagal!' : (type === 'success' ? 'Berhasil!' : 'Info'),
            text             : message,
            timer            : 2000,
            timerProgressBar : true,
            showConfirmButton: false,
            toast            : true,
            position         : 'top-end',
        });
    }

});
</script>
@endpush