@extends('layouts.app')

@section('title', 'Detail Program Evaluasi')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="ri-survey-line me-2"></i>{{ $evaluationProgram->title }}</h2>
            <p class="text-muted mb-0">Detail Program Evaluasi</p>
        </div>
        <div>
            <a href="{{ route('evaluation-programs.index') }}" class="btn btn-secondary me-2">
                <i class="ri-arrow-left-line me-1"></i>Kembali
            </a>
            <a href="{{ route('evaluation-programs.generate-all-pdf', $evaluationProgram->id) }}"
               class="btn btn-danger me-2" target="_blank">
                <i class="ri-file-pdf-line me-1"></i>Export PDF
            </a>
            <a href="{{ route('evaluation-programs.edit', $evaluationProgram->id) }}" class="btn btn-primary">
                <i class="ri-edit-line me-1"></i>Edit
            </a>
        </div>
    </div>

    {{-- Program Info --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="ri-information-line me-2"></i>Informasi Program</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%">No. Program</th>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $evaluationProgram->program_number }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Materi Pelatihan</th>
                            <td><strong>{{ $evaluationProgram->materi_pelatihan }}</strong></td>
                        </tr>
                        <tr>
                            <th>Hari/Tanggal</th>
                            <td>
                                {{ $evaluationProgram->hari_tanggal ? $evaluationProgram->hari_tanggal->format('d F Y') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Tempat Pelatihan</th>
                            <td>{{ $evaluationProgram->tempat_pelatihan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%">Status</th>
                            <td>
                                @if($evaluationProgram->status === 'draft')
                                    <span class="badge bg-warning">Draft</span>
                                @elseif($evaluationProgram->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Arsip</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total Item Evaluasi</th>
                            <td><span class="badge bg-primary">{{ $evaluationProgram->items->count() }} Item</span></td>
                        </tr>
                        <tr>
                            <th>Total Peserta</th>
                            <td><span class="badge bg-info">{{ $evaluationProgram->participants->count() }} Orang</span></td>
                        </tr>
                        <tr>
                            <th>Total Responses</th>
                            <td><span class="badge bg-purple">{{ $evaluationProgram->responses->count() }} Respon</span></td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td><small>{{ $evaluationProgram->created_at->format('d M Y H:i') }}</small></td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($evaluationProgram->description)
                <div class="alert alert-info mb-0 mt-3">
                    <strong>Deskripsi:</strong> {{ $evaluationProgram->description }}
                </div>
            @endif
        </div>
    </div>

    {{-- Items Evaluasi --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="ri-list-check me-2"></i>Item Evaluasi</h5>
        </div>
        <div class="card-body p-0">
            @forelse($evaluationProgram->items->sortBy('order') as $item)
                <div class="d-flex align-items-start p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <span class="badge bg-primary me-3 mt-1" style="min-width:2rem; font-size:.9rem;">
                        {{ $item->item_label }}
                    </span>
                    <div>
                        <p class="mb-0">{{ $item->item_content }}</p>
                    </div>
                </div>
            @empty
                <div class="alert alert-info m-3 mb-0">
                    <i class="ri-information-line me-2"></i>Belum ada item evaluasi.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Peserta Terdaftar --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="ri-group-line me-2"></i>Daftar Peserta Terdaftar</h5>
        </div>
        <div class="card-body p-0">
            @forelse($evaluationProgram->participants->sortBy('order') as $index => $participant)
                @php
                    $hasResponse = $evaluationProgram->responses
                        ->where('evaluation_participant_id', $participant->id)
                        ->isNotEmpty();
                @endphp
                <div class="d-flex align-items-center justify-content-between p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-light text-dark border me-3">{{ $index + 1 }}</span>
                        <div>
                            <strong>{{ $participant->nama_peserta }}</strong>
                            @if($participant->jabatan_lokasi_kerja)
                                <br><small class="text-muted">{{ $participant->jabatan_lokasi_kerja }}</small>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($hasResponse)
                            <span class="badge bg-success"><i class="ri-checkbox-circle-line me-1"></i>Sudah Mengisi</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="ri-time-line me-1"></i>Belum Mengisi</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="alert alert-info m-3 mb-0">
                    <i class="ri-information-line me-2"></i>Belum ada peserta terdaftar.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Responses --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ri-file-text-line me-2"></i>Responses Peserta</h5>
            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addResponseModal">
                <i class="ri-add-line me-1"></i>Tambah Response
            </button>
        </div>
        <div class="card-body">
            @if($evaluationProgram->responses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Nama Peserta</th>
                                <th width="25%">Jabatan/Lokasi</th>
                                <th width="20%">Yang Mengetahui</th>
                                <th width="15%">Tanggal Submit</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluationProgram->responses as $index => $response)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $response->nama_peserta }}</strong></td>
                                    <td>{{ $response->jabatan_lokasi_kerja ?? '-' }}</td>
                                    <td>
                                        @if($response->mengetahui_atasan_nama || $response->mengetahui_personalia_nama)
                                            @if($response->mengetahui_atasan_nama)
                                                <div class="small">
                                                    <span class="text-muted">Atasan:</span>
                                                    <strong>{{ $response->mengetahui_atasan_nama }}</strong>
                                                </div>
                                            @endif
                                            @if($response->mengetahui_personalia_nama)
                                                <div class="small">
                                                    <span class="text-muted">Personalia:</span>
                                                    <strong>{{ $response->mengetahui_personalia_nama }}</strong>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $response->created_at->format('d M Y H:i') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewResponseModal{{ $response->id }}">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal Detail Response --}}
                                <div class="modal fade" id="viewResponseModal{{ $response->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">
                                                    <i class="ri-file-text-line me-2"></i>Detail Response — {{ $response->nama_peserta }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                {{-- Info Peserta --}}
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Nama Peserta:</strong> {{ $response->nama_peserta }}</p>
                                                        <p class="mb-0"><strong>Jabatan/Lokasi:</strong> {{ $response->jabatan_lokasi_kerja ?? '-' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1">
                                                            <strong>Atasan:</strong>
                                                            {{ $response->mengetahui_atasan_nama ?? '-' }}
                                                            @if($response->mengetahui_atasan_tanggal)
                                                                <span class="text-muted">({{ $response->mengetahui_atasan_tanggal->format('d M Y') }})</span>
                                                            @endif
                                                        </p>
                                                        <p class="mb-0">
                                                            <strong>Personalia:</strong>
                                                            {{ $response->mengetahui_personalia_nama ?? '-' }}
                                                            @if($response->mengetahui_personalia_tanggal)
                                                                <span class="text-muted">({{ $response->mengetahui_personalia_tanggal->format('d M Y') }})</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>

                                                <hr>

                                                <h6 class="mb-3">Jawaban Evaluasi:</h6>
                                                @if(is_array($response->responses) && count($response->responses) > 0)
                                                    @foreach($evaluationProgram->items->sortBy('order') as $item)
                                                        <div class="mb-3 p-3 bg-light rounded border-start border-primary border-3">
                                                            <p class="fw-bold mb-2">
                                                                <span class="badge bg-primary me-2">{{ $item->item_label }}</span>
                                                                {{ $item->item_content }}
                                                            </p>
                                                            <div class="ps-2">
                                                                <span class="badge bg-success mb-1">Jawaban:</span>
                                                                <p class="mb-0">
                                                                    {{ $response->responses[$item->id] ?? '<em class="text-muted">Tidak dijawab</em>' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="alert alert-warning">
                                                        <i class="ri-alert-line me-2"></i>Data jawaban tidak tersedia.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <a href="{{ route('evaluation-programs.generate-pdf', [$evaluationProgram->id, $response->evaluation_participant_id ?? 0]) }}"
                                                   class="btn btn-danger" target="_blank">
                                                    <i class="ri-file-pdf-line me-1"></i>Export PDF
                                                </a>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="ri-information-line me-2"></i>
                    Belum ada peserta yang mengisi evaluasi. Klik <strong>"Tambah Response"</strong> untuk menambahkan.
                </div>
            @endif
        </div>
    </div>

    {{-- Images --}}
    @if($evaluationProgram->images->count() > 0)
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="ri-image-line me-2"></i>Dokumentasi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($evaluationProgram->images->sortBy('order') as $image)
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     class="card-img-top cursor-pointer"
                                     alt="{{ $image->caption }}"
                                     style="height: 180px; object-fit: cover;"
                                     data-bs-toggle="modal"
                                     data-bs-target="#imageModal"
                                     data-image="{{ asset('storage/' . $image->image_path) }}"
                                     data-caption="{{ $image->caption }}">
                                @if($image->caption)
                                    <div class="card-body p-2">
                                        <small class="text-muted">{{ $image->caption }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Modal: Tambah Response --}}
<div class="modal fade" id="addResponseModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('evaluation-programs.fill-response', $evaluationProgram->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="ri-file-edit-line me-2"></i>Isi Form Evaluasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Info Peserta --}}
                    <div class="card mb-3 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Informasi Peserta</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Peserta Terdaftar</label>
                                    <select name="evaluation_participant_id" class="form-select" id="participantSelect">
                                        <option value="">— Pilih peserta atau isi manual —</option>
                                        @foreach($evaluationProgram->participants->sortBy('order') as $participant)
                                            <option value="{{ $participant->id }}"
                                                    data-nama="{{ $participant->nama_peserta }}"
                                                    data-jabatan="{{ $participant->jabatan_lokasi_kerja }}">
                                                {{ $participant->nama_peserta }}
                                                @if($participant->jabatan_lokasi_kerja)
                                                    — {{ $participant->jabatan_lokasi_kerja }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Peserta <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_peserta" id="inputNama" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jabatan/Lokasi Kerja</label>
                                    <input type="text" name="jabatan_lokasi_kerja" id="inputJabatan" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Item Evaluasi --}}
                    <div class="card mb-3 border">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="ri-list-check me-2"></i>Jawaban Evaluasi</h6>
                        </div>
                        <div class="card-body">
                            @forelse($evaluationProgram->items->sortBy('order') as $item)
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <span class="badge bg-primary me-2">{{ $item->item_label }}</span>
                                        {{ $item->item_content }}
                                    </label>
                                    <textarea name="responses[{{ $item->id }}]"
                                              class="form-control"
                                              rows="3"
                                              placeholder="Tulis jawaban Anda..."></textarea>
                                </div>
                            @empty
                                <div class="alert alert-warning mb-0">
                                    <i class="ri-alert-line me-2"></i>Belum ada item evaluasi pada program ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Yang Mengetahui --}}
                    <div class="card border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Yang Mengetahui <small class="text-muted">(Opsional)</small></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="small fw-bold text-muted mb-2">ATASAN</h6>
                                    <div class="row">
                                        <div class="col-md-8 mb-2">
                                            <label class="form-label small">Nama Atasan</label>
                                            <input type="text" name="mengetahui_atasan_nama" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">Tanggal</label>
                                            <input type="date" name="mengetahui_atasan_tanggal" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="small fw-bold text-muted mb-2">BAGIAN PERSONALIA</h6>
                                    <div class="row">
                                        <div class="col-md-8 mb-2">
                                            <label class="form-label small">Nama Personalia</label>
                                            <input type="text" name="mengetahui_personalia_nama" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">Tanggal</label>
                                            <input type="date" name="mengetahui_personalia_tanggal" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-save-line me-1"></i>Submit Evaluasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Image Preview --}}
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dokumentasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-fluid rounded" alt="">
                <p class="mt-3 text-muted mb-0" id="modalCaption"></p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    // Auto-fill nama & jabatan saat pilih peserta dari dropdown
    $('#participantSelect').on('change', function () {
        const selected = $(this).find('option:selected');
        $('#inputNama').val(selected.data('nama') || '');
        $('#inputJabatan').val(selected.data('jabatan') || '');
    });

    // Image preview modal
    $('#imageModal').on('show.bs.modal', function (event) {
        const btn = $(event.relatedTarget);
        $('#modalImage').attr('src', btn.data('image'));
        $('#modalCaption').text(btn.data('caption') || '');
    });
});
</script>
@endpush

@push('styles')
<style>
.cursor-pointer          { cursor: pointer; transition: transform .2s ease; }
.cursor-pointer:hover    { transform: scale(1.03); box-shadow: 0 .25rem .5rem rgba(0,0,0,.15); }
.border-3                { border-width: 3px !important; }
.bg-purple               { background-color: #6f42c1; color: white; }
</style>
@endpush
@endsection