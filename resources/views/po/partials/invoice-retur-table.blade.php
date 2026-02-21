<div>
    <table class="table table-hover table-striped align-middle" id="{{ $tableId }}">
        <thead class="table-light">
            <tr>
                <th width="50">No</th>
                <th>No GR/Invoice</th>
                <th>No PO</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th width="120">Status PO</th>
                <th width="150">Total</th>
                <th width="150">Info Retur</th>
                <th width="120">Total Retur</th>
                <th width="100" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseOrders as $po)
                @php
                    $returs = $po->returs; // Relasi ke tabel returs
                    $totalRetur = $returs->sum('total_nilai_retur');
                    $totalInvoice = $po->grand_total_diterima ?? $po->grand_total;
                    $sisaTagihan = $totalInvoice - $totalRetur;
                    
                    // Tentukan stage berdasarkan status PO
                    $stage = 'PO';
                    if ($po->no_invoice && $po->isInvoiceComplete()) {
                        $stage = 'Invoice';
                    } elseif ($po->no_gr) {
                        $stage = 'GR';
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        @if($po->no_invoice)
                            <strong class="text-warning">{{ $po->no_invoice }}</strong>
                        @elseif($po->no_gr)
                            <strong class="text-info">{{ $po->no_gr }}</strong>
                        @else
                            <strong class="text-primary">{{ $po->no_po }}</strong>
                        @endif
                        <br>
                        <span class="badge badge-sm bg-warning text-dark">
                            <i class="ri-refund-2-line"></i> Retur ({{ $stage }})
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            {{ $po->no_po }}
                        </small>
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="ri-calendar-line"></i> 
                            {{ $po->tanggal_permintaan->format('d/m/Y') }}
                        </small>
                    </td>
                    <td>
                        @if($po->supplier)
                            <span class="badge bg-secondary">{{ $po->supplier->nama_supplier }}</span>
                        @endif
                    </td>
                    <td>
                        @if($po->isInvoiceComplete())
                            <span class="badge bg-success">
                                <i class="ri-file-text-line"></i> Invoice
                            </span>
                        @elseif($po->no_gr)
                            <span class="badge bg-info">
                                <i class="ri-inbox-line"></i> GR
                            </span>
                        @else
                            <span class="badge bg-primary">
                                <i class="ri-file-list-line"></i> PO
                            </span>
                        @endif
                    </td>
                    <td>
                        <strong class="text-success">
                            Rp {{ number_format($totalInvoice, 0, ',', '.') }}
                        </strong>
                    </td>
                    <td>
                        <div class="mb-2">
                            <span class="badge bg-info">
                                {{ $returs->count() }} Retur
                            </span>
                        </div>
                        @foreach($returs as $retur)
                            <div class="mb-1">
                                <small class="text-muted d-block">
                                    <strong>{{ $retur->no_retur }}</strong>
                                </small>
                                <small class="text-muted">
                                    @php
                                        $statusBadge = [
                                            'draft' => 'secondary',
                                            'menunggu_persetujuan' => 'warning',
                                            'disetujui' => 'success',
                                            'ditolak' => 'danger',
                                            'diproses' => 'info',
                                            'selesai' => 'success',
                                            'dibatalkan' => 'dark'
                                        ];
                                    @endphp
                                    <span class="badge badge-sm bg-{{ $statusBadge[$retur->status] ?? 'secondary' }}">
                                        {{ ucwords(str_replace('_', ' ', $retur->status)) }}
                                    </span>
                                </small>
                            </div>
                        @endforeach
                    </td>
                    <td>
                        <strong class="text-danger d-block mb-1">
                            Rp {{ number_format($totalRetur, 0, ',', '.') }}
                        </strong>
                        <hr class="my-1">
                        <small class="text-muted">Sisa:</small>
                        <strong class="{{ $sisaTagihan > 0 ? 'text-success' : ($sisaTagihan == 0 ? 'text-muted' : 'text-warning') }} d-block">
                            Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                        </strong>
                        @if($sisaTagihan < 0)
                            <small class="text-warning">
                                <i class="ri-alert-line"></i> Retur > Invoice
                            </small>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-more-2-fill"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('po.show', $po->id_po) }}">
                                        <i class="ri-eye-fill me-2"></i>Detail PO
                                    </a>
                                </li>
                                
                                @if($po->no_invoice && $po->isInvoiceComplete())
                                    <li>
                                        <a class="dropdown-item" href="{{ route('po.print-invoice', $po->id_po) }}" target="_blank">
                                            <i class="ri-printer-line me-2"></i>Print Invoice
                                        </a>
                                    </li>
                                @endif
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <li class="dropdown-header">
                                    <i class="ri-refund-2-line me-1"></i> Daftar Retur
                                </li>
                                
                                @foreach($returs as $retur)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('returs.show', $retur->id_retur) }}">
                                            <i class="ri-file-list-line me-2"></i>{{ $retur->no_retur }}
                                            <span class="badge badge-sm bg-{{ $statusBadge[$retur->status] ?? 'secondary' }} ms-1">
                                                {{ ucfirst($retur->status) }}
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <li>
                                    <a class="dropdown-item text-primary" href="{{ route('returs.create', ['po_id' => $po->id_po]) }}">
                                        <i class="ri-add-line me-2"></i>Buat Retur Baru
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
        @if($purchaseOrders->count() > 0)
        <tfoot class="table-secondary">
            <tr>
                <th colspan="8" class="text-end">Total Keseluruhan Retur:</th>
                <th colspan="2">
                    @php
                        $grandTotalRetur = $purchaseOrders->sum(function($po) {
                            return $po->returs->sum('total_nilai_retur');
                        });
                    @endphp
                    <strong class="text-danger fs-5">
                        Rp {{ number_format($grandTotalRetur, 0, ',', '.') }}
                    </strong>
                </th>
            </tr>
        </tfoot>
        @endif
    </table>
</div>