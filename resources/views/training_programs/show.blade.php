@extends('layouts.app')
@section('title', 'Detail Program Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('trainings.index') }}">Program Pelatihan</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Detail</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0 text-gray-600">Detail Program Pelatihan</h1>
            <div>
                <a href="{{ route('trainings.edit', $training->id) }}" class="btn btn-warning">
                    <i class="ri-edit-line"></i> Edit
                </a>
                <a href="{{ route('trainings.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Kembali
                </a>
            </div>
        </div>

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
                                <th style="width: 30%">Nama Pelatihan</th>
                                <td style="width: 2%">:</td>
                                <td><strong>{{ $training->nama_pelatihan }}</strong></td>
                            </tr>
                            <tr>
                                <th>Peserta</th>
                                <td>:</td>
                                <td>{{ $training->peserta ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Instruktur</th>
                                <td>:</td>
                                <td>{{ $training->instruktur ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Metode</th>
                                <td>:</td>
                                <td>{{ $training->metode ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jadwal</th>
                                <td>:</td>
                                <td>{{ $training->jadwal ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Metode Penilaian</th>
                                <td>:</td>
                                <td>{{ $training->metode_penilaian ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>:</td>
                                <td>
                                    @if($training->tanggal_mulai)
                                        {{ $training->tanggal_mulai->format('d F Y') }}
                                        @if($training->tanggal_selesai)
                                            s/d {{ $training->tanggal_selesai->format('d F Y') }}
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Lokasi</th>
                                <td>:</td>
                                <td>{{ $training->lokasi ?? '-' }}</td>
                            </tr>
                            @if($training->catatan)
                                <tr>
                                    <th>Catatan</th>
                                    <td>:</td>
                                    <td>{{ $training->catatan }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Gambar Pelatihan --}}
                @if($training->images->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header bg-purple text-white">
                        <h5 class="mb-0">
                            <i class="ri-image-line me-2"></i>Galeri Dokumentasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($training->images as $image)
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
                                <td><i class="ri-image-line text-purple"></i></td>
                                <td>Total Gambar:</td>
                                <td><strong>{{ $training->images->count() }} foto</strong></td>
                            </tr>
                            <tr>
                                <td><i class="ri-calendar-line text-primary"></i></td>
                                <td>Dibuat pada:</td>
                                <td>{{ $training->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><i class="ri-refresh-line text-success"></i></td>
                                <td>Terakhir diubah:</td>
                                <td>{{ $training->updated_at->format('d M Y H:i') }}</td>
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
                            <a href="{{ route('evaluation-forms.create') }}" class="btn btn-outline-warning btn-sm">
                                <i class="ri-survey-line me-1"></i> Buat Form Evaluasi
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