<?php

namespace App\Http\Controllers\backend;

use App\Exports\TagihanExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\TagihanPo;
use App\Models\PembayaranTagihan;
use App\Models\Karyawan;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class TagihanPoController extends Controller
{
    /**
     * List semua tagihan dengan lazy loading
     */
    public function index(Request $request)
    {
        // Handle Export Excel
        if ($request->has('export') && $request->export === 'excel') {
            $tipeRelasi = str_contains($request->get('tab', 'aktif_supplier'), 'customer') ? 'customer' : 'supplier';
            return $this->exportExcel($request, $tipeRelasi);
        }

        // Handle Export PDF
        if ($request->has('export') && $request->export === 'pdf') {
            $tipeRelasi = str_contains($request->get('tab', 'aktif_supplier'), 'customer') ? 'customer' : 'supplier';
            return $this->exportPDF($request, $tipeRelasi);
        }

        // Handle Print
        if ($request->has('print')) {
            $tipeRelasi = str_contains($request->get('tab', 'aktif_supplier'), 'customer') ? 'customer' : 'supplier';
            return $this->printView($request, $tipeRelasi);
        }

        // Handle AJAX request for tab data
        if ($request->ajax() || $request->wantsJson()) {
            return $this->loadTabData($request);
        }

        // Get suppliers and customers for filter dropdowns - HANYA FIELD YANG DIPERLUKAN
        $suppliers = Supplier::select('id', 'nama_supplier')
            ->orderBy('nama_supplier')
            ->get();
            
        $customers = Customer::select('id', 'nama_customer')
            ->orderBy('nama_customer')
            ->get();

        // Calculate counts for badges - OPTIMIZED dengan cache
        $cacheKey = 'tagihan_counts_' . date('Y-m-d-H'); // Cache per jam
        $counts = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return [
                'aktif_supplier' => TagihanPo::where('tipe_relasi', 'supplier')
                    ->whereNotIn('status', ['draft', 'lunas', 'dibatalkan'])->count(),
                'lunas_supplier' => TagihanPo::where('tipe_relasi', 'supplier')
                    ->where('status', 'lunas')->count(),
                'draft_supplier' => TagihanPo::where('tipe_relasi', 'supplier')
                    ->where('status', 'draft')->count(),
                'aktif_customer' => TagihanPo::where('tipe_relasi', 'customer')
                    ->whereNotIn('status', ['draft', 'lunas', 'dibatalkan'])->count(),
                'lunas_customer' => TagihanPo::where('tipe_relasi', 'customer')
                    ->where('status', 'lunas')->count(),
                'draft_customer' => TagihanPo::where('tipe_relasi', 'customer')
                    ->where('status', 'draft')->count(),
            ];
        });

        // Calculate totals for summary cards - OPTIMIZED dengan cache
        $totalsCacheKey = 'tagihan_totals_' . date('Y-m-d-H');
        $totals = Cache::remember($totalsCacheKey, now()->addMinutes(60), function () {
            return [
                'outstanding_supplier' => TagihanPo::where('tipe_relasi', 'supplier')
                    ->whereNotIn('status', ['draft', 'lunas', 'dibatalkan'])
                    ->sum('sisa_tagihan'),
                'outstanding_customer' => TagihanPo::where('tipe_relasi', 'customer')
                    ->whereNotIn('status', ['draft', 'lunas', 'dibatalkan'])
                    ->sum('sisa_tagihan'),
                'lunas_supplier' => TagihanPo::where('tipe_relasi', 'supplier')
                    ->where('status', 'lunas')
                    ->sum('grand_total'),
                'lunas_customer' => TagihanPo::where('tipe_relasi', 'customer')
                    ->where('status', 'lunas')
                    ->sum('grand_total'),
                'draft_supplier' => TagihanPo::where('tipe_relasi', 'supplier')
                    ->where('status', 'draft')
                    ->sum('grand_total'),
                'draft_customer' => TagihanPo::where('tipe_relasi', 'customer')
                    ->where('status', 'draft')
                    ->sum('grand_total'),
            ];
        });

        return view('tagihan.index', [
            'suppliers' => $suppliers,
            'customers' => $customers,
            'tagihanAktifSupplierCount' => $counts['aktif_supplier'],
            'tagihanLunasSupplierCount' => $counts['lunas_supplier'],
            'tagihanDraftSupplierCount' => $counts['draft_supplier'],
            'tagihanAktifCustomerCount' => $counts['aktif_customer'],
            'tagihanLunasCustomerCount' => $counts['lunas_customer'],
            'tagihanDraftCustomerCount' => $counts['draft_customer'],
            'totalOutstandingSupplier' => $totals['outstanding_supplier'],
            'totalOutstandingCustomer' => $totals['outstanding_customer'],
            'totalLunasSupplier' => $totals['lunas_supplier'],
            'totalLunasCustomer' => $totals['lunas_customer'],
            'totalDraftSupplier' => $totals['draft_supplier'],
            'totalDraftCustomer' => $totals['draft_customer'],
        ]);
    }

    /**
     * AJAX endpoint to load data for specific tab
     */
    public function loadTabData(Request $request)
    {
        try {
            $tab = $request->get('tab', 'aktif_supplier');
            $tipeRelasi = str_contains($tab, 'customer') ? 'customer' : 'supplier';
            $statusType = explode('_', $tab)[0]; // aktif, lunas, or draft
            
            // Get filtered tagihan
            $tagihan = $this->getTagihanByType($tipeRelasi, $statusType, $request);
            
            // Get relasi for filter dropdown - HANYA FIELD YANG DIPERLUKAN
            $relasi = $tipeRelasi === 'customer' 
                ? Customer::select('id', 'nama_customer')->orderBy('nama_customer')->get() 
                : Supplier::select('id', 'nama_supplier')->orderBy('nama_supplier')->get();

            $tabType = $tab;

            // Return HTML partial view
            $html = view('tagihan.partials.table', compact('tagihan', 'tabType', 'relasi', 'tipeRelasi'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $tagihan->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading tab data: ' . $e->getMessage(), [
                'tab' => $request->get('tab'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tagihan by type and status - OPTIMIZED
     */
    private function getTagihanByType($tipeRelasi, $statusType, Request $request)
    {
        // OPTIMIZED: Select hanya kolom yang diperlukan
        $query = TagihanPo::select([
                'id_tagihan',
                'no_tagihan',
                'id_po',
                'id_relasi',
                'tipe_relasi',
                'tanggal_tagihan',
                'grand_total',
                'total_dibayar',
                'sisa_tagihan',
                'status',
                'created_at',
                'updated_at'
            ])
            ->with([
                'purchaseOrder:id_po,no_po,no_gr,no_invoice,tanggal_jatuh_tempo',
                'customer:id,nama_customer',
                'supplier:id,nama_supplier'
            ])
            ->where('tipe_relasi', $tipeRelasi);

        // Apply status filter
        if ($statusType === 'aktif') {
            $query->whereNotIn('status', ['draft', 'lunas', 'dibatalkan']);
        } elseif ($statusType === 'lunas') {
            $query->where('status', 'lunas');
        } else {
            $query->where('status', 'draft');
        }

        // Apply filters
        $query = $this->applyFilters($query, $request, $tipeRelasi);

        // OPTIMIZED: Limit data yang di-load
        $limit = $request->get('limit', 100); // Default 100 records
        
        // Get results
        $tagihan = $query->limit($limit)->get();

        // Sort in memory (lebih efisien untuk dataset kecil)
        if ($statusType === 'lunas') {
            return $tagihan->sortByDesc('updated_at')->values();
        } else {
            return $tagihan->sortBy(function($item) {
                return $item->purchaseOrder?->tanggal_jatuh_tempo ?? '9999-12-31';
            })->values();
        }
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request, $tipeRelasi)
    {
        // Filter by supplier or customer
        if ($request->filled('relasi_id')) {
            $query->where('id_relasi', $request->relasi_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tanggal jatuh tempo - dari
        if ($request->filled('tanggal_dari')) {
            $query->whereHas('purchaseOrder', function($q) use ($request) {
                $q->where('tanggal_jatuh_tempo', '>=', $request->tanggal_dari);
            });
        }

        // Filter by tanggal jatuh tempo - sampai
        if ($request->filled('tanggal_sampai')) {
            $query->whereHas('purchaseOrder', function($q) use ($request) {
                $q->where('tanggal_jatuh_tempo', '<=', $request->tanggal_sampai);
            });
        }

        // Filter by jatuh tempo status
        if ($request->filled('jatuh_tempo')) {
            if ($request->jatuh_tempo === 'lewat') {
                $query->whereHas('purchaseOrder', function($q) {
                    $q->where('tanggal_jatuh_tempo', '<', now());
                });
            } elseif ($request->jatuh_tempo === 'minggu_ini') {
                $query->whereHas('purchaseOrder', function($q) {
                    $q->whereBetween('tanggal_jatuh_tempo', [now(), now()->addWeek()]);
                });
            }
        }

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $tipeRelasi) {
                $q->where('no_tagihan', 'like', "%{$search}%")
                ->orWhereHas('purchaseOrder', function($subQ) use ($search) {
                    $subQ->where('no_po', 'like', "%{$search}%")
                         ->orWhere('no_gr', 'like', "%{$search}%")
                         ->orWhere('no_invoice', 'like', "%{$search}%");
                });

                // Search in customer or supplier name
                if ($tipeRelasi === 'customer') {
                    $q->orWhereHas('customer', function($subQ) use ($search) {
                        $subQ->where('nama_customer', 'like', "%{$search}%");
                    });
                } else {
                    $q->orWhereHas('supplier', function($subQ) use ($search) {
                        $subQ->where('nama_supplier', 'like', "%{$search}%");
                    });
                }
            });
        }

        return $query;
    }

    /**
     * Export to Excel
     */
    private function exportExcel(Request $request, $tipeRelasi)
    {
        $tab = $request->get('tab', 'aktif_supplier');
        $statusType = explode('_', $tab)[0];

        $query = TagihanPo::with(['purchaseOrder', 'customer', 'supplier'])
            ->where('tipe_relasi', $tipeRelasi);

        if ($statusType === 'aktif') {
            $query->whereNotIn('status', ['draft', 'lunas', 'dibatalkan']);
        } elseif ($statusType === 'lunas') {
            $query->where('status', 'lunas');
        } else {
            $query->where('status', 'draft');
        }

        $query = $this->applyFilters($query, $request, $tipeRelasi);
        $tagihan = $query->latest()->get();

        $filename = 'tagihan_' . $statusType . '_' . $tipeRelasi . '_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new TagihanExport($tagihan, $statusType . '_' . $tipeRelasi), $filename);
    }

    /**
     * Export to PDF
     */
    private function exportPDF(Request $request, $tipeRelasi)
    {
        $tab = $request->get('tab', 'aktif_supplier');
        $statusType = explode('_', $tab)[0];

        $query = TagihanPo::with(['purchaseOrder', 'customer', 'supplier'])
            ->where('tipe_relasi', $tipeRelasi);

        if ($statusType === 'aktif') {
            $query->whereNotIn('status', ['draft', 'lunas', 'dibatalkan']);
        } elseif ($statusType === 'lunas') {
            $query->where('status', 'lunas');
        } else {
            $query->where('status', 'draft');
        }

        $query = $this->applyFilters($query, $request, $tipeRelasi);
        $tagihan = $query->latest()->get();

        $pdf = Pdf::loadView('tagihan.pdf', [
            'tagihan' => $tagihan,
            'tab' => $statusType . '_' . $tipeRelasi,
            'filters' => $request->all()
        ]);

        $filename = 'tagihan_' . $statusType . '_' . $tipeRelasi . '_' . date('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Print View
     */
    private function printView(Request $request, $tipeRelasi)
    {
        $tab = $request->get('tab', 'aktif_supplier');
        $statusType = explode('_', $tab)[0];

        $query = TagihanPo::with(['purchaseOrder', 'customer', 'supplier'])
            ->where('tipe_relasi', $tipeRelasi);

        if ($statusType === 'aktif') {
            $query->whereNotIn('status', ['draft', 'lunas', 'dibatalkan']);
        } elseif ($statusType === 'lunas') {
            $query->where('status', 'lunas');
        } else {
            $query->where('status', 'draft');
        }

        $query = $this->applyFilters($query, $request, $tipeRelasi);
        $tagihan = $query->latest()->get();

        return view('tagihan.print', [
            'tagihan' => $tagihan,
            'tab' => $statusType . '_' . $tipeRelasi,
            'filters' => $request->all()
        ]);
    }

    /**
     * Detail tagihan
     */
    public function show($id_tagihan)
    {
        $tagihan = TagihanPo::with([
            'purchaseOrder.items',
            'items.produk',
            'supplier',
            'customer',
            'pembayaran.karyawanInput',
            'karyawanBuat',
        ])->findOrFail($id_tagihan);

        if (request()->wantsJson()) {
            return response()->json($tagihan, 200);
        }

        return view('tagihan.show', compact('tagihan'));
    }

    /**
     * Form input pembayaran
     */
    public function showPaymentForm($id_tagihan)
    {
        $tagihan = TagihanPo::with(['purchaseOrder', 'supplier', 'customer', 'items'])
            ->findOrFail($id_tagihan);

        if (!$tagihan->canBePaid()) {
            return redirect()->route('tagihan.show', $id_tagihan)
                ->with('error', 'Tagihan ini tidak dapat dibayar');
        }

        return view('tagihan.payment-form', compact('tagihan'));
    }

    /**
     * Proses pembayaran
     */
    public function processPayment(Request $request, $id_tagihan)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:transfer,cash,giro,kartu_kredit,lainnya',
            'nomor_referensi' => 'nullable|string|max:100',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

        // Verifikasi PIN
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'error' => 'PIN tidak valid'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $tagihan = TagihanPo::findOrFail($id_tagihan);

            if (!$tagihan->canBePaid()) {
                throw new \Exception('Tagihan ini tidak dapat dibayar');
            }

            // Validasi jumlah bayar tidak melebihi sisa tagihan
            if ($request->jumlah_bayar > $tagihan->sisa_tagihan) {
                throw new \Exception('Jumlah pembayaran melebihi sisa tagihan');
            }

            // Upload bukti pembayaran
            $buktiBayar = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = 'bukti_' . time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('pembayaran', $filename, 'public');
                $buktiBayar = $path;
            }

            // Create pembayaran record dengan status langsung diverifikasi
            $pembayaran = PembayaranTagihan::create([
                'id_tagihan' => $tagihan->id_tagihan,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'nomor_referensi' => $request->nomor_referensi,
                'bukti_pembayaran' => $buktiBayar,
                'catatan' => $request->catatan,
                'id_karyawan_input' => Auth::user()->id_karyawan,
                'status_pembayaran' => 'diverifikasi',
                'id_karyawan_approve' => Auth::user()->id_karyawan,
                'tanggal_approve' => now(),
            ]);

            // Update status tagihan langsung
            $tagihan->updatePembayaran();
            
            // Clear cache setelah pembayaran
            Cache::forget('tagihan_counts_' . date('Y-m-d-H'));
            Cache::forget('tagihan_totals_' . date('Y-m-d-H'));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil disimpan dan status tagihan telah diupdate',
                'data' => $pembayaran
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * History pembayaran
     */
    public function paymentHistory($id_tagihan)
    {
        $tagihan = TagihanPo::with([
            'pembayaran.karyawanInput',
            'pembayaran.karyawanApprove'
        ])->findOrFail($id_tagihan);

        if (request()->wantsJson()) {
            return response()->json($tagihan->pembayaran, 200);
        }

        return view('tagihan.payment-history', compact('tagihan'));
    }

    /**
     * Download bukti pembayaran
     */
    public function downloadBukti($id_pembayaran)
    {
        $pembayaran = PembayaranTagihan::findOrFail($id_pembayaran);

        if (!$pembayaran->bukti_pembayaran) {
            abort(404, 'Bukti pembayaran tidak ditemukan');
        }

        $path = storage_path('app/public/' . $pembayaran->bukti_pembayaran);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($path);
    }

    /**
     * Print tagihan
     */
    public function print($id_tagihan)
    {
        $tagihan = TagihanPo::with([
            'purchaseOrder.items',
            'items.produk',
            'supplier',
            'customer',
            'pembayaran' => function ($q) {
                $q->where('status_pembayaran', 'diverifikasi');
            }
        ])->findOrFail($id_tagihan);

        return view('tagihan.print', compact('tagihan'));
    }

    public function overdue()
    {
        $tagihan = TagihanPo::overdue()
            ->with(['purchaseOrder', 'supplier', 'customer', 'pembayaran'])
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->paginate(20);

        // Add days overdue untuk setiap tagihan
        $tagihan->getCollection()->transform(function($t) {
            $t->days_overdue = now()->diffInDays($t->tanggal_jatuh_tempo, false);
            return $t;
        });

        $totalOverdue = TagihanPo::overdue()->sum('sisa_tagihan');

        return view('tagihan.overdue', compact('tagihan', 'totalOverdue'));
    }

    /**
     * Display tagihan due soon (jatuh tempo dalam 7 hari)
     */
    public function dueSoon()
    {
        $tagihan = TagihanPo::dueWithinDays(7)
            ->with(['purchaseOrder', 'supplier', 'customer', 'pembayaran'])
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->paginate(20);

        // Add days left untuk setiap tagihan
        $tagihan->getCollection()->transform(function($t) {
            $t->days_left = now()->diffInDays($t->tanggal_jatuh_tempo, false);
            return $t;
        });

        $totalDueSoon = TagihanPo::dueWithinDays(7)->sum('sisa_tagihan');

        return view('tagihan.due-soon', compact('tagihan', 'totalDueSoon'));
    }
}