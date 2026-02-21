@extends('layouts.app')

@section('title', 'Detail Purchase Order - ' . $purchaseOrder->no_po)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.purchase-orders.index') }}">Laporan PO</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">{{ $purchaseOrder->no_po }}</li>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Detail Purchase Order</h4>
                    <p class="text-muted mb-0">{{ $purchaseOrder->no_po }}</p>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="ri-printer-line me-1"></i>Print
                    </button>
                    <a href="{{ route('reports.purchase-orders.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- PO Information --}}
    <div class="row">
        {{-- Left Column --}}
        <div class="col-lg-8">
            {{-- Basic Info Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-file-text-line me-2"></i>Informasi Purchase Order</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">No PO</label>
                            <p class="fw-semibold mb-0">{{ $purchaseOrder->no_po }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Tanggal Permintaan</label>
                            <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($purchaseOrder->tanggal_permintaan)->format('d F Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Tipe PO</label>
                            <p class="mb-0">
                                @if($purchaseOrder->tipe_po == 'internal')
                                    <span class="badge bg-primary">Internal</span>
                                @else
                                    <span class="badge bg-info">Eksternal</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Status</label>
                            <p class="mb-0">
                                @php
                                    $statusConfig = [
                                        'draft' => 'secondary',
                                        'menunggu_persetujuan_kepala_gudang' => 'warning',
                                        'menunggu_persetujuan_kasir' => 'warning',
                                        'disetujui' => 'success',
                                        'dikirim_ke_supplier' => 'info',
                                        'dalam_pengiriman' => 'info',
                                        'diterima' => 'success',
                                        'ditolak' => 'danger',
                                        'dibatalkan' => 'dark'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusConfig[$purchaseOrder->status] ?? 'secondary' }}">
                                    {{ str_replace('_', ' ', ucwords($purchaseOrder->status, '_')) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Unit Pemohon</label>
                            <p class="fw-semibold mb-0">{{ ucfirst($purchaseOrder->unit_pemohon) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Unit Tujuan</label>
                            <p class="fw-semibold mb-0">{{ ucfirst($purchaseOrder->unit_tujuan ?? '-') }}</p>
                        </div>
                        @if($purchaseOrder->catatan_pemohon)
                        <div class="col-12">
                            <label class="text-muted small">Catatan Pemohon</label>
                            <p class="mb-0">{{ $purchaseOrder->catatan_pemohon }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Items Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-shopping-cart-line me-2"></i>Item Purchase Order</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Qty Diminta</th>
                                    <th class="text-center">Qty Disetujui</th>
                                    <th class="text-center">Qty Diterima</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div>
                                            <span class="fw-semibold">{{ $item->nama_produk }}</span>
                                            @if($item->batch_number)
                                                <br><small class="text-muted">Batch: {{ $item->batch_number }}</small>
                                            @endif
                                            @if($item->tanggal_kadaluarsa)
                                                <br><small class="text-muted">Exp: {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d/m/Y') }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary">{{ number_format($item->qty_diminta) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->qty_disetujui)
                                            <span class="badge bg-success-subtle text-success">{{ number_format($item->qty_disetujui) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->qty_diterima > 0)
                                            <span class="badge bg-info-subtle text-info">{{ number_format($item->qty_diterima) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="6" class="text-end fw-semibold">Total Harga:</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($purchaseOrder->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end fw-semibold">Pajak:</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($purchaseOrder->pajak, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="6" class="text-end fw-bold">Grand Total:</td>
                                    <td class="text-end fw-bold text-primary fs-5">Rp {{ number_format($purchaseOrder->grand_total, 0, ',', '.') }}</td>
                                </tr>
                                @if($purchaseOrder->grand_total_diterima)
                                <tr class="table-success">
                                    <td colspan="6" class="text-end fw-semibold">Total Diterima:</td>
                                    <td class="text-end fw-semibold text-success">Rp {{ number_format($purchaseOrder->grand_total_diterima, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Approval History --}}
            @if($purchaseOrder->tipe_po == 'eksternal')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-shield-check-line me-2"></i>Riwayat Persetujuan</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        {{-- Kepala Gudang Approval --}}
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $purchaseOrder->status_approval_kepala_gudang == 'disetujui' ? 'bg-success' : ($purchaseOrder->status_approval_kepala_gudang == 'ditolak' ? 'bg-danger' : 'bg-warning') }}">
                                <i class="ri-user-line"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Persetujuan Kepala Gudang</h6>
                                @if($purchaseOrder->kepalaGudangApproval)
                                    <p class="mb-1">{{ $purchaseOrder->kepalaGudangApproval->nama_karyawan }}</p>
                                    <span class="badge bg-{{ $purchaseOrder->status_approval_kepala_gudang == 'disetujui' ? 'success' : ($purchaseOrder->status_approval_kepala_gudang == 'ditolak' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($purchaseOrder->status_approval_kepala_gudang) }}
                                    </span>
                                    @if($purchaseOrder->tanggal_approval_kepala_gudang)
                                        <p class="text-muted small mt-2 mb-0">
                                            {{ \Carbon\Carbon::parse($purchaseOrder->tanggal_approval_kepala_gudang)->format('d M Y H:i') }}
                                        </p>
                                    @endif
                                    @if($purchaseOrder->catatan_kepala_gudang)
                                        <p class="mt-2 mb-0"><em>{{ $purchaseOrder->catatan_kepala_gudang }}</em></p>
                                    @endif
                                @else
                                    <p class="text-muted mb-0">Menunggu persetujuan</p>
                                @endif
                            </div>
                        </div>

                        {{-- Kasir Approval --}}
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $purchaseOrder->status_approval_kasir == 'disetujui' ? 'bg-success' : ($purchaseOrder->status_approval_kasir == 'ditolak' ? 'bg-danger' : 'bg-warning') }}">
                                <i class="ri-user-line"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Persetujuan Kasir</h6>
                                @if($purchaseOrder->kasirApproval)
                                    <p class="mb-1">{{ $purchaseOrder->kasirApproval->nama_karyawan }}</p>
                                    <span class="badge bg-{{ $purchaseOrder->status_approval_kasir == 'disetujui' ? 'success' : ($purchaseOrder->status_approval_kasir == 'ditolak' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($purchaseOrder->status_approval_kasir) }}
                                    </span>
                                    @if($purchaseOrder->tanggal_approval_kasir)
                                        <p class="text-muted small mt-2 mb-0">
                                            {{ \Carbon\Carbon::parse($purchaseOrder->tanggal_approval_kasir)->format('d M Y H:i') }}
                                        </p>
                                    @endif
                                    @if($purchaseOrder->catatan_kasir)
                                        <p class="mt-2 mb-0"><em>{{ $purchaseOrder->catatan_kasir }}</em></p>
                                    @endif
                                @else
                                    <p class="text-muted mb-0">Menunggu persetujuan</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Pemohon Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-user-line me-2"></i>Informasi Pemohon</h5>
                </div>
                <div class="card-body">
                    @if($purchaseOrder->karyawanPemohon)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-md me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <span class="avatar-title rounded-circle text-white fw-bold fs-4">
                                    {{ substr($purchaseOrder->karyawanPemohon->nama_karyawan, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $purchaseOrder->karyawanPemohon->nama_karyawan }}</h6>
                                <small class="text-muted">{{ ucfirst($purchaseOrder->unit_pemohon) }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Supplier Info (if eksternal) --}}
            @if($purchaseOrder->supplier)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-store-line me-2"></i>Informasi Supplier</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">{{ $purchaseOrder->supplier->nama_supplier }}</h6>
                    @if($purchaseOrder->supplier->alamat)
                        <div class="mb-2">
                            <small class="text-muted d-block">Alamat</small>
                            <span>{{ $purchaseOrder->supplier->alamat }}</span>
                        </div>
                    @endif
                    @if($purchaseOrder->supplier->no_telp)
                        <div class="mb-2">
                            <small class="text-muted d-block">Telepon</small>
                            <span>{{ $purchaseOrder->supplier->no_telp }}</span>
                        </div>
                    @endif
                    @if($purchaseOrder->supplier->email)
                        <div>
                            <small class="text-muted d-block">Email</small>
                            <span>{{ $purchaseOrder->supplier->email }}</span>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Document Info --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-file-line me-2"></i>Dokumen</h5>
                </div>
                <div class="card-body">
                    @if($purchaseOrder->surat_jalan)
                        <div class="mb-3">
                            <small class="text-muted d-block">Surat Jalan</small>
                            <a href="{{ asset('storage/' . $purchaseOrder->surat_jalan) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="ri-file-download-line me-1"></i>Lihat Dokumen
                            </a>
                            @if($purchaseOrder->tanggal_surat_jalan)
                                <small class="d-block mt-1 text-muted">{{ \Carbon\Carbon::parse($purchaseOrder->tanggal_surat_jalan)->format('d/m/Y') }}</small>
                            @endif
                        </div>
                    @endif

                    @if($purchaseOrder->bukti_barang)
                        <div class="mb-3">
                            <small class="text-muted d-block">Bukti Barang</small>
                            <a href="{{ asset('storage/' . $purchaseOrder->bukti_barang) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="ri-file-download-line me-1"></i>Lihat Dokumen
                            </a>
                        </div>
                    @endif

                    @if(!$purchaseOrder->surat_jalan && !$purchaseOrder->bukti_barang)
                        <p class="text-muted mb-0">Tidak ada dokumen</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-md {
        width: 3.5rem;
        height: 3.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -30px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        z-index: 1;
    }

    .timeline-content {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    @media print {
        .btn, .breadcrumb {
            display: none !important;
        }
    }
</style>
@endpush