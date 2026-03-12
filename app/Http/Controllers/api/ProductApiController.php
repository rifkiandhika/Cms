<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Search produk untuk Select2
     */
    public function search(Request $request)
    {
        $search   = $request->input('q', '');
        $jenis    = $request->input('jenis', null);
        $page     = (int) $request->input('page', 1);
        $perPage  = 10;

        $query = Produk::with(['produkSatuans.satuan'])
            ->where('status', 'aktif');

        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'LIKE', "%{$search}%")
                  ->orWhere('kode_produk', 'LIKE', "%{$search}%")
                  ->orWhere('merk', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();

        $produks = $query->orderBy('nama_produk')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $items = $produks->map(function ($produk) {

            $satuans = $produk->produkSatuans->map(function ($ps) {
                return [
                    'id'         => $ps->id,
                    'label'      => $ps->label,             // ✅ dari accessor getLabelAttribute()
                    'isi'        => (int) $ps->konversi,    // ✅ pakai konversi, bukan isi
                    'is_default' => (bool) $ps->is_default,
                ];
            })->values();

            return [
                'id'             => $produk->id,
                'text'           => $produk->nama_produk . ' (' . $produk->kode_produk . ')',
                'kode_produk'    => $produk->kode_produk,
                'nama_produk'    => $produk->nama_produk,
                'merk'           => $produk->merk,
                'jenis'          => $produk->jenis,
                'produk_satuans' => $satuans,
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
     * Get detail produk by ID
     */
    public function show($id)
    {
        $produk = Produk::with(['produkSatuans.satuan'])->find($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $satuans = $produk->produkSatuans->map(function ($ps) {
            return [
                'id'         => $ps->id,
                'label'      => $ps->label,             // ✅
                'isi'        => (int) $ps->konversi,    // ✅
                'is_default' => (bool) $ps->is_default,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'id'          => $produk->id,
                'kode_produk' => $produk->kode_produk,
                'nama_produk' => $produk->nama_produk,
                'merk'        => $produk->merk,
                'jenis'       => $produk->jenis,
                'satuans'     => $satuans,
            ]
        ]);
    }


    /**
     * Get satuan produk by produk_id
     */
    public function getSatuans(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|uuid|exists:produks,id',
        ]);

        $produk = Produk::with(['produkSatuans.satuan'])
            ->findOrFail($request->produk_id);

        $satuans = $produk->produkSatuans->map(function ($ps) {
            return [
                'id'         => $ps->id,
                'label'      => $ps->label,             // ✅
                'isi'        => (int) $ps->konversi,    // ✅
                'is_default' => (bool) $ps->is_default,
                'satuan'     => $ps->satuan ? [
                    'id'          => $ps->satuan->id,
                    'nama_satuan' => $ps->satuan->nama_satuan,
                ] : null,
            ];
        })->values();

        return response()->json([
            'produk_id'   => $produk->id,
            'nama_produk' => $produk->nama_produk,
            'satuans'     => $satuans,
        ]);
    }
}