<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailSupplier;
use App\Models\Jenis;
use App\Models\Produk;
use App\Models\Satuan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('created_at', 'desc')->paginate(10);
        return view('supplier.index', compact('suppliers'));
    }

    public function create()
    {
        $jenis   = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuans = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();

        // Instance kosong agar blade tidak error saat akses $supplier->detailSuppliers
        $supplier = new Supplier();

        return view('supplier.create', compact('jenis', 'satuans', 'supplier'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Data utama supplier
            'nama_supplier'     => 'required|string|max:100',
            'npwp'              => 'nullable|string|max:20|unique:suppliers,npwp',
            'izin_operasional'  => 'nullable|string|max:100',
            'kontak_person'     => 'nullable|string|max:100',
            'email'             => 'nullable|email|max:100',
            'no_telp'           => 'nullable|string|max:15',
            'alamat'            => 'nullable|string',
            'kota'              => 'nullable|string|max:100',
            'provinsi'          => 'nullable|string|max:100',
            'status'            => 'required|in:Aktif,Nonaktif',
            'note'              => 'nullable|string',
            'file'              => 'nullable|mimes:pdf|max:2048',
            'file2'             => 'nullable|mimes:pdf|max:2048',

            // Detail barang — sesuai kolom schema detail_suppliers
            'jenis'                 => 'nullable|array',
            'jenis.*'               => 'nullable|string',
            'produk_id'             => 'nullable|array',
            'produk_id.*'           => 'nullable|uuid',
            'produk_satuan_id'      => 'nullable|array',
            'produk_satuan_id.*'    => 'nullable|uuid',
            'isi'                   => 'nullable|array',
            'isi.*'                 => 'nullable|integer|min:1',
            'harga_beli'            => 'nullable|array',
            'harga_beli.*'          => 'nullable|numeric|min:0',
            'catatan'               => 'nullable|array',
            'catatan.*'             => 'nullable|string',
            // Field tambahan yang ditampilkan di UI (tidak disimpan ke detail_suppliers)
            'no_batch'              => 'nullable|array',
            'no_batch.*'            => 'nullable|string',
            'judul'                 => 'nullable|array',
            'judul.*'               => 'nullable|string',
            'nama_manual'           => 'nullable|array',
            'nama_manual.*'         => 'nullable|string|max:200',
            'merk'                  => 'nullable|array',
            'merk.*'                => 'nullable|string',
            'exp_date'              => 'nullable|array',
            'exp_date.*'            => 'nullable|date',
            'stock_live'            => 'nullable|array',
            'stock_live.*'          => 'nullable|integer|min:0',
            'stock_po'              => 'nullable|array',
            'stock_po.*'            => 'nullable|integer|min:0',
            'min_persediaan'        => 'nullable|array',
            'min_persediaan.*'      => 'nullable|integer|min:0',
            'kode_rak'              => 'nullable|array',
            'kode_rak.*'            => 'nullable|string',
        ]);

        $filePath  = $this->uploadFile($request, 'file');
        $file2Path = $this->uploadFile($request, 'file2', '_2_');

        $supplier = Supplier::create([
            'nama_supplier'    => $request->nama_supplier,
            'npwp'             => $request->npwp,
            'izin_operasional' => $request->izin_operasional,
            'kontak_person'    => $request->kontak_person,
            'email'            => $request->email,
            'no_telp'          => $request->no_telp,
            'alamat'           => $request->alamat,
            'kota'             => $request->kota,
            'provinsi'         => $request->provinsi,
            'status'           => $request->status,
            'note'             => $request->note,
            'file'             => $filePath,
            'file2'            => $file2Path,
        ]);

        $this->saveDetails($supplier, $request);

        Alert::success('Berhasil', 'Data supplier berhasil ditambahkan!');
        return redirect()->route('suppliers.index');
    }

    public function edit(Supplier $supplier)
    {
        // Load relasi produk dan produkSatuan untuk ditampilkan di blade
        $supplier->load([
            'detailSuppliers.produk.produkSatuans',
            'detailSuppliers.produkSatuan',
        ]);

        $jenis   = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuans = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();

        return view('supplier.edit', compact('supplier', 'jenis', 'satuans'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nama_supplier'     => 'required|string|max:100',
            'npwp'              => 'nullable|string|max:20|unique:suppliers,npwp,' . $supplier->id,
            'izin_operasional'  => 'nullable|string|max:100',
            'kontak_person'     => 'nullable|string|max:100',
            'email'             => 'nullable|email|max:100',
            'no_telp'           => 'nullable|string|max:15',
            'alamat'            => 'nullable|string',
            'kota'              => 'nullable|string|max:100',
            'provinsi'          => 'nullable|string|max:100',
            'status'            => 'required|in:Aktif,Nonaktif',
            'note'              => 'nullable|string',
            'file'              => 'nullable|mimes:pdf|max:2048',
            'file2'             => 'nullable|mimes:pdf|max:2048',

            'jenis'                 => 'nullable|array',
            'jenis.*'               => 'nullable|string',
            'produk_id'             => 'nullable|array',
            'produk_id.*'           => 'nullable|uuid',
            'produk_satuan_id'      => 'nullable|array',
            'produk_satuan_id.*'    => 'nullable|uuid',
            'isi'                   => 'nullable|array',
            'isi.*'                 => 'nullable|integer|min:1',
            'harga_beli'            => 'nullable|array',
            'harga_beli.*'          => 'nullable|numeric|min:0',
            'catatan'               => 'nullable|array',
            'catatan.*'             => 'nullable|string',
        ]);

        $filePath  = $this->uploadFile($request, 'file', '_', $supplier->file);
        $file2Path = $this->uploadFile($request, 'file2', '_2_', $supplier->file2);

        $supplier->update([
            'nama_supplier'    => $request->nama_supplier,
            'npwp'             => $request->npwp,
            'izin_operasional' => $request->izin_operasional,
            'kontak_person'    => $request->kontak_person,
            'email'            => $request->email,
            'no_telp'          => $request->no_telp,
            'alamat'           => $request->alamat,
            'kota'             => $request->kota,
            'provinsi'         => $request->provinsi,
            'status'           => $request->status,
            'note'             => $request->note,
            'file'             => $filePath,
            'file2'            => $file2Path,
        ]);

        // Hapus detail lama, simpan ulang
        $supplier->detailSuppliers()->delete();
        $this->saveDetails($supplier, $request);

        Alert::info('Berhasil', 'Data supplier berhasil diperbarui!');
        return redirect()->route('suppliers.index');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->file && file_exists(public_path($supplier->file))) {
            unlink(public_path($supplier->file));
        }
        if ($supplier->file2 && file_exists(public_path($supplier->file2))) {
            unlink(public_path($supplier->file2));
        }

        $supplier->detailSuppliers()->delete();
        $supplier->delete();

        Alert::warning('Berhasil', 'Supplier berhasil dihapus!');
        return redirect()->route('suppliers.index');
    }

    // =========================================================
    // Private Helpers
    // =========================================================

    /**
     * Upload file PDF dan kembalikan path-nya.
     * Jika tidak ada file baru, kembalikan $existingPath.
     */
    private function uploadFile(Request $request, string $field, string $suffix = '_', ?string $existingPath = null): ?string
    {
        if (!$request->hasFile($field)) {
            return $existingPath;
        }

        if ($existingPath && File::exists(public_path($existingPath))) {
            File::delete(public_path($existingPath));
        }

        $file      = $request->file($field);
        $fileName  = time() . $suffix . $file->getClientOriginalName();
        $directory = public_path('uploads/supplier_files');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true, true);
        }

        $file->move($directory, $fileName);
        return 'uploads/supplier_files/' . $fileName;
    }

    /**
     * Simpan detail barang supplier.
     * Hanya kolom yang ada di schema detail_suppliers yang disimpan:
     *   supplier_id, produk_id, produk_satuan_id, harga_beli, is_aktif, catatan
     *
     * Constraint unique: (supplier_id, produk_id, produk_satuan_id)
     * → baris dengan kombinasi duplikat di-skip (updateOrCreate dipakai).
     */
    private function saveDetails(Supplier $supplier, Request $request): void
    {
        $produkIds = $request->input('produk_id', []);

        if (empty($produkIds)) {
            return;
        }

        foreach ($produkIds as $i => $produkId) {
            // Hanya proses baris yang memiliki produk_id (baris wajib punya produk)
            if (empty($produkId)) {
                continue;
            }

            $produkSatuanId = $request->input("produk_satuan_id.{$i}") ?: null;
            $hargaBeli      = $request->input("harga_beli.{$i}", 0);
            $catatan        = $request->input("catatan.{$i}") ?: null;

            // Bersihkan format rupiah (titik ribuan)
            $hargaBeli = str_replace('.', '', $hargaBeli);

            // updateOrCreate untuk menghindari duplicate unique constraint
            // (supplier_id, produk_id, produk_satuan_id)
            $supplier->detailSuppliers()->updateOrCreate(
                [
                    'produk_id'        => $produkId,
                    'produk_satuan_id' => $produkSatuanId,
                ],
                [
                    'harga_beli' => is_numeric($hargaBeli) ? $hargaBeli : 0,
                    'is_aktif'   => true,
                    'catatan'    => $catatan,
                ]
            );
        }
    }
}