{{-- resources/views/gudang/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Gudang — ' . $gudang->nama_gudang)

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
/* ─── TOKENS ─────────────────────────────────────────── */
:root {
    --c-bg:        #F5F6FA;
    --c-surface:   #FFFFFF;
    --c-border:    #E8EAF0;
    --c-text:      #1A1D2E;
    --c-muted:     #7B7F96;
    --c-accent:    #4F6EF7;
    --c-accent-lt: #EEF1FF;
    --c-green:     #22C55E;
    --c-green-lt:  #DCFCE7;
    --c-amber:     #F59E0B;
    --c-amber-lt:  #FEF3C7;
    --c-red:       #EF4444;
    --c-red-lt:    #FEE2E2;
    --c-purple:    #8B5CF6;
    --c-purple-lt: #EDE9FE;
    --radius:      12px;
    --radius-sm:   8px;
    --shadow:      0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
    --shadow-lg:   0 8px 32px rgba(0,0,0,.12);
    font-family: 'Plus Jakarta Sans', sans-serif;
}
body { background: var(--c-bg); color: var(--c-text); }

/* ─── PAGE HEADER ────────────────────────────────────── */
.page-header {
    background: var(--c-surface);
    border-bottom: 1px solid var(--c-border);
    padding: 20px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.page-header-left { display: flex; align-items: center; gap: 14px; }
.gudang-icon {
    width: 48px; height: 48px; border-radius: var(--radius);
    background: var(--c-accent-lt); color: var(--c-accent);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.page-title { font-size: 1.25rem; font-weight: 800; margin: 0; line-height: 1.2; }
.page-subtitle { font-size: 0.8rem; color: var(--c-muted); margin: 3px 0 0; }

/* ─── STAT CARDS ─────────────────────────────────────── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 14px;
    padding: 20px 28px 0;
}
.stat-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    padding: 16px 18px;
    display: flex; align-items: center; gap: 14px;
}
.stat-icon {
    width: 40px; height: 40px; border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.stat-val { font-size: 1.4rem; font-weight: 800; line-height: 1; }
.stat-lbl { font-size: 0.75rem; color: var(--c-muted); margin-top: 3px; }

/* ─── PRODUCT TABLE ──────────────────────────────────── */
.section-wrap { padding: 20px 28px 28px; }
.section-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}
.section-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--c-border);
}
.section-title { font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 8px; }

.prod-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.prod-table thead th {
    background: #FAFBFC; padding: 10px 14px;
    text-align: left; font-size: 0.72rem; font-weight: 700;
    color: var(--c-muted); text-transform: uppercase; letter-spacing: .6px;
    border-bottom: 1px solid var(--c-border); white-space: nowrap;
}
.prod-table tbody tr {
    border-bottom: 1px solid var(--c-border);
    transition: background .15s;
}
.prod-table tbody tr:hover { background: #FAFBFF; }
.prod-table tbody tr:last-child { border-bottom: none; }
.prod-table td { padding: 12px 14px; vertical-align: middle; }

.prod-name { font-weight: 600; color: var(--c-text); }
.prod-code {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.72rem; color: var(--c-muted);
    background: var(--c-bg); border-radius: 4px;
    padding: 1px 5px; display: inline-block; margin-top: 2px;
}
.nie-text { font-size: 0.78rem; color: var(--c-muted); }

/* ─── BADGES ─────────────────────────────────────────── */
.badge-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 9px; border-radius: 20px;
    font-size: 0.72rem; font-weight: 700; white-space: nowrap;
}
.badge-normal   { background: var(--c-green-lt);  color: #15803D; }
.badge-menipis  { background: var(--c-amber-lt);  color: #B45309; }
.badge-expired  { background: var(--c-red-lt);    color: #B91C1C; }
.badge-warning  { background: var(--c-purple-lt); color: #6D28D9; }

.stok-val { font-weight: 700; font-size: 0.9rem; }
.stok-pcs { font-size: 0.72rem; color: var(--c-muted); font-family: 'JetBrains Mono', monospace; }

/* ─── ACTION BUTTONS ─────────────────────────────────── */
.btn-icon {
    width: 32px; height: 32px; border-radius: var(--radius-sm);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 15px; transition: all .15s; border: 1px solid transparent;
    cursor: pointer; text-decoration: none;
}
.btn-detail { background: var(--c-accent-lt); color: var(--c-accent); border-color: #C7D2FE; }
.btn-detail:hover { background: var(--c-accent); color: #fff; }
.btn-history { background: var(--c-green-lt); color: #15803D; border-color: #BBF7D0; }
.btn-history:hover { background: var(--c-green); color: #fff; }

/* ─── MODAL BASE ─────────────────────────────────────── */
.modal-content { border: none; border-radius: var(--radius); box-shadow: var(--shadow-lg); }
.modal-header-custom {
    padding: 18px 22px;
    border-bottom: 1px solid var(--c-border);
    display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
}
.modal-body-custom { padding: 20px 22px; max-height: 75vh; overflow-y: auto; }

/* ─── DETAIL MODAL ───────────────────────────────────── */
.product-info-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px; margin-bottom: 20px;
}
.info-chip {
    background: var(--c-bg); border-radius: var(--radius-sm);
    padding: 10px 12px;
}
.info-chip-label { font-size: 0.68rem; font-weight: 700; color: var(--c-muted); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
.info-chip-val { font-size: 0.875rem; font-weight: 600; color: var(--c-text); }

.batch-section-title {
    font-size: 0.78rem; font-weight: 700; color: var(--c-muted);
    text-transform: uppercase; letter-spacing: .5px;
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 10px;
}
.batch-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
.batch-table th {
    background: #FAFBFC; padding: 8px 10px;
    text-align: left; font-size: 0.68rem; font-weight: 700;
    color: var(--c-muted); text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1px solid var(--c-border);
}
.batch-table td {
    padding: 10px 10px; border-bottom: 1px solid var(--c-border);
    vertical-align: middle;
}
.batch-table tr:last-child td { border-bottom: none; }
.batch-table tr.edit-row td { background: var(--c-accent-lt); }
.batch-table .form-control-xs {
    padding: 4px 8px; font-size: 0.8rem;
    border: 1px solid var(--c-border); border-radius: 6px;
    width: 100%; background: #fff;
}
.batch-table .form-control-xs:focus {
    outline: none; border-color: var(--c-accent);
    box-shadow: 0 0 0 2px rgba(79,110,247,.15);
}
.batch-add-row td { background: #f0f3ff; }

/* ─── HISTORY MODAL ──────────────────────────────────── */

/* Toolbar filter */
.hist-toolbar {
    padding: 14px 20px;
    background: #FAFBFC;
    border-bottom: 1px solid var(--c-border);
    display: flex; flex-direction: column; gap: 10px;
}
.hist-toolbar-row {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
}
.hist-toolbar-label {
    font-size: .68rem; font-weight: 700; color: var(--c-muted);
    text-transform: uppercase; letter-spacing: .5px;
    white-space: nowrap; min-width: 52px;
}
.filter-pill {
    padding: 4px 13px; border-radius: 20px; font-size: .75rem; font-weight: 600;
    border: 1.5px solid var(--c-border); background: var(--c-surface);
    cursor: pointer; transition: all .15s; white-space: nowrap; user-select: none;
}
.filter-pill:hover  { border-color: var(--c-accent); color: var(--c-accent); }
.filter-pill.active { background: var(--c-accent); color: #fff; border-color: var(--c-accent); }
.filter-pill[data-tipe="pembelian"].active           { background: #16A34A; border-color: #16A34A; }
.filter-pill[data-tipe="penjualan"].active            { background: var(--c-red); border-color: var(--c-red); }
.filter-pill[data-tipe="retur_dari_customer"].active  { background: #D97706; border-color: #D97706; }
.filter-pill[data-tipe="retur_ke_supplier"].active    { background: var(--c-purple); border-color: var(--c-purple); }
.filter-pill[data-tipe="penyesuaian_masuk"].active    { background: #0891B2; border-color: #0891B2; }
.filter-pill[data-tipe="penyesuaian_keluar"].active   { background: #9F1239; border-color: #9F1239; }

/* ─── SELECT2 SUPPLIER (History) ─────────────────────── */
.hist-supplier-wrap {
    min-width: 220px;
    max-width: 320px;
    flex: 1;
}
.hist-supplier-wrap .select2-container { width: 100% !important; }
.hist-supplier-wrap .select2-container--bootstrap-5 .select2-selection--single {
    height: 32px;
    font-size: .8rem;
    border: 1.5px solid var(--c-border);
    border-radius: 8px;
    background: var(--c-surface);
}
.hist-supplier-wrap .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    line-height: 21px;
    color: var(--c-text);
    padding-left: 10px;
    padding-right: 28px;
}
.hist-supplier-wrap .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
    color: var(--c-muted);
}
.hist-supplier-wrap .select2-container--bootstrap-5.select2-container--focus .select2-selection--single,
.hist-supplier-wrap .select2-container--bootstrap-5 .select2-selection--single:focus {
    border-color: var(--c-accent);
    box-shadow: 0 0 0 2px rgba(79,110,247,.15);
    outline: none;
}
/* Dropdown */
.select2-dropdown { border: 1.5px solid var(--c-border); border-radius: 8px; box-shadow: var(--shadow-lg); font-size: .82rem; }
.select2-results__option { padding: 8px 12px; }
.select2-results__option--highlighted { background: var(--c-accent-lt) !important; color: var(--c-accent) !important; }
.select2-results__option[aria-selected="true"] { background: var(--c-accent) !important; color: #fff !important; }
.select2-search--dropdown .select2-search__field {
    border: 1.5px solid var(--c-border);
    border-radius: 6px;
    padding: 5px 9px;
    font-size: .8rem;
}
.select2-search--dropdown .select2-search__field:focus {
    border-color: var(--c-accent);
    outline: none;
}
/* Tag "aktif" di sebelah kanan label Supplier saat ada pilihan */
.hist-supplier-active-badge {
    display: inline-flex; align-items: center; gap: 4px;
    background: #374151; color: #fff;
    padding: 2px 8px; border-radius: 20px;
    font-size: .7rem; font-weight: 700;
    white-space: nowrap;
}
.hist-supplier-active-badge .btn-clear-sup {
    cursor: pointer; opacity: .7; font-size: .75rem; line-height: 1;
    background: none; border: none; color: inherit; padding: 0 0 0 2px;
}
.hist-supplier-active-badge .btn-clear-sup:hover { opacity: 1; }

.hist-date-range { display: flex; align-items: center; gap: 6px; }
.hist-date-range input {
    padding: 4px 8px; font-size: .75rem; border: 1.5px solid var(--c-border);
    border-radius: 8px; width: 130px; cursor: pointer;
}
.hist-date-range input:focus { outline: none; border-color: var(--c-accent); }

/* Summary strip */
.hist-summary {
    display: flex; gap: 0; border-bottom: 1px solid var(--c-border);
    background: #fff;
}
.hist-sum-item {
    flex: 1; padding: 10px 16px; text-align: center;
    border-right: 1px solid var(--c-border);
}
.hist-sum-item:last-child { border-right: none; }
.hist-sum-val  { font-size: 1.1rem; font-weight: 800; line-height: 1; }
.hist-sum-lbl  { font-size: .68rem; color: var(--c-muted); margin-top: 3px; }
.hist-sum-item.masuk  .hist-sum-val { color: #16A34A; }
.hist-sum-item.keluar .hist-sum-val { color: var(--c-red); }
.hist-sum-item.net    .hist-sum-val { color: var(--c-accent); }

/* List wrapper */
.history-list {
    padding: 14px 18px;
    display: flex; flex-direction: column; gap: 9px;
}

/* ── Ticket card ── */
.mv-card {
    border: 1.5px solid var(--c-border);
    border-radius: var(--radius);
    overflow: hidden;
    position: relative;
    transition: box-shadow .15s, transform .1s;
    background: #fff;
}
.mv-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.09); transform: translateY(-1px); }

.mv-card::before {
    content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
}
.mv-card.masuk::before   { background: #16A34A; }
.mv-card.keluar::before  { background: var(--c-red); }
.mv-card.retur::before   { background: #D97706; }
.mv-card.adjust::before  { background: var(--c-purple); }

.mv-card::after {
    content: ''; position: absolute; right: 110px; top: 50%; transform: translateY(-50%);
    width: 14px; height: 14px; border-radius: 50%;
    background: var(--c-bg); border: 1.5px solid var(--c-border);
    pointer-events: none;
}

.mv-ticket {
    display: flex; align-items: stretch; min-height: 80px;
}
.mv-ticket-left {
    flex: 1; padding: 14px 16px 14px 20px;
    display: flex; align-items: center; gap: 16px;
}
.mv-ticket-divider {
    width: 1px; background: repeating-linear-gradient(
        to bottom, var(--c-border) 0, var(--c-border) 5px, transparent 5px, transparent 10px);
    margin: 10px 0; flex-shrink: 0;
}
.mv-ticket-right {
    width: 115px; padding: 12px 14px; flex-shrink: 0;
    display: flex; flex-direction: column; align-items: flex-end; justify-content: center;
    gap: 4px;
}

.mv-route {
    display: flex; align-items: center; gap: 10px; flex: 1;
}
.mv-route-stop { display: flex; flex-direction: column; gap: 2px; min-width: 80px; }
.mv-route-stop.dest { text-align: right; }
.mv-stop-label { font-size: .62rem; font-weight: 700; color: var(--c-muted); text-transform: uppercase; letter-spacing: .5px; }
.mv-stop-code  { font-size: .95rem; font-weight: 800; letter-spacing: -.3px; }
.mv-stop-name  { font-size: .72rem; color: var(--c-muted); max-width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.mv-route-mid {
    flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px;
}
.mv-route-duration {
    font-size: .65rem; color: var(--c-muted); font-weight: 600; white-space: nowrap;
}
.mv-route-line {
    width: 100%; height: 1px; position: relative;
    background: repeating-linear-gradient(to right,
        var(--c-muted) 0, var(--c-muted) 4px, transparent 4px, transparent 9px);
}
.mv-route-line::before, .mv-route-line::after {
    content: '●'; position: absolute; top: 50%; transform: translateY(-50%);
    color: var(--c-muted); font-size: 7px; line-height: 1;
}
.mv-route-line::before { left: 0; }
.mv-route-line::after  { right: 0; }
.mv-route-tipe-icon { font-size: .8rem; }

.mv-qty-big   { font-size: 1.2rem; font-weight: 800; line-height: 1; }
.mv-qty-big.plus  { color: #16A34A; }
.mv-qty-big.minus { color: var(--c-red); }
.mv-qty-flow  { font-size: .65rem; color: var(--c-muted); text-align: right; }
.mv-tipe-pill {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 7px; border-radius: 20px; font-size: .62rem; font-weight: 700;
    white-space: nowrap;
}

.mv-footer {
    display: flex; align-items: center; gap: 12px;
    padding: 6px 16px 7px 20px;
    background: #FAFBFC; border-top: 1px solid var(--c-border);
    font-size: .72rem; color: var(--c-muted);
    flex-wrap: wrap;
}
.mv-footer-item { display: flex; align-items: center; gap: 4px; }
.mv-footer-item i { font-size: .8rem; }
.mv-footer-note {
    margin-left: auto; font-style: italic;
    max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}

.history-loading, .history-empty {
    text-align: center; padding: 52px 20px; color: var(--c-muted);
}
.history-empty i { font-size: 2.8rem; opacity: .25; display: block; margin-bottom: 12px; }

.hist-pagination {
    padding: 10px 18px; border-top: 1px solid var(--c-border);
    display: flex; align-items: center; justify-content: space-between;
}
.hist-page-btn {
    width: 30px; height: 30px; border-radius: 6px; font-size: .8rem;
    display: inline-flex; align-items: center; justify-content: center;
    border: 1px solid var(--c-border); background: #fff; cursor: pointer;
    transition: all .15s;
}
.hist-page-btn:hover   { background: var(--c-accent-lt); border-color: var(--c-accent); }
.hist-page-btn.current { background: var(--c-accent); color: #fff; border-color: var(--c-accent); font-weight: 700; }

@media (max-width: 600px) {
    .mv-ticket-right { width: 80px; }
    .mv-qty-big { font-size: .95rem; }
    .mv-card::after { display: none; }
    .mv-route-stop.dest { display: none; }
}
</style>
@endpush

@section('content')

{{-- ── PAGE HEADER ─────────────────────────────────────── --}}
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('gudangs.index') }}" class="btn-icon btn-detail me-1">
            <i class="ri-arrow-left-line"></i>
        </a>
        <div class="gudang-icon"><i class="ri-store-2-line"></i></div>
        <div>
            <h1 class="page-title">{{ $gudang->nama_gudang }}</h1>
            <p class="page-subtitle">
                {{ $gudang->kode_gudang }}
                @if($gudang->lokasi) &nbsp;·&nbsp; <i class="ri-map-pin-line"></i> {{ $gudang->lokasi }} @endif
                @if($gudang->penanggung_jawab) &nbsp;·&nbsp; <i class="ri-user-line"></i> {{ $gudang->penanggung_jawab }} @endif
            </p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('gudangs.edit', $gudang->id) }}" class="btn btn-sm btn-outline-primary">
            <i class="ri-edit-line me-1"></i> Edit Gudang
        </a>
        <span class="badge-status {{ $gudang->status === 'Aktif' ? 'badge-normal' : 'badge-expired' }} px-3">
            {{ $gudang->status }}
        </span>
    </div>
</div>

{{-- ── STAT CARDS ──────────────────────────────────────── --}}
@php
    $totalProduk   = $gudang->details->groupBy('produk_id')->count();
    $totalBatch    = $gudang->details->count();
    $totalStok     = $gudang->details->sum('stock_gudang');
    $menipis       = $gudang->details->filter(fn($d) => $d->isBelowMinimum())->groupBy('produk_id')->count();
    $segeraExpired = $gudang->details->filter(fn($d) => $d->isNearExpiry(30))->count();
@endphp

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon" style="background:#EEF1FF;color:#4F6EF7"><i class="ri-medicine-bottle-line"></i></div>
        <div>
            <div class="stat-val">{{ $totalProduk }}</div>
            <div class="stat-lbl">Jenis Produk</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#DCFCE7;color:#15803D"><i class="ri-stack-line"></i></div>
        <div>
            <div class="stat-val">{{ number_format($totalStok) }}</div>
            <div class="stat-lbl">Total Stok (PCS)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FEF3C7;color:#B45309"><i class="ri-alarm-warning-line"></i></div>
        <div>
            <div class="stat-val">{{ $menipis }}</div>
            <div class="stat-lbl">Produk Menipis</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FEE2E2;color:#B91C1C"><i class="ri-time-line"></i></div>
        <div>
            <div class="stat-val">{{ $segeraExpired }}</div>
            <div class="stat-lbl">Batch Exp ≤30hr</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#EDE9FE;color:#6D28D9"><i class="ri-git-branch-line"></i></div>
        <div>
            <div class="stat-val">{{ $totalBatch }}</div>
            <div class="stat-lbl">Total Batch</div>
        </div>
    </div>
</div>

{{-- ── PRODUCT TABLE ───────────────────────────────────── --}}
<div class="section-wrap">
    <div class="section-card">
        <div class="section-head">
            <div class="section-title">
                <i class="ri-box-3-line" style="color:var(--c-accent)"></i>
                Daftar Produk dalam Gudang
            </div>
            <div class="d-flex gap-2">
                <input type="text" id="searchProduk" class="form-control form-control-sm" placeholder="Cari produk..." style="width:200px">
                <a href="{{ route('gudangs.edit', $gudang->id) }}" class="btn btn-sm btn-primary">
                    <i class="ri-add-line me-1"></i>Tambah Produk
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="prod-table" id="produkTable">
                <thead>
                    <tr>
                        <th style="width:36px">#</th>
                        <th>Nama Produk</th>
                        <th>Merk</th>
                        <th>Supplier</th>
                        <th>NIE</th>
                        <th>Total Stok</th>
                        <th>Status</th>
                        <th style="width:80px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $grouped = $gudang->details
                        ->groupBy('produk_id')
                        ->map(function ($details) {
                            return (object)[
                                'produk'          => $details->first()->produk,
                                'suppliers'       => $details->pluck('supplier')->filter()->unique('id'),
                                'totalStok'       => $details->sum('stock_gudang'),
                                'totalBatch'      => $details->count(),
                                'batchMenipis'    => $details->filter(fn($d) => $d->isBelowMinimum())->count(),
                                'batchExpired'    => $details->filter(fn($d) => $d->isNearExpiry(30))->count(),
                                'stokDalamSatuan' => $details->first()->stok_dalam_satuan,
                                'detailIds'       => $details->pluck('id'),
                                'produk_id'       => $details->first()->produk_id,
                            ];
                        })->values();
                @endphp

                @forelse($grouped as $i => $item)
                @php
                    $status = 'normal';
                    $statusLabel = 'Normal';
                    if ($item->batchExpired > 0)   { $status = 'expired'; $statusLabel = 'Segera Exp'; }
                    if ($item->batchMenipis > 0)    { $status = 'menipis'; $statusLabel = 'Stok Menipis'; }
                    if ($item->totalStok === 0)      { $status = 'expired'; $statusLabel = 'Habis'; }
                    $supplierNames = $item->suppliers->pluck('nama_supplier')->join(', ') ?: '-';
                @endphp
                <tr class="prod-row"
                    data-search="{{ strtolower($item->produk->nama_produk . ' ' . $item->produk->merk . ' ' . $item->produk->nie) }}"
                    data-produk-id="{{ $item->produk_id }}"
                    data-gudang-id="{{ $gudang->id }}">
                    <td>
                        <span style="font-size:.75rem;color:var(--c-muted);font-weight:600">{{ $i + 1 }}</span>
                    </td>
                    <td>
                        <div class="prod-name">{{ $item->produk->nama_produk ?? '-' }}</div>
                        <div class="prod-code">{{ $item->produk->kode_produk ?? '-' }}</div>
                        <div style="font-size:.72rem;color:var(--c-muted);margin-top:2px">
                            {{ $item->totalBatch }} batch
                        </div>
                    </td>
                    <td><span style="font-size:.85rem">{{ $item->produk->merk ?? '-' }}</span></td>
                    <td>
                        <span style="font-size:.82rem;color:var(--c-muted)">{{ $supplierNames }}</span>
                    </td>
                    <td>
                        <span class="nie-text">{{ $item->produk->nie ?? '-' }}</span>
                    </td>
                    <td>
                        <div class="stok-val">{{ number_format($item->totalStok) }}</div>
                        <div class="stok-pcs">= {{ $item->stokDalamSatuan }}</div>
                    </td>
                    <td>
                        <span class="badge-status badge-{{ $status }}">
                            @if($status === 'normal')      <i class="ri-checkbox-circle-fill"></i>
                            @elseif($status === 'menipis') <i class="ri-error-warning-fill"></i>
                            @else                          <i class="ri-close-circle-fill"></i>
                            @endif
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn-icon btn-detail"
                                    title="Detail & Batch"
                                    onclick="openDetailModal('{{ $item->produk_id }}', '{{ $gudang->id }}')">
                                <i class="ri-stack-line"></i>
                            </button>
                            <button class="btn-icon btn-history"
                                    title="History Mutasi"
                                    onclick="openHistoryModal('{{ $item->produk_id }}', '{{ $gudang->id }}', '{{ addslashes($item->produk->nama_produk) }}')">
                                <i class="ri-history-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div style="text-align:center;padding:48px;color:var(--c-muted)">
                            <i class="ri-inbox-line" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:8px"></i>
                            Belum ada produk di gudang ini.
                            <a href="{{ route('gudangs.edit', $gudang->id) }}" style="color:var(--c-accent)">Tambah produk</a>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL DETAIL
     ══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header-custom">
                <div>
                    <div style="font-size:1rem;font-weight:800" id="detailProdukNama">—</div>
                    <div style="font-size:.78rem;color:var(--c-muted);margin-top:2px" id="detailProdukMeta">—</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div style="padding:16px 22px 0; border-bottom:1px solid var(--c-border)" id="detailInfoArea">
                <div class="product-info-grid" id="detailInfoGrid"></div>
            </div>

            <div class="modal-body-custom">
                <div class="batch-section-title">
                    <span><i class="ri-git-branch-line me-1"></i>Stok per Batch</span>
                    <button class="btn btn-sm btn-primary" id="btnTambahBatch">
                        <i class="ri-add-line me-1"></i>Tambah Batch
                    </button>
                </div>

                <div id="batchTableWrap">
                    <table class="batch-table" id="batchTable">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th>No Batch</th>
                                <th style="text-align:center">Stok (PCS)</th>
                                <th>Min</th>
                                <th>Tgl Masuk</th>
                                <th>Tgl Produksi</th>
                                <th>Tgl Kadaluarsa</th>
                                <th>Lokasi Rak</th>
                                <th>Kondisi</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="batchTbody"></tbody>
                    </table>
                </div>

                <div id="batchLoading" class="history-loading" style="display:none">
                    <i class="ri-loader-4-line ri-spin" style="font-size:1.5rem"></i>
                    <div class="mt-2">Memuat data batch...</div>
                </div>
            </div>

            <div class="modal-footer" style="border-top:1px solid var(--c-border);padding:12px 22px">
                <span style="font-size:.78rem;color:var(--c-muted)" id="detailSaveStatus"></span>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL HISTORY
     ══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalHistory" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header-custom">
                <div>
                    <div style="font-size:1rem;font-weight:800">
                        <i class="ri-history-line me-1" style="color:var(--c-green)"></i>
                        Riwayat Mutasi Stok
                    </div>
                    <div style="font-size:.78rem;color:var(--c-muted);margin-top:2px" id="historyProdukNama">—</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="hist-toolbar">

                {{-- ── Filter Tipe ── --}}
                <div class="hist-toolbar-row">
                    <span class="hist-toolbar-label">Tipe</span>
                    <div id="historyFilterTipe" style="display:flex;gap:6px;flex-wrap:wrap">
                        <span class="filter-pill active" data-tipe="">Semua</span>
                        <span class="filter-pill" data-tipe="pembelian"><i class="ri-shopping-cart-line me-1"></i>Pembelian</span>
                        <span class="filter-pill" data-tipe="penjualan"><i class="ri-store-line me-1"></i>Penjualan</span>
                        <span class="filter-pill" data-tipe="retur_dari_customer"><i class="ri-arrow-go-back-line me-1"></i>Retur dr Customer</span>
                        <span class="filter-pill" data-tipe="retur_ke_supplier"><i class="ri-arrow-go-forward-line me-1"></i>Retur ke Supplier</span>
                        <span class="filter-pill" data-tipe="penyesuaian_masuk"><i class="ri-add-circle-line me-1"></i>Penyesuaian +</span>
                        <span class="filter-pill" data-tipe="penyesuaian_keluar"><i class="ri-subtract-line me-1"></i>Penyesuaian -</span>
                        {{-- <span class="filter-pill" data-tipe="transfer_masuk"><i class="ri-arrow-right-circle-line me-1"></i>Transfer Masuk</span>
                        <span class="filter-pill" data-tipe="transfer_keluar"><i class="ri-arrow-left-circle-line me-1"></i>Transfer Keluar</span> --}}
                    </div>
                </div>

                {{-- ── Filter Supplier — Select2 ── --}}
                <div class="hist-toolbar-row">
                    <span class="hist-toolbar-label">Supplier</span>
                    <div class="hist-supplier-wrap">
                        <select id="historySupplierSelect">
                            <option value="">— Semua Supplier —</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ── Filter Periode ── --}}
                <div class="hist-toolbar-row">
                    <span class="hist-toolbar-label">Periode</span>
                    <div class="hist-date-range">
                        <input type="date" id="histDari"   placeholder="Dari">
                        <span style="color:var(--c-muted);font-size:.8rem">s/d</span>
                        <input type="date" id="histSampai" placeholder="Sampai">
                        <button class="btn btn-sm btn-primary py-1 px-3" id="btnHistApply" style="font-size:.75rem">
                            <i class="ri-filter-line me-1"></i>Terapkan
                        </button>
                        <button class="btn btn-sm btn-outline-secondary py-1 px-2" id="btnHistReset" style="font-size:.75rem">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>
                </div>

            </div>

            <div class="hist-summary" id="histSummary">
                <div class="hist-sum-item masuk">
                    <div class="hist-sum-val" id="sumMasuk">—</div>
                    <div class="hist-sum-lbl">Total Masuk (PCS)</div>
                </div>
                <div class="hist-sum-item keluar">
                    <div class="hist-sum-val" id="sumKeluar">—</div>
                    <div class="hist-sum-lbl">Total Keluar (PCS)</div>
                </div>
                <div class="hist-sum-item net">
                    <div class="hist-sum-val" id="sumNet">—</div>
                    <div class="hist-sum-lbl">Net Perubahan</div>
                </div>
                <div class="hist-sum-item" style="color:var(--c-muted)">
                    <div class="hist-sum-val" id="sumTotal" style="color:var(--c-text)">—</div>
                    <div class="hist-sum-lbl">Jumlah Transaksi</div>
                </div>
            </div>

            <div id="historyListWrap" style="max-height:55vh;overflow-y:auto">
                <div class="history-loading" id="historyLoading">
                    <i class="ri-loader-4-line ri-spin" style="font-size:2rem"></i>
                    <div class="mt-2" style="font-size:.85rem">Memuat riwayat...</div>
                </div>
                <div class="history-list" id="historyList" style="display:none"></div>
                <div class="history-empty" id="historyEmpty" style="display:none">
                    <i class="ri-file-list-3-line"></i>
                    <div>Belum ada riwayat mutasi</div>
                    <div style="font-size:.78rem;margin-top:4px">Coba ubah filter atau periode</div>
                </div>
            </div>

            <div class="hist-pagination" id="historyPagination" style="display:none">
                <span style="font-size:.75rem;color:var(--c-muted)" id="historyMeta"></span>
                <div style="display:flex;gap:4px" id="historyPageBtns"></div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// ══════════════════════════════════════════════════════════
// DATA & STATE
// ══════════════════════════════════════════════════════════
const SUPPLIERS      = @json($suppliers->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama_supplier]));
const GUDANG_ID_PAGE = '{{ $gudang->id }}';

let currentProdukId = null;
let currentGudangId = null;

// ══════════════════════════════════════════════════════════
// SELECT2 SUPPLIER — inisialisasi saat DOM siap
// ══════════════════════════════════════════════════════════
$(function () {
    $('#historySupplierSelect').select2({
        theme:       'bootstrap-5',
        width:       '100%',
        placeholder: '— Semua Supplier —',
        allowClear:  true,
        language: {
            noResults:  () => 'Supplier tidak ditemukan',
            searching:  () => 'Mencari...',
        },
        // Dropdown muncul di dalam modal agar tidak terpotong
        dropdownParent: $('#modalHistory'),
    });

    // Trigger load saat nilai berubah
    $('#historySupplierSelect').on('change', function () {
        historySupplier = $(this).val() ?? '';
        historyPage     = 1;
        loadHistory();
    });
});

// ══════════════════════════════════════════════════════════
// SEARCH TABLE
// ══════════════════════════════════════════════════════════
document.getElementById('searchProduk').addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.prod-row').forEach(tr => {
        tr.style.display = (!q || tr.dataset.search.includes(q)) ? '' : 'none';
    });
});

// ══════════════════════════════════════════════════════════
// MODAL DETAIL
// ══════════════════════════════════════════════════════════
function openDetailModal(produkId, gudangId) {
    currentProdukId = produkId;
    currentGudangId = gudangId;

    document.getElementById('detailProdukNama').textContent = '…';
    document.getElementById('detailProdukMeta').textContent = '';
    document.getElementById('detailInfoGrid').innerHTML     = '';
    document.getElementById('batchTbody').innerHTML         = '';
    document.getElementById('batchLoading').style.display   = 'block';
    document.getElementById('batchTableWrap').style.display = 'none';
    document.getElementById('detailSaveStatus').textContent = '';

    const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
    modal.show();

    fetch(`/api/gudang/produk/${produkId}/detail?gudang_id=${gudangId}&include_produk=1`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('batchLoading').style.display   = 'none';
            document.getElementById('batchTableWrap').style.display = '';

            if (!data.length) {
                renderBatchEmpty();
                return;
            }

            const produk = data[0]?.produk;
            renderProdukInfo(produk);
            renderBatchRows(data);
        })
        .catch(() => {
            document.getElementById('batchLoading').style.display   = 'none';
            document.getElementById('batchTableWrap').style.display = '';
            renderBatchEmpty('Gagal memuat data.');
        });
}

function renderProdukInfo(produk) {
    if (!produk) return;
    document.getElementById('detailProdukNama').textContent = produk.nama_produk;
    document.getElementById('detailProdukMeta').textContent =
        `${produk.kode_produk} · ${produk.jenis ?? '-'} · ${produk.merk ?? '-'}`;

    const chips = [
        { label: 'Kode Produk', val: produk.kode_produk ?? '-' },
        { label: 'Jenis',       val: produk.jenis ?? '-' },
        { label: 'Merk',        val: produk.merk ?? '-' },
        { label: 'NIE',         val: produk.nie ?? '-' },
        { label: 'Deskripsi',   val: produk.deskripsi ?? '-' },
    ];
    document.getElementById('detailInfoGrid').innerHTML = chips.map(c => `
        <div class="info-chip">
            <div class="info-chip-label">${c.label}</div>
            <div class="info-chip-val">${c.val}</div>
        </div>
    `).join('');
}

function fmtDate(val) {
    if (!val) return '—';
    const d = new Date(val);
    if (isNaN(d)) return val;
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function supplierOpts(selectedId = '') {
    let o = '<option value="">— Tanpa —</option>';
    SUPPLIERS.forEach(s => {
        o += `<option value="${s.id}" ${String(s.id) === String(selectedId) ? 'selected' : ''}>${s.nama}</option>`;
    });
    return o;
}

function kondisiOpts(selected = 'Baik') {
    return ['Baik', 'Rusak', 'Kadaluarsa'].map(k =>
        `<option value="${k}" ${k === selected ? 'selected' : ''}>${k}</option>`
    ).join('');
}

function renderBatchRows(batches) {
    document.getElementById('batchTbody').innerHTML = batches.map(b => buildBatchViewRow(b)).join('');
}

function buildBatchViewRow(b) {
    const expClass = b.tanggal_kadaluarsa && new Date(b.tanggal_kadaluarsa) <= new Date(Date.now() + 30 * 86400000)
        ? 'color:var(--c-red);font-weight:700' : '';
    const supNama = SUPPLIERS.find(s => String(s.id) === String(b.supplier_id))?.nama ?? '—';
    return `
    <tr data-detail-id="${b.id}">
        <td>${supNama}</td>
        <td><code style="font-size:.78rem">${b.no_batch ?? '—'}</code></td>
        <td style="text-align:center">
            <strong style="font-size:.95rem">${Number(b.stock_gudang).toLocaleString('id')}</strong>
            <div style="font-size:.68rem;color:var(--c-muted)">${b.stok_dalam_satuan ?? ''}</div>
        </td>
        <td>${b.min_persediaan ?? 0}</td>
        <td style="font-size:.78rem">${fmtDate(b.tanggal_masuk)}</td>
        <td style="font-size:.78rem">${fmtDate(b.tanggal_produksi)}</td>
        <td style="font-size:.78rem;${expClass}">${fmtDate(b.tanggal_kadaluarsa)}</td>
        <td style="font-size:.78rem">${b.lokasi_rak ?? '—'}</td>
        <td>
            <span class="badge-status ${b.kondisi === 'Baik' ? 'badge-normal' : b.kondisi === 'Rusak' ? 'badge-menipis' : 'badge-expired'}"
                  style="font-size:.68rem;padding:2px 7px">
                ${b.kondisi}
            </span>
        </td>
        <td>
            <div class="d-flex gap-1">
                <button class="btn-icon btn-detail" title="Edit" onclick="editBatchRow('${b.id}')">
                    <i class="ri-edit-line"></i>
                </button>
                <button class="btn-icon" style="background:var(--c-red-lt);color:var(--c-red);border-color:#FECACA"
                        title="Hapus" onclick="deleteBatch('${b.id}')">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </td>
    </tr>`;
}

function buildBatchEditRow(b) {
    return `
    <tr class="edit-row" data-detail-id="${b.id ?? ''}">
        <td><select class="form-control-xs" name="supplier_id">${supplierOpts(b.supplier_id)}</select></td>
        <td><input class="form-control-xs" name="no_batch" value="${b.no_batch ?? ''}" placeholder="Batch"></td>
        <td><input class="form-control-xs" name="stock_gudang" type="number" min="0" value="${b.stock_gudang ?? 0}" style="width:70px"></td>
        <td><input class="form-control-xs" name="min_persediaan" type="number" min="0" value="${b.min_persediaan ?? 0}" style="width:60px"></td>
        <td><input class="form-control-xs" name="tanggal_masuk" type="date" value="${b.tanggal_masuk ?? ''}"></td>
        <td><input class="form-control-xs" name="tanggal_produksi" type="date" value="${b.tanggal_produksi ?? ''}"></td>
        <td><input class="form-control-xs" name="tanggal_kadaluarsa" type="date" value="${b.tanggal_kadaluarsa ?? ''}"></td>
        <td><input class="form-control-xs" name="lokasi_rak" value="${b.lokasi_rak ?? ''}" placeholder="A1-B2" style="width:70px"></td>
        <td><select class="form-control-xs" name="kondisi">${kondisiOpts(b.kondisi)}</select></td>
        <td>
            <div class="d-flex gap-1">
                <button class="btn btn-success btn-sm py-0 px-2" style="font-size:.75rem"
                        onclick="saveBatchRow(this)"><i class="ri-check-line"></i></button>
                <button class="btn btn-secondary btn-sm py-0 px-2" style="font-size:.75rem"
                        onclick="cancelBatchEdit(this, '${b.id ?? ''}')">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </td>
    </tr>`;
}

function renderBatchEmpty(msg = 'Belum ada batch untuk produk ini.') {
    document.getElementById('batchTbody').innerHTML = `
        <tr><td colspan="10" style="text-align:center;padding:32px;color:var(--c-muted)">${msg}</td></tr>`;
}

document.getElementById('btnTambahBatch').addEventListener('click', function () {
    const today  = new Date().toISOString().split('T')[0];
    const newRow = buildBatchEditRow({
        id: '', supplier_id: '', no_batch: '', stock_gudang: 0, min_persediaan: 0,
        tanggal_masuk: today, tanggal_produksi: '', tanggal_kadaluarsa: '',
        lokasi_rak: '', kondisi: 'Baik',
    });
    const empty = document.querySelector('#batchTbody tr td[colspan]');
    if (empty) empty.closest('tr').remove();
    document.getElementById('batchTbody').insertAdjacentHTML('afterbegin', newRow);
});

function editBatchRow(detailId) {
    const tr = document.querySelector(`#batchTbody tr[data-detail-id="${detailId}"]`);
    if (!tr || tr.classList.contains('edit-row')) return;

    fetch(`/api/gudang/produk/${currentProdukId}/detail?gudang_id=${currentGudangId}&include_produk=0`)
        .then(r => r.json())
        .then(data => {
            const b = data.find(d => String(d.id) === String(detailId));
            if (!b) return;
            tr.outerHTML = buildBatchEditRow(b);
        });
}

function cancelBatchEdit(btn, detailId) {
    const tr = btn.closest('tr');
    if (!detailId) {
        tr.remove();
        if (!document.querySelector('#batchTbody tr[data-detail-id]')) renderBatchEmpty();
        return;
    }
    fetch(`/api/gudang/produk/${currentProdukId}/detail?gudang_id=${currentGudangId}`)
        .then(r => r.json())
        .then(data => {
            const b = data.find(d => String(d.id) === String(detailId));
            if (b) tr.outerHTML = buildBatchViewRow(b);
        });
}

function saveBatchRow(btn) {
    const tr       = btn.closest('tr');
    const detailId = tr.dataset.detailId;
    const isNew    = !detailId;

    const payload = {
        gudang_id:          currentGudangId,
        produk_id:          currentProdukId,
        supplier_id:        tr.querySelector('[name=supplier_id]').value || null,
        no_batch:           tr.querySelector('[name=no_batch]').value || null,
        stock_gudang:       parseInt(tr.querySelector('[name=stock_gudang]').value) || 0,
        min_persediaan:     parseInt(tr.querySelector('[name=min_persediaan]').value) || 0,
        tanggal_masuk:      tr.querySelector('[name=tanggal_masuk]').value || null,
        tanggal_produksi:   tr.querySelector('[name=tanggal_produksi]').value || null,
        tanggal_kadaluarsa: tr.querySelector('[name=tanggal_kadaluarsa]').value || null,
        lokasi_rak:         tr.querySelector('[name=lokasi_rak]').value || null,
        kondisi:            tr.querySelector('[name=kondisi]').value,
    };

    const url    = isNew ? '/api/gudang/batch' : `/api/gudang/batch/${detailId}`;
    const method = isNew ? 'POST' : 'PUT';

    setDetailStatus('Menyimpan…');
    btn.disabled = true;

    fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            tr.outerHTML = buildBatchViewRow(res.data);
            setDetailStatus('✓ Tersimpan', 2000);
            refreshTableRow();
        } else {
            btn.disabled = false;
            setDetailStatus('⚠ ' + (res.message ?? 'Gagal menyimpan'), 4000);
        }
    })
    .catch(() => { btn.disabled = false; setDetailStatus('⚠ Gagal terhubung ke server', 4000); });
}

function deleteBatch(detailId) {
    if (!confirm('Hapus batch ini? Stok akan berkurang.')) return;

    fetch(`/api/gudang/batch/${detailId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': getCsrf() },
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const tr = document.querySelector(`#batchTbody tr[data-detail-id="${detailId}"]`);
            if (tr) tr.remove();
            if (!document.querySelector('#batchTbody tr[data-detail-id]')) renderBatchEmpty();
            setDetailStatus('✓ Batch dihapus', 2000);
            refreshTableRow();
        } else {
            alert(res.message ?? 'Gagal menghapus');
        }
    });
}

function setDetailStatus(msg, clearAfter = 0) {
    const el = document.getElementById('detailSaveStatus');
    el.textContent = msg;
    if (clearAfter) setTimeout(() => el.textContent = '', clearAfter);
}

function refreshTableRow() {
    fetch(`/api/gudang/produk/${currentProdukId}/detail?gudang_id=${currentGudangId}`)
        .then(r => r.json())
        .then(data => {
            if (!data.length) return;
            const totalStok = data.reduce((s, d) => s + (Number(d.stock_gudang) || 0), 0);
            const row = document.querySelector(`tr[data-produk-id="${currentProdukId}"]`);
            if (row) {
                row.querySelectorAll('td')[5].querySelector('.stok-val').textContent = totalStok.toLocaleString('id');
            }
        });
}

// ══════════════════════════════════════════════════════════
// MODAL HISTORY
// ══════════════════════════════════════════════════════════
let historyPage          = 1;
let historyTipe          = '';
let historySupplier      = '';
let historyDari          = '';
let historyCurrentSampai = '';
let historyTotalPage     = 1;

function openHistoryModal(produkId, gudangId, produkNama) {
    currentProdukId      = produkId;
    currentGudangId      = gudangId;
    historyPage          = 1;
    historyTipe          = '';
    historySupplier      = '';
    historyDari          = '';
    historyCurrentSampai = '';

    // Reset filter tipe pills
    document.querySelectorAll('#historyFilterTipe .filter-pill').forEach(p => p.classList.remove('active'));
    document.querySelector('#historyFilterTipe .filter-pill[data-tipe=""]').classList.add('active');

    // Reset Select2 supplier ke "Semua Supplier"
    $('#historySupplierSelect').val('').trigger('change.select2'); // trigger change.select2 agar tidak fire event loadHistory dobel

    // Reset tanggal
    document.getElementById('histDari').value   = '';
    document.getElementById('histSampai').value = '';

    document.getElementById('historyProdukNama').textContent = produkNama;

    const modal = new bootstrap.Modal(document.getElementById('modalHistory'));
    modal.show();
    loadHistory();
}

// ── Filter Tipe (pills) ───────────────────────────────────
document.getElementById('historyFilterTipe').addEventListener('click', function (e) {
    const pill = e.target.closest('.filter-pill');
    if (!pill) return;
    this.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
    pill.classList.add('active');
    historyTipe = pill.dataset.tipe;
    historyPage = 1;
    loadHistory();
});

// ── Filter Periode ────────────────────────────────────────
document.getElementById('btnHistApply').addEventListener('click', function () {
    historyDari          = document.getElementById('histDari').value;
    historyCurrentSampai = document.getElementById('histSampai').value;
    historyPage          = 1;
    loadHistory();
});

document.getElementById('btnHistReset').addEventListener('click', function () {
    // Reset periode
    document.getElementById('histDari').value   = '';
    document.getElementById('histSampai').value = '';
    historyDari          = '';
    historyCurrentSampai = '';

    // Reset supplier Select2
    $('#historySupplierSelect').val(null).trigger('change');

    // Reset tipe pills
    document.querySelectorAll('#historyFilterTipe .filter-pill').forEach(p => p.classList.remove('active'));
    document.querySelector('#historyFilterTipe .filter-pill[data-tipe=""]').classList.add('active');
    historyTipe     = '';
    historySupplier = '';
    historyPage     = 1;
    loadHistory();
});

// ── Load History ──────────────────────────────────────────
function loadHistory() {
    document.getElementById('historyLoading').style.display    = 'block';
    document.getElementById('historyList').style.display       = 'none';
    document.getElementById('historyEmpty').style.display      = 'none';
    document.getElementById('historyPagination').style.display = 'none';
    document.getElementById('histSummary').style.opacity       = '.4';

    let url = `/api/gudang/${currentGudangId}/mutasi?produk_id=${currentProdukId}&page=${historyPage}`;
    if (historyTipe)          url += `&tipe=${historyTipe}`;
    if (historySupplier)      url += `&supplier_id=${historySupplier}`;
    if (historyDari)          url += `&dari=${historyDari}`;
    if (historyCurrentSampai) url += `&sampai=${historyCurrentSampai}`;

    fetch(url)
        .then(r => r.json())
        .then(res => {
            document.getElementById('historyLoading').style.display = 'none';
            document.getElementById('histSummary').style.opacity    = '1';

            const items      = res.data ?? [];
            historyTotalPage = res.last_page ?? 1;

            updateSummary(items, res.total ?? items.length);

            if (!items.length) {
                document.getElementById('historyEmpty').style.display = 'block';
                return;
            }

            document.getElementById('historyList').style.display = 'flex';
            document.getElementById('historyList').innerHTML     = items.map(m => buildMovementCard(m)).join('');

            document.getElementById('historyPagination').style.display = 'flex';
            renderHistoryPagination(res);
        })
        .catch(() => {
            document.getElementById('historyLoading').style.display = 'none';
            document.getElementById('historyEmpty').style.display   = 'block';
            document.getElementById('historyEmpty').innerHTML = `
                <i class="ri-wifi-off-line"></i>
                <div>Gagal memuat riwayat</div>
                <div style="font-size:.78rem;margin-top:4px">Periksa koneksi dan coba lagi</div>`;
        });
}

function updateSummary(items, total) {
    let masuk = 0, keluar = 0;
    items.forEach(m => {
        const qty = Number(m.qty_perubahan);
        if (qty > 0) masuk  += qty;
        else         keluar += Math.abs(qty);
    });
    const net = masuk - keluar;
    document.getElementById('sumMasuk').textContent  = masuk.toLocaleString('id');
    document.getElementById('sumKeluar').textContent = keluar.toLocaleString('id');
    document.getElementById('sumNet').textContent    = (net >= 0 ? '+' : '') + net.toLocaleString('id');
    document.getElementById('sumTotal').textContent  = Number(total).toLocaleString('id');
}

// ── Tipe config ───────────────────────────────────────────
const TIPE_CFG = {
    pembelian:           { label: 'Pembelian',         color: '#16A34A', bg: '#DCFCE7', icon: 'ri-shopping-cart-line',     cls: 'masuk',         from: 'Supplier',     to: 'Gudang'        },
    penjualan:           { label: 'Penjualan',          color: '#EF4444', bg: '#FEE2E2', icon: 'ri-store-line',             cls: 'keluar',        from: 'Gudang',       to: 'Customer'      },
    retur_dari_customer: { label: 'Retur dr Customer', color: '#D97706', bg: '#FEF3C7', icon: 'ri-arrow-go-back-line',     cls: 'retur',         from: 'Customer',     to: 'Gudang'        },
    retur_ke_supplier:   { label: 'Retur ke Supplier', color: '#8B5CF6', bg: '#EDE9FE', icon: 'ri-arrow-go-forward-line',  cls: 'retur',         from: 'Gudang',       to: 'Supplier'      },
    penyesuaian_masuk:   { label: 'Penyesuaian +',     color: '#0891B2', bg: '#CFFAFE', icon: 'ri-add-circle-line',        cls: 'adjust masuk',  from: 'Koreksi',      to: 'Gudang'        },
    penyesuaian_keluar:  { label: 'Penyesuaian −',     color: '#9F1239', bg: '#FFE4E6', icon: 'ri-subtract-line',          cls: 'adjust keluar', from: 'Gudang',       to: 'Koreksi'       },
    transfer_masuk:      { label: 'Transfer Masuk',    color: '#16A34A', bg: '#DCFCE7', icon: 'ri-arrow-right-circle-line',cls: 'masuk',         from: 'Gudang Asal',  to: 'Gudang Ini'    },
    transfer_keluar:     { label: 'Transfer Keluar',   color: '#EF4444', bg: '#FEE2E2', icon: 'ri-arrow-left-circle-line', cls: 'keluar',        from: 'Gudang Ini',   to: 'Gudang Tujuan' },
    kadaluarsa:          { label: 'Kadaluarsa',        color: '#EF4444', bg: '#FEE2E2', icon: 'ri-time-line',              cls: 'keluar',        from: 'Gudang',       to: 'Dimusnahkan'   },
    rusak:               { label: 'Rusak/Hilang',      color: '#EF4444', bg: '#FEE2E2', icon: 'ri-error-warning-line',     cls: 'keluar',        from: 'Gudang',       to: 'Dibuang'       },
};

function buildMovementCard(m) {
    const cfg    = TIPE_CFG[m.tipe] ?? { label: m.tipe, color: '#888', bg: '#F3F4F6', icon: 'ri-exchange-line', cls: 'adjust', from: '—', to: '—' };
    const qty    = Number(m.qty_perubahan);
    const masuk  = qty > 0;
    const qtyAbs = Math.abs(qty).toLocaleString('id');

    let fromCode, fromName, toCode, toName;

    if (masuk) {
        if (m.supplier_nama) {
            fromCode = m.supplier_nama.substring(0, 6).toUpperCase();
            fromName = m.supplier_nama;
        } else {
            fromCode = (cfg.from ?? 'SRC').substring(0, 6).toUpperCase();
            fromName = cfg.from ?? 'Sumber';
        }
        toCode = 'STOCK';
        toName = 'Stok Gudang';
    } else {
        fromCode = 'STOCK';
        fromName = 'Stok Gudang';
        const toLabel = cfg.to ?? 'Tujuan';
        toCode = toLabel.substring(0, 6).toUpperCase();
        toName = toLabel;
    }

    const created = new Date(m.created_at);
    const dateStr = created.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    const timeStr = created.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

    return `
    <div class="mv-card ${cfg.cls}">
        <div class="mv-ticket">
            <div class="mv-ticket-left">
                <div class="mv-route">
                    <div class="mv-route-stop">
                        <div class="mv-stop-label">${cfg.from}</div>
                        <div class="mv-stop-code">${escHtml(fromCode)}</div>
                        <div class="mv-stop-name" title="${escHtml(fromName)}">${escHtml(fromName)}</div>
                    </div>

                    <div class="mv-route-mid">
                        <div class="mv-route-duration">${dateStr} · ${timeStr}</div>
                        <div class="mv-route-line"></div>
                        <div class="mv-route-tipe-icon">
                            <span class="mv-tipe-pill" style="background:${cfg.bg};color:${cfg.color}">
                                <i class="${cfg.icon}"></i> ${cfg.label}
                            </span>
                        </div>
                    </div>

                    <div class="mv-route-stop dest">
                        <div class="mv-stop-label">${cfg.to}</div>
                        <div class="mv-stop-code">${escHtml(toCode)}</div>
                        <div class="mv-stop-name" title="${escHtml(toName)}">${escHtml(toName)}</div>
                    </div>
                </div>
            </div>

            <div class="mv-ticket-divider"></div>

            <div class="mv-ticket-right">
                <div class="mv-qty-big ${masuk ? 'plus' : 'minus'}">
                    ${masuk ? '+' : '−'}${qtyAbs}
                </div>
                <div class="mv-qty-flow">${Number(m.qty_sebelum ?? 0).toLocaleString('id')} → ${Number(m.qty_sesudah ?? 0).toLocaleString('id')}</div>
                <div style="font-size:.62rem;color:var(--c-muted);margin-top:2px">PCS</div>
            </div>
        </div>

        <div class="mv-footer">
            ${m.referensi_no  ? `<div class="mv-footer-item"><i class="ri-file-list-line"></i><span style="font-family:monospace;font-size:.7rem">${escHtml(m.referensi_no)}</span></div>` : ''}
            ${m.no_batch      ? `<div class="mv-footer-item"><i class="ri-git-branch-line"></i><strong>${escHtml(m.no_batch)}</strong></div>` : ''}
            ${m.supplier_nama ? `<div class="mv-footer-item"><i class="ri-building-line"></i>${escHtml(m.supplier_nama)}</div>` : ''}
            <div class="mv-footer-item"><i class="ri-user-line"></i>${escHtml(m.karyawan?.nama_lengkap ?? '—')}</div>
            <div class="mv-footer-item"><i class="ri-time-line"></i>${dateStr} ${timeStr}</div>
            ${m.catatan ? `<div class="mv-footer-note" title="${escHtml(m.catatan)}"><i class="ri-chat-1-line"></i> ${escHtml(m.catatan)}</div>` : ''}
        </div>
    </div>`;
}

function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// ── Pagination ────────────────────────────────────────────
function renderHistoryPagination(res) {
    document.getElementById('historyMeta').textContent =
        `${res.from ?? 1}–${res.to ?? (res.data?.length ?? 0)} dari ${res.total ?? 0} mutasi`;

    let btns  = '';
    const total = res.last_page ?? 1;
    const cur   = res.current_page ?? 1;

    for (let p = 1; p <= total; p++) {
        if (total > 7 && p > 2 && p < total - 1 && Math.abs(p - cur) > 1) {
            if (p === 3 || p === total - 2) btns += `<span style="padding:0 4px;color:var(--c-muted)">…</span>`;
            continue;
        }
        btns += `<button class="hist-page-btn ${p === cur ? 'current' : ''}"
                         onclick="goHistoryPage(${p})">${p}</button>`;
    }
    document.getElementById('historyPageBtns').innerHTML = btns;
}

function goHistoryPage(p) {
    historyPage = p;
    document.getElementById('historyListWrap').scrollTop = 0;
    loadHistory();
}
</script>
@endpush