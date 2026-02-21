@extends('layouts.app')

@section('title', 'Tagihan Overdue')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">🚨 Tagihan Overdue</h2>
            <p class="text-muted">Tagihan yang sudah melewati tanggal jatuh tempo</p>
            <div class="alert alert-danger d-inline-block">
                <strong>Total Outstanding Overdue:</strong> Rp {{ number_format($totalOverdue, 0, ',', '.') }}
            </div>
        </div>
        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary">
            ← Kembali ke Dashboard
        </a>
    </div>

    @if($tagihan->count() > 0)
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Tagihan</th>
                            <th>No. PO</th>
                            <th>Supplier</th>
                            <th>Grand Total</th>
                            <th>Total Dibayar</th>
                            <th>Sisa Tagihan</th>
                            <th>Jatuh Tempo</th>
                            <th>Overdue</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tagihan as $t)
                        <tr class="table-danger">
                            <td><strong>{{ $t->no_tagihan }}</strong></td>
                            <td>{{ $t->purchaseOrder->no_po ?? '-' }}</td>
                            <td>{{ $t->supplier->nama_supplier ?? '-' }}</td>
                            <td>Rp {{ number_format($t->grand_total, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($t->total_dibayar, 0, ',', '.') }}</td>
                            <td class="text-danger">
                                <strong>Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</strong>
                            </td>
                            <td>{{ $t->tanggal_jatuh_tempo->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-danger">
                                    {{ abs($t->days_overdue) }} hari
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('tagihan.show', $t->id_tagihan) }}" class="btn btn-sm btn-danger">
                                    Bayar Sekarang
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $tagihan->links() }}
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Tidak ada tagihan yang overdue. Bagus!
    </div>
    @endif
</div>
@endsection