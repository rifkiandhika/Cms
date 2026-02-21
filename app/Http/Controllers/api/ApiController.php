<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\DetailSupplier;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Search products dari supplier tertentu
     * 
     * @param string $supplierId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchSupplierProducts($supplierId, Request $request)
    {
        $query = $request->get('q', '');

        // Ambil semua detail supplier dengan relasi produk
        $detailSuppliers = DetailSupplier::with('produk')
            ->where('supplier_id', $supplierId)
            ->get();

        $results = [];

        foreach ($detailSuppliers as $detail) {
            $nama = null;
            $barangId = null;

            // Jika ada relasi ke produk, gunakan data dari produk
            if ($detail->product_id && $detail->produk) {
                $nama = $detail->produk->nama_produk;
                $barangId = $detail->produk->id; // UUID dari tabel produks
            } else {
                // Jika tidak ada product_id (produk manual/lainnya), gunakan dari kolom nama
                $nama = $detail->nama;
                $barangId = $detail->id; // Gunakan ID detail_supplier sebagai referensi
            }

            // Skip jika nama kosong
            if (!$nama) continue;

            // Filter berdasarkan query pencarian
            if ($query) {
                $searchLower = strtolower($query);
                $namaMatch = stripos(strtolower($nama), $searchLower) !== false;
                $judulMatch = stripos(strtolower($detail->judul ?? ''), $searchLower) !== false;
                $jenisMatch = stripos(strtolower($detail->jenis ?? ''), $searchLower) !== false;
                $merkMatch = stripos(strtolower($detail->merk ?? ''), $searchLower) !== false;

                if (!$namaMatch && !$judulMatch && !$jenisMatch && !$merkMatch) {
                    continue;
                }
            }

            $results[] = [
                'id' => $barangId,
                'product_id' => $detail->product_id, // UUID produk atau null
                'detail_supplier_id' => $detail->id,
                'nama' => $nama,
                'judul' => $detail->judul ?? '-',
                'jenis' => $detail->jenis ?? '-',
                'merk' => $detail->merk ?? '-',
                'satuan' => $detail->satuan ?? '-',
                'exp_date' => $detail->exp_date ?? '',
                'no_batch' => $detail->no_batch ?? '',
                'stock_live' => $detail->stock_live ?? 0,
                'stock_po' => $detail->stock_po ?? 0,
                'min_persediaan' => $detail->min_persediaan ?? 0,
                'harga_beli' => $detail->harga_beli ?? 0,
                'kode_rak' => $detail->kode_rak ?? '-',
                'type' => $detail->jenis, // Penting untuk membedakan tipe barang
                'kode_produk' => $detail->produk->kode_produk ?? null, // Tambahan kode produk jika ada
            ];
        }

        return response()->json($results);
    }

    /**
     * Get detail produk dari supplier by detail_supplier_id
     * 
     * @param string $detailSupplierId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupplierProductDetail($detailSupplierId)
    {
        $detail = DetailSupplier::with('produk')
            ->find($detailSupplierId);

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Detail supplier tidak ditemukan'
            ], 404);
        }

        $nama = null;
        if ($detail->product_id && $detail->produk) {
            $nama = $detail->produk->nama_produk;
        } else {
            $nama = $detail->nama;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $detail->produk->id ?? $detail->id,
                'product_id' => $detail->product_id,
                'detail_supplier_id' => $detail->id,
                'nama' => $nama,
                'kode_produk' => $detail->produk->kode_produk ?? null,
                'judul' => $detail->judul ?? '-',
                'jenis' => $detail->jenis ?? '-',
                'merk' => $detail->merk ?? '-',
                'satuan' => $detail->satuan ?? '-',
                'exp_date' => $detail->exp_date ?? '',
                'no_batch' => $detail->no_batch ?? '',
                'stock_live' => $detail->stock_live ?? 0,
                'stock_po' => $detail->stock_po ?? 0,
                'min_persediaan' => $detail->min_persediaan ?? 0,
                'harga_beli' => $detail->harga_beli ?? 0,
                'kode_rak' => $detail->kode_rak ?? '-',
            ]
        ]);
    }

    /**
     * Get all products by supplier ID (untuk dropdown/select)
     * 
     * @param string $supplierId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupplierProductList($supplierId)
    {
        $detailSuppliers = DetailSupplier::with('produk')
            ->where('supplier_id', $supplierId)
            ->get();

        $results = $detailSuppliers->map(function ($detail) {
            $nama = null;
            if ($detail->product_id && $detail->produk) {
                $nama = $detail->produk->nama_produk;
            } else {
                $nama = $detail->nama;
            }

            return [
                'id' => $detail->id,
                'product_id' => $detail->product_id,
                'text' => $nama . ' - ' . ($detail->jenis ?? 'Lainnya'),
                'nama' => $nama,
                'jenis' => $detail->jenis,
                'merk' => $detail->merk,
                'satuan' => $detail->satuan,
                'harga_beli' => $detail->harga_beli,
            ];
        });

        return response()->json($results);
    }
}