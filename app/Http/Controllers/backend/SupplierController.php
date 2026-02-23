<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Alkes;
use App\Models\Department;
use App\Models\DetailobatRs;
use App\Models\DetailSupplier;
use App\Models\Jenis;
use App\Models\Produk;
use App\Models\Reagen;
use App\Models\Satuan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
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
        $jenis = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuans = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();

        // Buat instance kosong agar blade tidak error saat mengakses $supplier->detailSuppliers
        $supplier = new Supplier();

        return view('supplier.create', compact('jenis', 'satuans', 'supplier'));
    }

    public function store(Request $request)
    {
        $request->validate([
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

            // Detail barang
            'jenis'             => 'required|array',
            'jenis.*'           => 'required|string',
            'product_id'        => 'nullable|array',
            'product_id.*'      => 'nullable|uuid',
            'nama_manual'       => 'nullable|array',
            'nama_manual.*'     => 'nullable|string|max:200',
            'no_batch'          => 'nullable|array',
            'no_batch.*'        => 'nullable|string',
            'judul'             => 'nullable|array',
            'judul.*'           => 'nullable|string',
            'merk'              => 'nullable|array',
            'merk.*'            => 'nullable|string',
            'satuan'            => 'required|array',
            'satuan.*'          => 'required|string',
            'harga_beli'        => 'nullable|array',
            'harga_beli.*'      => 'nullable|numeric|min:0',
            'stock_live'        => 'nullable|array',
            'stock_live.*'      => 'nullable|integer|min:0',
            'stock_po'          => 'nullable|array',
            'stock_po.*'        => 'nullable|integer|min:0',
            'min_persediaan'    => 'nullable|array',
            'min_persediaan.*'  => 'nullable|integer|min:0',
            'exp_date'          => 'nullable|array',
            'exp_date.*'        => 'nullable|date',
            'kode_rak'          => 'nullable|array',
            'kode_rak.*'        => 'nullable|string',
        ]);

        // Handle upload file
        $filePath  = $this->uploadFile($request, 'file');
        $file2Path = $this->uploadFile($request, 'file2', '_2_');

        // Simpan supplier
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
        $supplier->load(['detailSuppliers.produk']);

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

            // Detail barang
            'jenis'             => 'required|array',
            'jenis.*'           => 'required|string',
            'product_id'        => 'nullable|array',
            'product_id.*'      => 'nullable|uuid',
            'nama_manual'       => 'nullable|array',
            'nama_manual.*'     => 'nullable|string|max:200',
            'satuan'            => 'required|array',
            'satuan.*'          => 'required|string',
        ]);

        // Handle file upload (pertahankan file lama jika tidak ada upload baru)
        $filePath  = $this->uploadFile($request, 'file', '_', $supplier->file);
        $file2Path = $this->uploadFile($request, 'file2', '_2_', $supplier->file2);

        // Update data utama
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

        // Hapus detail lama, simpan yang baru
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
     * Jika tidak ada file baru yang diupload, kembalikan $existingPath.
     */
    private function uploadFile(Request $request, string $field, string $suffix = '_', ?string $existingPath = null): ?string
    {
        if (!$request->hasFile($field)) {
            return $existingPath;
        }

        // Hapus file lama jika ada
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
     * Simpan detail barang supplier dari request.
     */
    private function saveDetails(Supplier $supplier, Request $request): void
    {
        if (!$request->has('jenis') || !is_array($request->jenis)) {
            return;
        }

        foreach ($request->jenis as $i => $jenis) {
            $productId  = $request->product_id[$i] ?? null;
            $namaBarang = null;
            $merk       = $request->merk[$i] ?? null;
            $satuan     = $request->satuan[$i] ?? null;
            $hargaBeli  = $request->harga_beli[$i] ?? 0;

            if ($productId) {
                $produk = Produk::find($productId);
                if ($produk) {
                    $namaBarang = $produk->nama_produk;
                    $merk       = $merk    ?: $produk->merk;
                    $satuan     = $satuan  ?: $produk->satuan;
                    $hargaBeli  = $hargaBeli ?: $produk->harga_beli;
                }
            } else {
                $namaBarang = $request->nama_manual[$i] ?? null;
                $productId  = null;
            }

            // Skip baris yang tidak ada nama produknya
            if (!$namaBarang) {
                continue;
            }

            $supplier->detailSuppliers()->create([
                'product_id'     => $productId,
                'no_batch'       => $request->no_batch[$i] ?? null,
                'judul'          => $request->judul[$i] ?? '-',
                'nama'           => $namaBarang,
                'jenis'          => $jenis,
                'merk'           => $merk,
                'satuan'         => $satuan,
                'exp_date'       => $request->exp_date[$i] ?? null,
                'stock_live'     => $request->stock_live[$i] ?? 0,
                'stock_po'       => $request->stock_po[$i] ?? 0,
                'min_persediaan' => $request->min_persediaan[$i] ?? 0,
                'harga_beli'     => str_replace('.', '', $hargaBeli),
                'kode_rak'       => $request->kode_rak[$i] ?? null,
            ]);
        }
    }
}
