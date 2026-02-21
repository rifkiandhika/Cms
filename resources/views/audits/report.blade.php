@extends('layouts.app')

@section('title', 'Laporan Audit')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('audits.index') }}">Audit</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Laporan</li>
@endsection

@section('content')
<div class="app-body">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><i class="ri-file-text-line me-2"></i>Laporan Audit: {{ $audit->title }}</h4>
                    <small>
                        <i class="ri-calendar-line me-1"></i>Tanggal: {{ $audit->audit_date->format('d/m/Y') }} | 
                        <i class="ri-user-line me-1 ms-2"></i>Auditor: {{ $audit->auditor_name ?? '-' }}
                    </small>
                </div>
                <div class="no-print">
                    <button onclick="window.print()" class="btn btn-light btn-sm">
                        <i class="ri-printer-line me-1"></i>Cetak
                    </button>
                    <a href="{{ route('audits.index') }}" class="btn btn-light btn-sm">
                        <i class="ri-arrow-left-line me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="avatar-lg rounded-circle bg-success bg-gradient d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="ri-check-line fs-2 text-white"></i>
                    </div>
                    <h2 class="text-success mb-1">{{ $summary['yes'] }}</h2>
                    <p class="text-muted mb-0">Ya</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="avatar-lg rounded-circle bg-danger bg-gradient d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="ri-close-line fs-2 text-white"></i>
                    </div>
                    <h2 class="text-danger mb-1">{{ $summary['no'] }}</h2>
                    <p class="text-muted mb-0">Tidak</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="avatar-lg rounded-circle bg-secondary bg-gradient d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="ri-indeterminate-circle-line fs-2 text-white"></i>
                    </div>
                    <h2 class="text-secondary mb-1">{{ $summary['na'] }}</h2>
                    <p class="text-muted mb-0">N/A</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="avatar-lg rounded-circle bg-warning bg-gradient d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="ri-subtract-line fs-2 text-white"></i>
                    </div>
                    <h2 class="text-warning mb-1">{{ $summary['partial'] }}</h2>
                    <p class="text-muted mb-0">Sebagian</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Completion Progress -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Tingkat Penyelesaian</h6>
                <span class="badge bg-primary">{{ $summary['total'] }} dari {{ $summary['total'] + $summary['unanswered'] }} pertanyaan</span>
            </div>
            @php
                $totalQuestions = $summary['total'] + $summary['unanswered'];
                $percentage = $totalQuestions > 0 ? round(($summary['total'] / $totalQuestions) * 100, 1) : 0;
            @endphp
            <div class="progress" style="height: 25px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                    {{ $percentage }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Responses -->
    @foreach($categories as $category)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="mb-0 text-white">
                    <i class="ri-folder-line me-2"></i>{{ $category->number }}. {{ $category->name }}
                </h5>
            </div>
            <div class="card-body">
                @foreach($category->subCategories as $subCategory)
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                            <div class="bg-primary text-white rounded px-3 py-2 me-2">
                                <strong>{{ $subCategory->label }}</strong>
                            </div>
                            <h6 class="mb-0">{{ $subCategory->name }}</h6>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80">No</th>
                                        <th width="30%">Pertanyaan</th>
                                        <th width="100" class="text-center">Jawaban</th>
                                        <th width="20%">Bukti</th>
                                        <th width="15%">Info Tambahan</th>
                                        <th width="15%">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subCategory->questions as $question)
                                        @php
                                            $response = $question->auditResponses->first();
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $question->number }}</span>
                                            </td>
                                            <td>{{ $question->question }}</td>
                                            <td class="text-center">
                                                @if($response && $response->response)
                                                    @if($response->response == 'yes')
                                                        <span class="badge bg-success">
                                                            <i class="ri-check-line"></i> Ya
                                                        </span>
                                                    @elseif($response->response == 'no')
                                                        <span class="badge bg-danger">
                                                            <i class="ri-close-line"></i> Tidak
                                                        </span>
                                                    @elseif($response->response == 'na')
                                                        <span class="badge bg-secondary">
                                                            <i class="ri-indeterminate-circle-line"></i> N/A
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="ri-subtract-line"></i> Sebagian
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($response && $response->evidence)
                                                    <small>{{ $response->evidence }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                                
                                                @if($response && $response->document_path)
                                                    <div class="mt-2">
                                                        <a href="{{ Storage::url($response->document_path) }}" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-info no-print">
                                                            <i class="ri-file-text-line me-1"></i>Lihat Dokumen
                                                        </a>
                                                    </div>
                                                @endif
                                                
                                                @if($response && $response->image_path)
                                                    <div class="mt-2">
                                                        <a href="{{ Storage::url($response->image_path) }}" 
                                                           target="_blank"
                                                           data-lightbox="evidence-{{ $question->id }}">
                                                            <img src="{{ Storage::url($response->image_path) }}" 
                                                                 alt="Evidence" 
                                                                 class="img-thumbnail" 
                                                                 style="max-height: 60px; cursor: pointer;">
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($response && ($response->evidence_date || $response->temperature))
                                                    @if($response->evidence_date)
                                                        <small class="d-block">
                                                            <i class="ri-calendar-line me-1"></i>
                                                            <strong>Tanggal:</strong> {{ $response->evidence_date->format('d/m/Y') }}
                                                        </small>
                                                    @endif
                                                    
                                                    @if($response->temperature)
                                                        <small class="d-block mt-1">
                                                            <i class="ri-temp-hot-line me-1"></i>
                                                            <strong>Suhu:</strong> {{ $response->temperature }}°C
                                                        </small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($response && $response->notes)
                                                    <small>{{ $response->notes }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Notes -->
    @if($audit->notes)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="ri-sticky-note-line me-2"></i>Catatan Audit</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $audit->notes }}</p>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .avatar-lg {
        width: 4rem;
        height: 4rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        border-radius: 0.5rem;
    }

    .table {
        border: 1px solid #dee2e6;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        
        .card {
            border: 1px solid #000;
            page-break-inside: avoid;
            box-shadow: none !important;
        }
        
        .table {
            font-size: 0.9rem;
        }
        
        .badge {
            border: 1px solid #000;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// Auto print prompt
document.addEventListener('DOMContentLoaded', function() {
    // Optional: Add lightbox for images if you want
    // You can add lightbox library here
});
</script>
@endpush