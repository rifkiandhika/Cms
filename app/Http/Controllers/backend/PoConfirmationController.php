<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailGudang;
use App\Models\DetailobatRs;
use App\Models\Gudang;
use App\Models\HistoryGudang;
use App\Models\Karyawan;
use App\Models\PoAuditTrail;
use App\Models\Produk;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\TagihanPoServices;

class PoConfirmationController extends Controller
{
    public function showConfirmation($id_po)
    {
        $po = PurchaseOrder::with([
            'items.produk',
            'karyawanPemohon',
            'kepalaGudang',
        ])->findOrFail($id_po);

        // ✅ Only PO Internal with status 'dikirim' (barang siap diterima)
        if ($po->tipe_po !== 'penjualan' || $po->status !== 'dikirim') {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'PO ini tidak memerlukan konfirmasi penerimaan');
        }

        Log::info('Show Confirmation Page:', [
            'po_id' => $id_po,
            'no_po' => $po->no_po,
            'tipe' => $po->tipe_po,
            'status' => $po->status,
            'items_count' => $po->items->count()
        ]);

        return view('po.confirm-receipt', compact('po'));
    }

    /**
     * Confirm receipt - Hanya mengurangi stok gudang dan mencatat history (PO INTERNAL ONLY)
     */
    public function confirmReceipt(Request $request, $id_po)
    {
        Log::info('=== START CONFIRM RECEIPT ===', [
            'po_id' => $id_po,
            'user_id' => Auth::user()->id_karyawan,
            'request_data' => $request->except('pin')
        ]);

        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'items' => 'required|array|min:1',
            'items.*.id_po_item' => 'required|uuid',
            'items.*.qty_diterima' => 'required|integer|min:0',
            'items.*.kondisi' => 'required|in:baik,rusak,kadaluarsa',
            'items.*.catatan' => 'nullable|string',
            'catatan_penerima' => 'nullable|string',
        ], [
            'items.*.id_po_item.required' => 'ID item tidak ditemukan. Mohon refresh halaman.',
            'items.*.id_po_item.uuid' => 'Format ID item tidak valid.',
        ]);

        if ($validator->fails()) {
            Log::error('Validation Failed:', $validator->errors()->toArray());
            
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            Log::warning('Invalid PIN', ['user_id' => Auth::user()->id_karyawan]);
            
            return response()->json([
                'success' => false,
                'error' => 'PIN tidak valid'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::with('items')->findOrFail($id_po);

            Log::info('PO Loaded:', [
                'po_id' => $po->id_po,
                'no_po' => $po->no_po,
                'tipe_po' => $po->tipe_po,
                'status' => $po->status,
                'items_count' => $po->items->count()
            ]);

            if ($po->tipe_po !== 'penjualan') {
                throw new \Exception('Hanya PO Internal yang dapat dikonfirmasi melalui halaman ini');
            }

            if ($po->status !== 'dikirim') {
                throw new \Exception('PO ini belum disetujui atau sudah dikonfirmasi sebelumnya. Status: ' . $po->status);
            }

            // ✅ VALIDASI: no_gr harus sudah ada (dari approval Kepala Gudang)
            if (!$po->no_gr) {
                throw new \Exception('Nomor GR belum tersedia. Silakan minta Kepala Gudang untuk approve PO terlebih dahulu.');
            }

            $dataBefore = $po->toArray();
            $noGr = $po->no_gr;

            $gudang = Gudang::first();
            if (!$gudang) {
                throw new \Exception('Gudang tidak ditemukan di sistem');
            }

            Log::info('Gudang Found:', ['gudang_id' => $gudang->id, 'nama' => $gudang->nama_gudang ?? 'N/A']);

            // ✅ TRACKING UNTUK TAGIHAN (sama seperti PO Pembelian)
            $totalDiterima = 0;        // Total qty yang diterima
            $totalBaik = 0;            // Total qty kondisi baik
            $totalRusak = 0;           // Total qty rusak/kadaluarsa
            $subtotalDiterima = 0;     // Subtotal harga (qty baik × harga)
            $pajakDiterima = 0;        // Pajak proporsional
            $grandTotalDiterima = 0;   // Grand total (subtotal + pajak)
            $itemsProcessed = [];

            foreach ($request->items as $itemData) {
                $poItem = PurchaseOrderItem::findOrFail($itemData['id_po_item']);

                Log::info('Processing Item:', [
                    'id_po_item' => $poItem->id_po_item,
                    'nama_produk' => $poItem->nama_produk,
                    'qty_diminta' => $poItem->qty_diminta,
                    'harga_satuan' => $poItem->harga_satuan
                ]);

                $produk = Produk::find($poItem->id_produk);

                if (!$produk) {
                    Log::error('Produk Not Found', ['id_produk' => $poItem->id_produk]);
                    throw new \Exception("Produk dengan ID {$poItem->id_produk} tidak ditemukan di master data");
                }

                $qtyDiterima = (int) $itemData['qty_diterima'];
                $kondisi = $itemData['kondisi'];
                $catatan = $itemData['catatan'] ?? null;

                if ($qtyDiterima == 0) {
                    Log::info('Skipping item with qty = 0', ['id_po_item' => $poItem->id_po_item]);
                    continue;
                }

                // Ambil detail gudang berdasarkan FIFO (expired date paling lama)
                $detailGudang = DetailGudang::where('barang_id', $poItem->id_produk)
                    ->where('gudang_id', $gudang->id)
                    ->where('stock_gudang', '>', 0)
                    ->orderBy('tanggal_kadaluarsa', 'asc')
                    ->first();

                if (!$detailGudang) {
                    Log::error('Detail Gudang Not Found', [
                        'barang_id' => $poItem->id_produk,
                        'gudang_id' => $gudang->id
                    ]);
                    throw new \Exception("Produk {$produk->nama} tidak ditemukan di gudang atau stock habis");
                }

                if ($detailGudang->stock_gudang < $qtyDiterima) {
                    Log::error('Insufficient Stock', [
                        'available' => $detailGudang->stock_gudang,
                        'requested' => $qtyDiterima
                    ]);
                    throw new \Exception("Stock {$produk->nama} (Batch: {$detailGudang->no_batch}) tidak mencukupi. Tersedia: {$detailGudang->stock_gudang}, Diminta: {$qtyDiterima}");
                }

                // Update PO Item
                $poItem->update([
                    'qty_diterima' => $qtyDiterima,
                    'qty_disetujui' => $qtyDiterima,
                    'kondisi_barang' => $kondisi,
                    'batch_number' => $detailGudang->no_batch,
                    'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa,
                ]);

                // ✅ KURANGI STOK GUDANG
                $stockBefore = $detailGudang->stock_gudang;
                $detailGudang->decrement('stock_gudang', $qtyDiterima);
                $detailGudang->refresh();

                Log::info('Stock Gudang Dikurangi:', [
                    'batch' => $detailGudang->no_batch,
                    'stock_before' => $stockBefore,
                    'decrement' => $qtyDiterima,
                    'stock_after' => $detailGudang->stock_gudang
                ]);

                // ✅ CATAT HISTORY PENGURANGAN STOK GUDANG
                $keteranganKondisi = $kondisi === 'baik' ? 'Barang Baik' : ucfirst($kondisi);
                
                HistoryGudang::create([
                    'gudang_id' => $gudang->id,
                    'supplier_id' => null,
                    'barang_id' => $poItem->id_produk,
                    'no_batch' => $detailGudang->no_batch,
                    'jumlah' => $qtyDiterima,
                    'waktu_proses' => now(),
                    'status' => 'keluar',
                    'referensi_type' => 'penjualan',
                    'referensi_id' => $po->id_po,
                    'no_referensi' => $noGr,
                    'keterangan' => "Konfirmasi PO Penjualan - {$po->no_po} | GR: {$noGr} | Unit: {$po->unit_pemohon} | Kondisi: {$keteranganKondisi}" . ($catatan ? " | Catatan: {$catatan}" : ""),
                ]);

                // ✅ HITUNG NILAI UNTUK TAGIHAN (hanya barang kondisi baik yang ditagih)
                $qtyBaik = ($kondisi === 'baik') ? $qtyDiterima : 0;
                $subtotalItem = $qtyBaik * $poItem->harga_satuan;
                
                // Update counters
                $totalDiterima += $qtyDiterima;
                
                if ($kondisi === 'baik') {
                    $totalBaik += $qtyDiterima;
                    $subtotalDiterima += $subtotalItem;
                } else {
                    $totalRusak += $qtyDiterima;
                }

                $itemsProcessed[] = [
                    'product' => $produk->nama,
                    'batch' => $detailGudang->no_batch,
                    'qty' => $qtyDiterima,
                    'kondisi' => $kondisi,
                    'harga_satuan' => $poItem->harga_satuan,
                    'subtotal' => $subtotalItem,
                    'stock_before' => $stockBefore,
                    'stock_after' => $detailGudang->stock_gudang,
                ];

                Log::info('Item Processed Successfully', [
                    'produk' => $produk->nama,
                    'qty' => $qtyDiterima,
                    'kondisi' => $kondisi,
                    'harga_satuan' => $poItem->harga_satuan,
                    'subtotal' => $subtotalItem
                ]);
            }

            // ✅ HITUNG PAJAK PROPORSIONAL (sama seperti PO Pembelian)
            if ($po->total_harga > 0 && $po->pajak > 0) {
                $pajakDiterima = ($subtotalDiterima / $po->total_harga) * $po->pajak;
            }
            
            $grandTotalDiterima = $subtotalDiterima + $pajakDiterima;

            // ✅ Update status PO menjadi selesai dengan tracking nilai diterima
            $po->update([
                'status' => 'selesai',
                'tanggal_diterima' => now(),
                'id_penerima' => Auth::user()->id_karyawan,
                'catatan_penerima' => $request->catatan_penerima,
                // ✅ Simpan nilai yang diterima (sama seperti PO Pembelian)
                'total_diterima' => $subtotalDiterima,
                'pajak_diterima' => $pajakDiterima,
                'grand_total_diterima' => $grandTotalDiterima,
            ]);

            Log::info('PO Status Updated to SELESAI', [
                'total_harga_diminta' => $po->total_harga,
                'pajak_diminta' => $po->pajak,
                'grand_total_diminta' => $po->grand_total,
                'total_diterima' => $subtotalDiterima,
                'pajak_diterima' => $pajakDiterima,
                'grand_total_diterima' => $grandTotalDiterima,
            ]);

            // ✅ UPDATE TAGIHAN setelah penerimaan
            $tagihanService = new TagihanPoServices();
            $tagihan = $tagihanService->updateTagihanAfterReceipt($po);

            if ($tagihan) {
                Log::info('Tagihan updated after receipt', [
                    'tagihan_id' => $tagihan->id_tagihan,
                    'status' => $tagihan->status,
                    'total_tagihan' => $tagihan->total_tagihan,
                    'pajak' => $tagihan->pajak,
                    'grand_total' => $tagihan->grand_total,
                    'sisa_tagihan' => $tagihan->sisa_tagihan
                ]);
            } else {
                Log::warning('No tagihan found to update', ['po_id' => $po->id_po]);
            }

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'konfirmasi_penerimaan',
                'deskripsi_aksi' => "Konfirmasi penerimaan dengan GR: {$noGr} - Total: {$totalDiterima} unit (Baik: {$totalBaik}, Rusak/Kadaluarsa: {$totalRusak}). Stok gudang telah dikurangi. Nilai tagihan: Rp " . number_format($grandTotalDiterima, 0, ',', '.') . ($tagihan ? " | Tagihan diupdate ke status: {$tagihan->status}" : ""),
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            Log::info('=== CONFIRM RECEIPT SUCCESS ===', [
                'po_id' => $po->id_po,
                'no_gr' => $noGr,
                'total_diterima' => $totalDiterima,
                'total_baik' => $totalBaik,
                'total_rusak' => $totalRusak,
                'subtotal_diterima' => $subtotalDiterima,
                'pajak_diterima' => $pajakDiterima,
                'grand_total_diterima' => $grandTotalDiterima,
                'items_processed' => count($itemsProcessed),
                'tagihan_status' => $tagihan?->status ?? 'no_tagihan'
            ]);

            $message = "✓ Konfirmasi penerimaan berhasil dengan nomor GR: {$noGr}!";
            if ($totalBaik > 0) {
                $message .= " {$totalBaik} unit (kondisi baik) telah dikurangi dari stok gudang.";
            }
            if ($totalRusak > 0) {
                $message .= " {$totalRusak} unit (rusak/kadaluarsa) telah dikurangi dari stok gudang.";
            }
            if ($tagihan) {
                $message .= " Tagihan telah diupdate ke status '{$tagihan->status}' dengan total Rp " . number_format($tagihan->grand_total, 0, ',', '.');
            }
            $message .= " History telah tercatat untuk tracking.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $po->fresh()->load('items'),
                'items_processed' => $itemsProcessed,
                'no_gr' => $noGr,
                'summary' => [
                    'total_diterima' => $totalDiterima,
                    'total_baik' => $totalBaik,
                    'total_rusak' => $totalRusak,
                    'subtotal_diterima' => $subtotalDiterima,
                    'pajak_diterima' => $pajakDiterima,
                    'grand_total_diterima' => $grandTotalDiterima,
                ],
                'tagihan' => $tagihan ? [
                    'id_tagihan' => $tagihan->id_tagihan,
                    'no_tagihan' => $tagihan->no_tagihan,
                    'status' => $tagihan->status,
                    'total_tagihan' => $tagihan->total_tagihan,
                    'pajak' => $tagihan->pajak,
                    'grand_total' => $tagihan->grand_total,
                    'sisa_tagihan' => $tagihan->sisa_tagihan,
                ] : null
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== CONFIRM RECEIPT ERROR ===', [
                'po_id' => $id_po,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal konfirmasi: ' . $e->getMessage()
            ], 500);
        }
    }
}