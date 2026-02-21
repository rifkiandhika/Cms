<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\TagihanPo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardPoApiController extends Controller
{
    public function getStats()
    {
        $stats = Cache::remember('dashboard_stats', 300, function () { // Cache for 5 minutes
            return [
                'po' => [
                    'total' => PurchaseOrder::count(),
                    'pending' => PurchaseOrder::whereIn('status', [
                        'menunggu_persetujuan_kepala_gudang',
                        'menunggu_persetujuan_kasir'
                    ])->count(),
                    'approved' => PurchaseOrder::where('status', 'disetujui')->count(),
                    'in_progress' => PurchaseOrder::whereIn('status', [
                        'dikirim_ke_supplier',
                        'dalam_pengiriman'
                    ])->count(),
                    'completed' => PurchaseOrder::where('status', 'selesai')->count(),
                ],
                'tagihan' => [
                    'total' => TagihanPo::count(),
                    'unpaid' => TagihanPo::unpaid()->count(),
                    'overdue' => TagihanPo::overdue()->count(),
                    'due_soon' => TagihanPo::dueWithinDays(7)->count(),
                    'total_outstanding' => TagihanPo::unpaid()->sum('sisa_tagihan'),
                ],
                'alerts' => [
                    'critical' => $this->getCriticalAlerts(),
                    'warning' => $this->getWarningAlerts(),
                    'info' => $this->getInfoAlerts(),
                ],
                'timestamp' => now()->toIso8601String(),
            ];
        });

        return response()->json($stats);
    }

    /**
     * Get PO statistics by status
     */
    public function getPoStats()
    {
        $stats = PurchaseOrder::selectRaw('
            status,
            COUNT(*) as count,
            SUM(grand_total) as total_value
        ')
        ->groupBy('status')
        ->get();

        return response()->json($stats);
    }

    /**
     * Get tagihan statistics
     */
    public function getTagihanStats()
    {
        $stats = TagihanPo::selectRaw('
            status,
            COUNT(*) as count,
            SUM(grand_total) as total_tagihan,
            SUM(total_dibayar) as total_dibayar,
            SUM(sisa_tagihan) as total_sisa
        ')
        ->groupBy('status')
        ->get();

        return response()->json($stats);
    }

    /**
     * Get critical alerts
     */
    private function getCriticalAlerts()
    {
        return [
            'overdue_tagihan' => TagihanPo::overdue()->count(),
            'po_expired' => PurchaseOrder::toBeAutoCancelled()->count(),
        ];
    }

    /**
     * Get warning alerts
     */
    private function getWarningAlerts()
    {
        return [
            'due_soon_tagihan' => TagihanPo::dueWithinDays(7)->count(),
            'po_near_deadline' => PurchaseOrder::whereIn('status', [
                'menunggu_persetujuan_kepala_gudang',
                'menunggu_persetujuan_kasir'
            ])->get()->filter(function($po) {
                return $po->isNearDeadline();
            })->count(),
        ];
    }

    /**
     * Get info alerts
     */
    private function getInfoAlerts()
    {
        return [
            'po_need_invoice' => PurchaseOrder::where('status', 'selesai')
                ->where('tipe_po', 'eksternal')
                ->where(function($query) {
                    $query->whereNull('no_invoice')
                        ->orWhereNull('bukti_invoice');
                })
                ->count(),
            'po_need_confirmation' => PurchaseOrder::whereIn('status', ['selesai', 'diterima'])
                ->whereNull('tanggal_diterima')
                ->count(),
        ];
    }

    /**
     * Get chart data for specific period
     */
    public function getChartData(Request $request)
    {
        $period = $request->input('period', 30); // default 30 days

        $poTrend = PurchaseOrder::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $tagihanTrend = TagihanPo::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(grand_total) as total_value')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'po_trend' => $poTrend,
            'tagihan_trend' => $tagihanTrend,
        ]);
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics()
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        $metrics = [
            'current_month' => [
                'po_count' => PurchaseOrder::where('created_at', '>=', $currentMonth)->count(),
                'po_value' => PurchaseOrder::where('created_at', '>=', $currentMonth)->sum('grand_total'),
                'tagihan_count' => TagihanPo::where('created_at', '>=', $currentMonth)->count(),
                'payment_received' => TagihanPo::where('created_at', '>=', $currentMonth)->sum('total_dibayar'),
            ],
            'last_month' => [
                'po_count' => PurchaseOrder::whereBetween('created_at', [$lastMonth, $currentMonth])->count(),
                'po_value' => PurchaseOrder::whereBetween('created_at', [$lastMonth, $currentMonth])->sum('grand_total'),
                'tagihan_count' => TagihanPo::whereBetween('created_at', [$lastMonth, $currentMonth])->count(),
                'payment_received' => TagihanPo::whereBetween('created_at', [$lastMonth, $currentMonth])->sum('total_dibayar'),
            ],
        ];

        // Calculate growth percentage
        $metrics['growth'] = [
            'po_count' => $this->calculateGrowth($metrics['last_month']['po_count'], $metrics['current_month']['po_count']),
            'po_value' => $this->calculateGrowth($metrics['last_month']['po_value'], $metrics['current_month']['po_value']),
            'tagihan_count' => $this->calculateGrowth($metrics['last_month']['tagihan_count'], $metrics['current_month']['tagihan_count']),
            'payment_received' => $this->calculateGrowth($metrics['last_month']['payment_received'], $metrics['current_month']['payment_received']),
        ];

        return response()->json($metrics);
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth($old, $new)
    {
        if ($old == 0) {
            return $new > 0 ? 100 : 0;
        }
        return round((($new - $old) / $old) * 100, 2);
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache()
    {
        Cache::forget('dashboard_stats');
        return response()->json(['message' => 'Cache cleared successfully']);
    }
}
