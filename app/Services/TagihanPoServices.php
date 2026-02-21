<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\TagihanPo;
use App\Models\TagihanPoItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagihanPoServices
{
    /**
     * Auto-create tagihan saat PO dibuat (Penjualan & Pembelian)
     */
    public function createTagihanFromPO(PurchaseOrder $po)
    {
        // ✅ Support untuk Penjualan & Pembelian
        if (!in_array($po->tipe_po, ['penjualan', 'pembelian'])) {
            Log::info('Skip create tagihan - Invalid tipe_po', ['po_id' => $po->id_po]);
            return null;
        }

        // Cek apakah sudah ada tagihan
        if ($po->hasTagihan()) {
            Log::info('Skip create tagihan - Already exists', ['po_id' => $po->id_po]);
            return $po->tagihan;
        }

        DB::beginTransaction();
        try {
            // ✅ id_supplier berisi customer_id (penjualan) atau supplier_id (pembelian)
            $idRelasi = $po->id_supplier; // Kolom id_supplier di PO

            // ✅ Tentukan tipe_relasi
            $tipeRelasi = $po->tipe_po === 'penjualan' ? 'customer' : 'supplier';

            // Create tagihan header (status: draft)
            $tagihan = TagihanPo::create([
                'id_po' => $po->id_po,
                'id_relasi' => $idRelasi, // ✅ Menggunakan kolom yang sudah direname
                'tipe_relasi' => $tipeRelasi, // ✅ customer atau supplier
                'status' => 'draft',
                'total_tagihan' => $po->total_harga,
                'pajak' => $po->pajak,
                'grand_total' => $po->grand_total,
                'total_dibayar' => 0,
                'sisa_tagihan' => $po->grand_total,
                'tenor_hari' => 30,
                'id_karyawan_buat' => $po->id_karyawan_pemohon,
                'catatan' => 'Auto-generated dari PO ' . ucfirst($po->tipe_po) . ': ' . $po->no_po,
            ]);

            // Create tagihan items
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
                    'batch_number' => $item->batch_number ?? null,
                    'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa ?? null,
                ]);
            }

            DB::commit();

            Log::info('Tagihan created successfully', [
                'tagihan_id' => $tagihan->id_tagihan,
                'no_tagihan' => $tagihan->no_tagihan,
                'po_id' => $po->id_po,
                'tipe_po' => $po->tipe_po,
                'tipe_relasi' => $tipeRelasi
            ]);

            return $tagihan;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create tagihan', [
                'po_id' => $po->id_po,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update tagihan saat barang diterima
     */
    public function updateTagihanAfterReceipt(PurchaseOrder $po)
    {
        if (!$po->hasTagihan()) {
            Log::warning('Tagihan not found for PO', ['po_id' => $po->id_po]);
            return null;
        }

        DB::beginTransaction();
        try {
            $tagihan = $po->tagihan;

            // Hitung subtotal berdasarkan qty_diterima (hanya barang kondisi baik)
            $subtotalDiterima = 0;

            foreach ($tagihan->items as $tagihanItem) {
                $poItem = $po->items->firstWhere('id_po_item', $tagihanItem->id_po_item);

                if ($poItem) {
                    // ✅ REFRESH poItem untuk mendapatkan data terbaru
                    $poItem->refresh();
                    
                    // Hitung qty yang kondisi baik saja
                    $qtyBaik = 0;
                    $qtyDiterima = $poItem->qty_diterima ?? 0;
                    
                    // ✅ CEK TIPE PO
                    if ($po->tipe_po === 'pembelian') {
                        // Untuk PO Pembelian: gunakan batches
                        if ($poItem->batches && $poItem->batches->count() > 0) {
                            foreach ($poItem->batches as $batch) {
                                if ($batch->kondisi === 'baik') {
                                    $qtyBaik += $batch->qty_diterima;
                                }
                            }
                        } else {
                            $qtyBaik = $qtyDiterima;
                        }
                    } else {
                        // ✅ Untuk PO Penjualan: cek kondisi_barang langsung
                        if ($poItem->kondisi_barang === 'baik') {
                            $qtyBaik = $qtyDiterima;
                        } else {
                            $qtyBaik = 0; // Rusak/kadaluarsa tidak ditagihkan
                        }
                    }

                    $subtotal = $qtyBaik * $tagihanItem->harga_satuan;

                    $tagihanItem->update([
                        'qty_diterima' => $qtyDiterima,
                        'qty_ditagihkan' => $qtyBaik,
                        'subtotal' => $subtotal,
                        'batch_number' => $poItem->batch_number ?? null,
                        'tanggal_kadaluarsa' => $poItem->tanggal_kadaluarsa ?? null,
                    ]);

                    $subtotalDiterima += $subtotal;
                    
                    Log::info('TagihanItem updated', [
                        'id_po_item' => $poItem->id_po_item,
                        'kondisi_barang' => $poItem->kondisi_barang,
                        'qty_diterima' => $qtyDiterima,
                        'qty_baik' => $qtyBaik,
                        'harga_satuan' => $tagihanItem->harga_satuan,
                        'subtotal' => $subtotal
                    ]);
                }
            }

            // Hitung pajak proporsional
            $pajakProporsional = 0;
            $pajakAwal = $po->pajak ?? 0;
            $totalHargaAwal = $po->total_harga ?? 0;

            if ($totalHargaAwal > 0 && $pajakAwal > 0) {
                $pajakProporsional = ($subtotalDiterima / $totalHargaAwal) * $pajakAwal;
            }

            $grandTotal = $subtotalDiterima + $pajakProporsional;

            // Update tagihan header
            $tagihan->update([
                'status' => 'menunggu_pembayaran',
                'total_tagihan' => $subtotalDiterima,
                'pajak' => $pajakProporsional,
                'grand_total' => $grandTotal,
                'sisa_tagihan' => $grandTotal - $tagihan->total_dibayar,
                'tanggal_tagihan' => now(),
                'tanggal_jatuh_tempo' => now()->addDays((int) $tagihan->tenor_hari),
            ]);

            DB::commit();

            Log::info('Tagihan updated after receipt', [
                'tagihan_id' => $tagihan->id_tagihan,
                'tipe_po' => $po->tipe_po,
                'subtotal_diterima' => $subtotalDiterima,
                'pajak_proporsional' => $pajakProporsional,
                'grand_total' => $grandTotal
            ]);

            return $tagihan;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update tagihan after receipt', [
                'po_id' => $po->id_po,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cancel tagihan
     */
    public function cancelTagihan(PurchaseOrder $po)
    {
        if (!$po->hasTagihan()) {
            return null;
        }

        $tagihan = $po->tagihan;

        if ($tagihan->total_dibayar > 0) {
            throw new \Exception('Tagihan tidak dapat dibatalkan karena sudah ada pembayaran');
        }

        $tagihan->update(['status' => 'dibatalkan']);

        Log::info('Tagihan cancelled', [
            'tagihan_id' => $tagihan->id_tagihan,
            'po_id' => $po->id_po
        ]);

        return $tagihan;
    }
}