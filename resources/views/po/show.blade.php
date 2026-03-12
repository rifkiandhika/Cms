@extends('layouts.app')

@section('title', 'Detail Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('po.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item text-primary" aria-current="page">{{ $po->no_po }}</li>
@endsection

@section('content')
<div class="app-body">
    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column - PO Details -->
        <div class="col-xl-8">
            <!-- Header Info Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><i class="ri-file-list-3-line me-2"></i>{{ $po->no_po }}</h5>
                            <small>Dibuat: {{ $po->tanggal_permintaan->format('d F Y, H:i') }}</small>
                        </div>
                        <div>
                            @if($po->tipe_po == 'penjualan')
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i class="ri-arrow-right-line"></i> Penjualan
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    <i class="ri-external-link-line"></i> Pembelian
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Pemohon</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                        <i class="ri-user-line fs-5"></i>
                                    </span>
                                </div>
                                <div>
                                    <strong>{{ $po->karyawanPemohon->nama_lengkap ?? '-' }}</strong>
                                    <br><small class="text-muted">{{ ucfirst($po->unit_pemohon) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Tujuan</label>
                            <div>
                                @if($po->tipe_po === 'penjualan')
                                    <strong class="text-primary">{{ $po->customer->nama_customer ?? '-' }}</strong>
                                    <br><small class="text-muted">Customer</small>
                                @else
                                    <strong class="text-primary">{{ $po->supplier->nama_supplier ?? '-' }}</strong>
                                    <br><small class="text-muted">Supplier</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($po->no_gr)
                    <div class="d-flex gap-2 mb-2">
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            <i class="ri-qr-code-line me-1"></i>GR: {{ $po->no_gr }}
                        </span>
                        @if($po->no_invoice)
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="ri-file-check-line me-1"></i>Inv: {{ $po->no_invoice }}
                        </span>
                        @endif
                    </div>
                    @endif

                    @if($po->catatan_pemohon)
                    <div class="alert alert-info mb-0">
                        <i class="ri-information-line me-2"></i>
                        <strong>Catatan:</strong> {{ $po->catatan_pemohon }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Confirmation Receipt Card for Penjualan --}}
            @if($po->tipe_po === 'penjualan' && $po->status === 'dikirim')
            <div class="card shadow-sm border-0 mb-4 border-warning" style="border-width: 2px !important;">
                <div class="card-header bg-warning py-3">
                    <h5 class="mb-0">
                        <i class="ri-inbox-line me-2"></i>Konfirmasi Penerimaan Barang oleh Customer
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <div class="d-flex align-items-start">
                            <i class="ri-alert-line me-2 fs-4"></i>
                            <div>
                                <strong>Perhatian:</strong> PO ini telah disetujui oleh Gudang. 
                                Silakan konfirmasi setelah customer menerima barang.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <i class="ri-box-3-line me-2 text-primary"></i>
                                <strong>Total Item:</strong> {{ $po->items->count() }} produk
                            </div>
                            <div class="mb-2">
                                <i class="ri-stack-line me-2 text-primary"></i>
                                <strong>Total Quantity:</strong> {{ $po->items->sum('qty_diminta') }} satuan
                            </div>
                            <div class="mb-0">
                                <i class="ri-user-line me-2 text-primary"></i>
                                <strong>Disetujui oleh:</strong> {{ $po->kepalaGudang->nama_lengkap ?? '-' }}
                                @if($po->tanggal_approval_kepala_gudang)
                                    <br><small class="text-muted ms-4">pada {{ $po->tanggal_approval_kepala_gudang->format('d/m/Y H:i') }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('po.confirm-receipt', $po->id_po) }}" class="btn btn-warning btn-lg w-100">
                                <i class="ri-checkbox-multiple-line me-2"></i>
                                Konfirmasi Penerimaan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Status jika sudah selesai (Penjualan) --}}
            @if($po->tipe_po === 'penjualan' && $po->status === 'selesai')
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-start">
                    <i class="ri-check-double-line me-3 fs-3"></i>
                    <div class="flex">
                        <h6 class="alert-heading mb-2"><strong>Penerimaan Dikonfirmasi</strong></h6>
                        <p class="mb-1">
                            Barang telah diterima customer pada 
                            <strong>{{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d/m/Y H:i') : '-' }}</strong>
                        </p>
                        @if($po->penerima)
                            <small class="text-muted d-block mb-2">
                                <i class="ri-user-line me-1"></i>Dikonfirmasi oleh: <strong>{{ $po->penerima->nama_lengkap }}</strong>
                            </small>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- Confirmation Receipt Card for Pembelian --}}
            @if($po->tipe_po === 'pembelian' && $po->status === 'diterima')
            <div class="card shadow-sm border-0 mb-4 border-success" style="border-width: 2px !important;">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0">
                        <i class="ri-inbox-line me-2"></i>Konfirmasi Penerimaan Barang dari Supplier
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success mb-3">
                        <div class="d-flex align-items-start">
                            <i class="ri-truck-line me-2 fs-4"></i>
                            <div>
                                <strong>Barang Sudah Tiba!</strong> Barang dari supplier <strong>{{ $po->supplier->nama_supplier ?? '-' }}</strong> telah sampai. 
                                Silakan lakukan konfirmasi penerimaan untuk menambahkan stok ke gudang.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <i class="ri-store-line me-2 text-success"></i>
                                <strong>Supplier:</strong> {{ $po->supplier->nama_supplier ?? '-' }}
                            </div>
                            <div class="mb-2">
                                <i class="ri-box-3-line me-2 text-success"></i>
                                <strong>Total Item:</strong> {{ $po->items->count() }} produk
                            </div>
                            <div class="mb-2">
                                <i class="ri-stack-line me-2 text-success"></i>
                                <strong>Total Quantity:</strong> {{ $po->items->sum('qty_diminta') }} satuan
                            </div>
                            @if($po->tanggal_diterima)
                            <div class="mb-0">
                                <i class="ri-calendar-check-line me-2 text-success"></i>
                                <strong>Tiba pada:</strong> {{ $po->tanggal_diterima->format('d/m/Y H:i') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('po.receive-form', $po->id_po) }}" class="btn btn-success btn-lg w-100">
                                <i class="ri-checkbox-multiple-line me-2"></i>
                                Konfirmasi Penerimaan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Status jika sudah selesai (Pembelian) --}}
            @if($po->tipe_po === 'pembelian' && $po->status === 'selesai')
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-start">
                    <i class="ri-check-double-line me-3 fs-3"></i>
                    <div class="flex">
                        <h6 class="alert-heading mb-2"><strong>Penerimaan Dikonfirmasi</strong></h6>
                        <p class="mb-1">
                            Barang dari supplier <strong>{{ $po->supplier->nama_supplier ?? '-' }}</strong> telah diterima pada 
                            <strong>{{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d/m/Y H:i') : '-' }}</strong>
                        </p>
                        <p class="mb-1">
                            <small class="text-success">Total stok masuk: <strong>{{ $po->items->sum('qty_diterima_satuan_dasar') }} PCS</strong></small>
                        </p>
                        @if($po->penerima)
                            <small class="text-muted d-block">
                                <i class="ri-user-line me-1"></i>Dikonfirmasi oleh: <strong>{{ $po->penerima->nama_lengkap }}</strong>
                            </small>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- Invoice Input Card --}}
            @if($po->needsInvoice())
            <div class="card shadow-sm border-0 mb-4 border-info" style="border-width: 2px !important;">
                <div class="card-header bg-info text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ri-file-text-line me-2"></i>Input Invoice/Faktur Supplier
                    </h5>
                    <button type="button" class="btn btn-light btn-sm" onclick="showInvoiceProofModal()">
                        <i class="ri-image-line me-1"></i> 
                        {{ $po->hasInvoiceProof() ? 'Lihat Bukti' : 'Upload Bukti' }} 
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <div class="d-flex align-items-start">
                            <i class="ri-information-line me-2 fs-4"></i>
                            <div>
                                <strong>Tukar Faktur:</strong> Barang sudah diterima (GR: <strong>{{ $po->no_gr }}</strong>).
                                Silakan input nomor invoice/faktur dari supplier untuk melanjutkan ke proses pembayaran.
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <i class="ri-checkbox-circle-line me-2 text-success"></i>
                                <strong>No. GR:</strong> <span class="badge bg-primary">{{ $po->no_gr }}</span>
                            </div>
                            <div class="mb-2">
                                <i class="ri-calendar-check-line me-2 text-info"></i>
                                <strong>Tanggal Diterima:</strong> {{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d/m/Y H:i') : '-' }}
                            </div>
                            <div class="mb-0">
                                <i class="ri-money-dollar-circle-line me-2 text-info"></i>
                                <strong>Grand Total:</strong> Rp {{ number_format($po->grand_total, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('po.invoice-form', $po->id_po) }}" class="btn btn-info btn-lg w-100">
                                <i class="ri-file-add-line me-2"></i>
                                Input Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Invoice Display Card --}}
            @if($po->hasInvoice())
                <div class="card shadow-sm border-0 mb-4 border-success" style="border-width: 2px !important;">
                    <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="ri-file-check-line me-2"></i>Invoice/Faktur Supplier
                        </h5>
                        @if(!$po->needsInvoice())
                            @if(!$po->hasInvoiceProof())
                                <button type="button" class="btn btn-warning btn-sm" onclick="showInvoiceProofModal()">
                                    <i class="ri-upload-2-line me-1"></i>Upload Bukti
                                </button>
                            @else
                                <button type="button" class="btn btn-light btn-sm" onclick="showInvoiceProofModal()">
                                    <i class="ri-image-line me-1"></i>Lihat Bukti
                                </button>
                            @endif
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="150"><strong>No. Invoice</strong></td>
                                        <td>: <span class="badge bg-success">{{ $po->no_invoice }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Invoice</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($po->tanggal_invoice)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jatuh Tempo</strong></td>
                                        <td>: 
                                            @php
                                                $dueDate = \Carbon\Carbon::parse($po->tanggal_jatuh_tempo);
                                                $today = \Carbon\Carbon::today();
                                                $daysLeft = $today->diffInDays($dueDate, false);
                                            @endphp
                                            <strong class="{{ $daysLeft < 0 ? 'text-danger' : ($daysLeft <= 3 ? 'text-warning' : 'text-success') }}">
                                                {{ $dueDate->format('d/m/Y') }}
                                                @if($daysLeft < 0)
                                                    (Terlambat {{ abs($daysLeft) }} hari)
                                                @elseif($daysLeft == 0)
                                                    (Hari ini!)
                                                @else
                                                    ({{ $daysLeft }} hari lagi)
                                                @endif
                                            </strong>
                                        </td>
                                    </tr>
                                    @if($po->nomor_faktur_pajak)
                                    <tr>
                                        <td><strong>Faktur Pajak</strong></td>
                                        <td>: {{ $po->nomor_faktur_pajak }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="150"><strong>No. GR</strong></td>
                                        <td>: <span class="badge bg-primary">{{ $po->no_gr }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Grand Total</strong></td>
                                        <td>: <strong class="text-success">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Diinput oleh</strong></td>
                                        <td>: {{ $po->karyawanInputInvoice->nama_lengkap ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Input</strong></td>
                                        <td>: {{ $po->tanggal_input_invoice ? \Carbon\Carbon::parse($po->tanggal_input_invoice)->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        @if($daysLeft <= 3 && $daysLeft >= 0)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="ri-time-line me-2"></i>
                            <strong>Perhatian:</strong> Invoice akan jatuh tempo dalam {{ $daysLeft }} hari.
                        </div>
                        @elseif($daysLeft < 0)
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="ri-error-warning-line me-2"></i>
                            <strong>Terlambat!</strong> Invoice sudah melewati jatuh tempo {{ abs($daysLeft) }} hari.
                        </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Items Table with Batch Details --}}
            @if(in_array($po->status, ['diterima', 'selesai']) && $po->items->first() && $po->items->first()->batches->count() > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-shopping-cart-line me-2"></i>Item PO & Batch Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Produk</th>
                                    <th width="100">Satuan</th>
                                    <th width="80" class="text-center">Konversi</th>
                                    <th width="90" class="text-center">Qty Diminta</th>
                                    <th width="90" class="text-center">Qty Diterima</th>
                                    <th>Batch Details</th>
                                    <th width="120" class="text-end">Harga</th>
                                    <th width="150" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($po->items as $index => $item)
                                <tr>
                                    <td class="text-center" rowspan="{{ $item->batches->count() > 0 ? $item->batches->count() + 1 : 1 }}">{{ $index + 1 }}</td>
                                    <td rowspan="{{ $item->batches->count() > 0 ? $item->batches->count() + 1 : 1 }}">
                                        <strong>{{ $item->nama_produk }}</strong>
                                        @if($item->kode_produk)<br><small class="text-muted">{{ $item->kode_produk }}</small>@endif
                                    </td>
                                    <td rowspan="{{ $item->batches->count() > 0 ? $item->batches->count() + 1 : 1 }}">
                                        @if($item->produkSatuan)
                                            <span class="badge bg-secondary">{{ $item->produkSatuan->satuan->nama_satuan ?? 'PCS' }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center" rowspan="{{ $item->batches->count() > 0 ? $item->batches->count() + 1 : 1 }}">
                                        <small class="text-muted">× {{ $item->konversi_snapshot }}</small>
                                    </td>
                                    <td class="text-center" rowspan="{{ $item->batches->count() > 0 ? $item->batches->count() + 1 : 1 }}">
                                        <span class="badge bg-secondary">{{ $item->qty_diminta }}</span>
                                        @if($item->qty_diminta_satuan_dasar)<br><small class="text-muted">{{ $item->qty_diminta_satuan_dasar }} PCS</small>@endif
                                    </td>
                                    <td class="text-center" rowspan="{{ $item->batches->count() > 0 ? $item->batches->count() + 1 : 1 }}">
                                        <span class="badge bg-success">{{ $item->qty_diterima }}</span>
                                        @if($item->qty_diterima_satuan_dasar)<br><small class="text-muted">{{ $item->qty_diterima_satuan_dasar }} PCS</small>@endif
                                    </td>
                                    <td colspan="3" class="bg-light"><small class="text-muted"><strong>Detail Batch:</strong></small></td>
                                </tr>
                                @foreach($item->batches as $batch)
                                <tr>
                                    <td>
                                        <small>
                                            <i class="ri-stack-line me-1"></i>
                                            <strong>{{ $batch->batch_number }}</strong><br>
                                            <span class="text-muted">Exp: {{ $batch->tanggal_kadaluarsa ? \Carbon\Carbon::parse($batch->tanggal_kadaluarsa)->format('d/m/Y') : '-' }}</span><br>
                                            <span class="badge bg-{{ $batch->kondisi === 'baik' ? 'success' : ($batch->kondisi === 'rusak' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($batch->kondisi) }}</span>
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <small>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small>
                                        <br><small class="text-muted">× {{ $batch->qty_diterima }}</small>
                                    </td>
                                    <td class="text-end">
                                        <small>Rp {{ number_format($item->harga_satuan * $batch->qty_diterima, 0, ',', '.') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                @php
                                    $pajak = ($po->pajak_diterima ?? 0) > 0 ? $po->pajak_diterima : $po->pajak;
                                    $grand = ($po->grand_total_diterima ?? 0) > 0 ? $po->grand_total_diterima : $po->grand_total;
                                    $subTotal = ($po->total_diterima ?? 0) > 0 ? $po->total_diterima : $po->total_harga;
                                @endphp
                                <tr>
                                    <th colspan="8" class="text-end">Subtotal:</th>
                                    <th class="text-end">Rp {{ number_format($subTotal, 0, ',', '.') }}</th>
                                </tr>
                                @if($pajak > 0)
                                <tr>
                                    <th colspan="8" class="text-end">Pajak:</th>
                                    <th class="text-end">Rp {{ number_format($pajak, 0, ',', '.') }}</th>
                                </tr>
                                @endif
                                <tr class="table-primary">
                                    <th colspan="8" class="text-end">Grand Total:</th>
                                    <th class="text-end">Rp {{ number_format($grand, 0, ',', '.') }}</th>
                                </tr>
                                @if($po->status === 'selesai' && $po->tipe_po === 'pembelian')
                                <tr class="table-success">
                                    <th colspan="8" class="text-end text-success">Total Stok Masuk:</th>
                                    <th class="text-end text-success">{{ $po->items->sum('qty_diterima_satuan_dasar') }} PCS</th>
                                </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Items Table Utama -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-shopping-cart-line me-2"></i>Item Purchase Order</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Produk</th>
                                    <th width="100">Satuan</th>
                                    <th width="80" class="text-center">Konversi</th>
                                    <th width="90" class="text-center">Qty Diminta</th>
                                    <th width="90" class="text-center">Qty Disetujui</th>
                                    <th width="90" class="text-center">Qty Diterima</th>
                                    @if($po->tipe_po !== 'penjualan')
                                    <th width="120" class="text-end">Harga</th>
                                    <th width="150" class="text-end">Subtotal</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($po->items as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->nama_produk }}</strong>
                                        @if($item->kode_produk)<br><small class="text-muted">Kode: {{ $item->kode_produk }}</small>@endif
                                    </td>
                                    <td>
                                        @if($item->produkSatuan)
                                            <span class="badge bg-secondary">{{ $item->produkSatuan->satuan->nama_satuan ?? 'PCS' }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center"><small class="text-muted">× {{ $item->konversi_snapshot }}</small></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $item->qty_diminta }}</span>
                                        @if($item->qty_diminta_satuan_dasar)<br><small class="text-muted">{{ $item->qty_diminta_satuan_dasar }} PCS</small>@endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->qty_disetujui)
                                            <span class="badge bg-primary">{{ $item->qty_disetujui }}</span>
                                            @if($item->qty_disetujui_satuan_dasar)<br><small class="text-muted">{{ $item->qty_disetujui_satuan_dasar }} PCS</small>@endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->qty_diterima)
                                            <span class="badge bg-success">{{ $item->qty_diterima }}</span>
                                            @if($item->qty_diterima_satuan_dasar)<br><small class="text-muted">{{ $item->qty_diterima_satuan_dasar }} PCS</small>@endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @if($po->tipe_po !== 'penjualan')
                                        <td class="text-end"><small>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small></td>
                                        <td class="text-end"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                @if($po->tipe_po !== 'penjualan')
                                    @php
                                        $pajak = ($po->pajak_diterima ?? 0) > 0 ? $po->pajak_diterima : $po->pajak;
                                        $grand = ($po->grand_total_diterima ?? 0) > 0 ? $po->grand_total_diterima : $po->grand_total;
                                        $subTotal = ($po->total_diterima ?? 0) > 0 ? $po->total_diterima : $po->total_harga;
                                    @endphp
                                    <tr>
                                        <th colspan="8" class="text-end">Subtotal:</th>
                                        <th class="text-end">Rp {{ number_format($subTotal, 0, ',', '.') }}</th>
                                    </tr>
                                    @if($pajak > 0)
                                    <tr>
                                        <th colspan="8" class="text-end">Pajak:</th>
                                        <th class="text-end">Rp {{ number_format($pajak, 0, ',', '.') }}</th>
                                    </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <th colspan="8" class="text-end">Grand Total:</th>
                                        <th class="text-end">Rp {{ number_format($grand, 0, ',', '.') }}</th>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Audit Trail -->
            @if($po->auditTrails && $po->auditTrails->count() > 0)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-history-line me-2"></i>Audit Trail</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="180">Waktu</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($po->auditTrails->sortByDesc('tanggal_aksi') as $audit)
                                <tr>
                                    <td><small>{{ $audit->tanggal_aksi->format('d/m/Y H:i:s') }}</small></td>
                                    <td><small>{{ $audit->karyawan->nama_lengkap ?? '-' }}</small></td>
                                    <td><span class="badge bg-secondary">{{ str_replace('_', ' ', $audit->aksi) }}</span></td>
                                    <td><small>{{ $audit->deskripsi_aksi }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Status & Actions -->
        <div class="col-xl-4">
            <!-- Status Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-status-line me-2"></i>Status PO</h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $statusConfig = [
                            'draft' => ['color' => 'secondary', 'icon' => 'ri-draft-line', 'label' => 'Draft'],
                            'menunggu_persetujuan_kepala_gudang' => [
                                'color' => 'warning', 
                                'icon' => 'ri-time-line', 
                                'label' => $po->tipe_po === 'penjualan' ? 'Menunggu Gudang' : 'Menunggu Kepala Gudang'
                            ],
                            'menunggu_persetujuan_kasir' => ['color' => 'warning', 'icon' => 'ri-time-line', 'label' => 'Menunggu Kasir'],
                            'dikirim' => ['color' => 'info', 'icon' => 'ri-truck-line', 'label' => 'Dikirim'],
                            'selesai' => ['color' => 'success', 'icon' => 'ri-checkbox-circle-line', 'label' => 'Selesai'],
                            'disetujui' => ['color' => 'success', 'icon' => 'ri-checkbox-circle-line', 'label' => 'Disetujui'],
                            'dikirim_ke_supplier' => ['color' => 'info', 'icon' => 'ri-truck-line', 'label' => 'Dikirim ke Supplier'],
                            'diterima' => ['color' => 'success', 'icon' => 'ri-checkbox-circle-fill', 'label' => 'Diterima'],
                            'ditolak' => ['color' => 'danger', 'icon' => 'ri-close-circle-line', 'label' => 'Ditolak'],
                        ];
                        $currentStatus = $statusConfig[$po->status] ?? ['color' => 'secondary', 'icon' => 'ri-question-line', 'label' => $po->status];
                    @endphp
                    <div class="mb-3">
                        <i class="{{ $currentStatus['icon'] }} text-{{ $currentStatus['color'] }}" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-{{ $currentStatus['color'] }}">{{ $currentStatus['label'] }}</h4>
                    
                    @if($po->status === 'selesai' && $po->tipe_po === 'pembelian')
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted">Total Stok Masuk</small>
                        <h5 class="text-success mb-0">{{ $po->items->sum('qty_diterima_satuan_dasar') }} PCS</h5>
                    </div>
                    @endif

                    {{-- Tampilkan data suhu jika sudah diisi --}}
                    @if($po->suhu_barang_dikirim || $po->suhu_barang_datang)
                    <div class="mt-3 p-3 bg-light rounded text-start">
                        <small class="text-muted d-block mb-2"><i class="ri-temp-cold-line me-1"></i><strong>Data Suhu</strong></small>
                        @if($po->suhu_barang_dikirim)
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Saat Dikirim</small>
                            <small><strong>{{ $po->suhu_barang_dikirim }} °C</strong></small>
                        </div>
                        @endif
                        @if($po->suhu_barang_datang)
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Saat Datang</small>
                            <small><strong>{{ $po->suhu_barang_datang }} °C</strong></small>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Approval Info -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-shield-check-line me-2"></i>Persetujuan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small mb-2">
                            {{ $po->tipe_po === 'penjualan' ? 'Gudang' : 'Kepala Gudang' }}
                        </label>
                        @if($po->kepalaGudang)
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-user-line me-2 text-primary"></i>
                                <strong>{{ $po->kepalaGudang->nama_lengkap }}</strong>
                            </div>
                            <span class="badge bg-{{ $po->status_approval_kepala_gudang === 'disetujui' ? 'success' : ($po->status_approval_kepala_gudang === 'ditolak' ? 'danger' : 'warning text-dark') }}">
                                {{ ucfirst($po->status_approval_kepala_gudang ?? 'pending') }}
                            </span>
                            @if($po->tanggal_approval_kepala_gudang)
                                <br><small class="text-muted">{{ $po->tanggal_approval_kepala_gudang->format('d/m/Y H:i') }}</small>
                            @endif
                            @if($po->catatan_kepala_gudang)
                                <div class="alert alert-light mt-2 mb-0"><small>{{ $po->catatan_kepala_gudang }}</small></div>
                            @endif
                        @else
                            <span class="text-muted">Belum ada approval</span>
                        @endif
                    </div>
                    <div>
                        <label class="text-muted small mb-2">Kasir</label>
                        @if($po->kasir)
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-user-line me-2 text-primary"></i>
                                <strong>{{ $po->kasir->nama_lengkap }}</strong>
                            </div>
                            <span class="badge bg-{{ $po->status_approval_kasir === 'disetujui' ? 'success' : ($po->status_approval_kasir === 'ditolak' ? 'danger' : 'warning text-dark') }}">
                                {{ ucfirst($po->status_approval_kasir ?? 'pending') }}
                            </span>
                            @if($po->tanggal_approval_kasir)
                                <br><small class="text-muted">{{ $po->tanggal_approval_kasir->format('d/m/Y H:i') }}</small>
                            @endif
                            @if($po->catatan_kasir)
                                <div class="alert alert-light mt-2 mb-0"><small>{{ $po->catatan_kasir }}</small></div>
                            @endif
                        @else
                            <span class="text-muted">Belum ada approval</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="ri-tools-line me-2"></i>Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('po.index') }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>

                        @if(in_array($po->status, ['draft', 'ditolak']))
                            <a href="{{ route('po.edit', $po->id_po) }}" class="btn btn-outline-info">
                                <i class="ri-pencil-line me-1"></i> Edit PO
                            </a>
                        @endif

                        @if(in_array($po->status, ['draft', 'ditolak']))
                            <button class="btn btn-primary" onclick="submitPO('{{ $po->id_po }}')">
                                <i class="ri-send-plane-fill me-1"></i> Submit PO
                            </button>
                        @endif

                        @if($po->status === 'menunggu_persetujuan_kepala_gudang' && auth()->user()->hasAnyRole(['kepala_gudang', 'Superadmin']))
                            <button class="btn btn-success" onclick="showApprovalModal('kepala_gudang', 'disetujui')">
                                <i class="ri-checkbox-circle-line me-1"></i> Setujui
                            </button>
                            <button class="btn btn-danger" onclick="showApprovalModal('kepala_gudang', 'ditolak')">
                                <i class="ri-close-circle-line me-1"></i> Tolak
                            </button>
                        @endif

                        @if($po->status === 'menunggu_persetujuan_kasir' && auth()->user()->hasAnyRole(['kasir', 'Superadmin']))
                            <button class="btn btn-success" onclick="showApprovalModal('kasir', 'disetujui')">
                                <i class="ri-checkbox-circle-line me-1"></i> Setujui
                            </button>
                            <button class="btn btn-danger" onclick="showApprovalModal('kasir', 'ditolak')">
                                <i class="ri-close-circle-line me-1"></i> Tolak
                            </button>
                        @endif

                        @if(in_array($po->status, ['disetujui']) && $po->tipe_po === 'pembelian')
                            <a href="{{ route('po.showex-confirmation', $po->id_po) }}" class="btn btn-success">
                                <i class="ri-inbox-archive-line me-1"></i> Konfirmasi Penerimaan
                            </a>
                        @endif

                        {{-- ============================================================ --}}
                        {{-- TOMBOL PRINT — sekarang membuka modal, bukan langsung print  --}}
                        {{-- ============================================================ --}}
                        <button type="button" class="btn btn-outline-primary" onclick="showPrintModal()">
                            <i class="ri-printer-line me-1"></i>
                            {{ $po->status === 'selesai' ? 'Print GR' : 'Print PO' }}
                        </button>

                        @if(in_array($po->status, ['draft', 'ditolak']))
                            <button class="btn btn-outline-danger" onclick="deletePO('{{ $po->id_po }}')">
                                <i class="ri-delete-bin-line me-1"></i> Hapus PO
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================ --}}
{{-- MODAL PRINT PO — input suhu + toggle harga sebelum cetak         --}}
{{-- ================================================================ --}}
<div class="modal fade" id="printModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title">
                    <i class="ri-printer-line me-2"></i>Pengaturan Cetak PO
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-4">

                {{-- Toggle Tampilkan Harga --}}
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-4">
                    <div>
                        <div class="fw-semibold"><i class="ri-price-tag-3-line me-2 text-primary"></i>Tampilkan Harga</div>
                        <small class="text-muted">Harga satuan & total akan dicetak</small>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="toggleTampilHarga" checked style="width:2.5rem; height:1.3rem; cursor:pointer;">
                    </div>
                </div>

                {{-- Input Suhu --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="ri-temp-cold-line me-1 text-info"></i>Suhu Barang Dikirim (°C)
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="suhuDikirim" 
                               placeholder="Contoh: 4.5" step="0.1"
                               value="{{ $po->suhu_barang_dikirim ?? '' }}">
                        <span class="input-group-text">°C</span>
                    </div>
                </div>

                <div class="mb-1">
                    <label class="form-label fw-semibold">
                        <i class="ri-temp-cold-line me-1 text-info"></i>Suhu Barang Datang (°C)
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="suhuDatang" 
                               placeholder="Contoh: 6.0" step="0.1"
                               value="{{ $po->suhu_barang_datang ?? '' }}">
                        <span class="input-group-text">°C</span>
                    </div>
                </div>

                <small class="text-muted"><i class="ri-information-line me-1"></i>Data suhu akan disimpan ke database PO ini.</small>

                <div id="printSavingIndicator" class="alert alert-info mt-3 mb-0 d-none">
                    <span class="spinner-border spinner-border-sm me-2"></span>Menyimpan data suhu...
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnCetakSekarang" onclick="saveSuhuAndPrint()">
                    <i class="ri-printer-line me-1"></i>Cetak Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Universal PIN Modal --}}
<div class="modal fade" id="pinModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="pinModalTitle">
                    <i class="ri-lock-password-line me-2"></i>Masukkan PIN
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4 py-4">
                <p class="text-muted mb-4" id="pinModalDescription">Masukkan PIN 6 digit untuk melanjutkan</p>
                <div class="otp-container d-flex justify-content-center gap-2 mb-4">
                    @for($i = 0; $i < 6; $i++)
                    <input type="password" class="otp-input form-control text-center" maxlength="1" pattern="\d" inputmode="numeric" data-index="{{ $i }}">
                    @endfor
                </div>
                <div id="notesContainer" class="mb-3" style="display: none;">
                    <label class="form-label text-start w-100">Catatan (opsional)</label>
                    <textarea class="form-control" id="modalNotes" rows="3" placeholder="Tambahkan catatan..."></textarea>
                </div>
                <input type="hidden" id="modalAction">
                <input type="hidden" id="modalPoId">
                <input type="hidden" id="modalRole">
                <input type="hidden" id="modalStatus">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmPinBtn" disabled>Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .timeline { position: relative; padding-left: 30px; }
    .timeline-item { position: relative; }
    .timeline-marker { position: absolute; left: -30px; top: 5px; width: 12px; height: 12px; border-radius: 50%; background: #0d6efd; }
    .timeline-item::before { content: ''; position: absolute; left: -24px; top: 17px; bottom: -17px; width: 2px; background: #dee2e6; }
    .timeline-item:last-child::before { display: none; }
    .timeline-content { background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 3px solid #0d6efd; }
    
    .avatar-sm { width: 3rem; height: 3rem; display: flex; align-items: center; justify-content: center; }
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
    
    .otp-input { width: 50px; height: 60px; font-size: 24px; font-weight: bold; border: 2px solid #dee2e6; border-radius: 10px; transition: all 0.3s ease; }
    .otp-input:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); outline: none; transform: scale(1.05); }
    .otp-input.filled { background-color: #f8f9fa; border-color: #198754; }
    .otp-input.error { border-color: #dc3545; animation: shake 0.5s; }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ================================================================
    // PRINT MODAL
    // ================================================================
    function showPrintModal() {
        const modal = new bootstrap.Modal(document.getElementById('printModal'));
        modal.show();
    }

    function saveSuhuAndPrint() {
        const suhuDikirim = document.getElementById('suhuDikirim').value;
        const suhuDatang  = document.getElementById('suhuDatang').value;
        const tampilHarga = document.getElementById('toggleTampilHarga').checked;
        const btn         = document.getElementById('btnCetakSekarang');
        const indicator   = document.getElementById('printSavingIndicator');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        indicator.classList.remove('d-none');

        // Simpan suhu ke database via AJAX
        fetch('{{ route("po.save-suhu", $po->id_po) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                suhu_barang_dikirim: suhuDikirim !== '' ? suhuDikirim : null,
                suhu_barang_datang:  suhuDatang  !== '' ? suhuDatang  : null,
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Tutup modal lalu buka halaman print dengan parameter hide_price
                bootstrap.Modal.getInstance(document.getElementById('printModal')).hide();
                const printUrl = '{{ route("po.print", $po->id_po) }}' 
                    + (tampilHarga ? '' : '?hide_price=1');
                window.open(printUrl, '_blank');
            } else {
                Swal.fire('Gagal', data.message || 'Gagal menyimpan data suhu', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-printer-line me-1"></i>Cetak Sekarang';
            indicator.classList.add('d-none');
        });
    }

    // ================================================================
    // OTP PIN Input Handler
    // ================================================================
    document.addEventListener('DOMContentLoaded', function() {
        const otpInputs = document.querySelectorAll('.otp-input');
        
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                if (!/^\d$/.test(value)) { e.target.value = ''; return; }
                e.target.classList.add('filled');
                if (value && index < otpInputs.length - 1) otpInputs[index + 1].focus();
                checkPinComplete();
            });
            
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    if (!e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                        otpInputs[index - 1].value = '';
                        otpInputs[index - 1].classList.remove('filled');
                    } else {
                        e.target.value = '';
                        e.target.classList.remove('filled');
                    }
                } else if (e.key === 'ArrowLeft' && index > 0) {
                    e.preventDefault(); otpInputs[index - 1].focus();
                } else if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                    e.preventDefault(); otpInputs[index + 1].focus();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const confirmBtn = document.getElementById('confirmPinBtn');
                    if (!confirmBtn.disabled) confirmBtn.click();
                }
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').trim();
                if (/^\d{6}$/.test(pastedData)) {
                    pastedData.split('').forEach((char, i) => {
                        if (otpInputs[i]) { otpInputs[i].value = char; otpInputs[i].classList.add('filled'); }
                    });
                    otpInputs[5].focus();
                    checkPinComplete();
                }
            });
            
            input.addEventListener('focus', function() { this.select(); });
        });
        
        function checkPinComplete() {
            const allFilled = Array.from(otpInputs).every(input => input.value !== '');
            document.getElementById('confirmPinBtn').disabled = !allFilled;
        }
    });

    function getPinValue() {
        return Array.from(document.querySelectorAll('.otp-input')).map(i => i.value).join('');
    }

    function resetPinInputs() {
        const otpInputs = document.querySelectorAll('.otp-input');
        otpInputs.forEach(input => { input.value = ''; input.classList.remove('filled', 'error'); });
        document.getElementById('confirmPinBtn').disabled = true;
        setTimeout(() => { if (otpInputs[0]) otpInputs[0].focus(); }, 100);
    }

    function showPinError() {
        const otpInputs = document.querySelectorAll('.otp-input');
        otpInputs.forEach(input => input.classList.add('error'));
        setTimeout(() => otpInputs.forEach(input => input.classList.remove('error')), 500);
    }

    function showApprovalModal(role, status) {
        const modal = new bootstrap.Modal(document.getElementById('pinModal'));
        const poType = '{{ $po->tipe_po }}';
        const roleText = role === 'kepala_gudang' ? (poType === 'penjualan' ? 'Gudang' : 'Kepala Gudang') : 'Kasir';
        const actionText = status === 'disetujui' ? 'Setujui' : 'Tolak';
        
        document.getElementById('modalAction').value = 'approval';
        document.getElementById('modalRole').value = role;
        document.getElementById('modalStatus').value = status;
        document.getElementById('modalPoId').value = '{{ $po->id_po }}';
        document.getElementById('pinModalTitle').innerHTML = `<i class="ri-lock-password-line me-2"></i>${actionText} PO - ${roleText}`;
        document.getElementById('pinModalDescription').textContent = `Masukkan PIN untuk ${actionText.toLowerCase()} PO sebagai ${roleText}`;
        
        document.getElementById('notesContainer').style.display = 'block';
        document.getElementById('modalNotes').value = '';
        document.getElementById('modalNotes').placeholder = status === 'disetujui' ? 'Catatan persetujuan (opsional)' : 'Alasan penolakan (opsional)';
        
        const confirmBtn = document.getElementById('confirmPinBtn');
        confirmBtn.className = status === 'disetujui' ? 'btn btn-success' : 'btn btn-danger';
        confirmBtn.innerHTML = status === 'disetujui' ? '<i class="ri-check-line me-1"></i> Setujui' : '<i class="ri-close-line me-1"></i> Tolak';
        
        resetPinInputs();
        modal.show();
    }

    function submitPO(poId) {
        Swal.fire({
            title: 'Submit Purchase Order?',
            text: 'PO akan dikirim untuk persetujuan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Submit',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/po/${poId}/submit`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.message) {
                        Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.error || 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    }

    function deletePO(poId) {
        Swal.fire({
            title: 'Hapus Purchase Order?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = new bootstrap.Modal(document.getElementById('pinModal'));
                document.getElementById('modalAction').value = 'delete';
                document.getElementById('modalPoId').value = poId;
                document.getElementById('pinModalTitle').innerHTML = '<i class="ri-lock-password-line me-2"></i>Konfirmasi Penghapusan';
                document.getElementById('pinModalDescription').textContent = 'Masukkan PIN untuk menghapus PO ini';
                document.getElementById('notesContainer').style.display = 'none';
                document.getElementById('confirmPinBtn').className = 'btn btn-danger';
                document.getElementById('confirmPinBtn').innerHTML = '<i class="ri-delete-bin-line me-1"></i> Hapus';
                resetPinInputs();
                modal.show();
            }
        });
    }

    document.getElementById('confirmPinBtn').addEventListener('click', function() {
        const pin = getPinValue();
        const action = document.getElementById('modalAction').value;
        const poId = document.getElementById('modalPoId').value;
        const role = document.getElementById('modalRole').value;
        const status = document.getElementById('modalStatus').value;
        const notes = document.getElementById('modalNotes').value;
        const btn = this;
        
        if (pin.length !== 6) { showPinError(); Swal.fire('PIN Tidak Lengkap', 'Silakan masukkan 6 digit PIN', 'error'); return; }
        
        btn.disabled = true;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        
        if (action === 'approval') {
            handleApproval(poId, role, status, notes, pin, btn, originalHTML);
        } else if (action === 'delete') {
            handleDelete(poId, pin, btn, originalHTML);
        }
    });

    function handleApproval(poId, role, status, notes, pin, btn, originalHTML) {
        const endpoint = role === 'kepala_gudang' ? `/po/${poId}/approve-kepala-gudang` : `/po/${poId}/approve-kasir`;
        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ pin, status_approval: status, catatan: notes })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false; btn.innerHTML = originalHTML;
            if (data.message) {
                bootstrap.Modal.getInstance(document.getElementById('pinModal')).hide();
                Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
            } else {
                showPinError();
                Swal.fire('Gagal!', data.error || 'Terjadi kesalahan', 'error');
                resetPinInputs();
            }
        });
    }

    function handleDelete(poId, pin, btn, originalHTML) {
        fetch(`/po/${poId}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ pin })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false; btn.innerHTML = originalHTML;
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('pinModal')).hide();
                Swal.fire('Berhasil!', data.message, 'success').then(() => { window.location.href = '{{ route("po.index") }}'; });
            } else {
                showPinError();
                Swal.fire('Gagal!', data.message || 'Terjadi kesalahan', 'error');
                resetPinInputs();
            }
        });
    }

    document.getElementById('pinModal').addEventListener('hidden.bs.modal', function() {
        resetPinInputs();
        document.getElementById('modalNotes').value = '';
    });

    document.getElementById('pinModal').addEventListener('shown.bs.modal', function() {
        const firstInput = document.querySelector('.otp-input');
        if (firstInput) firstInput.focus();
    });
</script>

@include('po._script_invoice_proof')
@endpush