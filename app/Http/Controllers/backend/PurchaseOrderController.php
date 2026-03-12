<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DetailCustomer;
use App\Models\DetailGudang;
use App\Models\DetailSupplier;
use App\Models\Gudang;
use App\Models\Karyawan;
use App\Models\PoAuditTrail;
use App\Models\PoProof;
use App\Models\Produk;
use App\Models\ProdukSatuan;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\TagihanPoItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TagihanPoServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with([
            'items', 
            'karyawanPemohon', 
            'supplier',
            'customer',
            'returs' => function($q) {
                $q->where('tipe_retur', 'po');
            }
        ]);

        // Filter by tipe_po
        if ($request->filled('tipe_po')) {
            $query->where('tipe_po', $request->tipe_po);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_po', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($sq) use ($search) {
                        $sq->where('nama_supplier', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('nama_customer', 'like', "%{$search}%");
                    });
            });
        }

        $purchaseOrders = $query->latest()->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($purchaseOrders, 200);
        }

        return view('po.index', compact('purchaseOrders'));
    }

    public function create(Request $request)
    {
        $type      = $request->get('type', 'pembelian');
        $suppliers = Supplier::where('status', 'Aktif')->get();
        $customers = Customer::where('status', 'aktif')->get();

        if ($type === 'penjualan') {
    // Ambil SEMUA gudang, bukan hanya Gudang::first()
    // karena BTC-003 mungkin tersimpan di gudang_id yang berbeda
    $gudangIds = Gudang::pluck('id');
    
    if ($gudangIds->isEmpty()) {
        return back()->with('error', 'Gudang tidak ditemukan');
    }

    $detailGudangs = DetailGudang::with([
        'produk.produkSatuans.satuan',
        'supplier',
    ])
    ->whereIn('gudang_id', $gudangIds)      // ← pakai whereIn bukan where
    ->whereRaw('CAST(stock_gudang AS SIGNED) > 0')  // ← cast eksplisit atasi bug tipe data
    ->orderBy('produk_id')
    ->orderBy('supplier_id')
    ->get();

    \Log::info('DetailGudang debug', [
        'gudang_ids'  => $gudangIds->toArray(),
        'total_rows'  => $detailGudangs->count(),
        'batches'     => $detailGudangs->map(fn($d) => [
            'id'          => $d->id,
            'no_batch'    => $d->no_batch,
            'supplier_id' => $d->supplier_id,
            'supplier'    => $d->supplier?->nama_supplier ?? 'NULL - cek FK!',
            'stock'       => $d->stock_gudang,
            'gudang_id'   => $d->gudang_id,
        ])->toArray(),
    ]);

    $produkList = $detailGudangs
        ->filter(fn($d) => $d->produk !== null)
        ->map(function ($detail) {
            $produk = $detail->produk;

            $satuans = $produk->produkSatuans
                ->map(fn($ps) => [
                    'id'         => $ps->id,
                    'label'      => $ps->satuan->nama_satuan ?? 'PCS',
                    'konversi'   => (int) $ps->konversi,
                    'isi'        => (float) $ps->isi,
                    'is_default' => (bool) $ps->is_default,
                ])
                ->values()
                ->toArray();

            return [
                'id'                 => $detail->id,
                'detail_gudang_id'   => $detail->id,
                'produk_id'          => $produk->id,
                'nama'               => $produk->nama_produk,
                'merk'               => $produk->merk ?? '',
                'stock_gudang'       => (int) $detail->stock_gudang,
                'no_batch'           => $detail->no_batch ?? '-',
                'tanggal_kadaluarsa' => $detail->tanggal_kadaluarsa
                    ? Carbon::parse($detail->tanggal_kadaluarsa)->format('d/m/Y')
                    : '-',
                'satuans'            => $satuans,
                'supplier_id'        => $detail->supplier_id,
                // Fallback berlapis jika relasi gagal load
                'supplier_name'      => $detail->supplier?->nama_supplier
                                        ?? $detail->supplier_id
                                        ?? '-',
            ];
        })
        ->values()
        ->toArray();

        } else {
            // ──────────────────────────────────────────────────────────────
            // PO PEMBELIAN
            // Ambil dari detail_suppliers — SEMUA supplier aktif (filter di JS
            // berdasarkan supplier yang dipilih di form)
            // ──────────────────────────────────────────────────────────────
            $detailSuppliers = DetailSupplier::with([
                'supplier',
                // DetailSupplier.produk() pakai FK 'product_id'
                'produk.produkSatuans.satuan',
                // produkSatuan = satuan SPESIFIK yang dijual oleh supplier ini
                'produkSatuan.satuan',
            ])
            ->where('is_aktif', true)
            ->get();

            $produkList = $detailSuppliers
                ->filter(fn($d) => $d->produk !== null)
                ->map(function ($detail) {
                    $produk      = $detail->produk;
                    $produkSat   = $detail->produkSatuan;   // ProdukSatuan spesifik supplier
                    $satuan      = $produkSat?->satuan;

                    // Konversi: gunakan kolom 'konversi' (integer) dari ProdukSatuan
                    // Fallback ke 'isi' jika konversi belum diset
                    $konversi = $produkSat
                        ? (int) ($produkSat->konversi ?: (int) ceil($produkSat->isi) ?: 1)
                        : 1;

                    $namaSatuan = $satuan?->nama_satuan ?? 'PCS';

                    // Harga beli: ambil dari detail_suppliers.harga_beli (per-supplier)
                    // bukan dari ProdukSatuan.harga_beli (global)
                    $hargaBeli = (float) ($detail->harga_beli ?? 0);

                    return [
                        'id'                 => $produk->id,
                        'detail_supplier_id' => $detail->id,
                        'supplier_id'        => $detail->supplier_id,
                        'nama'               => $produk->nama_produk,
                        'merk'               => $produk->merk ?? '',
                        // Satuan untuk dropdown (e.g. "Box")
                        'satuan'             => $namaSatuan,
                        // ID ProdukSatuan untuk disimpan ke purchase_order_items
                        'produk_satuan_id'   => $produkSat?->id,
                        // Konversi yang benar: 1 Box = 50 PCS
                        'konversi'           => $konversi,
                        // Harga beli dari detail_suppliers (harga negosiasi dengan supplier)
                        'harga_beli'         => $hargaBeli,
                        'supplier_name'      => $detail->supplier?->nama_supplier ?? '-',
                    ];
                })
                ->filter()
                ->values()
                ->toArray();
        }

        return view('po.create', compact('type', 'suppliers', 'customers', 'produkList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe_po'                          => 'required|in:penjualan,pembelian',
            'id_unit_pemohon'                  => 'required',
            'unit_pemohon'                     => 'required|string',
            'catatan_pemohon'                  => 'nullable|string',
            'id_supplier'                      => 'required_if:tipe_po,pembelian|nullable|uuid',
            'id_customer'                      => 'required_if:tipe_po,penjualan|nullable|uuid',
            'pajak'                            => 'nullable|numeric',
            'pajak_persen'                     => 'nullable|numeric',
            'items'                            => 'required|array|min:1',
            'items.*.id_produk'                => 'required|uuid',
            'items.*.produk_satuan_id'         => 'nullable|uuid',
            'items.*.qty_diminta'              => 'required|integer|min:1',
            'items.*.harga'                    => 'nullable|numeric',
            // ── Kolom diskon (baru) ──────────────────────────────
            'items.*.diskon_persen'            => 'nullable|numeric|min:0|max:100',
            'items.*.diskon_nominal'           => 'nullable|numeric|min:0',
            'items.*.is_free'                  => 'nullable|boolean',
            // ────────────────────────────────────────────────────
            'items.*.detail_gudang_id'         => 'nullable|uuid',
            'items.*.no_batch'                 => 'nullable|string',
            'items.*.tanggal_kadaluarsa'       => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $itemsInput  = array_values($request->items);
            $total       = 0;
            $itemsToSave = [];

            foreach ($itemsInput as $item) {
                $produk = Produk::find($item['id_produk']);
                if (!$produk) {
                    throw new \Exception('Produk tidak ditemukan: ' . $item['id_produk']);
                }

                $harga             = 0;
                $konversiSnapshot  = 1;
                $detailGudangId    = null;
                $batchNumber       = null;
                $tanggalKadaluarsa = null;

                // Ambil konversi dari ProdukSatuan yang dipilih
                if (!empty($item['produk_satuan_id'])) {
                    $produkSatuan = ProdukSatuan::find($item['produk_satuan_id']);
                    if ($produkSatuan) {
                        $konversiSnapshot = (int) ($produkSatuan->konversi
                            ?: (int) ceil($produkSatuan->isi)
                            ?: 1);
                    }
                }

                if ($request->tipe_po === 'penjualan') {
                    // ── Harga dari detail_customers ────────────────────────
                    $detailCustomer = DetailCustomer::where('produk_id', $item['id_produk'])
                        ->where('customer_id', $request->id_customer)
                        ->when(
                            !empty($item['produk_satuan_id']),
                            fn($q) => $q->where('produk_satuan_id', $item['produk_satuan_id'])
                        )
                        ->first();

                    if (!$detailCustomer) {
                        throw new \Exception(
                            'Harga jual produk "' . $produk->nama_produk . '" untuk customer ini tidak ditemukan. ' .
                            'Pastikan harga sudah diisi di Detail Customer.'
                        );
                    }
                    $harga = (float) ($detailCustomer->harga_jual ?? 0);

                    // ── Ambil DetailGudang ─────────────────────────────────
                    $detailGudang = null;
                    if (!empty($item['detail_gudang_id'])) {
                        $detailGudang = DetailGudang::find($item['detail_gudang_id']);
                    }
                    if (!$detailGudang) {
                        $gudang = Gudang::first();
                        if ($gudang) {
                            $detailGudang = DetailGudang::where('produk_id', $item['id_produk'])
                                ->where('gudang_id', $gudang->getKey())
                                ->where('stock_gudang', '>', 0)
                                ->orderBy('tanggal_kadaluarsa', 'asc')
                                ->first();
                        }
                    }
                    if ($detailGudang) {
                        $detailGudangId    = $detailGudang->id;
                        $batchNumber       = $detailGudang->no_batch;
                        $tanggalKadaluarsa = $detailGudang->tanggal_kadaluarsa;
                    }

                } else {
                    // ── Harga dari detail_suppliers ────────────────────────
                    $detailSupplier = DetailSupplier::where('produk_id', $item['id_produk'])
                        ->where('supplier_id', $request->id_supplier)
                        ->when(
                            !empty($item['produk_satuan_id']),
                            fn($q) => $q->where('produk_satuan_id', $item['produk_satuan_id'])
                        )
                        ->first();

                    if (!$detailSupplier) {
                        throw new \Exception(
                            'Harga beli produk "' . $produk->nama_produk . '" untuk supplier ini tidak ditemukan. ' .
                            'Pastikan Detail Supplier sudah diisi.'
                        );
                    }
                    $harga = (float) ($detailSupplier->harga_beli ?? 0);
                }

                // ── Hitung diskon ──────────────────────────────────────────
                $isFree        = !empty($item['is_free']) && (bool) $item['is_free'];
                $diskonPersen  = $isFree ? 100 : (float) ($item['diskon_persen'] ?? 0);
                $diskonNominal = $isFree ? $harga : (float) ($item['diskon_nominal'] ?? ($harga * $diskonPersen / 100));

                // Pastikan nominal tidak melebihi harga
                $diskonNominal     = min($diskonNominal, $harga);
                $hargaSetelahDiskon = $harga - $diskonNominal;
                // ──────────────────────────────────────────────────────────

                $qtyDiminta            = (int) $item['qty_diminta'];
                $qtyDimintaSatuanDasar = $qtyDiminta * $konversiSnapshot;
                // Subtotal dihitung dari harga SETELAH diskon
                $subtotal              = $hargaSetelahDiskon * $qtyDiminta;

                $itemsToSave[] = [
                    'produk'                   => $produk,
                    'id_produk'                => $produk->id,
                    'produk_satuan_id'         => $item['produk_satuan_id'] ?? null,
                    'konversi_snapshot'        => $konversiSnapshot,
                    'qty_diminta'              => $qtyDiminta,
                    'qty_diminta_satuan_dasar' => $qtyDimintaSatuanDasar,
                    'harga'                    => $harga,              // harga asli sebelum diskon
                    'diskon_persen'            => $diskonPersen,
                    'diskon_nominal'           => $diskonNominal,
                    'harga_setelah_diskon'     => $hargaSetelahDiskon,
                    'is_free'                  => $isFree,
                    'subtotal'                 => $subtotal,           // subtotal sudah after diskon
                    'detail_gudang_id'         => $detailGudangId,
                    'batch_number'             => $batchNumber,
                    'tanggal_kadaluarsa'       => $tanggalKadaluarsa,
                ];

                $total += $subtotal;
            }

            $pajak      = (float) ($request->pajak ?? 0);
            $grandTotal = $total + $pajak;

            // Buat PO
            $po = PurchaseOrder::create([
                'tipe_po'             => $request->tipe_po,
                'status'              => 'draft',
                'id_unit_pemohon'     => $request->id_unit_pemohon,
                'unit_pemohon'        => $request->unit_pemohon,
                'id_karyawan_pemohon' => Auth::user()->id_karyawan,
                'tanggal_permintaan'  => now(),
                'catatan_pemohon'     => $request->catatan_pemohon,
                'unit_tujuan'         => $request->tipe_po === 'penjualan' ? 'customer' : 'supplier',
                'id_supplier'         => $request->tipe_po === 'pembelian'
                                            ? $request->id_supplier
                                            : $request->id_customer,
                'total_harga'         => $total,
                'pajak'               => $pajak,
                'grand_total'         => $grandTotal,
                'tanggal_jatuh_tempo' => now()->addDays(30),
            ]);

            // Buat PO Items
            foreach ($itemsToSave as $item) {
                PurchaseOrderItem::create([
                    'id_po'                    => $po->id_po,
                    'id_produk'                => $item['id_produk'],
                    'nama_produk'              => $item['produk']->nama_produk,
                    'kode_produk'              => $item['produk']->kode_produk,
                    'produk_satuan_id'         => $item['produk_satuan_id'],
                    'konversi_snapshot'        => $item['konversi_snapshot'],
                    'qty_diminta'              => $item['qty_diminta'],
                    'qty_diminta_satuan_dasar' => $item['qty_diminta_satuan_dasar'],
                    'harga_satuan'             => $item['harga'],            // harga asli
                    // ── Kolom diskon (baru) ──────────────────────────────
                    'diskon_persen'            => $item['diskon_persen'],
                    'diskon_nominal'           => $item['diskon_nominal'],
                    'harga_setelah_diskon'     => $item['harga_setelah_diskon'],
                    'is_free'                  => $item['is_free'],
                    // ────────────────────────────────────────────────────
                    'subtotal'                 => $item['subtotal'],
                    'detail_gudang_id'         => $item['detail_gudang_id'],
                    'batch_number'             => $item['batch_number'],
                    'tanggal_kadaluarsa'       => $item['tanggal_kadaluarsa'],
                ]);
            }

            // Audit Trail
            PoAuditTrail::create([
                'id_po'          => $po->id_po,
                'id_karyawan'    => Auth::user()->id_karyawan,
                'pin_karyawan'   => $request->pin ?? null,
                'aksi'           => 'buat_po',
                'deskripsi_aksi' => 'Membuat PO ' . ucfirst($request->tipe_po),
                'data_sesudah'   => $po->toArray(),
            ]);

            DB::commit();

            return redirect()
                ->route('po.show', $po->id_po)
                ->with('success', 'Purchase Order berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PO Store Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors([
                'error' => 'Gagal membuat PO: ' . $e->getMessage()
            ])->withInput();
        }
    }


    public function show($id_po)
    {
        $po = PurchaseOrder::with([
            'items.produk',
            'items.produkSatuan.satuan',
            'karyawanPemohon',
            'kepalaGudang',
            'kasir',
            'supplier',
            'customer',
            'auditTrails.karyawan',
            'proofs'
        ])->findOrFail($id_po);

        if (request()->wantsJson()) {
            return response()->json($po, 200);
        }

        return view('po.show', compact('po'));
    }

    public function edit($id_po)
    {
        $po = PurchaseOrder::with('items')->findOrFail($id_po);

        if (!in_array($po->status, ['draft', 'ditolak'])) {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'Hanya PO dengan status draft atau ditolak yang dapat diedit');
        }

        $suppliers = Supplier::where('status', 'Aktif')->get();
        $customers = Customer::where('status', 'aktif')->get();

        if ($po->tipe_po === 'penjualan') {
            // PO PENJUALAN
            $gudang = Gudang::first();
            if (!$gudang) {
                return back()->with('error', 'Gudang tidak ditemukan');
            }

            $detailGudangs = DetailGudang::with(['produk.produkSatuans.satuan'])
                ->where('gudang_id', $gudang->id)
                ->where('stock_gudang', '>', 0)
                ->get();

            $produkList = [];

            foreach ($detailGudangs as $detail) {
                if (!$detail->produk) continue;

                $produk = $detail->produk;

                $satuans = $produk->produkSatuans->map(function ($ps) {
                    return [
                        'id'         => $ps->id,
                        'label'      => $ps->satuan->nama_satuan ?? 'PCS',
                        'konversi'   => (float) $ps->konversi,
                        'is_default' => (bool) $ps->is_default,
                    ];
                })->values()->toArray();

                $produkList[] = [
                    'id'                => $produk->id,
                    'detail_gudang_id'  => $detail->id,
                    'nama'              => $produk->nama_produk,
                    'merk'              => $produk->merk ?? '',
                    'stock_gudang'      => $detail->stock_gudang,
                    'no_batch'          => $detail->no_batch ?? '-',
                    'tanggal_kadaluarsa' => $detail->tanggal_kadaluarsa
                        ? Carbon::parse($detail->tanggal_kadaluarsa)->format('d/m/Y')
                        : '-',
                    'satuans'           => $satuans,
                ];
            }
        } else {
            // PO PEMBELIAN
            $detailSuppliers = DetailSupplier::with(['supplier', 'produk', 'produkSatuan.satuan'])
                ->where('supplier_id', $po->id_supplier)
                ->where('is_aktif', true)
                ->get();

            $produkList = $detailSuppliers->map(function ($detail) {
                if (!$detail->produk) return null;

                $produk = $detail->produk;

                return [
                    'id' => $produk->id,
                    'detail_supplier_id' => $detail->id,
                    'supplier_id' => $detail->supplier_id,
                    'nama' => $produk->nama_produk,
                    'merk' => $produk->merk ?? '',
                    'satuan' => $detail->produkSatuan->satuan->nama_satuan ?? 'PCS',
                    'produk_satuan_id' => $detail->produk_satuan_id,
                    'harga_beli' => $detail->harga_beli ?? 0,
                ];
            })->filter()->values();
        }

        return view('po.edit', compact('po', 'suppliers', 'customers', 'produkList'));
    }

    public function update(Request $request, $id_po)
    {
        $po = PurchaseOrder::findOrFail($id_po);

        if (!in_array($po->status, ['draft', 'ditolak'])) {
            if ($request->wantsJson()) return response()->json(['error' => 'Hanya PO draft atau ditolak yang dapat diedit'], 400);
            return back()->with('error', 'Hanya PO dengan status draft atau ditolak yang dapat diedit');
        }

        $request->validate([
            'catatan_pemohon'          => 'nullable|string',
            'pin'                      => 'required|size:6',
            'items'                    => 'required|array|min:1',
            'items.*.id_produk'        => 'required|uuid',
            'items.*.produk_satuan_id' => 'nullable|uuid',
            'items.*.qty_diminta'      => 'required|integer|min:1',
            'items.*.harga'            => 'nullable|numeric',
            'items.*.diskon_persen'    => 'nullable|numeric|min:0|max:100',
            'items.*.diskon_nominal'   => 'nullable|numeric|min:0',
            'items.*.is_free'          => 'nullable|boolean',
        ]);

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)->first();

        if (!$karyawan) {
            if ($request->wantsJson()) return response()->json(['error' => 'PIN tidak valid'], 403);
            return back()->withErrors(['pin' => 'PIN tidak valid']);
        }

        DB::beginTransaction();
        try {
            $dataBefore    = $po->toArray();
            $statusSebelum = $po->status;
            $po->items()->delete();

            $total = 0;
            foreach ($request->items as $item) {
                $produk = Produk::find($item['id_produk']);
                if (!$produk) throw new \Exception('Produk tidak ditemukan');

                $hargaSatuan      = (float) ($item['harga'] ?? 0);
                $konversiSnapshot = 1;

                if (!empty($item['produk_satuan_id'])) {
                    $ps = ProdukSatuan::find($item['produk_satuan_id']);
                    if ($ps) $konversiSnapshot = (int) $ps->konversi;
                }

                // ── Hitung diskon ──────────────────────────────────
                $isFree        = !empty($item['is_free']) && (bool) $item['is_free'];
                $diskonPersen  = $isFree ? 100 : (float) ($item['diskon_persen'] ?? 0);
                $diskonNominal = $isFree
                    ? $hargaSatuan
                    : (float) ($item['diskon_nominal'] ?? ($hargaSatuan * $diskonPersen / 100));
                $diskonNominal      = min($diskonNominal, $hargaSatuan);
                $hargaSetelahDiskon = $hargaSatuan - $diskonNominal;
                // ──────────────────────────────────────────────────

                $qtyDiminta            = (int) $item['qty_diminta'];
                $qtyDimintaSatuanDasar = $qtyDiminta * $konversiSnapshot;
                $subtotal              = $hargaSetelahDiskon * $qtyDiminta;

                PurchaseOrderItem::create([
                    'id_po'                    => $po->id_po,
                    'id_produk'                => $produk->id,
                    'nama_produk'              => $produk->nama_produk,
                    'produk_satuan_id'         => $item['produk_satuan_id'] ?? null,
                    'konversi_snapshot'        => $konversiSnapshot,
                    'qty_diminta'              => $qtyDiminta,
                    'qty_diminta_satuan_dasar' => $qtyDimintaSatuanDasar,
                    'harga_satuan'             => $hargaSatuan,
                    'diskon_persen'            => $diskonPersen,
                    'diskon_nominal'           => $diskonNominal,
                    'harga_setelah_diskon'     => $hargaSetelahDiskon,
                    'is_free'                  => $isFree,
                    'subtotal'                 => $subtotal,
                    'kode_produk'              => $produk->kode_produk,
                ]);

                $total += $subtotal;
            }

            $updateData = [
                'catatan_pemohon' => $request->catatan_pemohon,
                'total_harga'     => $total,
                'grand_total'     => $total + $po->pajak,
            ];
            if ($statusSebelum === 'ditolak') $updateData['status'] = 'draft';
            $po->update($updateData);

            $deskripsiAksi = 'Mengupdate PO';
            if ($statusSebelum === 'ditolak') $deskripsiAksi .= ' (status berubah dari ditolak menjadi draft)';

            PoAuditTrail::create([
                'id_po'          => $po->id_po,
                'id_karyawan'    => Auth::user()->id_karyawan,
                'pin_karyawan'   => $request->pin,
                'aksi'           => 'edit_po',
                'deskripsi_aksi' => $deskripsiAksi,
                'data_sebelum'   => $dataBefore,
                'data_sesudah'   => $po->fresh()->toArray(),
            ]);

            DB::commit();

            $successMessage = 'Purchase Order berhasil diupdate';
            if ($statusSebelum === 'ditolak') $successMessage .= ' dan status berubah menjadi draft';

            if ($request->wantsJson()) {
                return response()->json(['message' => $successMessage, 'data' => $po->load('items.produkSatuan')], 200);
            }
            return redirect()->route('po.show', $po->id_po)->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) return response()->json(['error' => 'Gagal update PO: ' . $e->getMessage()], 500);
            return back()->withErrors(['error' => 'Gagal update PO: ' . $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id_po)
    {
        $request->validate([
            'pin' => 'required|size:6'
        ]);

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'PIN tidak valid'], 403);
        }

        $po = PurchaseOrder::findOrFail($id_po);

        if (!in_array($po->status, ['draft', 'ditolak'])) {
            return response()->json(['success' => false, 'message' => 'PO tidak dapat dihapus'], 400);
        }

        DB::beginTransaction();
        try {
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'hapus_po',
                'deskripsi_aksi' => 'Menghapus PO',
                'data_sebelum' => $po->toArray(),
            ]);

            $po->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'PO berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus PO'], 500);
        }
    }

    public function saveSuhu(Request $request, $id_po)
    {
        $request->validate([
            'suhu_barang_dikirim' => 'nullable|numeric|between:-99.9,99.9',
            'suhu_barang_datang'  => 'nullable|numeric|between:-99.9,99.9',
        ]);

        try {
            $po = PurchaseOrder::findOrFail($id_po);

            $po->update([
                'suhu_barang_dikirim' => $request->suhu_barang_dikirim,
                'suhu_barang_datang'  => $request->suhu_barang_datang,
            ]);

            return response()->json([
                'success'             => true,
                'message'             => 'Data suhu berhasil disimpan',
                'suhu_barang_dikirim' => $po->suhu_barang_dikirim,
                'suhu_barang_datang'  => $po->suhu_barang_datang,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data suhu: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function submit(Request $request, $id_po)
    {
        DB::beginTransaction();

        try {
            $po = PurchaseOrder::findOrFail($id_po);
            $dataBefore = $po->toArray();

            if (!in_array($po->status, ['draft', 'ditolak'])) {
                return response()->json([
                    'error' => 'PO tidak dapat disubmit pada status saat ini'
                ], 403);
            }

            $newStatus = 'menunggu_persetujuan_kepala_gudang';

            $po->update([
                'status' => $newStatus
            ]);

            PoAuditTrail::create([
                'id_po'        => $po->id_po,
                'id_karyawan'  => Auth::user()->id_karyawan,
                'pin_karyawan' => null,
                'aksi'         => 'submit_approval',
                'deskripsi_aksi' => 'Mengirim PO untuk persetujuan kepala gudang',
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->toArray(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'PO berhasil diajukan',
                'data'    => $po
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Gagal submit PO: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * APPROVE BY KEPALA GUDANG
     */
    public function approveKepalaGudang(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'status_approval' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::with('items')->findOrFail($id_po);
            $dataBefore = $po->toArray();

            // Update approval kepala gudang
            $po->update([
                'id_kepala_gudang_approval' => Auth::user()->id_karyawan,
                'tanggal_approval_kepala_gudang' => now(),
                'catatan_kepala_gudang' => $request->catatan,
                'status_approval_kepala_gudang' => $request->status_approval,
            ]);

            if ($request->status_approval === 'ditolak') {
                $po->update(['status' => 'ditolak']);

                PoAuditTrail::create([
                    'id_po' => $po->id_po,
                    'id_karyawan' => Auth::user()->id_karyawan,
                    'pin_karyawan' => $request->pin,
                    'aksi' => 'reject_kepala_gudang',
                    'deskripsi_aksi' => 'Kepala Gudang menolak PO',
                    'data_sebelum' => $dataBefore,
                    'data_sesudah' => $po->toArray(),
                ]);

                DB::commit();
                return response()->json(['message' => 'PO ditolak oleh Kepala Gudang', 'data' => $po], 200);
            }

            // Jika disetujui
            if ($po->tipe_po === 'penjualan') {
                // PO PENJUALAN: Generate no_gr dan set status ke 'dikirim'
                $noGR = PurchaseOrder::generateNoGR();

                $po->update([
                    'status' => 'dikirim',
                    'no_gr' => $noGR,
                ]);

                $deskripsi = "Gudang menyetujui PO Penjualan dengan nomor GR: {$noGR} - Barang siap dikirim ke Customer";

                Log::info('PO Penjualan Approved with GR', [
                    'po_id' => $po->id_po,
                    'no_po' => $po->no_po,
                    'no_gr' => $noGR,
                    'status' => 'dikirim'
                ]);
            } else {
                // PO PEMBELIAN: Lanjut ke kasir
                $po->update(['status' => 'menunggu_persetujuan_kasir']);
                $deskripsi = 'Kepala Gudang menyetujui PO Pembelian - Menunggu approval Kasir';
            }

            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'approve_kepala_gudang',
                'deskripsi_aksi' => $deskripsi,
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            $message = $po->tipe_po === 'penjualan' 
                ? "Approval Gudang berhasil. Nomor GR: {$po->no_gr}. Barang siap dikirim ke Customer."
                : 'Approval Kepala Gudang berhasil';

            return response()->json([
                'message' => $message,
                'data' => $po->fresh()
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Approve Kepala Gudang Error', [
                'po_id' => $id_po,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Gagal approve: ' . $e->getMessage()], 500);
        }
    }

    /**
     * TERIMA BARANG (untuk PO Pembelian)
     * Method ini akan:
     * 1. Update status PO menjadi 'diterima'
     * 2. Buat entry di stock_movements untuk setiap item
     * 3. Update stok di detail_gudangs
     */
    public function markAsReceived(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'catatan' => 'nullable|string',
            'items' => 'required|array',
            'items.*.id_po_item' => 'required|uuid',
            'items.*.qty_diterima' => 'required|integer|min:0',
            'items.*.no_batch' => 'nullable|string|max:50',
            'items.*.tanggal_kadaluarsa' => 'nullable|date',
            'items.*.kondisi' => 'nullable|in:Baik,Rusak,Kadaluarsa',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::with(['items', 'supplier'])->findOrFail($id_po);

            // Validasi: hanya PO pembelian yang bisa diterima
            if ($po->tipe_po !== 'pembelian') {
                return response()->json([
                    'error' => 'Hanya PO Pembelian yang dapat ditandai sebagai diterima'
                ], 400);
            }

            // Validasi status
            if (!in_array($po->status, ['disetujui', 'dikirim_ke_supplier'])) {
                return response()->json([
                    'error' => 'PO tidak dapat diterima. Status saat ini: ' . $po->status
                ], 400);
            }

            $dataBefore = $po->toArray();
            $noGR = PurchaseOrder::generateNoGR();

            // Update status PO
            $po->update([
                'status' => 'diterima',
                'no_gr' => $noGR,
                'id_penerima' => Auth::user()->id_karyawan,
                'tanggal_diterima' => now(),
                'catatan_penerima' => $request->catatan,
            ]);

            // Proses setiap item yang diterima
            foreach ($request->items as $itemData) {
                $poItem = PurchaseOrderItem::find($itemData['id_po_item']);
                if (!$poItem) continue;

                $qtyDiterima = $itemData['qty_diterima'];
                $qtyDiterimaSatuanDasar = $qtyDiterima * $poItem->konversi_snapshot;

                // Update qty diterima di PO Item
                $poItem->update([
                    'qty_diterima' => $qtyDiterima,
                    'qty_diterima_satuan_dasar' => $qtyDiterimaSatuanDasar,
                ]);

                // Cari gudang tujuan (misal: gudang pertama atau berdasarkan konfigurasi)
                $gudang = Gudang::first();
                if (!$gudang) {
                    throw new \Exception('Gudang tidak ditemukan');
                }

                // Cek apakah sudah ada detail_gudang untuk produk + supplier + batch ini
                $detailGudang = DetailGudang::where('gudang_id', $gudang->id)
                    ->where('produk_id', $poItem->id_produk)
                    ->where('supplier_id', $po->id_supplier)
                    ->where('no_batch', $itemData['no_batch'] ?? null)
                    ->first();

                if ($detailGudang) {
                    // Update stok yang sudah ada
                    $stokSebelum = $detailGudang->stock_gudang;
                    $detailGudang->increment('stock_gudang', $qtyDiterimaSatuanDasar);
                    $stokSesudah = $stokSebelum + $qtyDiterimaSatuanDasar;

                    // Update tanggal kadaluarsa jika ada dan berbeda
                    if (!empty($itemData['tanggal_kadaluarsa']) && $detailGudang->tanggal_kadaluarsa != $itemData['tanggal_kadaluarsa']) {
                        // Lebih baik buat baris baru daripada update batch yang berbeda
                        // Tapi untuk sederhananya, kita update tanggal kadaluarsa
                        $detailGudang->update([
                            'tanggal_kadaluarsa' => $itemData['tanggal_kadaluarsa']
                        ]);
                    }
                } else {
                    // Buat detail gudang baru
                    $stokSebelum = 0;
                    $detailGudang = DetailGudang::create([
                        'gudang_id' => $gudang->id,
                        'produk_id' => $poItem->id_produk,
                        'supplier_id' => $po->id_supplier,
                        'stock_gudang' => $qtyDiterimaSatuanDasar,
                        'min_persediaan' => 0,
                        'no_batch' => $itemData['no_batch'] ?? null,
                        'tanggal_masuk' => now(),
                        'tanggal_produksi' => null,
                        'tanggal_kadaluarsa' => $itemData['tanggal_kadaluarsa'] ?? null,
                        'kondisi' => $itemData['kondisi'] ?? 'Baik',
                    ]);
                    $stokSesudah = $qtyDiterimaSatuanDasar;
                }

                // Buat entry stock movement
                StockMovement::create([
                    'gudang_id' => $gudang->id,
                    'produk_id' => $poItem->id_produk,
                    'tipe' => 'pembelian',
                    'referensi_tipe' => 'purchase_orders',
                    'referensi_id' => $po->id_po,
                    'referensi_no' => $po->no_po,
                    'qty_sebelum' => $stokSebelum,
                    'qty_perubahan' => $qtyDiterimaSatuanDasar, // Positif = masuk
                    'qty_sesudah' => $stokSesudah,
                    'no_batch' => $itemData['no_batch'] ?? null,
                    'tanggal_kadaluarsa' => $itemData['tanggal_kadaluarsa'] ?? null,
                    'id_karyawan' => Auth::user()->id_karyawan,
                    'catatan' => "Penerimaan dari PO: {$po->no_po}",
                ]);
            }

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'terima_barang',
                'deskripsi_aksi' => 'Menandai barang dari supplier sudah diterima dengan nomor GR: ' . $noGR . 
                                ($request->catatan ? ' - Catatan: ' . $request->catatan : ''),
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            Log::info('PO Marked as Received', [
                'po_id' => $po->id_po,
                'no_po' => $po->no_po,
                'no_gr' => $noGR,
                'by_user' => Auth::user()->id_karyawan,
            ]);

            return response()->json([
                'message' => 'Barang dari supplier berhasil ditandai sebagai diterima dengan nomor GR: ' . $noGR . '. Stok gudang telah diperbarui.',
                'data' => [
                    'po' => $po->fresh()->load(['items', 'supplier']),
                    'no_gr' => $noGR
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Mark as Received Error', [
                'po_id' => $id_po,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal menandai barang sebagai diterima: ' . $e->getMessage()
            ], 500);
        }
    }

    public function customerReceive(Request $request, $id_po)
    {
        $request->validate([
            'pin' => 'required|size:6'
        ]);

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($id_po);

            if ($po->tipe_po !== 'penjualan') {
                return response()->json(['success' => false, 'message' => 'Bukan PO Penjualan'], 400);
            }

            if ($po->status !== 'dikirim') {
                return response()->json(['success' => false, 'message' => 'Status PO tidak valid'], 400);
            }

            $dataBefore = $po->toArray();

            $po->update([
                'status'           => 'selesai',
                'id_penerima'      => Auth::user()->id_karyawan,
                'tanggal_diterima' => now(),
            ]);

            PoAuditTrail::create([
                'id_po'          => $po->id_po,
                'id_karyawan'    => Auth::user()->id_karyawan,
                'pin_karyawan'   => $request->pin,
                'aksi'           => 'konfirmasi_penerimaan_customer',
                'deskripsi_aksi' => 'Customer mengkonfirmasi penerimaan barang',
                'data_sebelum'   => $dataBefore,
                'data_sesudah'   => $po->fresh()->toArray(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penerimaan barang oleh customer berhasil dikonfirmasi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer Receive Error', [
                'po_id' => $id_po,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal konfirmasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showReceiveForm($id_po)
    {
        $po = PurchaseOrder::with([
            'items.produk',
            'items.produkSatuan.satuan',
            'supplier'
        ])->findOrFail($id_po);

        // Validasi: hanya PO pembelian dengan status 'disetujui' atau 'dikirim_ke_supplier'
        if ($po->tipe_po !== 'pembelian') {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'Hanya PO Pembelian yang dapat diterima');
        }

        if (!in_array($po->status, ['disetujui', 'dikirim_ke_supplier'])) {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'PO tidak dapat diterima. Status saat ini: ' . $po->status);
        }

        return view('po.receive-form', compact('po'));
    }

    public function printInvoice($id_po)
    {
        $po = PurchaseOrder::with([
            'items.produk',
            'supplier',
            'customer',
            'karyawanPemohon',
            'karyawanInputInvoice',
            'kepalaGudang',
            'kasir'
        ])->findOrFail($id_po);

        if (!$po->hasInvoice()) {
            abort(404, 'Invoice belum tersedia untuk PO ini');
        }

        return view('po.print-invoice', compact('po'));
    }

    public function approveKasir(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'status_approval' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($id_po);
            $dataBefore = $po->toArray();

            $statusPO = $request->status_approval === 'disetujui'
                ? 'disetujui'
                : 'ditolak';

            $updateData = [
                'id_kasir_approval'      => Auth::user()->id_karyawan,
                'tanggal_approval_kasir' => now(),
                'catatan_kasir'          => $request->catatan,
                'status_approval_kasir'  => $request->status_approval,
                'status'                 => $statusPO,
            ];

            // Generate GR otomatis saat kasir approve PO Pembelian
            if ($request->status_approval === 'disetujui' && $po->tipe_po === 'pembelian') {
                $noGR = PurchaseOrder::generateNoGR();
                $updateData['no_gr'] = $noGR;
            }

            $po->update($updateData);

            $aksi = $request->status_approval === 'disetujui' ? 'approve_kasir' : 'reject_kasir';
            $deskripsi = $request->status_approval === 'disetujui'
                ? 'Kasir menyetujui PO' . (isset($noGR) ? " - Nomor GR: {$noGR}" : '')
                : 'Kasir menolak PO';

            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => $aksi,
                'deskripsi_aksi' => $deskripsi,
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->toArray(),
            ]);

            DB::commit();
            $message = 'Approval Kasir berhasil';
            if (isset($noGR)) {
                $message .= ". Nomor GR: {$noGR}";
            }
            return response()->json(['message' => $message, 'data' => $po->fresh()], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal approve: ' . $e->getMessage()], 500);
        }
    }

    public function sendToSupplier(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($id_po);

            if ($po->status !== 'disetujui') {
                return response()->json(['error' => 'PO belum disetujui'], 400);
            }

            $dataBefore = $po->toArray();

            $po->update([
                'status' => 'dikirim_ke_supplier',
                'tanggal_dikirim_ke_supplier' => now(),
                'id_karyawan_pengirim' => Auth::user()->id_karyawan,
            ]);

            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'kirim_ke_supplier',
                'deskripsi_aksi' => 'Mengirim PO ke Supplier',
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'PO berhasil dikirim ke Supplier', 'data' => $po], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal kirim PO: ' . $e->getMessage()], 500);
        }
    }

    public function print($id_po)
    {
        $po = PurchaseOrder::with(['items', 'karyawanPemohon', 'supplier', 'customer'])
            ->findOrFail($id_po);

        return view('po.print', compact('po'));
    }

    // ========== API: UPLOAD BUKTI ==========
    public function uploadProof(Request $request, $id_po)
    {
        try {
            Log::info('Upload Proof Request', [
                'po_id' => $id_po,
                'user_id' => Auth::id(),
                'has_invoice' => $request->hasFile('bukti_invoice'),
                'has_barang' => $request->hasFile('bukti_barang'),
            ]);

            $validator = Validator::make($request->all(), [
                'bukti_invoice' => [
                    'nullable',
                    'file',
                    'mimes:jpeg,jpg,png,pdf',
                    'max:5120',
                ],
                'bukti_barang' => [
                    'nullable',
                    'file',
                    'mimes:jpeg,jpg,png,pdf',
                    'max:5120',
                ],
                'pin' => [
                    'required',
                    'string',
                    'size:6',
                    'regex:/^[0-9]{6}$/'
                ],
                'replace_mode' => [
                    'nullable',
                    'boolean'
                ]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!$request->hasFile('bukti_invoice') && !$request->hasFile('bukti_barang')) {
                return response()->json([
                    'error' => 'Minimal satu file (Invoice atau Barang) harus diupload'
                ], 422);
            }

            $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
                ->where('pin', $request->pin)
                ->first();

            if (!$karyawan) {
                return response()->json(['error' => 'PIN tidak valid'], 403);
            }

            $po = PurchaseOrder::find($id_po);
            
            if (!$po) {
                return response()->json(['error' => 'Purchase Order tidak ditemukan'], 404);
            }

            DB::beginTransaction();
            try {
                $uploadedFiles = [];
                $deskripsiAksi = [];
                $replaceMode = $request->input('replace_mode', false);

                // Upload Bukti Invoice
                if ($request->hasFile('bukti_invoice')) {
                    if ($replaceMode) {
                        PoProof::where('id_po', $id_po)
                            ->where('tipe_bukti', 'invoice')
                            ->where('is_active', true)
                            ->update(['is_active' => false]);
                    }

                    $file = $request->file('bukti_invoice');
                    $invoiceNo = preg_replace('/[^A-Za-z0-9\-_]/', '_', $po->no_invoice ?? $po->no_po);
                    $extension = $file->getClientOriginalExtension();
                    $timestamp = time();
                    $fileName = 'invoice_' . $invoiceNo . '_' . $timestamp . '.' . $extension;
                    
                    Storage::disk('public')->makeDirectory('invoices');
                    $filePath = $file->storeAs('invoices', $fileName, 'public');

                    if (!$filePath || !Storage::disk('public')->exists($filePath)) {
                        throw new \Exception('Gagal menyimpan bukti invoice');
                    }

                    PoProof::create([
                        'id_po' => $id_po,
                        'tipe_bukti' => 'invoice',
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'id_karyawan_upload' => Auth::user()->id_karyawan,
                        'tanggal_upload' => now(),
                        'is_active' => true,
                    ]);
                    
                    $uploadedFiles[] = 'invoice: ' . $fileName;
                    $deskripsiAksi[] = ($replaceMode ? 'Replace' : 'Tambah') . ' bukti invoice: ' . $fileName;
                }

                // Upload Bukti Barang
                if ($request->hasFile('bukti_barang')) {
                    if ($replaceMode) {
                        PoProof::where('id_po', $id_po)
                            ->where('tipe_bukti', 'barang')
                            ->where('is_active', true)
                            ->update(['is_active' => false]);
                    }

                    $file = $request->file('bukti_barang');
                    $invoiceNo = preg_replace('/[^A-Za-z0-9\-_]/', '_', $po->no_invoice ?? $po->no_po);
                    $extension = $file->getClientOriginalExtension();
                    $timestamp = time();
                    $fileName = 'barang_' . $invoiceNo . '_' . $timestamp . '.' . $extension;
                    
                    Storage::disk('public')->makeDirectory('barang');
                    $filePath = $file->storeAs('barang', $fileName, 'public');

                    if (!$filePath || !Storage::disk('public')->exists($filePath)) {
                        throw new \Exception('Gagal menyimpan bukti barang');
                    }

                    PoProof::create([
                        'id_po' => $id_po,
                        'tipe_bukti' => 'barang',
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'id_karyawan_upload' => Auth::user()->id_karyawan,
                        'tanggal_upload' => now(),
                        'is_active' => true,
                    ]);
                    
                    $uploadedFiles[] = 'barang: ' . $fileName;
                    $deskripsiAksi[] = ($replaceMode ? 'Replace' : 'Tambah') . ' bukti barang: ' . $fileName;
                }

                PoAuditTrail::create([
                    'id_po' => $po->id_po,
                    'id_karyawan' => Auth::user()->id_karyawan,
                    'pin_karyawan' => $request->pin,
                    'aksi' => 'upload_bukti',
                    'deskripsi_aksi' => implode('; ', $deskripsiAksi),
                    'data_sebelum' => ['total_proofs' => $po->proofs()->count()],
                    'data_sesudah' => ['total_proofs' => $po->fresh()->proofs()->count()],
                ]);

                DB::commit();

                $message = count($uploadedFiles) > 1 
                    ? 'Bukti invoice dan barang berhasil diupload'
                    : 'Bukti ' . (strpos($uploadedFiles[0], 'invoice') !== false ? 'invoice' : 'barang') . ' berhasil diupload';

                return response()->json([
                    'message' => $message,
                    'data' => [
                        'uploaded_files' => $uploadedFiles
                    ]
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Upload Proof Error', [
                'po_id' => $id_po,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Gagal upload bukti: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProof(Request $request, $id_po)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pin' => 'required|string|size:6|regex:/^[0-9]{6}$/',
                'type' => 'required|in:invoice,barang',
                'id_proof' => 'nullable|uuid'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
                ->where('pin', $request->pin)
                ->first();

            if (!$karyawan) {
                return response()->json(['error' => 'PIN tidak valid'], 403);
            }

            $po = PurchaseOrder::find($id_po);
            if (!$po) {
                return response()->json(['error' => 'Purchase Order tidak ditemukan'], 404);
            }

            DB::beginTransaction();
            try {
                $type = $request->type;
                
                if ($request->id_proof) {
                    $proof = PoProof::where('id_po_proof', $request->id_proof)
                        ->where('id_po', $id_po)
                        ->where('tipe_bukti', $type)
                        ->first();
                        
                    if (!$proof) {
                        return response()->json(['error' => 'Bukti tidak ditemukan'], 404);
                    }
                    
                    if (Storage::disk('public')->exists($proof->file_path)) {
                        Storage::disk('public')->delete($proof->file_path);
                    }
                    
                    $proof->delete();
                    
                    $message = 'Bukti ' . $type . ' berhasil dihapus';
                    $aksi = 'delete_bukti_' . $type;
                    
                } else {
                    $proofs = PoProof::where('id_po', $id_po)
                        ->where('tipe_bukti', $type)
                        ->where('is_active', true)
                        ->get();
                    
                    if ($proofs->isEmpty()) {
                        return response()->json(['error' => 'Bukti ' . $type . ' tidak ditemukan'], 404);
                    }
                    
                    foreach ($proofs as $proof) {
                        if (Storage::disk('public')->exists($proof->file_path)) {
                            Storage::disk('public')->delete($proof->file_path);
                        }
                        $proof->delete();
                    }
                    
                    $message = 'Semua bukti ' . $type . ' berhasil dihapus';
                    $aksi = 'delete_all_bukti_' . $type;
                }

                PoAuditTrail::create([
                    'id_po' => $po->id_po,
                    'id_karyawan' => Auth::user()->id_karyawan,
                    'pin_karyawan' => $request->pin,
                    'aksi' => $aksi,
                    'deskripsi_aksi' => $message,
                    'data_sebelum' => ['deleted_count' => isset($proofs) ? $proofs->count() : 1],
                    'data_sesudah' => ['bukti_count' => $po->fresh()->proofs()->count()],
                ]);

                DB::commit();

                return response()->json(['message' => $message], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Delete Proof Error', [
                'po_id' => $id_po,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Gagal menghapus bukti: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCompleted(Request $request)
    {
        try {
            $query = PurchaseOrder::with(['supplier', 'customer', 'items'])
                ->whereIn('status', ['diterima', 'selesai'])
                ->whereNotNull('no_gr')
                ->orderBy('tanggal_permintaan', 'desc');

            if ($request->has('supplier_id')) {
                $query->where('id_supplier', $request->supplier_id);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('no_po', 'like', "%{$search}%")
                      ->orWhere('no_gr', 'like', "%{$search}%")
                      ->orWhere('no_invoice', 'like', "%{$search}%");
                });
            }

            $purchaseOrders = $query->limit(100)->get();

            return response()->json([
                'success' => true,
                'data' => $purchaseOrders->map(function($po) {
                    return [
                        'id' => $po->id_po,
                        'no_po' => $po->no_po,
                        'no_gr' => $po->no_gr,
                        'no_invoice' => $po->no_invoice,
                        'tanggal_permintaan' => $po->tanggal_permintaan,
                        'status' => $po->status,
                        'supplier' => $po->supplier ? [
                            'id' => $po->supplier->id,
                            'nama' => $po->supplier->nama_supplier,
                        ] : null,
                        'total_items' => $po->items->count(),
                    ];
                }),
                'message' => 'Data berhasil diambil'
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting completed POs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getItems($id)
    {
        try {
            $po = PurchaseOrder::with([
                'items.produk',
                'items.produkSatuan.satuan'
            ])->findOrFail($id);

            $items = $po->items->map(function($item) {
                return [
                    'id_po_item' => $item->id_po_item,
                    'id_produk' => $item->id_produk,
                    'nama_produk' => $item->nama_produk,
                    'qty_diminta' => $item->qty_diminta,
                    'qty_diterima' => $item->qty_diterima ?? 0,
                    'harga_satuan' => $item->harga_satuan,
                    'satuan' => $item->produkSatuan->satuan->nama_satuan ?? 'PCS',
                    'konversi' => $item->konversi_snapshot,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $items,
                'message' => 'Items berhasil diambil'
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting PO items: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil items: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function pending()
    {
        $pos = PurchaseOrder::whereIn('status', [
            'menunggu_persetujuan_kepala_gudang',
            'menunggu_persetujuan_kasir'
        ])
        ->with(['karyawanPemohon', 'supplier', 'customer'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        $pos->getCollection()->transform(function($po) {
            $po->hours_left = $po->hoursLeftBeforeCancel();
            $po->is_near_deadline = $po->isNearDeadline();
            return $po;
        });

        return view('po.pending', compact('pos'));
    }

    public function getHargaCustomer(Request $request)
    {
        $request->validate([
            'customer_id'      => 'required|uuid',
            'produk_id'        => 'required|uuid',
            'produk_satuan_id' => 'nullable|uuid',
        ]);

        $detailCustomer = DetailCustomer::where('customer_id', $request->customer_id)
            ->where('produk_id', $request->produk_id)
            ->when(
                $request->filled('produk_satuan_id'),
                fn($q) => $q->where('produk_satuan_id', $request->produk_satuan_id)
            )
            ->first();

        if (!$detailCustomer) {
            return response()->json([
                'found' => false,
                'harga' => 0,
                'message' => 'Harga tidak ditemukan untuk customer & produk ini'
            ]);
        }

        return response()->json([
            'found' => true,
            'harga' => (float) $detailCustomer->harga_jual,
            'message' => 'OK'
        ]);
    }
}