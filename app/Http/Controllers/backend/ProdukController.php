<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use App\Models\Produk;
use App\Models\ProdukSatuan;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::with(['satuanDasar', 'produkSatuans'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('produk.index', compact('produks'));
    }

    public function create()
    {
        $jenis  = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuan = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();

        return view('produk.create', compact('jenis', 'satuan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nie'             => 'required|string|max:255',
            'nama_produk'     => 'required|string|max:255',
            'merk'            => 'nullable|string|max:255',
            'jenis'           => 'required|string|max:255',
            'satuan_dasar_id' => 'required|exists:satuans,id',
            'harga_beli'      => 'nullable|numeric|min:0',
            'harga_jual'      => 'nullable|numeric|min:0',
            'harga_dasar'     => 'required|numeric|min:0',
            'deskripsi'       => 'nullable|string',
            'status'          => 'required|in:aktif,nonaktif',

            // Satuan jual (array)
            'satuan_jual'                => 'required|array|min:1',
            'satuan_jual.*.satuan_id'    => 'required|exists:satuans,id',
            'satuan_jual.*.label'        => 'required|string|max:100',
            'satuan_jual.*.isi'          => 'required|numeric|min:0.0001',
            'satuan_jual.*.harga_beli'   => 'nullable|numeric|min:0',
            'satuan_jual.*.harga_jual'   => 'nullable|numeric|min:0',
            'satuan_jual.*.harga_otomatis' => 'nullable|boolean',
            'satuan_jual.*.is_default'   => 'nullable|boolean',
        ]);

        $validated['harga_beli']  = $this->cleanRupiah($validated['harga_beli']);
        $validated['harga_jual']  = $this->cleanRupiah($validated['harga_jual']);
        $validated['harga_dasar'] = $this->cleanRupiah($validated['harga_dasar']);

        // Pastikan hanya satu is_default
        $defaultCount = collect($validated['satuan_jual'])->where('is_default', true)->count();
        if ($defaultCount === 0) {
            $validated['satuan_jual'][0]['is_default'] = true; // default ke baris pertama
        }

        DB::beginTransaction();
        try {
            $produk = Produk::create($validated);

            foreach ($validated['satuan_jual'] as $s) {
                $produk->produkSatuans()->create([
                    'satuan_id'      => $s['satuan_id'],
                    'label'          => $s['label'],
                    'isi'            => $s['isi'],
                    'harga_beli'     => $this->cleanRupiah($s['harga_beli'] ?? 0),
                    'harga_jual'     => $this->cleanRupiah($s['harga_jual'] ?? 0),
                    'harga_otomatis' => isset($s['harga_otomatis']) ? (bool)$s['harga_otomatis'] : true,
                    'is_default'     => isset($s['is_default']) ? (bool)$s['is_default'] : false,
                ]);
            }

            DB::commit();
            Alert::success('Berhasil', 'Produk berhasil ditambahkan!');
            return redirect()->route('produks.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $produk = Produk::with(['satuanDasar', 'produkSatuans.satuan'])->findOrFail($id);
        return view('produk.show', compact('produk'));
    }

    public function edit(string $id)
    {
        $produk = Produk::with('produkSatuans.satuan')->findOrFail($id);
        $jenis  = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuan = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();

        return view('produk.edit', compact('produk', 'jenis', 'satuan'));
    }

    public function update(Request $request, string $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'nie'             => 'required|string|max:255',
            'nama_produk'     => 'required|string|max:255',
            'merk'            => 'nullable|string|max:255',
            'jenis'           => 'required|string|max:255',
            'satuan_dasar_id' => 'required|exists:satuans,id',
            'harga_beli'      => 'required|numeric|min:0',
            'harga_jual'      => 'nullable|numeric|min:0',
            'harga_dasar'     => 'required|numeric|min:0',
            'deskripsi'       => 'nullable|string',
            'status'          => 'required|in:aktif,nonaktif',

            'satuan_jual'                  => 'required|array|min:1',
            'satuan_jual.*.satuan_id'      => 'required|exists:satuans,id',
            'satuan_jual.*.label'          => 'required|string|max:100',
            'satuan_jual.*.isi'            => 'required|numeric|min:0.0001',
            'satuan_jual.*.harga_beli'     => 'nullable|numeric|min:0',
            'satuan_jual.*.harga_jual'     => 'nullable|numeric|min:0',
            'satuan_jual.*.harga_otomatis' => 'nullable|boolean',
            'satuan_jual.*.is_default'     => 'nullable|boolean',
        ]);

        $validated['harga_beli']  = $this->cleanRupiah($validated['harga_beli'] ?? 0);
        $validated['harga_jual']  = $this->cleanRupiah($validated['harga_jual'] ?? 0);
        $validated['harga_dasar'] = $this->cleanRupiah($validated['harga_dasar'] ?? 0);

        $satuan_jual = $validated['satuan_jual'];
        unset($validated['satuan_jual']);

        DB::beginTransaction();
        try {
            $produk->update($validated); 

            $produk->produkSatuans()->delete();

            foreach ($satuan_jual as $s) {
                $produk->produkSatuans()->create([
                    'satuan_id'      => $s['satuan_id'],
                    'label'          => $s['label'],
                    'isi'            => $s['isi'],
                    'harga_beli'     => $this->cleanRupiah($s['harga_beli'] ?? 0),
                    'harga_jual'     => $this->cleanRupiah($s['harga_jual'] ?? 0),
                    'harga_otomatis' => isset($s['harga_otomatis']) ? (bool)$s['harga_otomatis'] : true,
                    'is_default'     => isset($s['is_default']) ? (bool)$s['is_default'] : false,
                ]);
            }

            DB::commit();
            Alert::success('Berhasil', 'Produk berhasil diperbarui!');
            return redirect()->route('produks.index');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $produk = Produk::findOrFail($id);
            $produk->produkSatuans()->delete();
            $produk->delete();

            Alert::success('Berhasil', 'Produk berhasil dihapus!');
            return redirect()->route('produks.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    private function cleanRupiah($value): string
    {
        $cleaned = str_replace('.', '', $value);
        return str_replace(',', '.', $cleaned);
    }
}