@extends('layouts.app')

@section('title', 'Dashboard - Purchase Order & Tagihan')

@push('styles')
<style>
    :root {
        --primary-color: #3b82f6;
        --primary-light: #dbeafe;
        --success-color: #10b981;
        --success-light: #d1fae5;
        --warning-color: #f59e0b;
        --warning-light: #fef3c7;
        --danger-color: #ef4444;
        --danger-light: #fee2e2;
        --info-color: #06b6d4;
        --info-light: #cffafe;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: var(--gray-50);
        color: var(--gray-900);
        line-height: 1.6;
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* ============================================
       HEADER SECTION
    ============================================ */
    .dashboard-header {
        margin-bottom: 2.5rem;
    }

    .dashboard-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        letter-spacing: -0.025em;
    }

    .dashboard-header p {
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    /* ============================================
       ALERT CARDS - REDESIGNED
    ============================================ */
    .alerts-section {
        margin-bottom: 3rem;
    }

    .alerts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .alert-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-100);
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .alert-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        transition: width 0.3s ease;
    }

    .alert-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .alert-card:hover::before {
        width: 6px;
    }

    .alert-card.warning::before {
        background: var(--warning-color);
    }

    .alert-card.danger::before {
        background: var(--danger-color);
    }

    .alert-card.info::before {
        background: var(--info-color);
    }

    .alert-card-content {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .alert-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .alert-card.warning .alert-icon-wrapper {
        background: var(--warning-light);
    }

    .alert-card.danger .alert-icon-wrapper {
        background: var(--danger-light);
    }

    .alert-card.info .alert-icon-wrapper {
        background: var(--info-light);
    }

    .alert-details {
        flex: 1;
        min-width: 0;
    }

    .alert-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-900);
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .alert-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
        line-height: 1.4;
    }

    .alert-sublabel {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-top: 0.25rem;
    }

    /* ============================================
       SECTION HEADERS
    ============================================ */
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--gray-100);
    }

    .section-header h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        letter-spacing: -0.025em;
    }

    .section-icon {
        font-size: 1.5rem;
    }

    /* ============================================
       STATS CARDS - IMPROVED
    ============================================ */
    .stats-section {
        margin-bottom: 3rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-100);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        opacity: 0.05;
        transform: translate(30%, -30%);
    }

    .stat-card.primary::after {
        background: var(--primary-color);
    }

    .stat-card.success::after {
        background: var(--success-color);
    }

    .stat-card.warning::after {
        background: var(--warning-color);
    }

    .stat-card.danger::after {
        background: var(--danger-color);
    }

    .stat-card.info::after {
        background: var(--info-color);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
    }

    .stat-card-title {
        font-size: 0.8125rem;
        color: var(--gray-600);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .stat-card.primary .stat-card-icon {
        background: var(--primary-light);
    }

    .stat-card.success .stat-card-icon {
        background: var(--success-light);
    }

    .stat-card.warning .stat-card-icon {
        background: var(--warning-light);
    }

    .stat-card.danger .stat-card-icon {
        background: var(--danger-light);
    }

    .stat-card.info .stat-card-icon {
        background: var(--info-light);
    }

    .stat-card-body {
        position: relative;
        z-index: 1;
    }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-900);
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .stat-card-subtitle {
        font-size: 0.75rem;
        color: var(--gray-500);
        font-weight: 500;
    }

    /* ============================================
       CHART CARDS
    ============================================ */
    .charts-section {
        margin-bottom: 3rem;
    }

    .chart-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 1.5rem;
    }

    .chart-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-100);
    }

    .chart-card.full-width {
        grid-column: span 12;
    }

    .chart-card.two-thirds {
        grid-column: span 8;
    }

    .chart-card.half-width {
        grid-column: span 6;
    }

    .chart-card.one-third {
        grid-column: span 4;
    }

    .chart-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--gray-100);
    }

    .chart-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-card-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-chart-action {
        padding: 0.5rem 0.875rem;
        font-size: 0.75rem;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        background: white;
        color: var(--gray-700);
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .btn-chart-action:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        color: var(--gray-900);
    }

    /* ============================================
       TABLE CARDS
    ============================================ */
    .tables-section {
        margin-bottom: 2rem;
    }

    .table-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(600px, 1fr));
        gap: 1.5rem;
    }

    .table-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-100);
    }

    .table-card.full-width {
        grid-column: 1 / -1;
    }

    .table-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--gray-100);
    }

    .table-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-wrapper {
        overflow-x: auto;
        margin: 0 -1.5rem;
        padding: 0 1.5rem;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .data-table thead th {
        text-align: left;
        padding: 0.875rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-600);
        background: var(--gray-50);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid var(--gray-200);
        white-space: nowrap;
    }

    .data-table tbody td {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid var(--gray-100);
        color: var(--gray-700);
    }

    .data-table tbody tr {
        transition: background-color 0.2s;
    }

    .data-table tbody tr:hover {
        background: var(--gray-50);
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ============================================
       BADGES
    ============================================ */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        font-size: 0.6875rem;
        font-weight: 600;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        line-height: 1;
    }

    .badge-success {
        background: var(--success-light);
        color: var(--success-color);
    }

    .badge-warning {
        background: var(--warning-light);
        color: #d97706;
    }

    .badge-danger {
        background: var(--danger-light);
        color: var(--danger-color);
    }

    .badge-info {
        background: var(--info-light);
        color: #0891b2;
    }

    .badge-primary {
        background: var(--primary-light);
        color: var(--primary-color);
    }

    .badge-gray {
        background: var(--gray-100);
        color: var(--gray-600);
    }

    /* ============================================
       PROGRESS BAR
    ============================================ */
    .progress-bar {
        width: 100%;
        height: 6px;
        background: var(--gray-200);
        border-radius: 3px;
        overflow: hidden;
        margin-top: 0.75rem;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--success-color), #059669);
        transition: width 0.6s ease;
        border-radius: 3px;
    }

    /* ============================================
       UTILITIES
    ============================================ */
    .text-danger {
        color: var(--danger-color) !important;
        font-weight: 600;
    }

    .text-warning {
        color: var(--warning-color) !important;
        font-weight: 600;
    }

    .text-success {
        color: var(--success-color) !important;
        font-weight: 600;
    }

    /* ============================================
       SCROLLBAR
    ============================================ */
    .table-wrapper::-webkit-scrollbar {
        height: 6px;
    }

    .table-wrapper::-webkit-scrollbar-track {
        background: var(--gray-100);
        border-radius: 3px;
    }

    .table-wrapper::-webkit-scrollbar-thumb {
        background: var(--gray-300);
        border-radius: 3px;
    }

    .table-wrapper::-webkit-scrollbar-thumb:hover {
        background: var(--gray-400);
    }

    /* ============================================
       RESPONSIVE
    ============================================ */
    @media (max-width: 1200px) {
        .chart-card.two-thirds,
        .chart-card.one-third {
            grid-column: span 12;
        }

        .chart-card.half-width {
            grid-column: span 12;
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }

        .dashboard-header h1 {
            font-size: 1.5rem;
        }

        .alerts-grid,
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table-grid {
            grid-template-columns: 1fr;
        }

        .section-header h2 {
            font-size: 1.125rem;
        }

        .stat-card-value,
        .alert-value {
            font-size: 1.75rem;
        }

        .chart-card.full-width,
        .chart-card.two-thirds,
        .chart-card.half-width,
        .chart-card.one-third {
            grid-column: span 12;
        }
    }

    /* ============================================
       ANIMATIONS
    ============================================ */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dashboard-container > * {
        animation: fadeIn 0.5s ease-out backwards;
    }

    .dashboard-container > *:nth-child(1) { animation-delay: 0.05s; }
    .dashboard-container > *:nth-child(2) { animation-delay: 0.1s; }
    .dashboard-container > *:nth-child(3) { animation-delay: 0.15s; }
    .dashboard-container > *:nth-child(4) { animation-delay: 0.2s; }
    .dashboard-container > *:nth-child(5) { animation-delay: 0.25s; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="dashboard-header">
        <h1>Dashboard Overview</h1>
        <p>Monitoring Purchase Order & Tagihan - {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</p>
    </div>

    <!-- Alert Cards -->
    {{-- @if($alerts['po_near_deadline'] > 0 || $alerts['tagihan_overdue'] > 0 || $alerts['tagihan_due_soon'] > 0 || $alerts['po_need_invoice'] > 0 || $alerts['po_need_confirmation'] > 0)
    <div class="alerts-section">
        <div class="alerts-grid">
            @if($alerts['po_near_deadline'] > 0)
            <div class="alert-card warning" onclick="window.location.href='{{ route('po.pending') }}'">
                <div class="alert-card-content">
                    <div class="alert-icon-wrapper">⚠️</div>
                    <div class="alert-details">
                        <div class="alert-value">{{ $alerts['po_near_deadline'] }}</div>
                        <div class="alert-label">PO Mendekati Deadline</div>
                        <div class="alert-sublabel">Auto-cancel warning</div>
                    </div>
                </div>
            </div>
            @endif

            @if($alerts['tagihan_overdue'] > 0)
            <div class="alert-card danger" onclick="window.location.href='{{ route('tagihan.overdue') }}'">
                <div class="alert-card-content">
                    <div class="alert-icon-wrapper">🚨</div>
                    <div class="alert-details">
                        <div class="alert-value">{{ $alerts['tagihan_overdue'] }}</div>
                        <div class="alert-label">Tagihan Overdue</div>
                        <div class="alert-sublabel">Rp {{ number_format($nilaiOverdue, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            @endif

            @if($alerts['tagihan_due_soon'] > 0)
            <div class="alert-card warning" onclick="window.location.href='{{ route('tagihan.due-soon') }}'">
                <div class="alert-card-content">
                    <div class="alert-icon-wrapper">⏰</div>
                    <div class="alert-details">
                        <div class="alert-value">{{ $alerts['tagihan_due_soon'] }}</div>
                        <div class="alert-label">Jatuh Tempo 7 Hari</div>
                        <div class="alert-sublabel">Rp {{ number_format($nilaiDueSoon, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            @endif

            @if($alerts['po_need_invoice'] > 0)
            <div class="alert-card info" onclick="window.location.href='{{ route('po.need-invoice') }}'">
                <div class="alert-card-content">
                    <div class="alert-icon-wrapper">📄</div>
                    <div class="alert-details">
                        <div class="alert-value">{{ $alerts['po_need_invoice'] }}</div>
                        <div class="alert-label">PO Perlu Invoice</div>
                        <div class="alert-sublabel">Input required</div>
                    </div>
                </div>
            </div>
            @endif

            @if($alerts['po_need_confirmation'] > 0)
            <div class="alert-card info" onclick="window.location.href='{{ route('po.need-confirmation') }}'">
                <div class="alert-card-content">
                    <div class="alert-icon-wrapper">✅</div>
                    <div class="alert-details">
                        <div class="alert-value">{{ $alerts['po_need_confirmation'] }}</div>
                        <div class="alert-label">Perlu Konfirmasi</div>
                        <div class="alert-sublabel">Penerimaan barang</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif --}}

    <!-- Purchase Order Stats -->
    {{-- <div class="stats-section">
        <div class="section-header">
            <span class="section-icon">📦</span>
            <h2>Purchase Order Statistics</h2>
        </div>
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-card-header">
                    <span class="stat-card-title">Total PO</span>
                    <div class="stat-card-icon">📋</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">{{ number_format($poStats['total']) }}</div>
                    <div class="stat-card-subtitle">
                        Internal: {{ $poByType['internal'] ?? 0 }} · Eksternal: {{ $poByType['eksternal'] ?? 0 }}
                    </div>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-card-header">
                    <span class="stat-card-title">Pending</span>
                    <div class="stat-card-icon">⏳</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">{{ number_format($poStats['pending_approval']) }}</div>
                    <div class="stat-card-subtitle">Menunggu persetujuan</div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-card-header">
                    <span class="stat-card-title">Disetujui</span>
                    <div class="stat-card-icon">✓</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">{{ number_format($poStats['approved']) }}</div>
                    <div class="stat-card-subtitle">Siap diproses</div>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-card-header">
                    <span class="stat-card-title">In Progress</span>
                    <div class="stat-card-icon">🚚</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">{{ number_format($poStats['in_progress']) }}</div>
                    <div class="stat-card-subtitle">Sedang pengiriman</div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-card-header">
                    <span class="stat-card-title">Selesai</span>
                    <div class="stat-card-icon">✔️</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">{{ number_format($poStats['completed']) }}</div>
                    <div class="stat-card-subtitle">PO completed</div>
                </div>
            </div>

            <div class="stat-card danger">
                <div class="stat-card-header">
                    <span class="stat-card-title">Rejected</span>
                    <div class="stat-card-icon">✖️</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">{{ number_format($poStats['rejected'] + $poStats['cancelled']) }}</div>
                    <div class="stat-card-subtitle">
                        Ditolak: {{ $poStats['rejected'] }} · Batal: {{ $poStats['cancelled'] }}
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Tagihan Stats -->
    {{-- <div class="stats-section">
        <div class="section-header">
            <span class="section-icon">💰</span>
            <h2>Tagihan & Pembayaran Statistics</h2>
        </div>
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-card-header">
                    <span class="stat-card-title">Total Tagihan</span>
                    <div class="stat-card-icon">📊</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">{{ number_format($tagihanStats['total']) }}</div>
                    <div class="stat-card-subtitle">Semua status</div>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-card-header">
                    <span class="stat-card-title">Unpaid</span>
                    <div class="stat-card-icon">⏰</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">{{ number_format($tagihanStats['menunggu_pembayaran']) }}</div>
                    <div class="stat-card-subtitle">Belum dibayar</div>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-card-header">
                    <span class="stat-card-title">Partial</span>
                    <div class="stat-card-icon">📈</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">{{ number_format($tagihanStats['dibayar_sebagian']) }}</div>
                    <div class="stat-card-subtitle">Partial payment</div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-card-header">
                    <span class="stat-card-title">Lunas</span>
                    <div class="stat-card-icon">✅</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value">{{ number_format($tagihanStats['lunas']) }}</div>
                    <div class="stat-card-subtitle">Fully paid</div>
                </div>
            </div>

            <div class="stat-card primary">
                <div class="stat-card-header">
                    <span class="stat-card-title">Nilai Total</span>
                    <div class="stat-card-icon">💵</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value" style="font-size: 1.5rem;">
                        Rp {{ number_format($tagihanNilai->total_tagihan ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="stat-card-subtitle">Grand total tagihan</div>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-card-header">
                    <span class="stat-card-title">Dibayar</span>
                    <div class="stat-card-icon">💳</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value" style="font-size: 1.5rem;">
                        Rp {{ number_format($tagihanNilai->total_dibayar ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="stat-card-subtitle">Sudah terbayar</div>
                    @if($tagihanNilai->total_tagihan > 0)
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: {{ ($tagihanNilai->total_dibayar / $tagihanNilai->total_tagihan * 100) }}%"></div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="stat-card danger">
                <div class="stat-card-header">
                    <span class="stat-card-title">Outstanding</span>
                    <div class="stat-card-icon">💸</div>
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-value" style="font-size: 1.5rem;">
                        Rp {{ number_format($tagihanNilai->total_sisa ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="stat-card-subtitle">Sisa tagihan</div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="chart-grid">
            <!-- PO Trend Chart -->
            <div class="chart-card two-thirds">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">📈 Purchase Order Trend (30 Hari)</h3>
                    <div class="chart-card-actions">
                        <button class="btn-chart-action" onclick="downloadChart('poTrendChart')">📥 Download</button>
                    </div>
                </div>
                <canvas id="poTrendChart" height="100"></canvas>
            </div>

            <!-- PO Status Distribution -->
            <div class="chart-card one-third">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">🎯 PO Status</h3>
                </div>
                <canvas id="poStatusChart"></canvas>
            </div>

            <!-- Monthly PO Value -->
            <div class="chart-card two-thirds">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">💰 Monthly PO Value (6 Bulan)</h3>
                </div>
                <canvas id="monthlyValueChart" height="100"></canvas>
            </div>

            <!-- Tagihan Payment Status -->
            <div class="chart-card one-third">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">💳 Payment Status</h3>
                </div>
                <canvas id="tagihanStatusChart"></canvas>
            </div>

            <!-- Aging Analysis -->
            <div class="chart-card half-width">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">⏳ Aging Analysis</h3>
                </div>
                <canvas id="agingChart"></canvas>
            </div>

            <!-- Top Suppliers -->
            <div class="chart-card half-width">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">🏆 Top Suppliers</h3>
                </div>
                <canvas id="topSuppliersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities Section -->
    <div class="tables-section">
        <div class="table-grid">
            <!-- Recent PO -->
            <div class="table-card">
                <div class="table-card-header">
                    <h3 class="table-card-title">📋 Recent Purchase Orders</h3>
                    <a href="{{ route('po.index') }}" class="btn-chart-action">Lihat Semua →</a>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No. PO</th>
                                <th>Tipe</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPO as $po)
                            <tr onclick="window.location.href='{{ route('po.show', $po->id_po) }}'" style="cursor: pointer;">
                                <td><strong>{{ $po->no_po }}</strong></td>
                                <td>
                                    <span class="badge badge-{{ $po->tipe_po == 'internal' ? 'info' : 'primary' }}">
                                        {{ ucfirst($po->tipe_po) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ 
                                        $po->status == 'selesai' ? 'success' : 
                                        ($po->status == 'ditolak' || $po->status == 'dibatalkan' ? 'danger' : 
                                        (str_contains($po->status, 'menunggu') ? 'warning' : 'info')) 
                                    }}">
                                        {{ ucwords(str_replace('_', ' ', $po->status)) }}
                                    </span>
                                </td>
                                <td>Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
                                <td>{{ $po->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--gray-500); padding: 2rem;">
                                    Tidak ada data
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Tagihan -->
            <div class="table-card">
                <div class="table-card-header">
                    <h3 class="table-card-title">💰 Recent Tagihan</h3>
                    <a href="{{ route('tagihan.index') }}" class="btn-chart-action">Lihat Semua →</a>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No. Tagihan</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Sisa</th>
                                <th>Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTagihan as $tagihan)
                            <tr onclick="window.location.href='{{ route('tagihan.show', $tagihan->id_tagihan) }}'" style="cursor: pointer;">
                                <td><strong>{{ $tagihan->no_tagihan }}</strong></td>
                                <td>
                                    <span class="badge badge-{{ 
                                        $tagihan->status == 'lunas' ? 'success' : 
                                        ($tagihan->status == 'dibatalkan' ? 'danger' : 
                                        ($tagihan->status == 'dibayar_sebagian' ? 'warning' : 'info')) 
                                    }}">
                                        {{ ucwords(str_replace('_', ' ', $tagihan->status)) }}
                                    </span>
                                </td>
                                <td>Rp {{ number_format($tagihan->grand_total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="{{ $tagihan->sisa_tagihan > 0 ? 'text-danger' : '' }}">
                                        Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @if($tagihan->tanggal_jatuh_tempo)
                                        <span class="{{ $tagihan->isOverdue() ? 'text-danger' : ($tagihan->isDueSoon() ? 'text-warning' : '') }}">
                                            {{ $tagihan->tanggal_jatuh_tempo->format('d M Y') }}
                                        </span>
                                    @else
                                        <span style="color: var(--gray-400);">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--gray-500); padding: 2rem;">
                                    Tidak ada data
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="table-card full-width">
                <div class="table-card-header">
                    <h3 class="table-card-title">💳 Recent Payments</h3>
                    <a href="#" class="btn-chart-action">Lihat Semua →</a>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No. Pembayaran</th>
                                <th>No. Tagihan</th>
                                <th>Jumlah Bayar</th>
                                <th>Metode</th>
                                <th>Tanggal Bayar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                            <tr onclick="window.location.href='#'" style="cursor: pointer;">
                                <td><strong>{{ $payment->no_pembayaran }}</strong></td>
                                <td>{{ $payment->tagihan->no_tagihan }}</td>
                                <td>Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ ucfirst($payment->metode_pembayaran) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($payment->tanggal_bayar)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $payment->status_pembayaran == 'diverifikasi' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payment->status_pembayaran) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--gray-500); padding: 2rem;">
                                    Tidak ada data
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Chart.js Configuration
    Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.color = '#6b7280';

    // Color Palette
    const colors = {
        primary: '#3b82f6',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        info: '#06b6d4',
        purple: '#8b5cf6',
        pink: '#ec4899',
        gray: '#6b7280'
    };

    // 1. PO Trend Chart
    const poTrendData = {!! json_encode($poTrend) !!};
    new Chart(document.getElementById('poTrendChart'), {
        type: 'line',
        data: {
            labels: poTrendData.map(item => new Date(item.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })),
            datasets: [{
                label: 'Purchase Orders',
                data: poTrendData.map(item => item.total),
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Total PO: ' + context.parsed.y;
                        }
                    },
                    backgroundColor: '#1f2937',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#374151',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // 2. PO Status Distribution
    const poStatusData = {!! json_encode($poStatusDistribution) !!};
    new Chart(document.getElementById('poStatusChart'), {
        type: 'doughnut',
        data: {
            labels: poStatusData.map(item => item.status.replace(/_/g, ' ').toUpperCase()),
            datasets: [{
                data: poStatusData.map(item => item.total),
                backgroundColor: [
                    colors.primary,
                    colors.warning,
                    colors.success,
                    colors.info,
                    colors.danger,
                    colors.purple,
                    colors.pink,
                    colors.gray
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // 3. Tagihan Payment Status
    const tagihanStatusData = {!! json_encode($tagihanPaymentStatus) !!};
    new Chart(document.getElementById('tagihanStatusChart'), {
        type: 'pie',
        data: {
            labels: tagihanStatusData.map(item => item.status.replace(/_/g, ' ').toUpperCase()),
            datasets: [{
                data: tagihanStatusData.map(item => item.total),
                backgroundColor: [
                    colors.gray,
                    colors.warning,
                    colors.info,
                    colors.success,
                    colors.danger
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // 4. Monthly PO Value
    const monthlyValueData = {!! json_encode($monthlyPOValue) !!};
    new Chart(document.getElementById('monthlyValueChart'), {
        type: 'bar',
        data: {
            labels: monthlyValueData.map(item => {
                const [year, month] = item.month.split('-');
                return new Date(year, month - 1).toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Total Value (Rp)',
                data: monthlyValueData.map(item => item.total_value),
                backgroundColor: colors.primary,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    },
                    backgroundColor: '#1f2937',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#374151',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                        }
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // 5. Aging Analysis
    const agingData = {!! json_encode($agingAnalysis) !!};
    new Chart(document.getElementById('agingChart'), {
        type: 'bar',
        data: {
            labels: ['Current', '1-30 Days', '31-60 Days', '61-90 Days', '90+ Days'],
            datasets: [{
                label: 'Outstanding (Rp)',
                data: [
                    agingData.current,
                    agingData.overdue_1_30,
                    agingData.overdue_31_60,
                    agingData.overdue_61_90,
                    agingData.overdue_90_plus
                ],
                backgroundColor: [
                    colors.success,
                    colors.info,
                    colors.warning,
                    colors.danger,
                    '#7f1d1d'
                ],
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.x.toLocaleString('id-ID');
                        }
                    },
                    backgroundColor: '#1f2937',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#374151',
                    borderWidth: 1
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                        }
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // 6. Top Suppliers
    const topSuppliersData = {!! json_encode($topSuppliers) !!};
    new Chart(document.getElementById('topSuppliersChart'), {
        type: 'bar',
        data: {
            labels: topSuppliersData.map(item => item.supplier ? item.supplier.nama_supplier.substring(0, 20) : 'Unknown'),
            datasets: [{
                label: 'Total Value (Rp)',
                data: topSuppliersData.map(item => item.total_value),
                backgroundColor: colors.info,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const item = topSuppliersData[context.dataIndex];
                            return [
                                'Total Value: Rp ' + context.parsed.x.toLocaleString('id-ID'),
                                'Total PO: ' + item.total_po
                            ];
                        }
                    },
                    backgroundColor: '#1f2937',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#374151',
                    borderWidth: 1
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                        }
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Auto-refresh stats setiap 5 menit
    setInterval(function() {
        fetch('{{ route("dashboardpo.realtime-stats") }}')
            .then(response => response.json())
            .then(data => {
                console.log('Stats updated:', data);
            })
            .catch(error => console.error('Error updating stats:', error));
    }, 300000); // 5 minutes

    // Download chart function
    function downloadChart(chartId) {
        const canvas = document.getElementById(chartId);
        const url = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = chartId + '-' + new Date().getTime() + '.png';
        link.href = url;
        link.click();
    }
</script>
@endpush