    <table class="table table-responsive table-striped align-middle" id="{{ $tableId }}">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th width="100">No PO</th>
                <th>Tanggal</th>
                <th>Pemohon</th>
                <th>Supplier</th>
                <th width="150">Total</th>
                <th width="200">Alasan Penolakan</th>
                <th width="200">Ditolak Oleh</th>
                <th width="100" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseOrders as $po)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong class="text-danger">{{ $po->no_po }}</strong>
                        <br>
                        {{-- <span class="badge badge-sm bg-warning text-dark">Eksternal</span> --}}
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="ri-calendar-line"></i> 
                            {{ $po->tanggal_permintaan->format('d/m/Y') }}
                        </small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs me-2">
                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                    <i class="ri-user-line"></i>
                                </span>
                            </div>
                            <div>
                                <strong>{{ $po->karyawanPemohon->nama_lengkap }}</strong>
                                <br><small class="text-muted">{{ ucfirst($po->unit_pemohon) }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($po->supplier)
                            <span class="badge bg-secondary">{{ $po->supplier->nama_supplier }}</span>
                        @endif
                    </td>
                    <td>
                        <strong class="text-danger">
                            Rp {{ number_format($po->grand_total, 0, ',', '.') }}
                        </strong>
                    </td>
                    <td>
                        @if($po->status_approval_kepala_gudang === 'ditolak')
                            <div class="mb-2">
                                <span class="badge bg-danger mb-1">
                                    <i class="ri-close-circle-line"></i> Kepala Gudang
                                </span>
                                @if($po->catatan_kepala_gudang)
                                    <br><small class="text-muted">{{ $po->catatan_kepala_gudang }}</small>
                                @endif
                            </div>
                        @endif
                        
                        @if($po->status_approval_kasir === 'ditolak')
                            <div>
                                <span class="badge bg-danger mb-1">
                                    <i class="ri-close-circle-line"></i> Kasir
                                </span>
                                @if($po->catatan_kasir)
                                    <br><small class="text-muted">{{ $po->catatan_kasir }}</small>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($po->status_approval_kepala_gudang === 'ditolak' && $po->kepalaGudang)
                            <div class="mb-2">
                                <small class="text-muted d-block">
                                    <strong>{{ $po->kepalaGudang->nama_lengkap }}</strong>
                                </small>
                                <small class="text-muted">
                                    <i class="ri-calendar-line"></i> 
                                    {{ $po->tanggal_approval_kepala_gudang ? $po->tanggal_approval_kepala_gudang->format('d/m/Y H:i') : '-' }}
                                </small>
                            </div>
                        @endif
                        
                        @if($po->status_approval_kasir === 'ditolak' && $po->kasir)
                            <div>
                                <small class="text-muted d-block">
                                    <strong>{{ $po->kasir->nama_lengkap }}</strong>
                                </small>
                                <small class="text-muted">
                                    <i class="ri-calendar-line"></i> 
                                    {{ $po->tanggal_approval_kasir ? $po->tanggal_approval_kasir->format('d/m/Y H:i') : '-' }}
                                </small>
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-more-2-fill"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('po.show', $po->id_po) }}">
                                        <i class="ri-eye-fill me-2"></i>Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('po.edit', $po->id_po) }}">
                                        <i class="ri-pencil-fill me-2"></i>Edit & Submit Ulang
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger" onclick="deletePO('{{ $po->id_po }}')">
                                        <i class="ri-delete-bin-6-line me-2"></i>Hapus
                                    </button>
                                </li>
                            </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>