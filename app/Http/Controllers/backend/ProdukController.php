<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use App\Models\Produk;
use App\Models\Satuan;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produks = Produk::with('jenis')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('produk.index', compact('produks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenis = Jenis::where('status', 'Aktif')
            ->orderBy('nama_jenis')
            ->get();
        $satuan = Satuan::where('status', 'Aktif')
            ->orderBy('nama_satuan')
            ->get();

        return view('produk.create', compact('jenis', 'satuan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nie' => 'required|string|max:255',
            'nama_produk' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'jenis' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Bersihkan format rupiah jika ada
        $validated['harga_beli'] = $this->cleanRupiahFormat($validated['harga_beli']);
        $validated['harga_jual'] = $this->cleanRupiahFormat($validated['harga_jual']);

        try {
            Produk::create($validated);

            Alert::success('success', 'Produk berhasil ditambahkan!');
            return redirect()
                ->route('produks.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produk = Produk::with('jenis')->findOrFail($id);
        return view('produk.show', compact('produk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $produk = Produk::findOrFail($id);
        
        $jenis = Jenis::where('status', 'Aktif')
            ->orderBy('nama_jenis')
            ->get();
        $satuan = Satuan::where('status', 'Aktif')
            ->orderBy('nama_satuan')
            ->get();

        return view('produk.edit', compact('produk', 'jenis', 'satuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'nie' => 'required|string|max:255',
            'nama_produk' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'jenis' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Bersihkan format rupiah jika ada
        $validated['harga_beli'] = $this->cleanRupiahFormat($validated['harga_beli']);
        $validated['harga_jual'] = $this->cleanRupiahFormat($validated['harga_jual']);

        try {
            $produk->update($validated);

            Alert::success('success', 'Produk berhasil diperbarui!');
            return redirect()
                ->route('produks.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $produk = Produk::findOrFail($id);
            $produk->delete();

            Alert::success('success', 'Produk berhasil dihapus!');
            return redirect()
                ->route('produks.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    /**
     * Helper function untuk membersihkan format rupiah
     */
    private function cleanRupiahFormat($value)
    {
        // Hapus titik pemisah ribuan
        $cleaned = str_replace('.', '', $value);
        // Ganti koma dengan titik untuk desimal
        $cleaned = str_replace(',', '.', $cleaned);
        
        return $cleaned;
    }
}
