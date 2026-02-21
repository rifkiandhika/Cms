<?php

namespace App\Http\Controllers\backend;

use App\Exports\PurchaseOrderReportExport;
use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderReportController extends Controller
{
    public function index(Request $request)
    {
        // Query builder
        $query = PurchaseOrder::with([
            'karyawanPemohon:id_karyawan,nama_lengkap',
            'supplier:id,nama_supplier',
            'items.produk'
        ]);

        // Filter by Date Range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_permintaan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_permintaan', '<=', $request->tanggal_sampai);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Tipe PO
        if ($request->filled('tipe_po')) {
            $query->where('tipe_po', $request->tipe_po);
        }

        // Filter by Supplier
        if ($request->filled('id_supplier')) {
            $query->where('id_supplier', $request->id_supplier);
        }

        // Filter by Pemohon
        if ($request->filled('id_karyawan_pemohon')) {
            $query->where('id_karyawan_pemohon', $request->id_karyawan_pemohon);
        }

        // Filter by No PO
        if ($request->filled('no_po')) {
            $query->where('no_po', 'like', '%' . $request->no_po . '%');
        }

        // Filter by Unit Pemohon
        if ($request->filled('unit_pemohon')) {
            $query->where('unit_pemohon', $request->unit_pemohon);
        }

        // Filter by Unit Tujuan
        if ($request->filled('unit_tujuan')) {
            $query->where('unit_tujuan', $request->unit_tujuan);
        }

        // Filter by Approval Status Kepala Gudang
        if ($request->filled('status_approval_kepala_gudang')) {
            $query->where('status_approval_kepala_gudang', $request->status_approval_kepala_gudang);
        }

        // Filter by Approval Status Kasir
        if ($request->filled('status_approval_kasir')) {
            $query->where('status_approval_kasir', $request->status_approval_kasir);
        }

        // Filter by Grand Total Range
        if ($request->filled('grand_total_min')) {
            $query->where('grand_total', '>=', $request->grand_total_min);
        }

        if ($request->filled('grand_total_max')) {
            $query->where('grand_total', '<=', $request->grand_total_max);
        }

        // Clone query for statistics before pagination
        $statisticsQuery = clone $query;

        // Sorting
        $sortBy = $request->get('sort_by', 'tanggal_permintaan');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Get data
        $purchaseOrders = $query->paginate($request->get('per_page', 25))->withQueryString();

        // Calculate Statistics
        $statistics = [
            'total_po' => $statisticsQuery->count(),
            'total_nilai' => $statisticsQuery->sum('grand_total'),
            'total_diterima' => $statisticsQuery->sum('grand_total_diterima'),
            'total_outstanding' => $statisticsQuery->sum('grand_total') - $statisticsQuery->sum('grand_total_diterima'),
            
            // By Status
            'draft' => (clone $statisticsQuery)->where('status', 'draft')->count(),
            'menunggu_persetujuan' => (clone $statisticsQuery)->whereIn('status', [
                'menunggu_persetujuan_kepala_gudang',
                'menunggu_persetujuan_kasir'
            ])->count(),
            'disetujui' => (clone $statisticsQuery)->where('status', 'disetujui')->count(),
            'dalam_proses' => (clone $statisticsQuery)->whereIn('status', [
                'dikirim_ke_supplier',
                'dalam_pengiriman'
            ])->count(),
            'diterima' => (clone $statisticsQuery)->where('status', 'diterima')->count(),
            'ditolak' => (clone $statisticsQuery)->where('status', 'ditolak')->count(),
            'dibatalkan' => (clone $statisticsQuery)->where('status', 'dibatalkan')->count(),
            
            // By Type
            'internal' => (clone $statisticsQuery)->where('tipe_po', 'internal')->count(),
            'eksternal' => (clone $statisticsQuery)->where('tipe_po', 'eksternal')->count(),
            
            // Financial
            'nilai_internal' => (clone $statisticsQuery)->where('tipe_po', 'internal')->sum('grand_total'),
            'nilai_eksternal' => (clone $statisticsQuery)->where('tipe_po', 'eksternal')->sum('grand_total'),
        ];

        // Get filter options
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();

        // Status options
        $statusOptions = [
            'draft' => 'Draft',
            'menunggu_persetujuan_kepala_gudang' => 'Menunggu Persetujuan Kepala Gudang',
            'menunggu_persetujuan_kasir' => 'Menunggu Persetujuan Kasir',
            'disetujui' => 'Disetujui',
            'dikirim_ke_supplier' => 'Dikirim ke Supplier',
            'dalam_pengiriman' => 'Dalam Pengiriman',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan'
        ];

        // Handle Export
        if ($request->has('export')) {
            $exportData = $query->get();
            
            // Tambahkan supplier name ke filters jika ada
            $filtersWithSupplier = $request->all();
            if ($request->filled('id_supplier')) {
                $supplier = Supplier::find($request->id_supplier);
                $filtersWithSupplier['supplier_name'] = $supplier ? $supplier->nama_supplier : null;
            }
            
            if ($request->export === 'excel') {
                return Excel::download(
                    new PurchaseOrderReportExport($exportData, $statistics, $filtersWithSupplier),
                    'purchase-order-report-' . date('Y-m-d') . '.xlsx'
                );
            }
            
            if ($request->export === 'pdf') {
                $pdf = Pdf::loadView('reports.purchase-orders.pdf', [
                    'purchaseOrders' => $exportData,
                    'statistics' => $statistics,
                    'filters' => $filtersWithSupplier
                ]);
                
                return $pdf->download('purchase-order-report-' . date('Y-m-d') . '.pdf');
            }
        }

        // Handle Print
        if ($request->has('print')) {
            $printData = $query->get();
            
            $filtersWithSupplier = $request->all();
            if ($request->filled('id_supplier')) {
                $supplier = Supplier::find($request->id_supplier);
                $filtersWithSupplier['supplier_name'] = $supplier ? $supplier->nama_supplier : null;
            }
            
            return view('reports.purchase-orders.print', [
                'purchaseOrders' => $printData,
                'statistics' => $statistics,
                'filters' => $filtersWithSupplier
            ]);
        }

        // Handle Print
        if ($request->has('print')) {
            $printData = $query->get();
            
            return view('reports.purchase-orders.print', [
                'purchaseOrders' => $printData,
                'statistics' => $statistics,
                'filters' => $request->all()
            ]);
        }

        return view('reports.purchase-orders.index', compact(
            'purchaseOrders',
            'statistics',
            'suppliers',
            'karyawans',
            'statusOptions'
        ));
    }

    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with([
            'karyawanPemohon',
            'supplier',
            'items.produk',
            'kepalaGudangApproval',
            'kasirApproval',
            'karyawanPengirim'
        ])->findOrFail($id);

        return view('reports.purchase-orders.show', compact('purchaseOrder'));
    }

    public function summary(Request $request)
    {
        // Summary by Period (Daily, Weekly, Monthly, Yearly)
        $period = $request->get('period', 'monthly');
        
        $query = PurchaseOrder::query();

        // Apply date filters
        if ($request->filled('year')) {
            $query->whereYear('tanggal_permintaan', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('tanggal_permintaan', $request->month);
        }

        $summaryData = [];

        switch ($period) {
            case 'daily':
                $summaryData = $query->selectRaw('
                    DATE(tanggal_permintaan) as period,
                    COUNT(*) as total_po,
                    SUM(grand_total) as total_nilai,
                    SUM(CASE WHEN status = "diterima" THEN 1 ELSE 0 END) as po_diterima,
                    SUM(CASE WHEN tipe_po = "internal" THEN 1 ELSE 0 END) as po_internal,
                    SUM(CASE WHEN tipe_po = "eksternal" THEN 1 ELSE 0 END) as po_eksternal
                ')
                ->groupBy('period')
                ->orderBy('period', 'desc')
                ->get();
                break;

            case 'weekly':
                $summaryData = $query->selectRaw('
                    YEARWEEK(tanggal_permintaan) as period,
                    MIN(DATE(tanggal_permintaan)) as start_date,
                    MAX(DATE(tanggal_permintaan)) as end_date,
                    COUNT(*) as total_po,
                    SUM(grand_total) as total_nilai,
                    SUM(CASE WHEN status = "diterima" THEN 1 ELSE 0 END) as po_diterima,
                    SUM(CASE WHEN tipe_po = "internal" THEN 1 ELSE 0 END) as po_internal,
                    SUM(CASE WHEN tipe_po = "eksternal" THEN 1 ELSE 0 END) as po_eksternal
                ')
                ->groupBy('period')
                ->orderBy('period', 'desc')
                ->get();
                break;

            case 'monthly':
                $summaryData = $query->selectRaw('
                    DATE_FORMAT(tanggal_permintaan, "%Y-%m") as period,
                    COUNT(*) as total_po,
                    SUM(grand_total) as total_nilai,
                    SUM(CASE WHEN status = "diterima" THEN 1 ELSE 0 END) as po_diterima,
                    SUM(CASE WHEN tipe_po = "internal" THEN 1 ELSE 0 END) as po_internal,
                    SUM(CASE WHEN tipe_po = "eksternal" THEN 1 ELSE 0 END) as po_eksternal
                ')
                ->groupBy('period')
                ->orderBy('period', 'desc')
                ->get();
                break;

            case 'yearly':
                $summaryData = $query->selectRaw('
                    YEAR(tanggal_permintaan) as period,
                    COUNT(*) as total_po,
                    SUM(grand_total) as total_nilai,
                    SUM(CASE WHEN status = "diterima" THEN 1 ELSE 0 END) as po_diterima,
                    SUM(CASE WHEN tipe_po = "internal" THEN 1 ELSE 0 END) as po_internal,
                    SUM(CASE WHEN tipe_po = "eksternal" THEN 1 ELSE 0 END) as po_eksternal
                ')
                ->groupBy('period')
                ->orderBy('period', 'desc')
                ->get();
                break;
        }

        return view('reports.purchase-orders.summary', compact('summaryData', 'period'));
    }
}
