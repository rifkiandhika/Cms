@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Detail Produk</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('produks.index') }}">Produk</a></li>
                    <li class="breadcrumb-item active">{{ $produk->nama_produk }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('produks.edit', $produk->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('produks.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── Kolom Kiri: Info Produk ── --}}
        <div class="col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-box-seam me-2 text-primary"></i>Informasi Produk
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width:45%">Kode Produk</td>
                            <td><strong>{{ $produk->kode_produk }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIE</td>
                            <td>{{ $produk->nie }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Produk</td>
                            <td>{{ $produk->nama_produk }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Merk</td>
                            <td>{{ $produk->merk ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis</td>
                            <td>{{ $produk->jenis ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Satuan Dasar</td>
                            <td>
                                @if($produk->satuanDasar)
                                    <span class="badge bg-primary">{{ $produk->satuanDasar->nama_satuan }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($produk->status === 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat</td>
                            <td>{{ $produk->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Diperbarui</td>
                            <td>{{ $produk->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                    </table>

                    @if($produk->deskripsi)
                        <hr>
                        <p class="text-muted small mb-1">Deskripsi</p>
                        <p class="mb-0">{{ $produk->deskripsi }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Kolom Kanan: Harga & Satuan Jual ── --}}
        <div class="col-md-7">

            {{-- Harga Referensi --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-currency-dollar me-2 text-success"></i>Harga Referensi (per Satuan Dasar)
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <p class="text-muted small mb-1">Harga Beli</p>
                            <h5 class="fw-bold text-danger">
                                Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}
                            </h5>
                        </div>
                        <div class="col-6">
                            <p class="text-muted small mb-1">Harga Dasar / Jual</p>
                            <h5 class="fw-bold text-success">
                                Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Satuan Jual --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-tags me-2 text-warning"></i>Satuan Jual
                </div>
                <div class="card-body p-0">
                    @if($produk->produkSatuans->count())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Satuan</th>
                                        <th>Label</th>
                                        <th>Isi</th>
                                        <th>Harga Beli</th>
                                        <th>Harga Jual</th>
                                        <th>Mode</th>
                                        <th>Default</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($produk->produkSatuans as $ps)
                                    <tr>
                                        <td>{{ $ps->satuan->nama_satuan ?? '-' }}</td>
                                        <td><strong>{{ $ps->label }}</strong></td>
                                        <td class="text-center">{{ $ps->isi }}</td>
                                        <td class="text-end">
                                            Rp {{ number_format($ps->harga_beli_final, 0, ',', '.') }}
                                            @if($ps->harga_otomatis)
                                                <br><small class="text-muted">
                                                    {{ number_format($produk->harga_beli, 0, ',', '.') }} × {{ $ps->isi }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            Rp {{ number_format($ps->harga_jual_final, 0, ',', '.') }}
                                            @if($ps->harga_otomatis)
                                                <br><small class="text-muted">
                                                    {{ number_format($produk->harga_dasar, 0, ',', '.') }} × {{ $ps->isi }}
                                                </small>
                                            </td>
                                            @endif
                                        <td class="text-center">
                                            @if($ps->harga_otomatis)
                                                <span class="badge bg-info text-dark">Otomatis</span>
                                            @else
                                                <span class="badge bg-secondary">Manual</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($ps->is_default)
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-check-lg"></i> Default
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada satuan jual yang didefinisikan.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Tombol Hapus --}}
    <div class="mt-4 d-flex justify-content-end">
        <form action="{{ route('produks.destroy', $produk->id) }}" method="POST"
              onsubmit="return confirm('Yakin ingin menghapus produk ini? Semua satuan jual akan ikut terhapus.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash me-1"></i> Hapus Produk
            </button>
        </form>
    </div>

</div>
@endsection