@extends('layouts.app')

@section('title', 'Daftar Tagihan Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item text-primary" aria-current="page">Tagihan PO</li>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Tab Navigation (Supplier vs Customer) -->
    <ul class="nav nav-pills nav-pills-custom mb-3" id="mainTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="supplier-main-tab" data-bs-toggle="pill" data-bs-target="#supplier-content" type="button" role="tab">
                <i class="ri-store-2-line me-2"></i>Tagihan Supplier
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="customer-main-tab" data-bs-toggle="pill" data-bs-target="#customer-content" type="button" role="tab">
                <i class="ri-user-line me-2"></i>Tagihan Customer
            </button>
        </li>
    </ul>

    <!-- Main Tab Content -->
    <div class="tab-content" id="mainTabContent">
        
        <!-- SUPPLIER CONTENT -->
        <div class="tab-pane fade show active" id="supplier-content" role="tabpanel">
            <!-- Sub Tab Navigation for Supplier -->
            <ul class="nav nav-tabs nav-tabs-custom mb-4" id="supplierTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="aktif_supplier-tab" data-bs-toggle="tab" data-bs-target="#aktif_supplier" type="button" role="tab" data-tab-name="aktif_supplier">
                        <i class="ri-file-list-3-line me-2"></i>Tagihan Aktif
                        <span class="badge bg-primary ms-2">{{ $tagihanAktifSupplierCount }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lunas_supplier-tab" data-bs-toggle="tab" data-bs-target="#lunas_supplier" type="button" role="tab" data-tab-name="lunas_supplier">
                        <i class="ri-checkbox-circle-line me-2"></i>Lunas
                        <span class="badge bg-success ms-2">{{ $tagihanLunasSupplierCount }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="draft_supplier-tab" data-bs-toggle="tab" data-bs-target="#draft_supplier" type="button" role="tab" data-tab-name="draft_supplier">
                        <i class="ri-draft-line me-2"></i>Draft
                        <span class="badge bg-secondary ms-2">{{ $tagihanDraftSupplierCount }}</span>
                    </button>
                </li>
            </ul>

            <!-- Supplier Tab Content -->
            <div class="tab-content" id="supplierTabContent">
                <!-- SUPPLIER AKTIF TAB -->
                <div class="tab-pane fade show active" id="aktif_supplier" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="card-body text-white">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="text-white mb-2">
                                                <i class="ri-money-dollar-circle-line me-2"></i>Total Outstanding Supplier
                                            </h5>
                                            <h2 class="text-white mb-0">
                                                Rp {{ number_format($totalOutstandingSupplier, 0, ',', '.') }}
                                            </h2>
                                            <small class="text-white-50">Dari {{ $tagihanAktifSupplierCount }} tagihan aktif</small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <i class="ri-wallet-3-line" style="font-size: 5rem; opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="aktif_supplier-content">
                        <!-- Data akan dimuat via AJAX -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Memuat data...</p>
                        </div>
                    </div>
                </div>

                <!-- SUPPLIER LUNAS TAB -->
                <div class="tab-pane fade" id="lunas_supplier" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-success bg-soft">
                                                <span class="avatar-title rounded-circle bg-success bg-gradient">
                                                    <i class="ri-checkbox-circle-line fs-4 text-white"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-muted mb-1">Total Lunas Supplier</p>
                                            <h4 class="mb-0">{{ $tagihanLunasSupplierCount }}</h4>
                                            <small class="text-muted">Tagihan terbayar penuh</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <div class="card-body text-white">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="text-white mb-2">
                                                <i class="ri-money-dollar-circle-line me-2"></i>Total Nilai Lunas
                                            </h5>
                                            <h2 class="text-white mb-0">
                                                Rp {{ number_format($totalLunasSupplier, 0, ',', '.') }}
                                            </h2>
                                            <small class="text-white-50">Dari {{ $tagihanLunasSupplierCount }} tagihan</small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <i class="ri-check-double-line" style="font-size: 5rem; opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="lunas_supplier-content">
                        <!-- Data akan dimuat via AJAX -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Memuat data...</p>
                        </div>
                    </div>
                </div>

                <!-- SUPPLIER DRAFT TAB -->
                <div class="tab-pane fade" id="draft_supplier" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-xl-4 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-secondary bg-soft">
                                                <span class="avatar-title rounded-circle bg-secondary bg-gradient">
                                                    <i class="ri-draft-line fs-4 text-white"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-muted mb-1">Total Draft Supplier</p>
                                            <h4 class="mb-0">{{ $tagihanDraftSupplierCount }}</h4>
                                            <small class="text-muted">Belum diproses</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #868e96 0%, #6c757d 100%);">
                                <div class="card-body text-white">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="text-white mb-2">
                                                <i class="ri-money-dollar-circle-line me-2"></i>Total Nilai Draft
                                            </h5>
                                            <h2 class="text-white mb-0">
                                                Rp {{ number_format($totalDraftSupplier, 0, ',', '.') }}
                                            </h2>
                                            <small class="text-white-50">Dari {{ $tagihanDraftSupplierCount }} tagihan draft</small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <i class="ri-file-draft-line" style="font-size: 5rem; opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="draft_supplier-content">
                        <!-- Data akan dimuat via AJAX -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-secondary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Memuat data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CUSTOMER CONTENT -->
        <div class="tab-pane fade" id="customer-content" role="tabpanel">
            <!-- Sub Tab Navigation for Customer -->
            <ul class="nav nav-tabs nav-tabs-custom mb-4" id="customerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="aktif_customer-tab" data-bs-toggle="tab" data-bs-target="#aktif_customer" type="button" role="tab" data-tab-name="aktif_customer">
                        <i class="ri-file-list-3-line me-2"></i>Tagihan Aktif
                        <span class="badge bg-primary ms-2">{{ $tagihanAktifCustomerCount }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lunas_customer-tab" data-bs-toggle="tab" data-bs-target="#lunas_customer" type="button" role="tab" data-tab-name="lunas_customer">
                        <i class="ri-checkbox-circle-line me-2"></i>Lunas
                        <span class="badge bg-success ms-2">{{ $tagihanLunasCustomerCount }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="draft_customer-tab" data-bs-toggle="tab" data-bs-target="#draft_customer" type="button" role="tab" data-tab-name="draft_customer">
                        <i class="ri-draft-line me-2"></i>Draft
                        <span class="badge bg-secondary ms-2">{{ $tagihanDraftCustomerCount }}</span>
                    </button>
                </li>
            </ul>

            <!-- Customer Tab Content -->
            <div class="tab-content" id="customerTabContent">
                <!-- CUSTOMER AKTIF TAB -->
                <div class="tab-pane fade show active" id="aktif_customer" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="card-body text-white">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="text-white mb-2">
                                                <i class="ri-money-dollar-circle-line me-2"></i>Total Piutang Customer
                                            </h5>
                                            <h2 class="text-white mb-0">
                                                Rp {{ number_format($totalOutstandingCustomer, 0, ',', '.') }}
                                            </h2>
                                            <small class="text-white-50">Dari {{ $tagihanAktifCustomerCount }} tagihan aktif</small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <i class="ri-wallet-3-line" style="font-size: 5rem; opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="aktif_customer-content">
                        <!-- Data akan dimuat via AJAX -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Memuat data...</p>
                        </div>
                    </div>
                </div>

                <!-- CUSTOMER LUNAS TAB -->
                <div class="tab-pane fade" id="lunas_customer" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-success bg-soft">
                                                <span class="avatar-title rounded-circle bg-success bg-gradient">
                                                    <i class="ri-checkbox-circle-line fs-4 text-white"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-muted mb-1">Total Lunas Customer</p>
                                            <h4 class="mb-0">{{ $tagihanLunasCustomerCount }}</h4>
                                            <small class="text-muted">Tagihan terbayar penuh</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <div class="card-body text-white">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="text-white mb-2">
                                                <i class="ri-money-dollar-circle-line me-2"></i>Total Nilai Lunas
                                            </h5>
                                            <h2 class="text-white mb-0">
                                                Rp {{ number_format($totalLunasCustomer, 0, ',', '.') }}
                                            </h2>
                                            <small class="text-white-50">Dari {{ $tagihanLunasCustomerCount }} tagihan</small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <i class="ri-check-double-line" style="font-size: 5rem; opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="lunas_customer-content">
                        <!-- Data akan dimuat via AJAX -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Memuat data...</p>
                        </div>
                    </div>
                </div>

                <!-- CUSTOMER DRAFT TAB -->
                <div class="tab-pane fade" id="draft_customer" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-xl-4 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-secondary bg-soft">
                                                <span class="avatar-title rounded-circle bg-secondary bg-gradient">
                                                    <i class="ri-draft-line fs-4 text-white"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-muted mb-1">Total Draft Customer</p>
                                            <h4 class="mb-0">{{ $tagihanDraftCustomerCount }}</h4>
                                            <small class="text-muted">Belum diproses</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #868e96 0%, #6c757d 100%);">
                                <div class="card-body text-white">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="text-white mb-2">
                                                <i class="ri-money-dollar-circle-line me-2"></i>Total Nilai Draft
                                            </h5>
                                            <h2 class="text-white mb-0">
                                                Rp {{ number_format($totalDraftCustomer, 0, ',', '.') }}
                                            </h2>
                                            <small class="text-white-50">Dari {{ $tagihanDraftCustomerCount }} tagihan draft</small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <i class="ri-file-draft-line" style="font-size: 5rem; opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="draft_customer-content">
                        <!-- Data akan dimuat via AJAX -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-secondary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Memuat data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nav-pills-custom .nav-link {
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s;
    }
    
    .nav-pills-custom .nav-link:hover {
        background-color: #f8f9fa;
        color: #495057;
    }
    
    .nav-pills-custom .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .nav-tabs-custom {
        border-bottom: 2px solid #e9ecef;
    }
    
    .nav-tabs-custom .nav-link {
        border: none;
        color: #6c757d;
        padding: 1rem 1.5rem;
        font-weight: 500;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }
    
    .nav-tabs-custom .nav-link:hover {
        color: #495057;
        border-bottom-color: #dee2e6;
    }
    
    .nav-tabs-custom .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: transparent;
    }
    
    .nav-tabs-custom .nav-link .badge {
        font-size: 0.75rem;
    }

    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
        font-size: 0.85rem;
    }
    
    .avatar-xs {
        height: 2rem;
        width: 2rem;
    }
    
    .avatar-sm {
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-title {
        align-items: center;
        display: flex;
        font-weight: 600;
        height: 100%;
        justify-content: center;
        width: 100%;
    }
    
    .bg-soft {
        opacity: 0.1;
    }
    
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.15) !important;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    
    .card {
        border-radius: 0.5rem;
    }

    .dropdown-item i {
        width: 20px;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .table {
        border: 1px solid #ced4da !important;
    }

    .progress {
        background-color: #e9ecef;
    }
</style>
@endpush

@push('scripts')
<script>
    // Track which tabs have been loaded
    const loadedTabs = new Set();
    
    // Function to load tab data via AJAX
    function loadTabData(tabName) {
        // Skip if already loaded
        if (loadedTabs.has(tabName)) {
            // console.log('✓ Tab sudah dimuat:', tabName);
            return;
        }
        
        // console.log('⏳ Memuat tab:', tabName);
        
        // PENTING: Gunakan underscore dalam ID, bukan dash
        const contentDiv = document.getElementById(tabName + '-content');
        
        if (!contentDiv) {
            console.error('❌ Content div tidak ditemukan untuk:', tabName);
            // console.log('Mencari ID:', tabName + '-content');
            return;
        }
        
        // Show loading state
        contentDiv.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2">Memuat data...</p>
            </div>
        `;
        
        // Build URL
        const url = '{{ route("tagihan.index") }}';
        const params = new URLSearchParams();
        params.set('tab', tabName);
        
        // Add other filters from current URL if any
        const currentParams = new URLSearchParams(window.location.search);
        currentParams.forEach((value, key) => {
            if (key !== 'tab') {
                params.set(key, value);
            }
        });
        
        // Fetch data
        fetch(`${url}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.html) {
                contentDiv.innerHTML = data.html;
                loadedTabs.add(tabName);
                // console.log('✓ Tab berhasil dimuat:', tabName, '- Total data:', data.count || 0);
            } else {
                throw new Error(data.message || 'Format response tidak sesuai');
            }
        })
        .catch(error => {
            console.error('❌ Error loading tab:', error);
            contentDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="ri-error-warning-line me-2"></i>
                    <strong>Gagal memuat data:</strong> ${error.message}
                    <br>
                    <button onclick="location.reload()" class="btn btn-sm btn-primary mt-2">
                        <i class="ri-refresh-line me-1"></i>Refresh Halaman
                    </button>
                </div>
            `;
        });
    }
    
    // Export Excel Function
    function exportExcel(tab) {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'excel');
        params.set('tab', tab);
        window.location.href = '{{ route("tagihan.index") }}?' + params.toString();
    }

    // Export PDF Function
    function exportPDF(tab) {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'pdf');
        params.set('tab', tab);
        window.open('{{ route("tagihan.index") }}?' + params.toString(), '_blank');
    }

    // Print Function
    function printTagihan(tab) {
        const params = new URLSearchParams(window.location.search);
        params.set('print', 'true');
        params.set('tab', tab);
        
        const printWindow = window.open('{{ route("tagihan.index") }}?' + params.toString(), '_blank');
        if (printWindow) {
            printWindow.onload = function() {
                printWindow.print();
            };
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // console.log('🚀 DOM loaded, initializing tabs...');
        
        // Auto dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Handle tab from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'aktif_supplier';
        
        // console.log('📍 Active tab dari URL:', activeTab);
        
        // Determine main tab (supplier or customer)
        const mainTab = activeTab.includes('customer') ? 'customer' : 'supplier';
        
        // Activate main tab
        if (mainTab === 'customer') {
            const mainTabButton = document.querySelector('#customer-main-tab');
            if (mainTabButton) {
                const mainTabTrigger = new bootstrap.Tab(mainTabButton);
                mainTabTrigger.show();
            }
        }
        
        // Activate sub tab dengan ID yang benar (menggunakan underscore)
        const subTabButton = document.querySelector(`#${activeTab}-tab`);
        if (subTabButton) {
            const subTabTrigger = new bootstrap.Tab(subTabButton);
            subTabTrigger.show();
        } else {
            // console.warn('⚠️ Sub tab button tidak ditemukan:', `#${activeTab}-tab`);
        }
        
        // Load initial tab data after a short delay
        setTimeout(() => {
            // console.log('📥 Loading initial tab data for:', activeTab);
            loadTabData(activeTab);
        }, 100);

        // Handle tab switching - load data when tab is shown
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(button => {
            button.addEventListener('shown.bs.tab', function (e) {
                const tabName = e.target.getAttribute('data-tab-name');
                
                // console.log('🔄 Tab switched to:', tabName);
                
                // Load data for this tab if it has a name
                if (tabName) {
                    loadTabData(tabName);
                    
                    // Update URL without reload
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tabName);
                    window.history.pushState({}, '', url);
                }
            });
        });

        // Handle main tab switching (supplier/customer)
        document.querySelectorAll('[data-bs-toggle="pill"]').forEach(button => {
            button.addEventListener('shown.bs.tab', function (e) {
                // When switching between supplier/customer, load the active sub-tab
                const mainTabId = e.target.getAttribute('data-bs-target').replace('#', '');
                const activeSubTab = document.querySelector(`#${mainTabId} .nav-tabs .nav-link.active`);
                
                if (activeSubTab) {
                    const tabName = activeSubTab.getAttribute('data-tab-name');
                    if (tabName) {
                        // console.log('🔄 Main tab switched, loading sub-tab:', tabName);
                        loadTabData(tabName);
                    }
                }
            });
        });
    });
</script>
@endpush