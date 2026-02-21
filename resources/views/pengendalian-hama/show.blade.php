
@extends('layouts.app')
@section('title', 'Detail Pengendalian Hama')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pengendalian-hama.index') }}">Pengendalian Hama</a></li>
    <li class="breadcrumb-item active text-primary" aria-current="page">{{ $pengendalianHama->bulan }} {{ $pengendalianHama->tahun }}</li>
@endsection
@section('content')
<div class="app-body">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Info Header --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-map-pin-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Lokasi</p>
                        <h6 class="mb-0 fw-bold">{{ $pengendalianHama->lokasi }}</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-calendar-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Periode</p>
                        <h6 class="mb-0 fw-bold">{{ $pengendalianHama->bulan }} {{ $pengendalianHama->tahun }}</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-info bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-list-check-2 fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Catatan</p>
                        <h4 class="mb-0">{{ $pengendalianHama->details->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center me-3">
                        <i class="ri-image-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Foto</p>
                        <h4 class="mb-0">{{ $pengendalianHama->gambar->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Detail --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="ri-table-line me-2"></i>Catatan Pengendalian Hama</h5>
            <a href="{{ route('pengendalian-hama.edit', $pengendalianHama->id) }}" class="btn btn-warning btn-sm">
                <i class="ri-pencil-line me-1"></i>Edit Laporan
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" style="font-size:0.83rem;">
                    <thead class="table-light text-center">
                        <tr>
                            <th rowspan="2" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle">Tanggal</th>
                            <th rowspan="2" class="align-middle">Hari</th>
                            <th rowspan="2" class="align-middle">Waktu</th>
                            <th colspan="4">Treatment</th>
                            <th colspan="2">Perangkap</th>
                            <th rowspan="2" class="align-middle">Evaluasi</th>
                            <th colspan="2">Petugas</th>
                            <th rowspan="2" class="align-middle">Keterangan</th>
                        </tr>
                        <tr>
                            <th>C</th><th>B</th><th>F</th><th>I</th>
                            <th>Perlakuan</th><th>Jml Hama</th>
                            <th>Nama</th><th>Paraf</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengendalianHama->details as $i => $d)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('d F Y') }}</td>
                            <td>{{ $d->hari }}</td>
                            <td class="text-center">{{ $d->waktu ? \Carbon\Carbon::parse($d->waktu)->format('H:i') : '-' }}</td>
                            <td class="text-center">@if($d->treatment_c)<i class="ri-check-line text-success fw-bold"></i>@else<span class="text-muted">-</span>@endif</td>
                            <td class="text-center">@if($d->treatment_b)<i class="ri-check-line text-success fw-bold"></i>@else<span class="text-muted">-</span>@endif</td>
                            <td class="text-center">@if($d->treatment_f)<i class="ri-check-line text-success fw-bold"></i>@else<span class="text-muted">-</span>@endif</td>
                            <td class="text-center">@if($d->treatment_i)<i class="ri-check-line text-success fw-bold"></i>@else<span class="text-muted">-</span>@endif</td>
                            <td class="text-center">{{ $d->perangkap_perlakuan ?? '-' }}</td>
                            <td class="text-center">{{ $d->jumlah_hama }}</td>
                            <td class="text-center">{{ $d->evaluasi ?? '-' }}</td>
                            <td>{{ $d->nama_petugas ?? '-' }}</td>
                            <td class="text-center">@if($d->paraf_petugas)<i class="ri-check-line text-success fw-bold"></i>@else<span class="text-muted">-</span>@endif</td>
                            <td>{{ $d->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="14" class="text-center py-4 text-muted">Belum ada catatan harian</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted fw-bold">Treatment:</small>
                    <small class="text-muted"> C: CoolFog | B: Baiting | F: Fogging | I: Inspeksi</small>
                </div>
                <div class="col-md-6">
                    <small class="text-muted fw-bold">Perangkap:</small>
                    <small class="text-muted"> K: Kecoa | MS: Nyamuk | L: Lalat | RB: Tikus</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Galeri Foto --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="ri-image-line me-2"></i>Foto Pest Control</h5>
        </div>
        <div class="card-body">
            @if($pengendalianHama->gambar->count() > 0)
                <div class="row g-3">
                    @foreach($pengendalianHama->gambar as $gambar)
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="position-relative">
                            <a href="{{ Storage::url($gambar->path_gambar) }}" target="_blank">
                                <img src="{{ Storage::url($gambar->path_gambar) }}"
                                     alt="{{ $gambar->nama_file }}"
                                     class="img-fluid rounded shadow-sm"
                                     style="width:100%; height:130px; object-fit:cover;">
                            </a>
                            <form action="{{ route('pengendalian-hama.gambar.destroy', $gambar->id) }}"
                                  method="POST" class="delete-gambar-confirm"
                                  style="position:absolute; top:4px; right:4px;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="padding:2px 6px; font-size:0.75rem;">
                                    <i class="ri-close-line"></i>
                                </button>
                            </form>
                        </div>
                        <p class="text-muted small mt-1 mb-0 text-truncate">{{ $gambar->nama_file }}</p>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="ri-image-line ri-3x text-muted mb-2 d-block"></i>
                    <p class="text-muted">Belum ada foto pest control</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Penanggung Jawab --}}
    @if($pengendalianHama->penanggung_jawab)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 offset-md-8 text-center">
                    <p class="mb-1 fw-bold">Menyetujui,</p>
                    <div class="border rounded p-3 mb-2" style="min-height:80px;"></div>
                    <p class="mb-0 fw-bold">{{ $pengendalianHama->penanggung_jawab }}</p>
                    <small class="text-muted">Penanggung Jawab Teknis</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="d-flex justify-content-between mb-4">
        <a href="{{ route('pengendalian-hama.index') }}" class="btn btn-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Kembali
        </a>
        <a href="{{ route('pengendalian-hama.edit', $pengendalianHama->id) }}" class="btn btn-warning btn-sm">
            <i class="ri-pencil-line me-1"></i>Edit Laporan
        </a>
    </div>
</div>
@endsection
@push('styles')
<style>
    .avatar-sm { width: 3rem; height: 3rem; display: flex; align-items: center; justify-content: center; }
    .table th, .table td { vertical-align: middle; font-size: 0.83rem; }
    .card { border-radius: 0.5rem; }
</style>
@endpush
@push('scripts')
<script>
$(document).ready(function () {
    $(document).on('submit', '.delete-gambar-confirm', function (e) {
        e.preventDefault(); var form = this;
        Swal.fire({ title: 'Hapus Foto?', text: 'Foto ini akan dihapus permanen!',
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
        }).then(r => { if (r.isConfirmed) form.submit(); });
    });
    setTimeout(() => $('.alert').fadeOut('slow'), 4000);
});
</script>
@endpush
