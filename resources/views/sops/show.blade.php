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
            <div>
                <a href="{{ route('sops.index') }}" class="btn btn-secondary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>Kembali
                </a>
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

        <div class="row">
            {{-- LEFT SIDE: PDF PREVIEW --}}
            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="ri-file-text-line me-2"></i>Live Preview PDF</h6>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-light btn-sm" id="zoomOut">
                                <i class="ri-zoom-out-line"></i>
                            </button>
                            <button type="button" class="btn btn-light btn-sm" id="zoomIn">
                                <i class="ri-zoom-in-line"></i>
                            </button>
                            <button type="button" class="btn btn-light btn-sm" id="resetZoom">
                                <i class="ri-refresh-line"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0" style="background: #525659;">
                        <div id="pdfPreviewContainer" style="overflow: auto; max-height: 85vh;">
                            <div id="pdfPreview" class="p-4" style="background: white; margin: 20px auto; box-shadow: 0 0 10px rgba(0,0,0,0.3); transform-origin: top center;">
                                {{-- PDF Content will be rendered here --}}
                                @include('sops.partials.pdf-template')
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE: EDIT FORMS --}}
            <div class="col-lg-5">
                {{-- Section Tabs --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" id="editTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="header-tab" data-bs-toggle="tab" data-bs-target="#header-content" type="button">
                                    <i class="ri-layout-top-line me-1"></i>Header
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="sections-tab" data-bs-toggle="tab" data-bs-target="#sections-content" type="button">
                                    <i class="ri-list-check-2 me-1"></i>Konten
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="approvals-tab" data-bs-toggle="tab" data-bs-target="#approvals-content" type="button">
                                    <i class="ri-checkbox-circle-line me-1"></i>Persetujuan
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body" style="max-height: 75vh; overflow-y: auto;">
                        <div class="tab-content" id="editTabContent">
                            {{-- TAB 1: HEADER --}}
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
                                            <option value="draft" {{ $sop->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="active" {{ $sop->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                            <option value="archived" {{ $sop->status == 'archived' ? 'selected' : '' }}>Arsip</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ri-save-line me-1"></i>Simpan Header
                                    </button>
                                </form>
                            </div>

                            {{-- TAB 2: SECTIONS --}}
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

                            {{-- TAB 3: APPROVALS --}}
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
                                                        <input type="date" name="tanda_tangan" class="form-control" value="{{ $approval->tanda_tangan ? $approval->tanda_tangan->format('Y-m-d') : '' }}">
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
        </div>
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
                    {{-- Font Family --}}
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

                    {{-- Base Font Size --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Base Font Size</label>
                        <div class="input-group">
                            <input type="number" id="baseFontSize" class="form-control" min="8" max="16" step="0.5" value="11">
                            <span class="input-group-text">pt</span>
                        </div>
                    </div>

                    {{-- Header Font Size --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Header Table Font Size</label>
                        <div class="input-group">
                            <input type="number" id="headerFontSize" class="form-control" min="7" max="14" step="0.5" value="10">
                            <span class="input-group-text">pt</span>
                        </div>
                    </div>

                    {{-- Section Title Font Size --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Section Title Font Size</label>
                        <div class="input-group">
                            <input type="number" id="sectionTitleSize" class="form-control" min="9" max="16" step="0.5" value="11">
                            <span class="input-group-text">pt</span>
                        </div>
                    </div>

                    {{-- Content Font Size --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Content Font Size</label>
                        <div class="input-group">
                            <input type="number" id="contentFontSize" class="form-control" min="8" max="14" step="0.5" value="11">
                            <span class="input-group-text">pt</span>
                        </div>
                    </div>

                    {{-- Line Height --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Line Height</label>
                        <div class="input-group">
                            <input type="number" id="lineHeight" class="form-control" min="1" max="2.5" step="0.1" value="1.6">
                            <span class="input-group-text">x</span>
                        </div>
                    </div>

                    {{-- Margin Top --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Margin Top</label>
                        <div class="input-group">
                            <input type="number" id="marginTop" class="form-control" min="10" max="50" step="1" value="20">
                            <span class="input-group-text">mm</span>
                        </div>
                    </div>

                    {{-- Margin Bottom --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Margin Bottom</label>
                        <div class="input-group">
                            <input type="number" id="marginBottom" class="form-control" min="10" max="50" step="1" value="20">
                            <span class="input-group-text">mm</span>
                        </div>
                    </div>

                    {{-- Margin Left --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Margin Left</label>
                        <div class="input-group">
                            <input type="number" id="marginLeft" class="form-control" min="10" max="50" step="1" value="20">
                            <span class="input-group-text">mm</span>
                        </div>
                    </div>

                    {{-- Margin Right --}}
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
    .sticky-top {
        position: sticky;
        z-index: 100;
    }

    #pdfPreview {
        transition: transform 0.2s ease;
        width: 210mm;
        min-height: 297mm;
        box-sizing: border-box;
    }

    #pdfPreviewContainer::-webkit-scrollbar {
        width: 8px;
    }

    #pdfPreviewContainer::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #pdfPreviewContainer::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .nav-tabs .nav-link {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 600;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e7f3ff;
        color: #0d6efd;
    }

    .item-group {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .approval-card {
        border-left: 3px solid #0d6efd;
    }

    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    /* Print styles untuk memastikan konsistensi */
    @media print {
        #pdfPreview {
            width: 210mm;
            min-height: 297mm;
            margin: 0;
            padding: 0;
            box-shadow: none;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const sopId = {{ $sop->id }};
    let zoomLevel = 1;

    // Font Settings Manager
    const FontSettings = {
        defaults: {
            fontFamily: 'Arial, sans-serif',
            baseFontSize: 11,
            headerFontSize: 10,
            sectionTitleSize: 11,
            contentFontSize: 11,
            lineHeight: 1.6,
            marginTop: 20,
            marginBottom: 20,
            marginLeft: 20,
            marginRight: 20
        },

        load: function() {
            const saved = localStorage.getItem('sop_font_settings');
            return saved ? JSON.parse(saved) : this.defaults;
        },

        save: function(settings) {
            localStorage.setItem('sop_font_settings', JSON.stringify(settings));
        },

        apply: function(settings) {
            const $preview = $('#pdfPreview');
            const $document = $preview.find('.pdf-document');

            // Apply to preview
            $document.css({
                'font-family': settings.fontFamily,
                'font-size': settings.baseFontSize + 'pt',
                'line-height': settings.lineHeight,
                'padding-top': settings.marginTop + 'mm',
                'padding-bottom': settings.marginBottom + 'mm',
                'padding-left': settings.marginLeft + 'mm',
                'padding-right': settings.marginRight + 'mm'
            });

            // Header table font
            $document.find('.header-table').css('font-size', settings.headerFontSize + 'pt');

            // Section titles
            $document.find('.section-title').css('font-size', settings.sectionTitleSize + 'pt');

            // Content
            $document.find('.section-content').css('font-size', settings.contentFontSize + 'pt');

            // Store in hidden input for PDF generation
            $('#fontSettingsData').val(JSON.stringify(settings));
        },

        loadToModal: function(settings) {
            $('#fontFamily').val(settings.fontFamily);
            $('#baseFontSize').val(settings.baseFontSize);
            $('#headerFontSize').val(settings.headerFontSize);
            $('#sectionTitleSize').val(settings.sectionTitleSize);
            $('#contentFontSize').val(settings.contentFontSize);
            $('#lineHeight').val(settings.lineHeight);
            $('#marginTop').val(settings.marginTop);
            $('#marginBottom').val(settings.marginBottom);
            $('#marginLeft').val(settings.marginLeft);
            $('#marginRight').val(settings.marginRight);
        },

        getFromModal: function() {
            return {
                fontFamily: $('#fontFamily').val(),
                baseFontSize: parseFloat($('#baseFontSize').val()),
                headerFontSize: parseFloat($('#headerFontSize').val()),
                sectionTitleSize: parseFloat($('#sectionTitleSize').val()),
                contentFontSize: parseFloat($('#contentFontSize').val()),
                lineHeight: parseFloat($('#lineHeight').val()),
                marginTop: parseInt($('#marginTop').val()),
                marginBottom: parseInt($('#marginBottom').val()),
                marginLeft: parseInt($('#marginLeft').val()),
                marginRight: parseInt($('#marginRight').val())
            };
        }
    };

    // Initialize font settings
    const currentSettings = FontSettings.load();
    FontSettings.apply(currentSettings);

    // Open font settings modal
    $('#fontSettingsModal').on('show.bs.modal', function() {
        const settings = FontSettings.load();
        FontSettings.loadToModal(settings);
    });

    // Apply font settings
    $('#applyFontSettings').click(function() {
        const settings = FontSettings.getFromModal();
        FontSettings.save(settings);
        FontSettings.apply(settings);
        $('#fontSettingsModal').modal('hide');
        showAlert('success', 'Pengaturan font berhasil diterapkan');
    });

    // Reset font settings
    $('#resetFontSettings').click(function() {
        FontSettings.save(FontSettings.defaults);
        FontSettings.loadToModal(FontSettings.defaults);
        FontSettings.apply(FontSettings.defaults);
        showAlert('info', 'Pengaturan font direset ke default');
    });

    // Zoom Controls
    $('#zoomIn').click(function() {
        zoomLevel += 0.1;
        if (zoomLevel > 2) zoomLevel = 2;
        $('#pdfPreview').css('transform', `scale(${zoomLevel})`);
    });

    $('#zoomOut').click(function() {
        zoomLevel -= 0.1;
        if (zoomLevel < 0.5) zoomLevel = 0.5;
        $('#pdfPreview').css('transform', `scale(${zoomLevel})`);
    });

    $('#resetZoom').click(function() {
        zoomLevel = 1;
        $('#pdfPreview').css('transform', 'scale(1)');
    });

    // Update Header
    $('#headerForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: `/sops/${sopId}/update-header`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                updatePreview();
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Gagal menyimpan header',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });

    // Update Section
    $('.sectionForm').submit(function(e) {
        e.preventDefault();
        const sectionId = $(this).data('section-id');
        const formData = $(this).serialize();
        
        $.ajax({
            url: `/sops/${sopId}/update-section/${sectionId}`,
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                updatePreview();
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Gagal menyimpan section',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });

    // Update Approval
    $('.approvalForm').submit(function(e) {
        e.preventDefault();
        const approvalId = $(this).data('approval-id');
        const formData = $(this).serialize();
        
        $.ajax({
            url: `/sops/${sopId}/update-approval/${approvalId}`,
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                updatePreview();
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Gagal menyimpan approval',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });

    // Add Item to Section
    $(document).on('click', '.addItemBtn', function() {
        const sectionId = $(this).data('section-id');
        const itemsList = $(`.items-list[data-section-id="${sectionId}"]`);
        const itemCount = itemsList.find('.item-group').length + 1;
        
        const newItem = $(`
            <div class="input-group mb-2 item-group" data-item-id="new">
                <span class="input-group-text">${itemCount}.</span>
                <input type="text" name="items[new_${Date.now()}]" class="form-control item-input" placeholder="Masukkan poin...">
                <button type="button" class="btn btn-outline-danger btn-sm deleteItem" data-item-id="new">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        `);
        
        itemsList.append(newItem);
        updateItemNumbers(itemsList);
    });

    // Delete Item
    $(document).on('click', '.deleteItem', function() {
        const itemId = $(this).data('item-id');
        const itemGroup = $(this).closest('.item-group');
        const itemsList = $(this).closest('.items-list');
        
        if (itemId === 'new') {
            itemGroup.remove();
            updateItemNumbers(itemsList);
        } else {
            Swal.fire({
                title: 'Hapus Item?',
                text: 'Item ini akan dihapus dari section',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/sops/${sopId}/delete-item/${itemId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            itemGroup.fadeOut(300, function() {
                                $(this).remove();
                                updateItemNumbers(itemsList);
                            });
                            showAlert('success', response.message);
                            updatePreview();
                        },
                        error: function(xhr) {
                            showAlert('danger', xhr.responseJSON?.message || 'Gagal menghapus item');
                        }
                    });
                }
            });
        }
    });

    // Delete Section
    $(document).on('click', '.deleteSection', function() {
        const sectionId = $(this).data('section-id');
        
        Swal.fire({
            title: 'Hapus Section?',
            text: 'Section dan semua item di dalamnya akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/sops/${sopId}/delete-section/${sectionId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $(`.section-accordion-item[data-section-id="${sectionId}"]`).fadeOut(300, function() {
                            $(this).remove();
                        });
                        showAlert('success', response.message);
                        updatePreview();
                    },
                    error: function(xhr) {
                        showAlert('danger', xhr.responseJSON?.message || 'Gagal menghapus section');
                    }
                });
            }
        });
    });

    // Delete Approval
    $(document).on('click', '.deleteApproval', function() {
        const approvalId = $(this).data('approval-id');
        
        Swal.fire({
            title: 'Hapus Approval?',
            text: 'Data approval akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/sops/${sopId}/delete-approval/${approvalId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $(`.approval-card[data-approval-id="${approvalId}"]`).fadeOut(300, function() {
                            $(this).remove();
                        });
                        showAlert('success', response.message);
                        updatePreview();
                    },
                    error: function(xhr) {
                        showAlert('danger', xhr.responseJSON?.message || 'Gagal menghapus approval');
                    }
                });
            }
        });
    });

    // Add New Section
    $('#addNewSection').click(function() {
        Swal.fire({
            title: 'Tambah Section Baru',
            html: `
                <input id="swal-section-code" class="swal2-input" placeholder="Kode (e.g. A)">
                <input id="swal-section-title" class="swal2-input" placeholder="Judul Section">
            `,
            showCancelButton: true,
            confirmButtonText: 'Tambah',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                return {
                    section_code: document.getElementById('swal-section-code').value,
                    section_title: document.getElementById('swal-section-title').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value.section_code && result.value.section_title) {
                $.ajax({
                    url: `/sops/${sopId}/add-section`,
                    method: 'POST',
                    data: result.value,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showAlert('success', response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        showAlert('danger', xhr.responseJSON?.message || 'Gagal menambah section');
                    }
                });
            }
        });
    });

    // Add New Approval
    $('#addNewApproval').click(function() {
        Swal.fire({
            title: 'Tambah Approval Baru',
            html: `
                <input id="swal-keterangan" class="swal2-input" placeholder="Keterangan (e.g. Dibuat Oleh)">
                <input id="swal-nama" class="swal2-input" placeholder="Nama">
                <input id="swal-jabatan" class="swal2-input" placeholder="Jabatan">
            `,
            showCancelButton: true,
            confirmButtonText: 'Tambah',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                return {
                    keterangan: document.getElementById('swal-keterangan').value,
                    nama: document.getElementById('swal-nama').value,
                    jabatan: document.getElementById('swal-jabatan').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value.keterangan) {
                $.ajax({
                    url: `/sops/${sopId}/add-approval`,
                    method: 'POST',
                    data: result.value,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showAlert('success', response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        showAlert('danger', xhr.responseJSON?.message || 'Gagal menambah approval');
                    }
                });
            }
        });
    });

    // Download PDF
    $('#downloadPDF').click(function() {
        const settings = FontSettings.load();
        const settingsParam = encodeURIComponent(JSON.stringify(settings));
        window.open(`/sops/${sopId}/download-pdf?font_settings=${settingsParam}`, '_blank');
    });

    // Update Preview Function
    function updatePreview() {
        $.ajax({
            url: `/sops/${sopId}/preview`,
            method: 'GET',
            success: function(response) {
                $('#pdfPreview').html(response);
                // Reapply font settings after content update
                const settings = FontSettings.load();
                FontSettings.apply(settings);
            },
            error: function(xhr) {
                console.error('Gagal memuat preview');
            }
        });
    }

    // Update Item Numbers
    function updateItemNumbers(itemsList) {
        itemsList.find('.item-group').each(function(index) {
            $(this).find('.input-group-text').text(`${index + 1}.`);
        });
    }

    // Show Alert
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="ri-${type === 'success' ? 'checkbox-circle' : 'error-warning'}-line me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#alertContainer').html(alertHtml);
        setTimeout(() => $('.alert').fadeOut('slow'), 4000);
    }
});
</script>
@endpush