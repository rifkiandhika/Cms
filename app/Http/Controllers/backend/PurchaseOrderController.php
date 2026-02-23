<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DetailGudang;
use App\Models\DetailobatRs;
use App\Models\DetailstockApotik;
use App\Models\DetailSupplier;
use App\Models\Gudang;
use App\Models\Karyawan;
use App\Models\ObatRs;
use App\Models\PoAuditTrail;
use App\Models\PoProof;
use App\Models\Produk;
use App\Models\ProdukSatuan;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ShippingActivity;
use App\Models\StockApotik;
use App\Models\Supplier;
use App\Models\TagihanPoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TagihanPoServices;
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
        $type = $request->get('type', 'pembelian'); // penjualan atau pembelian
        $suppliers = Supplier::where('status', 'Aktif')->get();
        $customers = Customer::where('status', 'aktif')->get();

        if ($type === 'penjualan') {
            $gudang = Gudang::first();
            if (!$gudang) {
                return back()->with('error', 'Gudang tidak ditemukan');
            }

            // ✅ Tambahkan with('produk.produkSatuans.satuan') agar satuans ikut ter-load
            $detailGudangs = DetailGudang::with(['produk.produkSatuans.satuan'])
                ->where('gudang_id', $gudang->id)
                ->where('stock_gudang', '>', 0)
                ->whereNotNull('barang_id')
                ->get();

            $produkList = [];

            foreach ($detailGudangs as $detail) {
                if (!$detail->produk) continue;

                $produk = $detail->produk;

                // ✅ Ambil semua satuan jual produk ini beserta harga finalnya
                $satuans = $produk->produkSatuans->map(function ($ps) use ($produk) {
                    return [
                        'id'         => $ps->id,
                        'label'      => $ps->label,
                        'isi'        => (float) $ps->isi,
                        'harga_jual' => $ps->harga_jual_final,  // pakai accessor dari model
                        'is_default' => (bool) $ps->is_default,
                    ];
                })->values()->toArray();

                $produkList[] = [
                    'id'               => $produk->id,
                    'detail_gudang_id' => $detail->id,
                    'nama'             => $produk->nama_produk,
                    'merk'             => $produk->merk ?? '',
                    // Jika tidak ada satuan jual terdefinisi, fallback ke harga_jual lama
                    'harga_jual'       => $produk->harga_jual ?? 0,
                    'stock_gudang'     => $detail->stock_gudang,
                    'no_batch'         => $detail->no_batch ?? '-',
                    'tanggal_kadaluarsa' => $detail->tanggal_kadaluarsa
                        ? \Carbon\Carbon::parse($detail->tanggal_kadaluarsa)->format('d/m/Y')
                        : '-',
                    // ✅ INI YANG BARU: array satuan jual
                    'satuans'          => $satuans,
                ];
            }
        } else {
            // ✅ PO PEMBELIAN: Beli dari Supplier - AMBIL DARI DETAIL_SUPPLIER
            $detailSuppliers = DetailSupplier::with(['supplier', 'produk'])
                ->whereNotNull('product_id')
                ->get();

            $produkList = $detailSuppliers->map(function ($detail) {
                if (!$detail->produk) return null;

                $produk = $detail->produk;

                return [
                    'id' => $produk->id,
                    'detail_supplier_id' => $detail->id,
                    'supplier_id' => $detail->supplier_id,
                    'nama' => $produk->nama_produk,
                    'jenis' => $detail->jenis ?? 'lainnya', // ✅ Ambil jenis dari detail_supplier
                    'merk' => $produk->merk ?? '',
                    'satuan' => $produk->satuan ?? 'pcs',
                    'harga_beli' => $detail->harga_beli ?? 0, // ✅ Ambil harga dari detail_supplier
                    'supplier_name' => $detail->supplier->nama_supplier ?? '-',
                ];
            })->filter()->values();
        }

        return view('po.create', compact('type', 'suppliers', 'customers', 'produkList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe_po'                    => 'required|in:penjualan,pembelian',
            'id_unit_pemohon'            => 'required',
            'unit_pemohon'               => 'required|string',
            'catatan_pemohon'            => 'nullable|string',
            'id_supplier'                => 'required_if:tipe_po,pembelian|nullable|uuid',
            'id_customer'                => 'required_if:tipe_po,penjualan|nullable|uuid',
            'pajak'                      => 'nullable|numeric',
            'pajak_persen'               => 'nullable|numeric',
            'items'                      => 'required|array|min:1',
            'items.*.id_produk'          => 'required|uuid',
            'items.*.id_produk_satuan'   => 'nullable|uuid',
            'items.*.qty_diminta'        => 'required|integer|min:1',
            'items.*.jenis'              => 'nullable|string',
            'items.*.harga'              => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $items = $request->items;
            $total = 0;

            /** ===============================
             * HITUNG TOTAL
             * =============================== */
            foreach ($items as $item) {
                $produk = Produk::find($item['id_produk']);

                if (!$produk) {
                    throw new \Exception('Produk tidak ditemukan: ' . $item['id_produk']);
                }

                $harga = 0;

                if ($request->tipe_po === 'penjualan') {
                    // Ambil harga dari satuan yang dipilih
                    if (!empty($item['id_produk_satuan'])) {
                        $produkSatuan = ProdukSatuan::find($item['id_produk_satuan']);
                        $harga = $produkSatuan
                            ? $produkSatuan->harga_jual_final
                            : ($produk->harga_jual ?? 0);
                    } else {
                        $harga = $produk->harga_jual ?? 0;
                    }
                } else {
                    // Pembelian: ambil dari detail_supplier
                    $detailSupplier = DetailSupplier::where('product_id', $item['id_produk'])
                        ->where('supplier_id', $request->id_supplier)
                        ->first();

                    if (!$detailSupplier) {
                        throw new \Exception('Detail supplier tidak ditemukan untuk produk: ' . $produk->nama_produk);
                    }

                    $harga = $detailSupplier->harga_beli ?? 0;
                }

                $total += $harga * $item['qty_diminta'];
            }

            $pajak      = $request->pajak ?? 0;
            $grandTotal = $total + $pajak;

            /** ===============================
             * CREATE PO
             * =============================== */
            $po = PurchaseOrder::create([
                'tipe_po'              => $request->tipe_po,
                'status'               => 'draft',
                'id_unit_pemohon'      => $request->id_unit_pemohon,
                'unit_pemohon'         => $request->unit_pemohon,
                'id_karyawan_pemohon'  => Auth::user()->id_karyawan,
                'tanggal_permintaan'   => now(),
                'catatan_pemohon'      => $request->catatan_pemohon,
                'unit_tujuan'          => $request->tipe_po === 'penjualan' ? 'customer' : 'supplier',
                'id_supplier'          => $request->tipe_po === 'pembelian'
                                            ? $request->id_supplier
                                            : $request->id_customer,
                'total_harga'          => $total,
                'pajak'                => $pajak,
                'grand_total'          => $grandTotal,
                'tanggal_jatuh_tempo'  => now()->addDays(30),
            ]);

            /** ===============================
             * CREATE PO ITEMS
             * =============================== */
            foreach ($items as $item) {
                $produk = Produk::find($item['id_produk']);

                if ($request->tipe_po === 'penjualan') {
                    // ── PENJUALAN ──
                    $hargaSatuan = 0;
                    $satuanLabel = null;

                    if (!empty($item['id_produk_satuan'])) {
                        $produkSatuan = ProdukSatuan::find($item['id_produk_satuan']);
                        if ($produkSatuan) {
                            $hargaSatuan = $produkSatuan->harga_jual_final;
                            $satuanLabel = $produkSatuan->label;
                        }
                    } else {
                        $hargaSatuan = $produk->harga_jual ?? 0;
                    }

                    PurchaseOrderItem::create([
                        'id_po'        => $po->id_po,
                        'id_produk'    => $produk->id,
                        'nama_produk'  => $produk->nama_produk,
                        'qty_diminta'  => $item['qty_diminta'],
                        'harga_satuan' => $hargaSatuan,
                        'subtotal'     => $hargaSatuan * $item['qty_diminta'],
                        'jenis'        => $satuanLabel,
                    ]);

                } else {
                    // ── PEMBELIAN ──
                    $hargaSatuan = 0;
                    $jenis       = null;

                    $detailSupplier = DetailSupplier::where('product_id', $item['id_produk'])
                        ->where('supplier_id', $request->id_supplier)
                        ->first();

                    if ($detailSupplier) {
                        $hargaSatuan = $detailSupplier->harga_beli ?? 0;
                        $jenis       = $detailSupplier->jenis ?? 'lainnya';
                    }

                    PurchaseOrderItem::create([
                        'id_po'        => $po->id_po,
                        'id_produk'    => $produk->id,
                        'nama_produk'  => $produk->nama_produk,
                        'qty_diminta'  => $item['qty_diminta'],
                        'harga_satuan' => $hargaSatuan,
                        'subtotal'     => $hargaSatuan * $item['qty_diminta'],
                        'jenis'        => $jenis,
                    ]);

                    // Update stock_po
                    $detailSupplierStock = DetailSupplier::where('product_id', $produk->id)
                        ->where('supplier_id', $request->id_supplier)
                        ->first();

                    if ($detailSupplierStock) {
                        $detailSupplierStock->increment('stock_po', $item['qty_diminta']);
                    }
                }
            }

            /** ===============================
             * AUDIT TRAIL
             * =============================== */
            PoAuditTrail::create([
                'id_po'          => $po->id_po,
                'id_karyawan'    => Auth::user()->id_karyawan,
                'pin_karyawan'   => $request->pin ?? null,
                'aksi'           => 'buat_po',
                'deskripsi_aksi' => 'Membuat PO ' . ucfirst($request->tipe_po),
                'data_sesudah'   => $po->toArray(),
            ]);

            /** ===============================
             * TAGIHAN
             * =============================== */
            $tagihanService = new TagihanPoServices();
            $tagihanService->createTagihanFromPO($po);

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
            'karyawanPemohon',
            'kepalaGudang',
            'kasir',
            'supplier',
            'customer',
            'auditTrails.karyawan'
        ])->findOrFail($id_po);

        if (request()->wantsJson()) {
            return response()->json($po, 200);
        }

        return view('po.show', compact('po'));
    }

    public function edit($id_po)
    {
        $po = PurchaseOrder::with('items')->findOrFail($id_po);

        if (!in_array($po->status, ['draft', 'ditolak', 'selesai'])) {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'Hanya PO dengan status draft atau ditolak yang dapat diedit');
        }

        $suppliers = Supplier::where('status', 'Aktif')->get();
        $customers = Customer::where('status', 'aktif')->get();

        if ($po->tipe_po === 'penjualan') {
            $gudang = Gudang::first();
            if (!$gudang) {
                return back()->with('error', 'Gudang tidak ditemukan');
            }

            $detailGudangs = DetailGudang::with(['produk'])
                ->where('gudang_id', $gudang->id)
                ->where('stock_gudang', '>', 0)
                ->whereNotNull('barang_id')
                ->get();

            $produkList = [];

            foreach ($detailGudangs as $detail) {
                if (!$detail->produk) continue;

                $produk = $detail->produk;

                $produkList[] = [
                    'id' => $produk->id,
                    'detail_gudang_id' => $detail->id,
                    'nama' => $produk->nama_produk,
                    'merk' => $produk->merk ?? '',
                    'satuan' => $produk->satuan ?? 'pcs',
                    'harga_jual' => $produk->harga_jual ?? 0,
                    'stock_gudang' => $detail->stock_gudang,
                    'no_batch' => $detail->no_batch ?? '-',
                    'tanggal_kadaluarsa' => $detail->tanggal_kadaluarsa
                        ? \Carbon\Carbon::parse($detail->tanggal_kadaluarsa)->format('d/m/Y')
                        : '-',
                ];
            }
        } else {
            // ✅ PO PEMBELIAN - AMBIL DARI DETAIL_SUPPLIER
            $detailSuppliers = DetailSupplier::with(['supplier', 'produk'])
                ->where('supplier_id', $po->id_supplier)
                ->whereNotNull('product_id')
                ->get();

            $produkList = $detailSuppliers->map(function ($detail) {
                if (!$detail->produk) return null;

                $produk = $detail->produk;

                return [
                    'id' => $produk->id,
                    'detail_supplier_id' => $detail->id,
                    'supplier_id' => $detail->supplier_id,
                    'nama' => $produk->nama_produk,
                    'jenis' => $detail->jenis ?? 'lainnya', // ✅ Dari detail_supplier
                    'merk' => $produk->merk ?? '',
                    'satuan' => $produk->satuan ?? 'pcs',
                    'harga_beli' => $detail->harga_beli ?? 0, // ✅ Dari detail_supplier
                ];
            })->filter()->values();
        }

        return view('po.edit', compact('po', 'suppliers', 'customers', 'produkList'));
    }

    public function update(Request $request, $id_po)
    {
        $po = PurchaseOrder::findOrFail($id_po);

        if (!in_array($po->status, ['draft', 'ditolak'])) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Hanya PO draft atau ditolak yang dapat diedit'], 400);
            }
            return back()->with('error', 'Hanya PO dengan status draft atau ditolak yang dapat diedit');
        }

        $validated = $request->validate([
            'catatan_pemohon' => 'nullable|string',
            'pin' => 'required|size:6',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|uuid',
            'items.*.qty_diminta' => 'required|integer|min:1',
            'items.*.jenis' => 'nullable|string', // ✅ Ubah validasi
        ]);

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'PIN tidak valid'], 403);
            }
            return back()->withErrors(['pin' => 'PIN tidak valid']);
        }

        DB::beginTransaction();
        try {
            $dataBefore = $po->toArray();
            $statusSebelum = $po->status;

            // Delete old items & reset stock_po
            foreach ($po->items as $oldItem) {
                if ($po->tipe_po === 'pembelian') {
                    $detailSupplier = DetailSupplier::where('product_id', $oldItem->id_produk)
                        ->where('supplier_id', $po->id_supplier)
                        ->first();

                    if ($detailSupplier) {
                        $detailSupplier->decrement('stock_po', $oldItem->qty_diminta);
                    }
                }
            }
            $po->items()->delete();

            // Create new items
            $total = 0;
            foreach ($request->items as $item) {
                $produk = Produk::find($item['id_produk']);

                if (!$produk) {
                    throw new \Exception('Produk tidak ditemukan');
                }

                $hargaSatuan = 0;
                $jenis = null;
                
                if ($po->tipe_po === 'penjualan') {
                    $hargaSatuan = $produk->harga_jual ?? 0;
                } else {
                    // ✅ Pembelian: ambil dari detail_supplier
                    $detailSupplier = DetailSupplier::where('product_id', $item['id_produk'])
                        ->where('supplier_id', $po->id_supplier)
                        ->first();
                    
                    if ($detailSupplier) {
                        $hargaSatuan = $detailSupplier->harga_beli ?? 0;
                        $jenis = $detailSupplier->jenis ?? 'lainnya';
                    }
                }

                PurchaseOrderItem::create([
                    'id_po' => $po->id_po,
                    'id_produk' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'qty_diminta' => $item['qty_diminta'],
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $hargaSatuan * $item['qty_diminta'],
                    'jenis' => $jenis, // ✅ Simpan jenis
                ]);

                $total += $hargaSatuan * $item['qty_diminta'];

                // Update stock_po untuk pembelian
                if ($po->tipe_po === 'pembelian') {
                    $detailSupplier = DetailSupplier::where('product_id', $produk->id)
                        ->where('supplier_id', $po->id_supplier)
                        ->first();

                    if ($detailSupplier) {
                        $detailSupplier->increment('stock_po', $item['qty_diminta']);
                    }
                }
            }

            // Update PO
            $updateData = [
                'catatan_pemohon' => $request->catatan_pemohon,
                'total_harga' => $total,
                'grand_total' => $total + $po->pajak,
            ];

            if ($statusSebelum === 'ditolak') {
                $updateData['status'] = 'draft';
            }

            $po->update($updateData);

            // ✅ Update tagihan juga
            if ($po->hasTagihan()) {
                $tagihan = $po->tagihan;
                
                // Delete old tagihan items
                $tagihan->items()->delete();
                
                // Create new tagihan items
                foreach ($po->items as $item) {
                    TagihanPoItem::create([
                        'id_tagihan' => $tagihan->id_tagihan,
                        'id_po_item' => $item->id_po_item,
                        'id_produk' => $item->id_produk,
                        'nama_produk' => $item->nama_produk,
                        'qty_diminta' => $item->qty_diminta,
                        'qty_diterima' => 0,
                        'qty_ditagihkan' => 0,
                        'harga_satuan' => $item->harga_satuan,
                        'subtotal' => 0,
                    ]);
                }
                
                // Update tagihan header
                $tagihan->update([
                    'total_tagihan' => $total,
                    'grand_total' => $total + $po->pajak,
                    'sisa_tagihan' => ($total + $po->pajak) - $tagihan->total_dibayar,
                ]);
            }

            $deskripsiAksi = 'Mengupdate PO';
            if ($statusSebelum === 'ditolak') {
                $deskripsiAksi .= ' (status berubah dari ditolak menjadi draft)';
            }

            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'edit_po',
                'deskripsi_aksi' => $deskripsiAksi,
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            $successMessage = 'Purchase Order berhasil diupdate';
            if ($statusSebelum === 'ditolak') {
                $successMessage .= ' dan status berubah menjadi draft';
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $successMessage,
                    'data' => $po->load('items')
                ], 200);
            }

            return redirect()->route('po.show', $po->id_po)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Gagal update PO: ' . $e->getMessage()], 500);
            }

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
            // Reset stock_po jika pembelian
            if ($po->tipe_po === 'pembelian') {
                foreach ($po->items as $item) {
                    $detailSupplier = DetailSupplier::where('product_id', $item->id_produk)
                        ->where('supplier_id', $po->id_supplier)
                        ->first();

                    if ($detailSupplier) {
                        $detailSupplier->decrement('stock_po', $item->qty_diminta);
                    }
                }
            }

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

    public function submit(Request $request, $id_po)
    {
        DB::beginTransaction();

        try {
            $po = PurchaseOrder::findOrFail($id_po);
            $dataBefore = $po->toArray();

            // Validasi status (opsional tapi sangat disarankan)
            if (!in_array($po->status, ['draft', 'ditolak'])) {
                return response()->json([
                    'error' => 'PO tidak dapat disubmit pada status saat ini'
                ], 403);
            }

            // Status berikutnya
            $newStatus = 'menunggu_persetujuan_kepala_gudang';

            $po->update([
                'status' => $newStatus
            ]);

            // Audit Trail
            PoAuditTrail::create([
                'id_po'        => $po->id_po,
                'id_karyawan'  => Auth::user()->id_karyawan,
                'pin_karyawan' => null, // PIN tidak digunakan
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
     * - PO Penjualan: Generate no_gr dan set status 'dikirim'
     * - PO Pembelian: Lanjut ke approval kasir
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
                // Jika ditolak, ubah status ke ditolak
                $po->update(['status' => 'ditolak']);

                // Audit Trail
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
                // ✅ PO PENJUALAN: Generate no_gr dan set status ke 'dikirim'
                $noGR = PurchaseOrder::generateNoGR();

                // ✅ Status = 'dikirim' (bukan 'diterima')
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

            // Audit Trail
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

    public function markAsReceived(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
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

            
            if (!in_array($po->status, ['disetujui'])) {
                return response()->json([
                    'error' => 'PO tidak dapat ditandai sebagai diterima. Status saat ini: ' . $po->status
                ], 400);
            }

            $dataBefore = $po->toArray();

            
            $noGR = PurchaseOrder::generateNoGR();

            
            $po->update([
                'status' => 'diterima',
                'no_gr' => $noGR,  
            ]);

            
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
                'message' => 'Barang dari supplier berhasil ditandai sebagai diterima dengan nomor GR: ' . $noGR . '. Silakan lakukan konfirmasi penerimaan untuk update stok gudang.',
                'data' => [
                    'po' => $po->fresh()->load(['items']),
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

    public function printInvoice($id_po)
    {
        $po = PurchaseOrder::with([
            'items',
            'supplier',
            'customer',
            'karyawanPemohon',
            'karyawanInputInvoice',
            'kepalaGudang',
            'kasir'
        ])->findOrFail($id_po);

        // Pastikan PO sudah ada invoice
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

            $po->update([
                'id_kasir_approval' => Auth::user()->id_karyawan,
                'tanggal_approval_kasir' => now(),
                'catatan_kasir' => $request->catatan,
                'status_approval_kasir' => $request->status_approval,
                'status' => $statusPO,
            ]);

            // Audit Trail
            $aksi = $request->status_approval === 'disetujui' ? 'approve_kasir' : 'reject_kasir';
            $deskripsi = $request->status_approval === 'disetujui'
                ? 'Kasir menyetujui PO'
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
            return response()->json(['message' => 'Approval Kasir berhasil', 'data' => $po], 200);
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

    // Backward compatibility methods
    public function createInternalPO(Request $request)
    {
        $request->merge(['tipe_po' => 'penjualan']);
        return $this->store($request);
    }

    public function createExternalPO(Request $request)
    {
        $request->merge(['tipe_po' => 'pembelian']);
        return $this->store($request);
    }

    public function submitInternalPO(Request $request, $id_po)
    {
        return $this->submit($request, $id_po);
    }

    public function submitExternalPO(Request $request, $id_po)
    {
        return $this->submit($request, $id_po);
    }

    // ========== API: APPROVE BY KEPALA GUDANG (backward compatibility) ==========
    public function approveByKepalaGudang(Request $request, $id_po)
    {
        return $this->approveKepalaGudang($request, $id_po);
    }

    // ========== API: APPROVE BY KASIR (backward compatibility) ==========
    public function approveByKasir(Request $request, $id_po)
    {
        return $this->approveKasir($request, $id_po);
    }

    public function uploadProof(Request $request, $id_po)
    {
        try {
            Log::info('Upload Proof Request', [
                'po_id' => $id_po,
                'user_id' => Auth::id(),
                'has_invoice' => $request->hasFile('bukti_invoice'),
                'has_barang' => $request->hasFile('bukti_barang'),
            ]);

            // Validasi - minimal satu file harus ada
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
            ], [
                'bukti_invoice.file' => 'Bukti invoice harus berupa file yang valid',
                'bukti_invoice.mimes' => 'Format bukti invoice harus JPEG, JPG, PNG, atau PDF',
                'bukti_invoice.max' => 'Ukuran bukti invoice maksimal 5MB',
                'bukti_barang.file' => 'Bukti barang harus berupa file yang valid',
                'bukti_barang.mimes' => 'Format bukti barang harus JPEG, JPG, PNG, atau PDF',
                'bukti_barang.max' => 'Ukuran bukti barang maksimal 5MB',
                'pin.required' => 'PIN harus diisi',
                'pin.size' => 'PIN harus 6 digit',
                'pin.regex' => 'PIN harus berupa 6 digit angka',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for proof upload', [
                    'errors' => $validator->errors()->toArray(),
                    'po_id' => $id_po
                ]);
                
                return response()->json([
                    'error' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Minimal satu file harus ada
            if (!$request->hasFile('bukti_invoice') && !$request->hasFile('bukti_barang')) {
                return response()->json([
                    'error' => 'Minimal satu file (Invoice atau Barang) harus diupload'
                ], 422);
            }

            // Verifikasi PIN
            $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
                ->where('pin', $request->pin)
                ->first();

            if (!$karyawan) {
                Log::warning('Invalid PIN for proof upload', [
                    'user_id' => Auth::user()->id_karyawan,
                    'po_id' => $id_po
                ]);
                
                return response()->json([
                    'error' => 'PIN yang Anda masukkan tidak valid'
                ], 403);
            }

            // Cari PO
            $po = PurchaseOrder::find($id_po);
            
            if (!$po) {
                Log::error('PO not found', ['po_id' => $id_po]);
                return response()->json([
                    'error' => 'Purchase Order tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();
            try {
                $uploadedFiles = [];
                $deskripsiAksi = [];
                
                // ✅ Cek apakah ini mode replace atau add
                $replaceMode = $request->input('replace_mode', false);

                // Upload Bukti Invoice
                if ($request->hasFile('bukti_invoice')) {
                    // ✅ Hanya non-aktifkan jika mode replace
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

                    // ✅ Simpan bukti baru ke tabel po_proofs
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
                    // ✅ Hanya non-aktifkan jika mode replace
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

                    // ✅ Simpan bukti baru ke tabel po_proofs
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

                // Audit Trail
                PoAuditTrail::create([
                    'id_po' => $po->id_po,
                    'id_karyawan' => Auth::user()->id_karyawan,
                    'pin_karyawan' => $request->pin,
                    'aksi' => 'upload_bukti',
                    'deskripsi_aksi' => implode('; ', $deskripsiAksi),
                    'data_sebelum' => json_encode(['total_proofs' => $po->proofs()->count()]),
                    'data_sesudah' => json_encode(['total_proofs' => $po->fresh()->proofs()->count()]),
                ]);

                DB::commit();

                Log::info('Proof Uploaded Successfully', [
                    'po_id' => $po->id_po,
                    'files' => $uploadedFiles,
                    'uploaded_by' => Auth::user()->id_karyawan,
                    'mode' => $replaceMode ? 'replace' : 'add'
                ]);

                $message = count($uploadedFiles) > 1 
                    ? 'Bukti invoice dan barang berhasil diupload'
                    : 'Bukti ' . (isset($uploadedFiles[0]) && strpos($uploadedFiles[0], 'invoice') !== false ? 'invoice' : 'barang') . ' berhasil diupload';

                return response()->json([
                    'message' => $message,
                    'data' => [
                        'uploaded_files' => $uploadedFiles
                    ]
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                
                Log::error('Transaction failed during proof upload', [
                    'po_id' => $id_po,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Upload Proof Error', [
                'po_id' => $id_po,
                'user_id' => Auth::user()->id_karyawan ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
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

            // Verify PIN
            $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
                ->where('pin', $request->pin)
                ->first();

            if (!$karyawan) {
                return response()->json([
                    'error' => 'PIN tidak valid'
                ], 403);
            }

            $po = PurchaseOrder::find($id_po);
            if (!$po) {
                return response()->json([
                    'error' => 'Purchase Order tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();
            try {
                $type = $request->type;
                
                // Jika ada id_proof spesifik, hapus bukti tersebut
                if ($request->id_proof) {
                    $proof = PoProof::where('id_po_proof', $request->id_proof)
                        ->where('id_po', $id_po)
                        ->where('tipe_bukti', $type)
                        ->first();
                        
                    if (!$proof) {
                        return response()->json([
                            'error' => 'Bukti tidak ditemukan'
                        ], 404);
                    }
                    
                    // Hapus file fisik
                    if (Storage::disk('public')->exists($proof->file_path)) {
                        Storage::disk('public')->delete($proof->file_path);
                    }
                    
                    // Soft delete
                    $proof->delete();
                    
                    $message = 'Bukti ' . $type . ' berhasil dihapus';
                    $aksi = 'delete_bukti_' . $type;
                    
                } else {
                    // Hapus semua bukti aktif dari tipe tertentu
                    $proofs = PoProof::where('id_po', $id_po)
                        ->where('tipe_bukti', $type)
                        ->where('is_active', true)
                        ->get();
                    
                    if ($proofs->isEmpty()) {
                        return response()->json([
                            'error' => 'Bukti ' . $type . ' tidak ditemukan'
                        ], 404);
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
                    'data_sebelum' => json_encode(['deleted_count' => $proofs->count() ?? 1]),
                    'data_sesudah' => json_encode(['bukti_count' => $po->fresh()->proofs()->count()]),
                ]);

                DB::commit();

                return response()->json([
                    'message' => $message
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Delete Proof Error', [
                'po_id' => $id_po,
                'type' => $request->type ?? null,
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
                ->whereIn('status', ['completed', 'diterima', 'selesai'])
                ->whereNotNull('no_gr')
                ->whereNotNull('no_invoice')
                ->orderBy('tanggal_permintaan', 'desc');

            // Optional: Filter by supplier
            if ($request->has('supplier_id')) {
                $query->where('id_supplier', $request->supplier_id);
            }

            // Optional: Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('no_po', 'like', "%{$search}%")
                    ->orWhere('no_gr', 'like', "%{$search}%")
                    ->orWhere('no_invoice', 'like', "%{$search}%")
                    ->orWhere('kode_po', 'like', "%{$search}%");
                });
            }

            // Limit untuk performance
            $purchaseOrders = $query->limit(100)->get();

            return response()->json([
                'success' => true,
                'data' => $purchaseOrders->map(function($po) {
                    return [
                        'id' => $po->id,
                        'id_po' => $po->id_po ?? $po->id,
                        'no_po' => $po->no_po,
                        'no_gr' => $po->no_gr,
                        'no_invoice' => $po->no_invoice,
                        'kode_po' => $po->kode_po ?? $po->no_po,
                        'tanggal_permintaan' => $po->tanggal_permintaan,
                        'tanggal_pengiriman' => $po->tanggal_pengiriman ?? null,
                        'status' => $po->status,
                        'supplier' => [
                            'id' => $po->supplier->id ?? null,
                            'nama' => $po->supplier->nama_supplier ?? $po->supplier->nama ?? 'N/A',
                            'nama_supplier' => $po->supplier->nama_supplier ?? $po->supplier->nama ?? 'N/A',
                        ],
                        'customer' => $po->customer ? [
                            'id' => $po->customer->id,
                            'nama' => $po->customer->nama_customer ?? 'N/A',
                        ] : null,
                        'total_items' => $po->items->count(),
                    ];
                }),
                'message' => 'Data berhasil diambil'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting completed POs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get PO items dengan batches
     * Endpoint: GET /api/purchase-orders/{id}/items
     */
    public function getItems($id)
    {
        try {
            $po = PurchaseOrder::with([
                'items.produk',
                'items.batches'
            ])->findOrFail($id);

            $items = $po->purchaseOrderItems->map(function($item) {
                return [
                    'id_po_item' => $item->id_po_item ?? $item->id,
                    'id_produk' => $item->id_produk,
                    'nama_produk' => $item->nama_produk,
                    'qty_dipesan' => $item->qty_dipesan ?? $item->qty,
                    'qty_diterima' => $item->qty_diterima ?? $item->qty,
                    'harga_satuan' => $item->harga_satuan ?? 0,
                    'satuan' => $item->satuan ?? 'pcs',
                    'batches' => $item->batches ? $item->batches->map(function($batch) {
                        return [
                            'batch_number' => $batch->batch_number,
                            'tanggal_kadaluarsa' => $batch->tanggal_kadaluarsa,
                            'qty_diterima' => $batch->qty_diterima ?? $batch->qty,
                            'kondisi' => $batch->kondisi ?? 'baik',
                        ];
                    }) : [],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $items,
                'message' => 'Items berhasil diambil'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting PO items: ' . $e->getMessage());
            
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

        // Add deadline info untuk setiap PO
        $pos->getCollection()->transform(function($po) {
            $po->hours_left = $po->hoursLeftBeforeCancel();
            $po->is_near_deadline = $po->isNearDeadline();
            return $po;
        });

        return view('po.pending', compact('pos'));
    }
}