@extends('layouts.app')

@section('title', 'Produk')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Produk</li>
@endsection

@section('page-actions')
    <div class="d-flex flex-row gap-1 day-sorting">
        <button class="btn btn-sm btn-primary">Today</button>
        <button class="btn btn-sm">7d</button>
        <button class="btn btn-sm">2w</button>
        <button class="btn btn-sm">1m</button>
        <button class="btn btn-sm">3m</button>
        <button class="btn btn-sm">6m</button>
        <button class="btn btn-sm">1y</button>
    </div>
@endsection

@section('content')
   <div class="app-body">
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between">
                        {{-- @if (session('success'))
                            <p class="alert alert-success">{{ session('success') }}</p>
                        @endif
                        
                        @if (session('error'))
                            <p class="alert alert-danger">{{ session('error') }}</p>
                        @endif --}}
                        
                        <a class="btn btn-outline-primary" href="{{ route('produks.create') }}">+ Add Produk</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-borderless table-responsive w-100 d-block d-md-table" id="myTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Produk</th>
                                <th>NIE</th>
                                <th>Nama Produk</th>
                                <th>Merk</th>
                                <th>Jenis</th>
                                {{-- <th>Satuan</th> --}}
                                {{-- <th>Harga Beli</th>
                                <th>Harga Jual</th> --}}
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produks as $x => $data)
                                <tr>
                                    <td>{{ $x + 1 }}</td>
                                    <td>{{ $data->kode_produk }}</td>
                                    <td>{{ $data->nie }}</td>
                                    <td>{{ $data->nama_produk }}</td>
                                    <td>{{ $data->merk ?? '-' }}</td>
                                    <td>{{ $data->jenis }}</td>
                                    {{-- <td>{{ $data->satuan }}</td> --}}
                                    {{-- <td>Rp {{ number_format($data->produkSatuans->harga_beli, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($data->produkSatuans->harga_jual, 0, ',', '.') }}</td> --}}
                                    <td>
                                        @if($data->status == 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn shadow" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li class="text-center">
                                                    <a class="dropdown-item" href="{{ route('produks.show', $data->id) }}">
                                                        <i class="ri-eye-line"></i> Detail
                                                    </a>
                                                </li>
                                                <li class="text-center">
                                                    <a class="dropdown-item editBtn" 
                                                    href="{{ route('produks.edit', $data->id) }}">
                                                        <i class="ri-pencil-fill"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('produks.destroy', $data->id) }}" method="POST" class="d-inline delete-confirm">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn w-100 btn-outline-secondary">
                                                            <i class="ri-delete-bin-6-line"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('styles')
    <!-- Custom styles -->
    <style>
        .bg-2 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
@endpush

@push('scripts')
    <!-- DataTable initialization -->
    <script>
        $(document).ready(function() {

            // Delete confirmation
            $('.delete-confirm').on('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                    this.submit();
                }
            });
        });
    </script>
@endpush