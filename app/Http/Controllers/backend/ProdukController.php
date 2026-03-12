<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use App\Models\Produk;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class ProdukController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────────
    public function index()
    {
        
        $produks = Produk::with(['produkSatuans.satuan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('produk.index', compact('produks'));
    }

    // ──────────────────────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────────────────────
    public function create()
    {
        $jenis  = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuan = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();

        // ← PERBAIKAN: tidak perlu pass $satuanDasar terpisah
        return view('produk.create', compact('jenis', 'satuan'));
    }

    // ──────────────────────────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nie'          => 'required|string|max:255',
            'nama_produk'  => 'required|string|max:255',
            'merk'         => 'nullable|string|max:255',
            'jenis'        => 'required|string|max:255',
            'status'       => 'required|in:aktif,nonaktif',
            'deskripsi'    => 'nullable|string',

            // ← HAPUS: 'satuan_dasar_id' => 'required|exists:satuans,id'

            'satuan_jual'             => 'required|array|min:1',
            'satuan_jual.*.satuan_id' => 'required|exists:satuans,id',
            // ← HAPUS: 'satuan_jual.*.label'
            // ← TAMBAH: konversi wajib integer >= 1
            'satuan_jual.*.konversi'  => 'required|integer|min:1',
            'satuan_jual.*.is_default'=> 'nullable|boolean',
            // ← TAMBAH: barcode per kemasan (opsional, boleh kosong)
            'satuan_jual.*.kode_barcode' => 'nullable|string|max:100',
        ]);

        $satuan_jual = $validated['satuan_jual'];
        unset($validated['satuan_jual']);

        // Pastikan tepat satu baris is_default = true
        $satuan_jual = $this->normalisasiDefault($satuan_jual);

        // ← TAMBAH: validasi konversi satuan default harus 1
        $defaultError = $this->validasiKonversiDefault($satuan_jual);
        if ($defaultError) {
            return redirect()->back()->withInput()
                ->withErrors(['satuan_jual' => $defaultError]);
        }

        // ← TAMBAH: validasi tidak boleh ada satuan yang sama dua kali
        $duplikat = $this->validasiDuplikatSatuan($satuan_jual);
        if ($duplikat) {
            return redirect()->back()->withInput()
                ->withErrors(['satuan_jual' => $duplikat]);
        }

        DB::beginTransaction();
        try {
            // ← PERBAIKAN: $validated tidak lagi mengandung satuan_dasar_id
            $produk = Produk::create($validated);

            foreach ($satuan_jual as $s) {
                $produk->produkSatuans()->create([
                    'satuan_id'    => $s['satuan_id'],
                    // ← PERBAIKAN: simpan konversi, bukan label
                    // Jika is_default=true, paksa konversi=1
                    'konversi'     => !empty($s['is_default']) ? 1 : (int) $s['konversi'],
                    'is_default'   => !empty($s['is_default']),
                    // ← TAMBAH: barcode per kemasan
                    'kode_barcode' => $s['kode_barcode'] ?? null,
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

    // ──────────────────────────────────────────────────────────────
    // SHOW
    // ──────────────────────────────────────────────────────────────
    public function show(string $id)
    {
        // ← PERBAIKAN: hapus 'satuanDasar' dari eager load
        $produk = Produk::with(['produkSatuans.satuan'])->findOrFail($id);
        return view('produk.show', compact('produk'));
    }

    // ──────────────────────────────────────────────────────────────
    // EDIT
    // ──────────────────────────────────────────────────────────────
    public function edit(string $id)
    {
        $produk = Produk::with('produkSatuans.satuan')->findOrFail($id);
        $jenis  = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuan = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();

        return view('produk.edit', compact('produk', 'jenis', 'satuan'));
    }

    // ──────────────────────────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'nie'          => 'required|string|max:255',
            'nama_produk'  => 'required|string|max:255',
            'merk'         => 'nullable|string|max:255',
            'jenis'        => 'required|string|max:255',
            'status'       => 'required|in:aktif,nonaktif',
            'deskripsi'    => 'nullable|string',

            // ← HAPUS: 'satuan_dasar_id'

            'satuan_jual'             => 'required|array|min:1',
            'satuan_jual.*.satuan_id' => 'required|exists:satuans,id',
            // ← HAPUS: 'satuan_jual.*.label'
            // ← TAMBAH: konversi
            'satuan_jual.*.konversi'  => 'required|integer|min:1',
            'satuan_jual.*.is_default'=> 'nullable|boolean',
            // ← TAMBAH: barcode per kemasan
            'satuan_jual.*.kode_barcode' => 'nullable|string|max:100',
        ]);

        $satuan_jual = $validated['satuan_jual'];
        unset($validated['satuan_jual']);

        $satuan_jual = $this->normalisasiDefault($satuan_jual);

        $defaultError = $this->validasiKonversiDefault($satuan_jual);
        if ($defaultError) {
            return redirect()->back()->withInput()
                ->withErrors(['satuan_jual' => $defaultError]);
        }

        $duplikat = $this->validasiDuplikatSatuan($satuan_jual);
        if ($duplikat) {
            return redirect()->back()->withInput()
                ->withErrors(['satuan_jual' => $duplikat]);
        }

        DB::beginTransaction();
        try {
            // ← PERBAIKAN: $validated tidak lagi mengandung satuan_dasar_id
            $produk->update($validated);

            // Hapus semua satuan lama lalu insert ulang
            // Catatan: jika produk sudah dipakai di transaksi, pertimbangkan
            // untuk hanya update/tidak hapus satuan yang sudah ada
            $produk->produkSatuans()->delete();

            foreach ($satuan_jual as $s) {
                $produk->produkSatuans()->create([
                    'satuan_id'    => $s['satuan_id'],
                    'konversi'     => !empty($s['is_default']) ? 1 : (int) $s['konversi'],
                    'is_default'   => !empty($s['is_default']),
                    // ← TAMBAH: barcode per kemasan
                    'kode_barcode' => $s['kode_barcode'] ?? null,
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

    // ──────────────────────────────────────────────────────────────
    // DESTROY
    // ──────────────────────────────────────────────────────────────
    public function destroy(string $id)
    {
        try {
            $produk = Produk::findOrFail($id);

            // ← TAMBAH: cek apakah produk masih punya stok di gudang
            // FK di detail_gudangs.produk_id pakai onDelete('restrict'),
            // artinya delete akan gagal di DB jika masih ada stok.
            // Lebih baik dicek dulu agar pesan error lebih jelas.
            $masihAdaStok = $produk->detailGudangs()->where('stock_gudang', '>', 0)->exists();
            if ($masihAdaStok) {
                Alert::error('Gagal', 'Produk tidak bisa dihapus karena masih memiliki stok di gudang.');
                return redirect()->back();
            }

            // Cek apakah produk dipakai di transaksi aktif (PO yang belum selesai)
            $dipakaidiPO = \App\Models\PurchaseOrderItem::where('id_produk', $produk->id)
                ->whereHas('purchaseOrder', fn($q) => $q->whereNotIn('status', ['diterima', 'ditolak', 'dibatalkan']))
                ->exists();
            if ($dipakaidiPO) {
                Alert::error('Gagal', 'Produk tidak bisa dihapus karena masih dipakai dalam Purchase Order aktif.');
                return redirect()->back();
            }

            $produk->produkSatuans()->delete();
            $produk->delete();

            Alert::success('Berhasil', 'Produk berhasil dihapus!');
            return redirect()->route('produks.index');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────────────
    // HELPER METHODS (PRIVATE)
    // ──────────────────────────────────────────────────────────────

    /**
     * Pastikan tepat satu baris is_default = true.
     * Jika tidak ada yang default, set baris pertama sebagai default.
     */
    private function normalisasiDefault(array $satuanJual): array
    {
        $defaultCount = collect($satuanJual)->filter(fn($s) => !empty($s['is_default']))->count();

        if ($defaultCount === 0) {
            $satuanJual[array_key_first($satuanJual)]['is_default'] = true;
        } elseif ($defaultCount > 1) {
            // Jika lebih dari 1, hanya pertahankan yang pertama
            $foundFirst = false;
            foreach ($satuanJual as &$s) {
                if (!empty($s['is_default'])) {
                    if ($foundFirst) {
                        $s['is_default'] = false;
                    } else {
                        $foundFirst = true;
                    }
                }
            }
            unset($s);
        }

        return $satuanJual;
    }

    /**
     * Validasi: satuan yang is_default=true wajib konversi=1.
     * Satuan yang is_default=false wajib konversi > 1.
     */
    private function validasiKonversiDefault(array $satuanJual): ?string
    {
        foreach ($satuanJual as $s) {
            if (!empty($s['is_default']) && (int) $s['konversi'] !== 1) {
                return 'Satuan dasar (default) harus memiliki konversi = 1.';
            }
            if (empty($s['is_default']) && (int) $s['konversi'] <= 1) {
                return 'Satuan non-default harus memiliki konversi lebih dari 1.';
            }
        }
        return null;
    }

    /**
     * Validasi: tidak boleh ada satuan_id yang sama dua kali dalam satu produk.
     */
    private function validasiDuplikatSatuan(array $satuanJual): ?string
    {
        $ids = array_column($satuanJual, 'satuan_id');
        if (count($ids) !== count(array_unique($ids))) {
            return 'Tidak boleh ada satuan yang sama lebih dari satu kali.';
        }
        return null;
    }
}