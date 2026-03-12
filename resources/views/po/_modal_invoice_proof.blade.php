{{-- Modal untuk Lihat/Upload Bukti Invoice & Barang --}}
<div class="modal fade" id="invoiceProofModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="ri-image-line me-2"></i>
                    <span id="invoiceProofModalTitle">Upload Bukti Invoice & Barang</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- PREVIEW MODE -->
                <div id="invoiceProofPreview" style="{{ $po->hasInvoiceProof() || $po->hasBarangProof() ? '' : 'display: none;' }}">
                    <div class="row">
                        <!-- Bukti Invoice Preview -->
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="ri-file-text-line me-1"></i>
                                        Bukti Invoice
                                    </h6>
                                    @php
                                        $invoiceProofs = $po->invoiceProofs;
                                    @endphp
                                    @if($invoiceProofs->count() > 0)
                                        <span class="badge bg-primary">{{ $invoiceProofs->count() }} File</span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if($invoiceProofs->count() > 0)
                                        <!-- Carousel untuk multiple bukti -->
                                        @if($invoiceProofs->count() > 1)
                                        <div id="invoiceCarousel" class="carousel slide mb-3" data-bs-ride="false">
                                            <div class="carousel-indicators">
                                                @foreach($invoiceProofs as $index => $proof)
                                                <button type="button" data-bs-target="#invoiceCarousel" data-bs-slide-to="{{ $index }}" 
                                                    class="{{ $index == 0 ? 'active' : '' }}" 
                                                    aria-label="Slide {{ $index + 1 }}"></button>
                                                @endforeach
                                            </div>
                                            <div class="carousel-inner rounded">
                                                @foreach($invoiceProofs as $index => $proof)
                                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                    <div class="text-center bg-light p-3 rounded" style="min-height: 350px; display: flex; align-items: center; justify-content: center;">
                                                        @if(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']))
                                                            <img src="{{ asset('storage/' . $proof->file_path) }}" 
                                                                class="img-fluid rounded shadow-sm" 
                                                                style="max-height: 350px; max-width: 100%; object-fit: contain;"
                                                                alt="Bukti Invoice {{ $index + 1 }}">
                                                        @elseif($proof->file_type == 'application/pdf')
                                                            <div class="text-center">
                                                                <i class="ri-file-pdf-line text-danger mb-3" style="font-size: 5rem;"></i>
                                                                <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                                <p class="text-muted mb-3">{{ $proof->file_size_formatted }}</p>
                                                                <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                                target="_blank" 
                                                                class="btn btn-sm btn-primary">
                                                                    <i class="ri-eye-line me-1"></i> Lihat PDF
                                                                </a>
                                                            </div>
                                                        @else
                                                            <div class="text-center">
                                                                <i class="ri-file-line text-secondary mb-3" style="font-size: 5rem;"></i>
                                                                <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                                <p class="text-muted">{{ $proof->file_size_formatted }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded mx-2 mb-2">
                                                        <small class="text-white">File {{ $index + 1 }} dari {{ $invoiceProofs->count() }}</small>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <button class="carousel-control-prev" type="button" data-bs-target="#invoiceCarousel" data-bs-slide="prev">
                                                <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="ri-arrow-left-s-line text-white fs-4"></i>
                                                </span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#invoiceCarousel" data-bs-slide="next">
                                                <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="ri-arrow-right-s-line text-white fs-4"></i>
                                                </span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        </div>
                                        @else
                                        <!-- Single file -->
                                        @php $proof = $invoiceProofs->first(); @endphp
                                        <div class="text-center mb-3 bg-light p-3 rounded" style="min-height: 350px; display: flex; align-items: center; justify-content: center;">
                                            @if(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']))
                                                <img src="{{ asset('storage/' . $proof->file_path) }}" 
                                                    class="img-fluid rounded shadow-sm border" 
                                                    style="max-height: 350px; max-width: 100%; object-fit: contain;"
                                                    alt="Bukti Invoice">
                                            @elseif($proof->file_type == 'application/pdf')
                                                <div class="text-center">
                                                    <i class="ri-file-pdf-line text-danger mb-3" style="font-size: 5rem;"></i>
                                                    <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                    <p class="text-muted mb-3">{{ $proof->file_size_formatted }}</p>
                                                    <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                    target="_blank" 
                                                    class="btn btn-sm btn-primary">
                                                        <i class="ri-eye-line me-1"></i> Lihat PDF
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-center">
                                                    <i class="ri-file-line text-secondary mb-3" style="font-size: 5rem;"></i>
                                                    <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                    <p class="text-muted">{{ $proof->file_size_formatted }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        <!-- File List -->
                                        <div class="mt-3">
                                            <div class="accordion" id="invoiceAccordion">
                                                <div class="accordion-item border-0">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#invoiceFileList">
                                                            <i class="ri-file-list-line me-2"></i>
                                                            <strong>Daftar File ({{ $invoiceProofs->count() }})</strong>
                                                        </button>
                                                    </h2>
                                                    <div id="invoiceFileList" class="accordion-collapse collapse" data-bs-parent="#invoiceAccordion">
                                                        <div class="accordion-body p-0">
                                                            <div class="list-group list-group-flush">
                                                                @foreach($invoiceProofs as $index => $proof)
                                                                <div class="list-group-item">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div class="flex-grow-1">
                                                                            <div class="d-flex align-items-start mb-2">
                                                                                <div class="me-2">
                                                                                    @if($proof->file_type == 'application/pdf')
                                                                                        <i class="ri-file-pdf-line text-danger fs-4"></i>
                                                                                    @elseif(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png']))
                                                                                        <i class="ri-image-line text-success fs-4"></i>
                                                                                    @else
                                                                                        <i class="ri-file-line text-secondary fs-4"></i>
                                                                                    @endif
                                                                                </div>
                                                                                <div class="flex-grow-1">
                                                                                    <h6 class="mb-1">
                                                                                        <span class="badge bg-secondary me-2">#{{ $index + 1 }}</span>
                                                                                        {{ Str::limit($proof->file_name, 40) }}
                                                                                    </h6>
                                                                                    <div class="small text-muted">
                                                                                        <div class="mb-1">
                                                                                            <i class="ri-hard-drive-line me-1"></i>
                                                                                            <strong>Ukuran:</strong> {{ $proof->file_size_formatted }}
                                                                                        </div>
                                                                                        <div class="mb-1">
                                                                                            <i class="ri-calendar-line me-1"></i>
                                                                                            <strong>Diupload:</strong> {{ $proof->tanggal_upload->format('d/m/Y H:i') }}
                                                                                        </div>
                                                                                        @if($proof->karyawan)
                                                                                        <div>
                                                                                            <i class="ri-user-line me-1"></i>
                                                                                            <strong>Oleh:</strong> {{ $proof->karyawan->nama_lengkap }}
                                                                                        </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="btn-group-vertical btn-group-sm ms-2">
                                                                            <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                                            target="_blank" 
                                                                            class="btn btn-sm btn-outline-success">
                                                                                <i class="ri-download-line me-1"></i> Download
                                                                            </a>
                                                                            <button type="button" 
                                                                                    class="btn btn-sm btn-outline-danger" 
                                                                                    onclick="confirmDeleteProof('invoice', '{{ $proof->id_po_proof }}')">
                                                                                <i class="ri-delete-bin-line me-1"></i> Hapus
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="mt-3 d-flex gap-2 justify-content-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning" 
                                                    onclick="changeProof('invoice')">
                                                <i class="ri-add-line me-1"></i> Tambah Bukti Invoice
                                            </button>
                                        </div>
                                    @else
                                        <!-- No files -->
                                        <div class="text-center py-5 text-muted">
                                            <i class="ri-file-forbid-line" style="font-size: 3rem;"></i>
                                            <p class="mt-3 mb-0 fw-semibold">Belum ada bukti invoice</p>
                                            <p class="text-muted small mb-3">Upload bukti invoice untuk melengkapi dokumentasi PO</p>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary" 
                                                    onclick="changeProof('invoice')">
                                                <i class="ri-upload-line me-1"></i> Upload Sekarang
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Bukti Barang Preview -->
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="ri-box-3-line me-1"></i>
                                        Bukti Barang
                                    </h6>
                                    @php
                                        $barangProofs = $po->barangProofs;
                                    @endphp
                                    @if($barangProofs->count() > 0)
                                        <span class="badge bg-success">{{ $barangProofs->count() }} File</span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if($barangProofs->count() > 0)
                                        <!-- Carousel untuk multiple bukti -->
                                        @if($barangProofs->count() > 1)
                                        <div id="barangCarousel" class="carousel slide mb-3" data-bs-ride="false">
                                            <div class="carousel-indicators">
                                                @foreach($barangProofs as $index => $proof)
                                                <button type="button" data-bs-target="#barangCarousel" data-bs-slide-to="{{ $index }}" 
                                                    class="{{ $index == 0 ? 'active' : '' }}" 
                                                    aria-label="Slide {{ $index + 1 }}"></button>
                                                @endforeach
                                            </div>
                                            <div class="carousel-inner rounded">
                                                @foreach($barangProofs as $index => $proof)
                                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                    <div class="text-center bg-light p-3 rounded" style="min-height: 350px; display: flex; align-items: center; justify-content: center;">
                                                        @if(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']))
                                                            <img src="{{ asset('storage/' . $proof->file_path) }}" 
                                                                class="img-fluid rounded shadow-sm" 
                                                                style="max-height: 350px; max-width: 100%; object-fit: contain;"
                                                                alt="Bukti Barang {{ $index + 1 }}">
                                                        @elseif($proof->file_type == 'application/pdf')
                                                            <div class="text-center">
                                                                <i class="ri-file-pdf-line text-danger mb-3" style="font-size: 5rem;"></i>
                                                                <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                                <p class="text-muted mb-3">{{ $proof->file_size_formatted }}</p>
                                                                <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                                target="_blank" 
                                                                class="btn btn-sm btn-success">
                                                                    <i class="ri-eye-line me-1"></i> Lihat PDF
                                                                </a>
                                                            </div>
                                                        @else
                                                            <div class="text-center">
                                                                <i class="ri-file-line text-secondary mb-3" style="font-size: 5rem;"></i>
                                                                <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                                <p class="text-muted">{{ $proof->file_size_formatted }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded mx-2 mb-2">
                                                        <small class="text-white">File {{ $index + 1 }} dari {{ $barangProofs->count() }}</small>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <button class="carousel-control-prev" type="button" data-bs-target="#barangCarousel" data-bs-slide="prev">
                                                <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="ri-arrow-left-s-line text-white fs-4"></i>
                                                </span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#barangCarousel" data-bs-slide="next">
                                                <span class="bg-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="ri-arrow-right-s-line text-white fs-4"></i>
                                                </span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        </div>
                                        @else
                                        <!-- Single file -->
                                        @php $proof = $barangProofs->first(); @endphp
                                        <div class="text-center mb-3 bg-light p-3 rounded" style="min-height: 350px; display: flex; align-items: center; justify-content: center;">
                                            @if(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']))
                                                <img src="{{ asset('storage/' . $proof->file_path) }}" 
                                                    class="img-fluid rounded shadow-sm border" 
                                                    style="max-height: 350px; max-width: 100%; object-fit: contain;"
                                                    alt="Bukti Barang">
                                            @elseif($proof->file_type == 'application/pdf')
                                                <div class="text-center">
                                                    <i class="ri-file-pdf-line text-danger mb-3" style="font-size: 5rem;"></i>
                                                    <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                    <p class="text-muted mb-3">{{ $proof->file_size_formatted }}</p>
                                                    <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                    target="_blank" 
                                                    class="btn btn-sm btn-success">
                                                        <i class="ri-eye-line me-1"></i> Lihat PDF
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-center">
                                                    <i class="ri-file-line text-secondary mb-3" style="font-size: 5rem;"></i>
                                                    <h6 class="mb-2">{{ $proof->file_name }}</h6>
                                                    <p class="text-muted">{{ $proof->file_size_formatted }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        <!-- File List -->
                                        <div class="mt-3">
                                            <div class="accordion" id="barangAccordion">
                                                <div class="accordion-item border-0">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#barangFileList">
                                                            <i class="ri-file-list-line me-2"></i>
                                                            <strong>Daftar File ({{ $barangProofs->count() }})</strong>
                                                        </button>
                                                    </h2>
                                                    <div id="barangFileList" class="accordion-collapse collapse" data-bs-parent="#barangAccordion">
                                                        <div class="accordion-body p-0">
                                                            <div class="list-group list-group-flush">
                                                                @foreach($barangProofs as $index => $proof)
                                                                <div class="list-group-item">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div class="flex-grow-1">
                                                                            <div class="d-flex align-items-start mb-2">
                                                                                <div class="me-2">
                                                                                    @if($proof->file_type == 'application/pdf')
                                                                                        <i class="ri-file-pdf-line text-danger fs-4"></i>
                                                                                    @elseif(in_array($proof->file_type, ['image/jpeg', 'image/jpg', 'image/png']))
                                                                                        <i class="ri-image-line text-success fs-4"></i>
                                                                                    @else
                                                                                        <i class="ri-file-line text-secondary fs-4"></i>
                                                                                    @endif
                                                                                </div>
                                                                                <div class="flex-grow-1">
                                                                                    <h6 class="mb-1">
                                                                                        <span class="badge bg-secondary me-2">#{{ $index + 1 }}</span>
                                                                                        {{ Str::limit($proof->file_name, 40) }}
                                                                                    </h6>
                                                                                    <div class="small text-muted">
                                                                                        <div class="mb-1">
                                                                                            <i class="ri-hard-drive-line me-1"></i>
                                                                                            <strong>Ukuran:</strong> {{ $proof->file_size_formatted }}
                                                                                        </div>
                                                                                        <div class="mb-1">
                                                                                            <i class="ri-calendar-line me-1"></i>
                                                                                            <strong>Diupload:</strong> {{ $proof->tanggal_upload->format('d/m/Y H:i') }}
                                                                                        </div>
                                                                                        @if($proof->karyawan)
                                                                                        <div>
                                                                                            <i class="ri-user-line me-1"></i>
                                                                                            <strong>Oleh:</strong> {{ $proof->karyawan->nama_lengkap }}
                                                                                        </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="btn-group-vertical btn-group-sm ms-2">
                                                                            <a href="{{ asset('storage/' . $proof->file_path) }}" 
                                                                            target="_blank" 
                                                                            class="btn btn-sm btn-outline-primary">
                                                                                <i class="ri-download-line me-1"></i> Download
                                                                            </a>
                                                                            <button type="button" 
                                                                                    class="btn btn-sm btn-outline-danger" 
                                                                                    onclick="confirmDeleteProof('barang', '{{ $proof->id_po_proof }}')">
                                                                                <i class="ri-delete-bin-line me-1"></i> Hapus
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="mt-3 d-flex gap-2 justify-content-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning" 
                                                    onclick="changeProof('barang')">
                                                <i class="ri-add-line me-1"></i> Tambah Bukti Barang
                                            </button>
                                        </div>
                                    @else
                                        <!-- No files -->
                                        <div class="text-center py-5 text-muted">
                                            <i class="ri-box-3-line" style="font-size: 3rem;"></i>
                                            <p class="mt-3 mb-0 fw-semibold">Belum ada bukti barang</p>
                                            <p class="text-muted small mb-3">Upload bukti barang untuk melengkapi dokumentasi PO</p>
                                            <button type="button" 
                                                    class="btn btn-sm btn-success" 
                                                    onclick="changeProof('barang')">
                                                <i class="ri-upload-line me-1"></i> Upload Sekarang
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>