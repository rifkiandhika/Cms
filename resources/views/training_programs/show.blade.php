@extends('layouts.app')
@section('title', 'Detail Program Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('training-programs.index') }}">Program Pelatihan</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Detail</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0 text-gray-600">Detail Program Pelatihan</h1>
            <div>
                <a href="{{ route('training-programs.edit', $trainingProgram->id) }}" class="btn btn-warning">
                    <i class="ri-edit-line"></i> Edit
                </a>
                <a href="{{ route('sops.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Kembali
                </a>
            </div>
        </div>

        @php
            $allImages = collect();
            foreach($trainingProgram->mainCategories as $mainCat) {
                foreach($mainCat->subCategories as $subCat) {
                    foreach($subCat->trainingItems as $item) {
                        $allImages = $allImages->merge($item->images);
                    }
                }
            }
        @endphp

        <div class="row">
            <div class="col-md-8">
                {{-- Info Pelatihan --}}
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="ri-book-line me-2"></i>Informasi Pelatihan
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 30%">Judul Program</th>
                                <td style="width: 2%">:</td>
                                <td><strong>{{ $trainingProgram->title }}</strong></td>
                            </tr>
                            <tr>
                                <th>Nomor Program</th>
                                <td>:</td>
                                <td>{{ $trainingProgram->program_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Efektif</th>
                                <td>:</td>
                                <td>{{ $trainingProgram->effective_date ? \Carbon\Carbon::parse($trainingProgram->effective_date)->format('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Revisi</th>
                                <td>:</td>
                                <td>{{ $trainingProgram->revision ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>:</td>
                                <td>
                                    @if($trainingProgram->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($trainingProgram->status === 'draft')
                                        <span class="badge bg-secondary">Draft</span>
                                    @else
                                        <span class="badge bg-danger">Archived</span>
                                    @endif
                                </td>
                            </tr>
                            @if($trainingProgram->description)
                            <tr>
                                <th>Deskripsi</th>
                                <td>:</td>
                                <td>{{ $trainingProgram->description }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Kategori Utama --}}
                @foreach($trainingProgram->mainCategories as $mainCategory)
                <div class="card shadow mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="ri-list-check me-2"></i>
                            {{ $mainCategory->roman_number }}. {{ $mainCategory->name }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($mainCategory->subCategories as $subCategory)
                        <div class="p-3 border-bottom">
                            <h6 class="fw-bold text-primary">
                                {{ $subCategory->letter }}. {{ $subCategory->name }}
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pelatihan</th>
                                            <th>Peserta</th>
                                            <th>Instruktur</th>
                                            <th>Metode</th>
                                            <th>Jadwal</th>
                                            <th>Metode Penilaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subCategory->trainingItems as $item)
                                        <tr>
                                            <td>{{ $item->number }}</td>
                                            <td>
                                                {{ $item->nama_pelatihan }}
                                                @if($item->details->count() > 0)
                                                    <ul class="mb-0 mt-1">
                                                        @foreach($item->details as $detail)
                                                            <li><small>{{ $detail->letter }}. {{ $detail->content }}</small></li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </td>
                                            <td>{{ $item->peserta ?? '-' }}</td>
                                            <td>{{ $item->instruktur ?? '-' }}</td>
                                            <td>{{ $item->metode ?? '-' }}</td>
                                            <td>{{ $item->jadwal ?? '-' }}</td>
                                            <td>{{ $item->metode_penilaian ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                {{-- Galeri Dokumentasi --}}
                @if($allImages->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header bg-purple text-white">
                        <h5 class="mb-0">
                            <i class="ri-image-line me-2"></i>Galeri Dokumentasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($allImages as $image)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             class="card-img-top" 
                                             alt="Training Image" 
                                             style="height: 250px; object-fit: cover; cursor: pointer;"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#imageModal{{ $image->id }}">
                                        @if($image->caption)
                                            <div class="card-body">
                                                <p class="card-text text-muted mb-0">
                                                    <i class="ri-chat-3-line"></i> {{ $image->caption }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Modal untuk gambar --}}
                                    <div class="modal fade" id="imageModal{{ $image->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ $image->caption ?? 'Gambar Pelatihan' }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                         class="img-fluid" 
                                                         alt="Training Image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-4">
                {{-- Ringkasan --}}
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="ri-information-line me-2"></i>Ringkasan
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td><i class="ri-book-2-line text-secondary"></i></td>
                                <td>Total Kategori:</td>
                                <td><strong>{{ $trainingProgram->mainCategories->count() }} kategori</strong></td>
                            </tr>
                            <tr>
                                <td><i class="ri-image-line text-purple"></i></td>
                                <td>Total Gambar:</td>
                                <td><strong>{{ $allImages->count() }} foto</strong></td>
                            </tr>
                            <tr>
                                <td><i class="ri-calendar-line text-primary"></i></td>
                                <td>Dibuat pada:</td>
                                <td>{{ $trainingProgram->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><i class="ri-refresh-line text-success"></i></td>
                                <td>Terakhir diubah:</td>
                                <td>{{ $trainingProgram->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">
                            <i class="ri-links-line me-2"></i>Link Terkait
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('attendance-forms.create') }}" class="btn btn-outline-success btn-sm">
                                <i class="ri-file-list-2-line me-1"></i> Buat Daftar Hadir
                            </a>
                            <a href="{{ route('evaluation-programs.create') }}" class="btn btn-outline-warning btn-sm">
                                <i class="ri-survey-line me-1"></i> Buat Form Evaluasi
                            </a>
                            <a href="{{ route('training-programs.export-pdf', $trainingProgram->id) }}" class="btn btn-outline-danger btn-sm">
                                <i class="ri-file-pdf-line me-1"></i> Export PDF
                            </a>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <i class="ri-information-line"></i> Link cepat untuk membuat form terkait pelatihan ini
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.bg-purple {
    background-color: #6f42c1;
    color: white;
}
</style>
@endpush