<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailGudang;
use App\Models\Gudang;
use App\Models\Karyawan;
use App\Models\PoAuditTrail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderItemBatch;
use App\Models\StockMovement;
use App\Services\TagihanPoServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PoexConfirmationController extends Controller
{
    /**
     * Show form konfirmasi penerimaan
     * Kirim $gudangs agar tiap item bisa pilih gudang sendiri
     */
    public function showConfirmation($id_po)
    {
        $po = PurchaseOrder::with([
            'items.produk',
            'items.produkSatuan.satuan',
            'karyawanPemohon',
            'kepalaGudang',
            'kasir',
            'supplier',
        ])->findOrFail($id_po);

        if ($po->tipe_po !== 'pembelian' || $po->status !== 'disetujui') {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'PO ini tidak memerlukan konfirmasi penerimaan');
        }

        $gudangs = Gudang::where('status', 'Aktif')
            ->orderBy('nama_gudang')
            ->get();

        return view('po.confirmex-receipt', compact('po', 'gudangs'));
    }

    /**
     * Proses konfirmasi penerimaan
     *
     * gudang_id sekarang per item — satu PO bisa masuk ke banyak gudang berbeda
     *
     * Logika merge batch:
     *   - gudang_id + produk_id + no_batch + tanggal_kadaluarsa SAMA → increment stok
     *   - Berbeda salah satu → buat baris baru
     */
    public function confirmReceipt(Request $request, $id_po)
    {
        Log::info('=== START CONFIRM RECEIPT (PEMBELIAN) ===', [
            'po_id'   => $id_po,
            'user_id' => Auth::user()->id_karyawan,
        ]);

        $validator = Validator::make($request->all(), [
            'pin'                                  => 'required|size:6',
            'catatan_penerima'                     => 'nullable|string',
            'items'                                => 'required|array|min:1',
            // gudang_id wajib diisi per item
            'items.*.gudang_id'                    => 'required|uuid|exists:gudangs,id',
            'items.*.id_po_item'                   => 'required|uuid',
            'items.*.batches'                      => 'required|array|min:1',
            'items.*.batches.*.batch_number'       => 'nullable|string',
            'items.*.batches.*.tanggal_kadaluarsa' => 'required|date',
            'items.*.batches.*.qty_diterima'       => 'required|integer|min:1',
            'items.*.batches.*.kondisi'            => 'required|in:Baik,Rusak,Kadaluarsa',
            'items.*.batches.*.catatan'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => 'Data tidak valid: ' . implode(', ', $validator->errors()->all()),
            ], 422);
        }

        // Verifikasi PIN
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['success' => false, 'error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::with(['items', 'supplier'])->findOrFail($id_po);

            if ($po->tipe_po !== 'pembelian' || $po->status !== 'disetujui') {
                throw new \Exception('PO ini tidak dapat dikonfirmasi');
            }
            if (!$po->no_gr) {
                throw new \Exception('Nomor GR belum tersedia');
            }
            if (!$po->id_supplier) {
                throw new \Exception('Supplier pada PO ini tidak ditemukan');
            }

            // Cache gudang yang dipakai (hindari query berulang)
            $gudangCache = [];

            $dataBefore       = $po->toArray();
            $noGR             = $po->no_gr;
            $totalDiterima    = 0;
            $totalBaik        = 0;
            $totalRusak       = 0;
            $subtotalDiterima = 0;
            $itemsProcessed   = [];

            // Hapus batch lama jika re-konfirmasi
            foreach ($po->items as $item) {
                $item->batches()->delete();
            }

            foreach ($request->items as $itemData) {
                $poItem  = PurchaseOrderItem::findOrFail($itemData['id_po_item']);

                // ── Ambil gudang per item ──────────────────────────────
                $gudangId = $itemData['gudang_id'];
                if (!isset($gudangCache[$gudangId])) {
                    $gudang = Gudang::where('id', $gudangId)
                        ->where('status', 'Aktif')
                        ->first();
                    if (!$gudang) {
                        throw new \Exception("Gudang tidak ditemukan atau tidak aktif untuk item: {$poItem->nama_produk}");
                    }
                    $gudangCache[$gudangId] = $gudang;
                }
                $gudang = $gudangCache[$gudangId];
                // ────────────────────────────────────────────────────────

                $totalQtyItem     = 0;
                $totalQtyBaikItem = 0;

                foreach ($itemData['batches'] as $batchData) {
                    $qtyDiterima = (int) $batchData['qty_diterima'];
                    $kondisi     = $batchData['kondisi'];
                    $batchNumber = !empty($batchData['batch_number'])
                        ? $batchData['batch_number']
                        : 'BATCH-' . strtoupper(uniqid());

                    if ($qtyDiterima <= 0) continue;

                    $tanggalKadaluarsa = Carbon::parse($batchData['tanggal_kadaluarsa']);
                    $qtyDalamPcs       = $qtyDiterima * $poItem->konversi_snapshot;

                    // Simpan batch ke PO item
                    PurchaseOrderItemBatch::create([
                        'id_po_item'         => $poItem->id_po_item,
                        'batch_number'       => $batchNumber,
                        'tanggal_kadaluarsa' => $tanggalKadaluarsa,
                        'qty_diterima'       => $qtyDiterima,
                        'kondisi'            => strtolower($kondisi),
                        'catatan'            => $batchData['catatan'] ?? null,
                    ]);

                    $totalQtyItem += $qtyDiterima;

                    // ── Cari batch di gudang yang dipilih untuk item ini ──
                    // Key merge: gudang_id + produk_id + no_batch + tanggal_kadaluarsa
                    $detailGudang = DetailGudang::where('gudang_id', $gudang->id)
                        ->where('produk_id',  $poItem->id_produk)
                        ->where('no_batch',   $batchNumber)
                        ->whereDate('tanggal_kadaluarsa', $tanggalKadaluarsa->toDateString())
                        ->first();

                    if ($detailGudang) {
                        // Batch sudah ada → increment stok
                        $stokSebelum = $detailGudang->stock_gudang;
                        $detailGudang->increment('stock_gudang', $qtyDalamPcs);
                        $detailGudang->refresh();
                        $stokSesudah = $detailGudang->stock_gudang;

                        Log::info('DetailGudang incremented', [
                            'detail_id' => $detailGudang->id,
                            'gudang'    => $gudang->nama_gudang,
                            'no_batch'  => $batchNumber,
                            'stok_lama' => $stokSebelum,
                            'tambah'    => $qtyDalamPcs,
                            'stok_baru' => $stokSesudah,
                        ]);
                    } else {
                        // Batch baru → buat baris baru
                        $stokSebelum  = 0;
                        $detailGudang = DetailGudang::create([
                            'gudang_id'          => $gudang->id,
                            'produk_id'          => $poItem->id_produk,
                            'supplier_id'        => $po->id_supplier,
                            'stock_gudang'       => $qtyDalamPcs,
                            'min_persediaan'     => 0,
                            'no_batch'           => $batchNumber,
                            'tanggal_masuk'      => now(),
                            'tanggal_produksi'   => null,
                            'tanggal_kadaluarsa' => $tanggalKadaluarsa,
                            'lokasi_rak'         => null,
                            'kondisi'            => ucfirst($kondisi),
                        ]);
                        $stokSesudah = $qtyDalamPcs;

                        Log::info('DetailGudang created', [
                            'detail_id' => $detailGudang->id,
                            'gudang'    => $gudang->nama_gudang,
                            'no_batch'  => $batchNumber,
                            'stok_baru' => $stokSesudah,
                        ]);
                    }

                    // Catat stock movement
                    StockMovement::create([
                        'gudang_id'          => $gudang->id,
                        'produk_id'          => $poItem->id_produk,
                        'tipe'               => 'pembelian',
                        'referensi_tipe'     => 'purchase_orders',
                        'referensi_id'       => $po->id_po,
                        'referensi_no'       => $po->no_po,
                        'qty_sebelum'        => $stokSebelum,
                        'qty_perubahan'      => $qtyDalamPcs,
                        'qty_sesudah'        => $stokSesudah,
                        'no_batch'           => $batchNumber,
                        'tanggal_kadaluarsa' => $tanggalKadaluarsa,
                        'id_karyawan'        => Auth::user()->id_karyawan,
                        'catatan'            => "Penerimaan dari supplier: {$po->supplier->nama_supplier}"
                            . " — PO: {$po->no_po} — GR: {$noGR}"
                            . " — Gudang: {$gudang->nama_gudang}",
                    ]);

                    if ($kondisi === 'Baik') {
                        $totalBaik        += $qtyDiterima;
                        $totalQtyBaikItem += $qtyDiterima;
                    } else {
                        $totalRusak += $qtyDiterima;
                    }

                    $itemsProcessed[] = [
                        'produk'   => $poItem->nama_produk,
                        'gudang'   => $gudang->nama_gudang,
                        'batch'    => $batchNumber,
                        'exp_date' => $tanggalKadaluarsa->toDateString(),
                        'qty'      => $qtyDiterima,
                        'kondisi'  => $kondisi,
                    ];
                }

                // Update PO Item
                $qtyDiterimaSatuanDasar = $totalQtyItem * $poItem->konversi_snapshot;
                $poItem->update([
                    'qty_diterima'              => $totalQtyItem,
                    'qty_disetujui'             => $totalQtyItem,
                    'qty_diterima_satuan_dasar' => $qtyDiterimaSatuanDasar,
                ]);

                $totalDiterima    += $totalQtyItem;
                $subtotalDiterima += $totalQtyBaikItem * $poItem->harga_satuan;
            }

            // Hitung pajak proporsional
            $pajakDiterima = 0;
            if ($po->total_harga > 0 && $po->pajak > 0) {
                $pajakDiterima = ($subtotalDiterima / $po->total_harga) * $po->pajak;
            }
            $grandTotalDiterima = $subtotalDiterima + $pajakDiterima;

            // Update status PO
            $po->update([
                'status'               => 'selesai',
                'tanggal_diterima'     => now(),
                'id_penerima'          => Auth::user()->id_karyawan,
                'catatan_penerima'     => $request->catatan_penerima,
                'total_diterima'       => $subtotalDiterima,
                'pajak_diterima'       => $pajakDiterima,
                'grand_total_diterima' => $grandTotalDiterima,
            ]);

            // Update tagihan
            $tagihanService = new TagihanPoServices();
            $tagihan        = $tagihanService->updateTagihanAfterReceipt($po);

            // Ringkasan gudang untuk audit trail
            $gudangSummary = collect($gudangCache)->map(fn($g) => $g->nama_gudang)->join(', ');

            PoAuditTrail::create([
                'id_po'          => $po->id_po,
                'id_karyawan'    => Auth::user()->id_karyawan,
                'pin_karyawan'   => $request->pin,
                'aksi'           => 'konfirmasi_penerimaan',
                'deskripsi_aksi' => "Konfirmasi penerimaan GR: {$noGR}"
                    . " — Gudang: {$gudangSummary}"
                    . " — Total: {$totalDiterima} unit (Baik: {$totalBaik}, Rusak: {$totalRusak})",
                'data_sebelum'   => $dataBefore,
                'data_sesudah'   => $po->fresh()->toArray(),
            ]);

            DB::commit();

            Log::info('=== CONFIRM RECEIPT SUCCESS ===', [
                'po_id'           => $po->id_po,
                'no_gr'           => $noGR,
                'gudangs'         => array_keys($gudangCache),
                'total_diterima'  => $totalDiterima,
                'items_processed' => $itemsProcessed,
            ]);

            $message = "✓ Konfirmasi penerimaan berhasil! GR: {$noGR}. "
                . "{$totalDiterima} unit telah ditambahkan ke " . count($gudangCache) . " gudang.";
            if ($tagihan) {
                $message .= " Tagihan diupdate: Rp " . number_format($grandTotalDiterima, 0, ',', '.');
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data'    => [
                    'no_gr'                => $noGR,
                    'gudangs'              => collect($gudangCache)->map(fn($g) => [
                        'id'   => $g->id,
                        'nama' => $g->nama_gudang,
                    ])->values(),
                    'total_diterima'       => $totalDiterima,
                    'total_baik'           => $totalBaik,
                    'total_rusak'          => $totalRusak,
                    'subtotal_diterima'    => $subtotalDiterima,
                    'pajak_diterima'       => $pajakDiterima,
                    'grand_total_diterima' => $grandTotalDiterima,
                    'items_processed'      => $itemsProcessed,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== CONFIRM RECEIPT ERROR ===', [
                'po_id' => $id_po,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Gagal konfirmasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Form input invoice/faktur
     */
    public function showInvoiceForm($id_po)
    {
        $po = PurchaseOrder::with([
            'supplier',
            'items.produk',
            'items.produkSatuan.satuan',
        ])->findOrFail($id_po);

        if (!$po->needsInvoice()) {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'PO ini tidak memerlukan input invoice atau sudah diinput');
        }

        return view('po.invoice-form', compact('po'));
    }

    /**
     * Simpan data invoice/faktur
     */
    public function storeInvoice(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin'                 => 'required|size:6',
            'no_invoice'          => 'required|string|max:100',
            'tanggal_invoice'     => 'required|date',
            'surat_jalan'         => 'nullable|string|max:100',
            'tanggal_surat_jalan' => 'nullable|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_invoice',
            'nomor_faktur_pajak'  => 'nullable|string|max:100',
            'no_kwitansi'         => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => 'Data tidak valid: ' . implode(', ', $validator->errors()->all()),
            ], 422);
        }

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['success' => false, 'error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($id_po);

            if (!$po->needsInvoice()) {
                throw new \Exception('PO ini tidak memerlukan input invoice');
            }

            $dataBefore = $po->toArray();

            $po->update([
                'no_invoice'                => $request->no_invoice,
                'tanggal_invoice'           => $request->tanggal_invoice,
                'surat_jalan'               => $request->surat_jalan,
                'tanggal_surat_jalan'       => $request->tanggal_surat_jalan,
                'tanggal_jatuh_tempo'       => $request->tanggal_jatuh_tempo,
                'nomor_faktur_pajak'        => $request->nomor_faktur_pajak,
                'no_kwitansi'               => $request->no_kwitansi,
                'id_karyawan_input_invoice' => Auth::user()->id_karyawan,
                'tanggal_input_invoice'     => now(),
            ]);

            $tagihanService = new TagihanPoServices();
            $tagihanService->updateJatuhTempo($po, $request->tanggal_jatuh_tempo);

            PoAuditTrail::create([
                'id_po'          => $po->id_po,
                'id_karyawan'    => Auth::user()->id_karyawan,
                'pin_karyawan'   => $request->pin,
                'aksi'           => 'input_invoice',
                'deskripsi_aksi' => "Input invoice: {$request->no_invoice}, jatuh tempo: "
                    . Carbon::parse($request->tanggal_jatuh_tempo)->format('d/m/Y'),
                'data_sebelum'   => $dataBefore,
                'data_sesudah'   => $po->fresh()->toArray(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data invoice berhasil disimpan',
                'data'    => [
                    'no_invoice'          => $request->no_invoice,
                    'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Store Invoice Error', [
                'po_id' => $id_po,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Gagal menyimpan invoice: ' . $e->getMessage(),
            ], 500);
        }
    }
}