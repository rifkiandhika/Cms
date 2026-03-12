<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailGudang;
use App\Models\Gudang;
use App\Models\Karyawan;
use App\Models\PoAuditTrail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PoConfirmationController extends Controller
{
    /**
     * Show form konfirmasi penerimaan untuk PO Penjualan
     */
    public function showConfirmation($id_po)
    {
        $po = PurchaseOrder::with([
            'items.produk',
            'items.produkSatuan.satuan',
            'karyawanPemohon',
            'kepalaGudang',
            'customer',
        ])->findOrFail($id_po);

        // Validasi: hanya PO Penjualan dengan status 'dikirim'
        if ($po->tipe_po !== 'penjualan' || $po->status !== 'dikirim') {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'PO ini tidak memerlukan konfirmasi penerimaan');
        }

        return view('po.confirm-receipt', compact('po'));
    }

    /**
     * Proses konfirmasi penerimaan untuk PO Penjualan
     *
     * Mendukung multi baris kondisi per item:
     *   - Item A → Baris 1: qty 10 baik, Baris 2: qty 5 rusak
     *   - Stok gudang dikurangi total qty (baik + rusak) karena semua sudah dikirim ke customer
     *   - Pengurangan stok menggunakan FIFO (tanggal kadaluarsa terpendek lebih dulu)
     *   - Jika stok tersebar di beberapa batch/supplier, pengurangan dilakukan berurutan (FIFO multi-row)
     */
    public function confirmReceipt(Request $request, $id_po)
    {
        Log::info('=== START CONFIRM RECEIPT (PENJUALAN) ===', [
            'po_id'   => $id_po,
            'user_id' => Auth::user()->id_karyawan,
        ]);

        $validator = Validator::make($request->all(), [
            'pin'                                 => 'required|size:6',
            'catatan_penerima'                    => 'nullable|string',
            'items'                               => 'required|array|min:1',
            'items.*.id_po_item'                  => 'required|uuid',
            'items.*.kondisi_rows'                => 'required|array|min:1',
            'items.*.kondisi_rows.*.qty_diterima' => 'required|integer|min:1',
            'items.*.kondisi_rows.*.kondisi'      => 'required|in:baik,rusak,kadaluarsa',
            'items.*.kondisi_rows.*.catatan'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => 'Data tidak valid: ' . implode(', ', $validator->errors()->all()),
            ], 422);
        }

        // 🔐 Verifikasi PIN
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'error'   => 'PIN tidak valid'
            ], 403);
        }

        DB::beginTransaction();
        try {

            $po = PurchaseOrder::with(['items.detailGudang.gudang', 'customer'])
                ->findOrFail($id_po);

            if ($po->tipe_po !== 'penjualan' || $po->status !== 'dikirim') {
                throw new \Exception('PO ini tidak dapat dikonfirmasi. Status saat ini: ' . $po->status);
            }

            if (!$po->no_gr) {
                throw new \Exception('Nomor GR belum tersedia');
            }

            $dataBefore    = $po->toArray();
            $noGR          = $po->no_gr;
            $totalDiterima = 0;
            $totalBaik     = 0;
            $totalRusak    = 0;

            foreach ($request->items as $itemData) {

                $poItem = PurchaseOrderItem::with('detailGudang.gudang')
                    ->findOrFail($itemData['id_po_item']);

                // 🔢 Hitung total qty
                $totalQtyItem = collect($itemData['kondisi_rows'])
                    ->sum(fn($r) => (int) $r['qty_diterima']);

                if ($totalQtyItem <= 0) continue;

                $totalQtyBaikItem = collect($itemData['kondisi_rows'])
                    ->where('kondisi', 'baik')
                    ->sum('qty_diterima');

                $totalQtyItemPcs = $totalQtyItem * $poItem->konversi_snapshot;

                // ===============================
                // 🔍 AMBIL DETAIL GUDANG
                // ===============================

                if ($poItem->detail_gudang_id) {

                    $detailGudang = DetailGudang::where('id', $poItem->detail_gudang_id)
                        ->where('no_batch', $poItem->batch_number)
                        ->whereDate('tanggal_kadaluarsa', $poItem->tanggal_kadaluarsa)
                        ->first();

                    if (!$detailGudang) {
                        throw new \Exception(
                            "Data stok untuk produk \"{$poItem->nama_produk}\" tidak cocok di gudang."
                        );
                    }

                    $stokSebelum = $detailGudang->stock_gudang;

                    if ($stokSebelum < $totalQtyItemPcs) {
                        throw new \Exception(
                            "Stok batch \"{$detailGudang->no_batch}\" tidak mencukupi. " .
                            "Tersedia: {$stokSebelum}, dibutuhkan: {$totalQtyItemPcs}"
                        );
                    }

                    $detailGudang->decrement('stock_gudang', $totalQtyItemPcs);

                    $stokSesudah = $stokSebelum - $totalQtyItemPcs;
                    $firstBatch  = $detailGudang;
                    $gudangId    = $detailGudang->gudang_id;

                } else {

                    // 🔁 FIFO fallback
                    $detailGudangs = DetailGudang::where('produk_id', $poItem->id_produk)
                        ->where('stock_gudang', '>', 0)
                        ->orderBy('tanggal_kadaluarsa')
                        ->get();

                    if ($detailGudangs->isEmpty()) {
                        throw new \Exception("Stok produk \"{$poItem->nama_produk}\" tidak ditemukan.");
                    }

                    $sisaKurang = $totalQtyItemPcs;
                    $stokSebelum = $detailGudangs->sum('stock_gudang');
                    $firstBatch = null;

                    foreach ($detailGudangs as $dg) {
                        if ($sisaKurang <= 0) break;

                        if (!$firstBatch) $firstBatch = $dg;

                        $ambil = min($dg->stock_gudang, $sisaKurang);
                        $dg->decrement('stock_gudang', $ambil);
                        $sisaKurang -= $ambil;
                    }

                    if ($sisaKurang > 0) {
                        throw new \Exception("Stok tidak mencukupi untuk produk {$poItem->nama_produk}");
                    }

                    $stokSesudah = $stokSebelum - $totalQtyItemPcs;
                    $gudangId    = $firstBatch->gudang_id;
                }

                // ===============================
                // 📦 STOCK MOVEMENT
                // ===============================

                StockMovement::create([
                    'gudang_id'          => $gudangId,
                    'produk_id'          => $poItem->id_produk,
                    'tipe'               => 'penjualan',
                    'referensi_tipe'     => 'purchase_orders',
                    'referensi_id'       => $po->id_po,
                    'referensi_no'       => $po->no_po,
                    'qty_sebelum'        => $stokSebelum,
                    'qty_perubahan'      => -$totalQtyItemPcs,
                    'qty_sesudah'        => $stokSesudah,
                    'no_batch'           => $firstBatch->no_batch,
                    'tanggal_kadaluarsa' => $firstBatch->tanggal_kadaluarsa,
                    'id_karyawan'        => Auth::user()->id_karyawan,
                    'catatan'            => "Penjualan ke {$po->customer->nama_customer} — PO: {$po->no_po} — GR: {$noGR}",
                ]);

                // ===============================
                // UPDATE PO ITEM
                // ===============================

                $poItem->update([
                    'qty_diterima'              => $totalQtyItem,
                    'qty_disetujui'             => $totalQtyItem,
                    'qty_diterima_satuan_dasar' => $totalQtyItemPcs,
                    'batch_number'              => $firstBatch->no_batch,
                    'tanggal_kadaluarsa'        => $firstBatch->tanggal_kadaluarsa,
                    'kondisi_barang'            => $totalQtyBaikItem === $totalQtyItem ? 'baik' : 'campuran',
                ]);

                $totalDiterima += $totalQtyItem;
                $totalBaik     += $totalQtyBaikItem;
                $totalRusak    += ($totalQtyItem - $totalQtyBaikItem);
            }

            if ($totalDiterima === 0) {
                throw new \Exception('Tidak ada item yang dikonfirmasi.');
            }

            $po->update([
                'status'           => 'selesai',
                'tanggal_diterima' => now(),
                'id_penerima'      => Auth::user()->id_karyawan,
                'catatan_penerima' => $request->catatan_penerima,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "✓ Konfirmasi berhasil. {$totalDiterima} unit dikurangi dari stok.",
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('CONFIRM RECEIPT ERROR', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Gagal konfirmasi: ' . $e->getMessage()
            ], 500);
        }
    }
}