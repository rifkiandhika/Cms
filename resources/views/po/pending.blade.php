@extends('layouts.app')

@section('title', 'PO Pending Approval')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">⏳ PO Pending Approval</h2>
            <p class="text-muted">Purchase Order yang menunggu persetujuan</p>
        </div>
        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary">
            ← Kembali ke Dashboard
        </a>
    </div>

    @if($pos->count() > 0)
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. PO</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Pemohon</th>
                            <th>Supplier</th>
                            <th>Grand Total</th>
                            <th>Deadline</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pos as $po)
                        <tr class="{{ $po->is_near_deadline ? 'table-warning' : '' }}">
                            <td>
                                <strong>{{ $po->no_po }}</strong>
                                @if($po->is_near_deadline)
                                    <span class="badge bg-danger ms-1">Urgent!</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $po->tipe_po == 'internal' ? 'info' : 'primary' }}">
                                    {{ ucfirst($po->tipe_po) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-warning">
                                    {{ ucwords(str_replace('_', ' ', $po->status)) }}
                                </span>
                            </td>
                            <td>{{ $po->karyawanPemohon->nama ?? '-' }}</td>
                            <td>{{ $po->supplier->nama_supplier ?? '-' }}</td>
                            <td>Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
                            <td>
                                @if($po->hours_left !== null)
                                    <span class="badge bg-{{ $po->hours_left < 6 ? 'danger' : 'warning' }}">
                                        {{ $po->hours_left }} jam lagi
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('po.show', $po->id_po) }}" class="btn btn-sm btn-primary">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pos->links() }}
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Tidak ada PO yang menunggu approval.
    </div>
    @endif
</div>
@endsection