{{-- Sub Tabs untuk External: PO, GR, Invoice, Invoice Retur, Ditolak --}}
<ul class="nav nav-tabs nav-tabs-sub mb-3" id="externalSubTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="external-po-tab" data-bs-toggle="tab" data-bs-target="#external-po-content" 
                type="button" role="tab">
            <i class="ri-file-list-line me-2"></i>Purchase Order
            <span class="badge bg-primary ms-2">
                {{ $purchaseOrders->filter(function($po) {
                    // PO yang belum ada GR dan belum ditolak dan tidak ada retur
                    return is_null($po->no_gr) && $po->status != 'ditolak' && !$po->hasRetur();
                })->count() }}
            </span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="external-gr-tab" data-bs-toggle="tab" data-bs-target="#external-gr-content" 
                type="button" role="tab">
            <i class="ri-inbox-line me-2"></i>Goods Receipt (GR)
            <span class="badge bg-info ms-2">
                {{ $purchaseOrders->filter(function($po) {
                    // GR yang belum invoice lengkap, belum ditolak, dan tidak ada retur
                    return $po->no_gr && 
                           (!$po->no_invoice || !$po->isInvoiceComplete()) && 
                           $po->status != 'ditolak' && 
                           !$po->hasRetur();
                })->count() }}
            </span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="external-invoice-tab" data-bs-toggle="tab" data-bs-target="#external-invoice-content" 
                type="button" role="tab">
            <i class="ri-file-text-line me-2"></i>Invoice
            <span class="badge bg-success ms-2">
                {{ $purchaseOrders->filter(function($po) {
                    // Invoice lengkap dan tidak ada retur
                    return $po->isInvoiceComplete() && !$po->hasRetur();
                })->count() }}
            </span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="external-retur-tab" data-bs-toggle="tab" data-bs-target="#external-retur-content" 
                type="button" role="tab">
            <i class="ri-refund-2-line me-2"></i>Retur
            <span class="badge bg-warning ms-2">
                {{ $purchaseOrders->filter(function($po) {
                    // Semua PO yang punya retur (dari GR atau Invoice)
                    return $po->hasRetur();
                })->count() }}
            </span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="external-rejected-tab" data-bs-toggle="tab" data-bs-target="#external-rejected-content" 
                type="button" role="tab">
            <i class="ri-close-circle-line me-2"></i>Ditolak
            <span class="badge bg-danger ms-2">
                {{ $purchaseOrders->where('status', 'ditolak')->count() }}
            </span>
        </button>
    </li>
</ul>

{{-- Filter Section --}}
@include('po.partials.filter-form', ['type' => 'pembelian'])

{{-- Tab Content --}}
<div class="tab-content" id="externalSubTabsContent">
    {{-- Tab PO --}}
    <div class="tab-pane fade show active" id="external-po-content" role="tabpanel">
        @include('po.partials.po-table', [
            'purchaseOrders' => $purchaseOrders->filter(function($po) {
                return is_null($po->no_gr) && $po->status != 'ditolak' && !$po->hasRetur();
            }),
            'tableId' => 'externalPoTable'
        ])
    </div>

    {{-- Tab GR --}}
    <div class="tab-pane fade" id="external-gr-content" role="tabpanel">
        @include('po.partials.gr-table-external', [
            'purchaseOrders' => $purchaseOrders->filter(function($po) {
                return $po->no_gr && 
                       (!$po->no_invoice || !$po->isInvoiceComplete()) && 
                       $po->status != 'ditolak' && 
                       !$po->hasRetur();
            }),
            'tableId' => 'externalGrTable'
        ])
    </div>

    {{-- Tab Invoice --}}
    <div class="tab-pane fade" id="external-invoice-content" role="tabpanel">
        @include('po.partials.invoice-table', [
            'purchaseOrders' => $purchaseOrders->filter(function($po) {
                return $po->isInvoiceComplete() && !$po->hasRetur();
            }),
            'tableId' => 'externalInvoiceTable'
        ])
    </div>

    {{-- Tab Retur (dari GR atau Invoice) --}}
    <div class="tab-pane fade" id="external-retur-content" role="tabpanel">
        @include('po.partials.invoice-retur-table', [
            'purchaseOrders' => $purchaseOrders->filter(function($po) {
                return $po->hasRetur();
            }),
            'tableId' => 'externalReturTable'
        ])
    </div>

    {{-- Tab Ditolak --}}
    <div class="tab-pane fade" id="external-rejected-content" role="tabpanel">
        @include('po.partials.rejected-table', [
            'purchaseOrders' => $purchaseOrders->where('status', 'ditolak'),
            'tableId' => 'externalRejectedTable'
        ])
    </div>
</div>