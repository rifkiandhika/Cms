<?php

namespace App\Http\Controllers\backend;

use App\Exports\GudangReportExport;
use App\Http\Controllers\Controller;
use App\Models\Gudang;
use App\Models\HistoryGudang;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GudangReportController extends Controller
{
    public function index(Request $request)
    {
        // Base query
        $query = HistoryGudang::with([
            'supplier',
            'detailGudang.gudang',
            'detailGudang.barangObat',
            'detailGudang.alkes',
            'detailGudang.reagensia'
        ]);

        // Filters
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('waktu_proses', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('waktu_proses', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('gudang_id')) {
            $query->whereHas('detailGudang', function($q) use ($request) {
                $q->where('gudang_id', $request->gudang_id);
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('barang_type')) {
            $query->whereHas('detailGudang', function($q) use ($request) {
                $q->where('barang_type', $request->barang_type);
            });
        }

        if ($request->filled('no_referensi')) {
            $query->where('no_referensi', 'like', '%' . $request->no_referensi . '%');
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'waktu_proses');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Statistics
        $statistics = $this->getStatistics($request);

        // Pagination
        $perPage = $request->input('per_page', 25);
        
        // Handle export
        if ($request->has('export')) {
            return $this->handleExport($request, $query);
        }

        // Handle print
        if ($request->has('print')) {
            return $this->print($request);
        }

        $historyGudang = $query->paginate($perPage)->appends($request->all());

        // Get filter options
        $gudangs = Gudang::where('status', 'Aktif')->get();
        $suppliers = Supplier::all();
        
        $statusOptions = [
            'penerimaan' => 'Penerimaan',
            'pengiriman' => 'Pengiriman'
        ];

        $barangTypeOptions = [
            'DetailObatRs' => 'Obat',
            'Alkes' => 'Alkes',
            'Reagensia' => 'Reagensia'
        ];

        return view('reports.gudang.index', compact(
            'historyGudang',
            'statistics',
            'gudangs',
            'suppliers',
            'statusOptions',
            'barangTypeOptions'
        ));
    }

    private function getStatistics($request)
    {
        $query = HistoryGudang::query();

        // Apply same filters as main query
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('waktu_proses', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('waktu_proses', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('gudang_id')) {
            $query->whereHas('detailGudang', function($q) use ($request) {
                $q->where('gudang_id', $request->gudang_id);
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $totalPenerimaan = (clone $query)->where('status', 'penerimaan')->sum('jumlah');
        $totalPengiriman = (clone $query)->where('status', 'pengiriman')->sum('jumlah');
        $jumlahPenerimaan = (clone $query)->where('status', 'penerimaan')->count();
        $jumlahPengiriman = (clone $query)->where('status', 'pengiriman')->count();
        $totalTransaksi = $query->count();

        return [
            'total_penerimaan' => $totalPenerimaan,
            'total_pengiriman' => $totalPengiriman,
            'jumlah_penerimaan' => $jumlahPenerimaan,
            'jumlah_pengiriman' => $jumlahPengiriman,
            'total_transaksi' => $totalTransaksi,
            'selisih' => $totalPenerimaan - $totalPengiriman
        ];
    }

    private function handleExport($request, $query)
    {
        $type = $request->input('export');
        
        if ($type === 'excel') {
            return $this->exportExcel($request);
        } elseif ($type === 'pdf') {
            return $this->exportPdf($request);
        }
    }

    public function print(Request $request)
    {
        $query = HistoryGudang::with([
            'supplier',
            'detailGudang.gudang',
            'detailGudang.barangObat',
            'detailGudang.alkes',
            'detailGudang.reagensia'
        ]);

        // Apply filters (sama seperti index)
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('waktu_proses', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('waktu_proses', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('gudang_id')) {
            $query->whereHas('detailGudang', function($q) use ($request) {
                $q->where('gudang_id', $request->gudang_id);
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->input('sort_by', 'waktu_proses');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $historyGudang = $query->get();
        $statistics = $this->getStatistics($request);

        return view('reports.gudang.print', compact('historyGudang', 'statistics'));
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'laporan-gudang-' . date('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new GudangReportExport($request), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $query = HistoryGudang::with([
            'supplier',
            'detailGudang.gudang',
            'detailGudang.barangObat',
            'detailGudang.alkes',
            'detailGudang.reagensia'
        ]);

        // Apply filters
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('waktu_proses', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('waktu_proses', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('gudang_id')) {
            $query->whereHas('detailGudang', function($q) use ($request) {
                $q->where('gudang_id', $request->gudang_id);
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->input('sort_by', 'waktu_proses');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $historyGudang = $query->get();
        $statistics = $this->getStatistics($request);

        $pdf = Pdf::loadView('reports.gudang.pdf', compact('historyGudang', 'statistics'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-gudang-' . date('Y-m-d-His') . '.pdf');
    }
}
