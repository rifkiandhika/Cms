@extends('layouts.app')
@section('title', 'Detail Daftar Hadir Pelatihan')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('attendance-forms.index') }}">Daftar Hadir Pelatihan</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Detail</li>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0 text-gray-600">Detail Daftar Hadir Pelatihan</h1>
            <div class="d-flex gap-2 no-print">
                <a href="{{ route('attendance-forms.edit', $attendanceForm->id) }}" class="btn btn-warning btn-sm">
                    <i class="ri-edit-line me-1"></i>Edit
                </a>
                <button onclick="window.print()" class="btn btn-info btn-sm">
                    <i class="ri-printer-line me-1"></i>Cetak
                </button>
                <a href="{{ route('attendance-forms.index') }}" class="btn btn-secondary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>Kembali
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">

                {{-- Header Info --}}
                <div class="border p-4 mb-4 bg-light rounded print-section">
                    <h4 class="mb-4 text-center fw-bold">DAFTAR HADIR PELATIHAN</h4>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th style="width:25%">Topik Pelatihan</th>
                            <td style="width:2%">:</td>
                            <td><strong>{{ $attendanceForm->topik_pelatihan }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th><td>:</td>
                            <td>{{ $attendanceForm->tanggal?->translatedFormat('d F Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tempat</th><td>:</td>
                            <td>{{ $attendanceForm->tempat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Instruktur</th><td>:</td>
                            <td>{{ $attendanceForm->instruktur ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Tabel Peserta --}}
                <div class="table-responsive mb-4 print-section">
                    <table class="table table-striped align-middle" style="font-size:0.85rem;">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width:5%">No</th>
                                <th style="width:25%">Nama Karyawan</th>
                                <th style="width:20%">Jabatan</th>
                                <th style="width:20%">Lokasi Kerja</th>
                                {{-- Kolom custom header --}}
                                @foreach($attendanceForm->getCustomColumnLabels() as $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                                <th style="width:10%">Paraf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceForm->participants as $i => $p)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $p->nama_karyawan }}</td>
                                <td>{{ $p->jabatan ?? '-' }}</td>
                                <td>{{ $p->lokasi_kerja ?? '-' }}</td>
                                {{-- Nilai kolom custom --}}
                                @foreach($attendanceForm->getCustomColumnLabels() as $ci => $label)
                                    <td class="text-center">{{ $p->custom_values[$ci] ?? '-' }}</td>
                                @endforeach
                                <td style="height:50px;"></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 4 + count($attendanceForm->getCustomColumnLabels()) + 1 }}"
                                    class="text-center py-4 text-muted">
                                    Belum ada peserta terdaftar
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Ringkasan --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="card-title fw-bold">Ringkasan</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td>Total Peserta</td>
                                        <td><strong>{{ $attendanceForm->participants->count() }} orang</strong></td>
                                    </tr>
                                    @if($attendanceForm->getCustomColumnLabels())
                                    <tr>
                                        <td>Kolom Tambahan</td>
                                        <td>
                                            @foreach($attendanceForm->getCustomColumnLabels() as $label)
                                                <span class="badge bg-primary me-1">{{ $label }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td>Dibuat pada</td>
                                        <td>{{ $attendanceForm->created_at->format('d F Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Terakhir diubah</td>
                                        <td>{{ $attendanceForm->updated_at->format('d F Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    @if($attendanceForm->catatan)
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="card-title fw-bold">Catatan</h6>
                                <p class="mb-0">{{ $attendanceForm->catatan }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .table th, .table td { vertical-align: middle; }
    .card { border-radius: 0.5rem; }
    @media print {
        .no-print, .breadcrumb, nav { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .print-section { page-break-inside: avoid; }
        table { font-size: 12px; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
    }
</style>
@endpush