<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Search produk untuk Select2
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $search = $request->input('q', '');
        $jenis = $request->input('jenis', null);
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = Produk::where('status', 'aktif');

        // Filter berdasarkan jenis jika ada
        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        // Search by nama_produk, kode_produk, atau merk
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'LIKE', "%{$search}%")
                  ->orWhere('kode_produk', 'LIKE', "%{$search}%")
                  ->orWhere('merk', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();
        
        $produks = $query->select('id', 'kode_produk', 'nama_produk', 'merk', 'jenis', 'satuan', 'harga_beli')
                        ->orderBy('nama_produk')
                        ->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();

        $items = $produks->map(function($produk) {
            return [
                'id' => $produk->id,
                'text' => $produk->nama_produk . ' (' . $produk->kode_produk . ')',
                'kode_produk' => $produk->kode_produk,
                'nama_produk' => $produk->nama_produk,
                'merk' => $produk->merk,
                'jenis' => $produk->jenis,
                'satuan' => $produk->satuan,
                'harga_beli' => $produk->harga_beli,
            ];
        });

        return response()->json([
            'items' => $items,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }

    /**
     * Get produk by ID
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $produk->id,
                'kode_produk' => $produk->kode_produk,
                'nama_produk' => $produk->nama_produk,
                'merk' => $produk->merk,
                'jenis' => $produk->jenis,
                'satuan' => $produk->satuan,
                'harga_beli' => $produk->harga_beli,
                'harga_jual' => $produk->harga_jual,
            ]
        ]);
    }
}
