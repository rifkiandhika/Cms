<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\PembayaranTagihan;
use App\Models\PurchaseOrder;
use App\Models\TagihanPo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardPoController extends Controller
{
    public function index()
    {
        // === STATISTIK PO ===
        $poStats = [
            'total' => PurchaseOrder::count(),
            'draft' => PurchaseOrder::where('status', 'draft')->count(),
            'pending_approval' => PurchaseOrder::whereIn('status', [
                'menunggu_persetujuan_kepala_gudang',
                'menunggu_persetujuan_kasir'
            ])->count(),
            'approved' => PurchaseOrder::where('status', 'disetujui')->count(),
            'in_progress' => PurchaseOrder::whereIn('status', [
                'dikirim_ke_supplier',
                'dalam_pengiriman'
            ])->count(),
            'completed' => PurchaseOrder::where('status', 'selesai')->count(),
            'rejected' => PurchaseOrder::where('status', 'ditolak')->count(),
            'cancelled' => PurchaseOrder::where('status', 'dibatalkan')->count(),
        ];

        // PO by Type
        $poByType = PurchaseOrder::select('tipe_po', DB::raw('count(*) as total'))
            ->groupBy('tipe_po')
            ->get()
            ->pluck('total', 'tipe_po');

        // PO Near Deadline (akan auto-cancel dalam 6 jam)
        $poNearDeadline = PurchaseOrder::whereIn('status', [
            'menunggu_persetujuan_kepala_gudang',
            'menunggu_persetujuan_kasir'
        ])->get()->filter(function($po) {
            return $po->isNearDeadline();
        })->count();

        // PO yang perlu konfirmasi penerimaan
        $poNeedConfirmation = PurchaseOrder::whereIn('status', ['selesai', 'diterima'])
            ->whereNull('tanggal_diterima')
            ->count();

        // PO yang perlu input invoice
        $poNeedInvoice = PurchaseOrder::where('status', 'selesai')
            ->where('tipe_po', 'eksternal')
            ->where(function($query) {
                $query->whereNull('no_invoice')
                    ->orWhereNull('tanggal_invoice')
                    ->orWhereNull('bukti_invoice')
                    ->orWhereNull('bukti_barang');
            })
            ->count();

        // === STATISTIK TAGIHAN ===
        $tagihanStats = [
            'total' => TagihanPo::count(),
            'draft' => TagihanPo::where('status', 'draft')->count(),
            'menunggu_pembayaran' => TagihanPo::where('status', 'menunggu_pembayaran')->count(),
            'dibayar_sebagian' => TagihanPo::where('status', 'dibayar_sebagian')->count(),
            'lunas' => TagihanPo::where('status', 'lunas')->count(),
            'cancelled' => TagihanPo::where('status', 'dibatalkan')->count(),
        ];

        // Total nilai tagihan
        $tagihanNilai = TagihanPo::select(
            DB::raw('SUM(grand_total) as total_tagihan'),
            DB::raw('SUM(total_dibayar) as total_dibayar'),
            DB::raw('SUM(sisa_tagihan) as total_sisa')
        )->first();

        // Tagihan Overdue
        $tagihanOverdue = TagihanPo::overdue()->count();
        $nilaiOverdue = TagihanPo::overdue()->sum('sisa_tagihan');

        // Tagihan Due Soon (7 hari ke depan)
        $tagihanDueSoon = TagihanPo::dueWithinDays(7)->count();
        $nilaiDueSoon = TagihanPo::dueWithinDays(7)->sum('sisa_tagihan');

        // === CHART DATA ===
        
        // 1. PO Trend (30 hari terakhir)
        $poTrend = PurchaseOrder::where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 2. PO Status Distribution
        $poStatusDistribution = PurchaseOrder::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // 3. Tagihan Payment Status
        $tagihanPaymentStatus = TagihanPo::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // 4. Monthly PO Value (6 bulan terakhir)
        $monthlyPOValue = PurchaseOrder::where('created_at', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(grand_total) as total_value'),
                DB::raw('count(*) as total_po')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 5. Tagihan Trend (30 hari terakhir)
        $tagihanTrend = TagihanPo::where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(grand_total) as total_nilai'),
                DB::raw('count(*) as total_tagihan')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 6. Payment Performance (pembayaran per bulan)
        $paymentPerformance = PembayaranTagihan::where('tanggal_bayar', '>=', Carbon::now()->subMonths(6))
            ->where('status_pembayaran', 'diverifikasi')
            ->select(
                DB::raw('DATE_FORMAT(tanggal_bayar, "%Y-%m") as month'),
                DB::raw('SUM(jumlah_bayar) as total_payment'),
                DB::raw('count(*) as total_transactions')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 7. Top Suppliers by PO Value
        $topSuppliers = PurchaseOrder::where('tipe_po', 'eksternal')
            ->whereNotNull('id_supplier')
            ->with('supplier')
            ->select('id_supplier', DB::raw('SUM(grand_total) as total_value'), DB::raw('count(*) as total_po'))
            ->groupBy('id_supplier')
            ->orderByDesc('total_value')
            ->limit(10)
            ->get();

        // 8. Aging Analysis - Tagihan berdasarkan umur
        $agingAnalysis = [
            'current' => TagihanPo::unpaid()
                ->where('tanggal_jatuh_tempo', '>=', Carbon::now())
                ->sum('sisa_tagihan'),
            'overdue_1_30' => TagihanPo::unpaid()
                ->whereBetween('tanggal_jatuh_tempo', [Carbon::now()->subDays(30), Carbon::now()->subDay()])
                ->sum('sisa_tagihan'),
            'overdue_31_60' => TagihanPo::unpaid()
                ->whereBetween('tanggal_jatuh_tempo', [Carbon::now()->subDays(60), Carbon::now()->subDays(31)])
                ->sum('sisa_tagihan'),
            'overdue_61_90' => TagihanPo::unpaid()
                ->whereBetween('tanggal_jatuh_tempo', [Carbon::now()->subDays(90), Carbon::now()->subDays(61)])
                ->sum('sisa_tagihan'),
            'overdue_90_plus' => TagihanPo::unpaid()
                ->where('tanggal_jatuh_tempo', '<', Carbon::now()->subDays(90))
                ->sum('sisa_tagihan'),
        ];

        // === ALERTS & NOTIFICATIONS ===
        $alerts = [
            'po_near_deadline' => $poNearDeadline,
            'po_need_confirmation' => $poNeedConfirmation,
            'po_need_invoice' => $poNeedInvoice,
            'tagihan_overdue' => $tagihanOverdue,
            'tagihan_due_soon' => $tagihanDueSoon,
        ];

        // === RECENT ACTIVITIES ===
        $recentPO = PurchaseOrder::with(['karyawanPemohon', 'supplier'])
            ->latest()
            ->limit(10)
            ->get();

        $recentTagihan = TagihanPo::with(['purchaseOrder', 'supplier'])
            ->latest()
            ->limit(10)
            ->get();

        $recentPayments = PembayaranTagihan::with(['tagihan.purchaseOrder', 'karyawanInput'])
            ->where('status_pembayaran', 'diverifikasi')
            ->latest('tanggal_bayar')
            ->limit(10)
            ->get();

        return view('dashboard.po.index', compact(
            'poStats',
            'poByType',
            'poNearDeadline',
            'poNeedConfirmation',
            'poNeedInvoice',
            'tagihanStats',
            'tagihanNilai',
            'tagihanOverdue',
            'nilaiOverdue',
            'tagihanDueSoon',
            'nilaiDueSoon',
            'poTrend',
            'poStatusDistribution',
            'tagihanPaymentStatus',
            'monthlyPOValue',
            'tagihanTrend',
            'paymentPerformance',
            'topSuppliers',
            'agingAnalysis',
            'alerts',
            'recentPO',
            'recentTagihan',
            'recentPayments'
        ));
    }

    /**
     * Get real-time statistics via AJAX
     */
    public function getRealtimeStats()
    {
        return response()->json([
            'po_pending' => PurchaseOrder::whereIn('status', [
                'menunggu_persetujuan_kepala_gudang',
                'menunggu_persetujuan_kasir'
            ])->count(),
            'tagihan_unpaid' => TagihanPo::unpaid()->count(),
            'tagihan_overdue' => TagihanPo::overdue()->count(),
            'total_outstanding' => TagihanPo::unpaid()->sum('sisa_tagihan'),
        ]);
    }

    /**
     * Export dashboard data to Excel
     */
    public function exportData(Request $request)
    {
        // Implementation for export functionality
        // You can use Laravel Excel package
    }
}
