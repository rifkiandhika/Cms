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
        $jenis = Jenis::where('status', 'Aktif')
                     ->orderBy('nama_jenis')
                     ->get();
        
        $satuans = Satuan::where('status', 'Aktif')
                        ->orderBy('nama_satuan')
                        ->get();

        return view('supplier.create', compact('jenis', 'satuans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'npwp'           => 'nullable|string|max:20|unique:suppliers,npwp',
            'nama_supplier'  => 'required|string|max:100',
            'alamat'         => 'nullable|string',
            'file'           => 'nullable|mimes:pdf|max:2048',
            'file2'          => 'nullable|mimes:pdf|max:2048',
            'note'           => 'nullable|string',

            // Validasi array detail
            'jenis'          => 'required|array',
            'jenis.*'        => 'required|string',

            'product_id'     => 'nullable|array',
            'product_id.*'   => 'nullable|uuid',

            'nama_manual'    => 'nullable|array',
            'nama_manual.*'  => 'nullable|string|max:200',

            'no_batch'       => 'nullable|array',
            'no_batch.*'     => 'nullable|string',

            'judul'          => 'nullable|array',
            'judul.*'        => 'nullable|string',

            'merk'           => 'nullable|array',
            'merk.*'         => 'nullable|string',

            'satuan'         => 'required|array',
            'satuan.*'       => 'required|string',

            'harga_beli'     => 'nullable|array',
            'harga_beli.*'   => 'nullable|numeric|min:0',

            'stock_live'     => 'nullable|array',
            'stock_live.*'   => 'nullable|integer|min:0',

            'stock_po'       => 'nullable|array',
            'stock_po.*'     => 'nullable|integer|min:0',

            'min_persediaan' => 'nullable|array',
            'min_persediaan.*' => 'nullable|integer|min:0',

            'exp_date'       => 'nullable|array',
            'exp_date.*'     => 'nullable|date',

            'kode_rak'       => 'nullable|array',
            'kode_rak.*'     => 'nullable|string',
        ]);

        // Handle upload file PDF
        $filePath = null;
        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $fileName  = time() . '_' . $file->getClientOriginalName();
            $directory = public_path('uploads/supplier_files');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file->move($directory, $fileName);
            $filePath = 'uploads/supplier_files/' . $fileName;
        }

        $file2Path = null;
        if ($request->hasFile('file2')) {
            $file2      = $request->file('file2');
            $file2Name  = time() . '_2_' . $file2->getClientOriginalName();
            $directory  = public_path('uploads/supplier_files');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file2->move($directory, $file2Name);
            $file2Path = 'uploads/supplier_files/' . $file2Name;
        }

        // Simpan data supplier
        $supplier = Supplier::create([
            'npwp'          => $request->npwp,
            'nama_supplier' => $request->nama_supplier,
            'alamat'        => $request->alamat,
            'file'          => $filePath,
            'file2'         => $file2Path,
            'note'          => $request->note,
        ]);

        // Simpan detail suppliers
        if ($request->has('jenis') && is_array($request->jenis)) {
            foreach ($request->jenis as $i => $jenis) {
                $productId = $request->product_id[$i] ?? null;
                $namaBarang = null;
                $merk = $request->merk[$i] ?? null;
                $satuan = $request->satuan[$i] ?? null;
                $hargaBeli = $request->harga_beli[$i] ?? 0;

                // Jika ada product_id, ambil data dari tabel produk
                if ($productId) {
                    $produk = Produk::find($productId);
                    if ($produk) {
                        $namaBarang = $produk->nama_produk;
                        // Gunakan data dari produk jika tidak diisi manual
                        $merk = $merk ?: $produk->merk;
                        $satuan = $satuan ?: $produk->satuan;
                        $hargaBeli = $hargaBeli ?: $produk->harga_beli;
                    }
                } else {
                    // Jika tidak ada product_id, gunakan input manual
                    $namaBarang = $request->nama_manual[$i] ?? null;
                    $productId = null;
                }

                // Skip jika nama kosong
                if (!$namaBarang) {
                    continue;
                }

                // Simpan detail
                $supplier->detailSuppliers()->create([
                    'product_id'        => $productId,
                    'no_batch'          => $request->no_batch[$i] ?? null,
                    'judul'             => $request->judul[$i] ?? '-',
                    'nama'              => $namaBarang,
                    'jenis'             => $jenis,
                    'merk'              => $merk,
                    'satuan'            => $satuan,
                    'exp_date'          => $request->exp_date[$i] ?? null,
                    'stock_live'        => $request->stock_live[$i] ?? 0,
                    'stock_po'          => $request->stock_po[$i] ?? 0,
                    'min_persediaan'    => $request->min_persediaan[$i] ?? 0,
                    'harga_beli'        => str_replace('.', '', $hargaBeli),
                    'kode_rak'          => $request->kode_rak[$i] ?? null,
                ]);
            }
        }

        Alert::success('Berhasil', 'Data supplier berhasil ditambahkan!');
        return redirect()->route('suppliers.index');
    }

    public function edit(Supplier $supplier)
    {
        // Load relasi produk
        $supplier->load(['detailSuppliers.produk']);

        $jenis = Jenis::where('status', 'Aktif')
                     ->orderBy('nama_jenis')
                     ->get();
        
        $satuans = Satuan::where('status', 'Aktif')
                        ->orderBy('nama_satuan')
                        ->get();

        return view('supplier.edit', compact('supplier', 'jenis', 'satuans'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'npwp'           => 'nullable|string|max:20|unique:suppliers,npwp,' . $supplier->id,
            'nama_supplier'  => 'required|string|max:100',
            'alamat'         => 'nullable|string',
            'file'           => 'nullable|mimes:pdf|max:2048',
            'file2'          => 'nullable|mimes:pdf|max:2048',
            'note'           => 'nullable|string',

            'jenis'          => 'required|array',
            'jenis.*'        => 'required|string',

            'product_id'     => 'nullable|array',
            'product_id.*'   => 'nullable|uuid',

            'nama_manual'    => 'nullable|array',
            'nama_manual.*'  => 'nullable|string|max:200',

            'satuan'         => 'required|array',
            'satuan.*'       => 'required|string',
        ]);

        // Handle file upload
        $filePath = $supplier->file;
        if ($request->hasFile('file')) {
            if ($supplier->file && File::exists(public_path($supplier->file))) {
                File::delete(public_path($supplier->file));
            }

            $file      = $request->file('file');
            $fileName  = time() . '_' . $file->getClientOriginalName();
            $directory = public_path('uploads/supplier_files');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file->move($directory, $fileName);
            $filePath = 'uploads/supplier_files/' . $fileName;
        }

        $file2Path = $supplier->file2;
        if ($request->hasFile('file2')) {
            if ($supplier->file2 && File::exists(public_path($supplier->file2))) {
                File::delete(public_path($supplier->file2));
            }

            $file2      = $request->file('file2');
            $file2Name  = time() . '_2_' . $file2->getClientOriginalName();
            $directory  = public_path('uploads/supplier_files');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file2->move($directory, $file2Name);
            $file2Path = 'uploads/supplier_files/' . $file2Name;
        }

        // Update data utama
        $supplier->update([
            'npwp'          => $request->npwp,
            'nama_supplier' => $request->nama_supplier,
            'alamat'        => $request->alamat,
            'file'          => $filePath,
            'file2'         => $file2Path,
            'note'          => $request->note,
        ]);

        // Hapus semua detail lama
        $supplier->detailSuppliers()->delete();

        // Simpan detail baru
        if ($request->has('jenis') && is_array($request->jenis)) {
            foreach ($request->jenis as $i => $jenis) {
                $productId = $request->product_id[$i] ?? null;
                $namaBarang = null;
                $merk = $request->merk[$i] ?? null;
                $satuan = $request->satuan[$i] ?? null;
                $hargaBeli = $request->harga_beli[$i] ?? 0;

                if ($productId) {
                    $produk = Produk::find($productId);
                    if ($produk) {
                        $namaBarang = $produk->nama_produk;
                        $merk = $merk ?: $produk->merk;
                        $satuan = $satuan ?: $produk->satuan;
                        $hargaBeli = $hargaBeli ?: $produk->harga_beli;
                    }
                } else {
                    $namaBarang = $request->nama_manual[$i] ?? null;
                    $productId = null;
                }

                if (!$namaBarang) continue;

                $supplier->detailSuppliers()->create([
                    'product_id'        => $productId,
                    'no_batch'          => $request->no_batch[$i] ?? null,
                    'judul'             => $request->judul[$i] ?? '-',
                    'nama'              => $namaBarang,
                    'jenis'             => $jenis,
                    'merk'              => $merk,
                    'satuan'            => $satuan,
                    'exp_date'          => $request->exp_date[$i] ?? null,
                    'stock_live'        => $request->stock_live[$i] ?? 0,
                    'stock_po'          => $request->stock_po[$i] ?? 0,
                    'min_persediaan'    => $request->min_persediaan[$i] ?? 0,
                    'harga_beli'        => str_replace('.', '', $hargaBeli),
                    'kode_rak'          => $request->kode_rak[$i] ?? null,
                ]);
            }
        }

        Alert::info('Berhasil', 'Data supplier berhasil diperbarui!');
        return redirect()->route('suppliers.index');
    }

    public function destroy(Supplier $supplier)
    {
        // Hapus file PDF jika ada
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
}
