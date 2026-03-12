<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailGudang;
use App\Models\Gudang;
use App\Models\Produk;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class GudangController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Gudang::with(['details.produk', 'details.supplier']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $gudangs     = $query->latest()->paginate(10)->withQueryString();
        $totalGudang = Gudang::count();
        $gudangAktif = Gudang::where('status', 'Aktif')->count();
        $stokMenipis = DetailGudang::whereColumn('stock_gudang', '<', 'min_persediaan')->count();
        $totalItems  = DetailGudang::sum('stock_gudang');
        $suppliers   = Supplier::orderBy('nama_supplier')->get();

        return view('gudang.index', compact(
            'gudangs',
            'totalGudang',
            'gudangAktif',
            'stokMenipis',
            'totalItems',
            'suppliers'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // SHOW
    // ──────────────────────────────────────────────────────────────
    public function show(Gudang $gudang)
    {
        $gudang->load([
            'details.produk.produkSatuans.satuan',
            'details.supplier',
        ]);

        $suppliers = Supplier::orderBy('nama_supplier')->get();

        return view('gudang.show', compact('gudang', 'suppliers'));
    }

    // ──────────────────────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────────────────────
    public function create()
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        return view('gudang.create', compact('suppliers'));
    }

    // ──────────────────────────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'kode_gudang'      => 'required|string|max:50|unique:gudangs,kode_gudang',
            'nama_gudang'      => 'required|string|max:100',
            'lokasi'           => 'nullable|string|max:200',
            'penanggung_jawab' => 'nullable|string|max:100',
            'status'           => 'required|in:Aktif,Nonaktif',
            'keterangan'       => 'nullable|string',

            'produk_id'              => 'nullable|array',
            'produk_id.*'            => 'required|uuid|exists:produks,id',
            'detail_supplier_id'     => 'nullable|array',
            'detail_supplier_id.*'   => 'nullable|uuid|exists:suppliers,id',

            'no_batch'        => 'nullable|array',
            'no_batch.*'      => 'nullable|string|max:50',
            'stock_gudang'    => 'nullable|array',
            'stock_gudang.*'  => 'required|integer|min:0',
            'min_persediaan'  => 'nullable|array',
            'min_persediaan.*'=> 'required|integer|min:0',
            'tanggal_masuk'   => 'nullable|array',
            'tanggal_masuk.*' => 'nullable|date',
            'tanggal_produksi'   => 'nullable|array',
            'tanggal_produksi.*' => 'nullable|date',
            'tanggal_kadaluarsa'   => 'nullable|array',
            'tanggal_kadaluarsa.*' => 'nullable|date',
            'lokasi_rak'      => 'nullable|array',
            'lokasi_rak.*'    => 'nullable|string|max:50',
            'kondisi'         => 'nullable|array',
            'kondisi.*'       => 'required|in:Baik,Rusak,Kadaluarsa',
        ]);

        DB::beginTransaction();
        try {
            $gudang = Gudang::create([
                'kode_gudang'      => $request->kode_gudang,
                'nama_gudang'      => $request->nama_gudang,
                'lokasi'           => $request->lokasi,
                'penanggung_jawab' => $request->penanggung_jawab,
                'keterangan'       => $request->keterangan,
                'status'           => $request->status,
            ]);

            if ($request->filled('produk_id')) {
                foreach ($request->produk_id as $index => $produkId) {
                    $stokAwal = (int) ($request->stock_gudang[$index] ?? 0);

                    $detail = DetailGudang::create([
                        'gudang_id'          => $gudang->id,
                        'produk_id'          => $produkId,
                        'supplier_id'        => $request->detail_supplier_id[$index] ?? null,
                        'no_batch'           => $request->no_batch[$index] ?? null,
                        'stock_gudang'       => $stokAwal,
                        'min_persediaan'     => (int) ($request->min_persediaan[$index] ?? 0),
                        'tanggal_masuk'      => $request->tanggal_masuk[$index] ?? null,
                        'tanggal_produksi'   => $request->tanggal_produksi[$index] ?? null,
                        'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa[$index] ?? null,
                        'lokasi_rak'         => $request->lokasi_rak[$index] ?? null,
                        'kondisi'            => $request->kondisi[$index] ?? 'Baik',
                    ]);

                    if ($stokAwal > 0) {
                        StockMovement::create([
                            'gudang_id'      => $gudang->id,
                            'produk_id'      => $produkId,
                            'tipe'           => 'penyesuaian_masuk',
                            'referensi_tipe' => 'gudangs',
                            'referensi_id'   => $gudang->id,
                            'referensi_no'   => $gudang->kode_gudang,
                            'qty_sebelum'    => 0,
                            'qty_perubahan'  => $stokAwal,
                            'qty_sesudah'    => $stokAwal,
                            'no_batch'       => $request->no_batch[$index] ?? null,
                            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa[$index] ?? null,
                            'id_karyawan'    => Auth::user()->id_karyawan,
                            'catatan'        => 'Stok awal saat gudang dibuat',
                        ]);
                    }
                }
            }

            DB::commit();
            Alert::success('Berhasil', 'Gudang berhasil ditambahkan!');
            return redirect()->route('gudangs.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    // ──────────────────────────────────────────────────────────────
    // EDIT
    // ──────────────────────────────────────────────────────────────
    public function edit(Gudang $gudang)
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $gudang->load(['details.produk.produkSatuans.satuan', 'details.supplier']);
        return view('gudang.edit', compact('gudang', 'suppliers'));
    }

    // ──────────────────────────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────────────────────────
    public function update(Request $request, Gudang $gudang)
    {
        $request->validate([
            'kode_gudang'      => 'required|string|max:50|unique:gudangs,kode_gudang,' . $gudang->id,
            'nama_gudang'      => 'required|string|max:100',
            'lokasi'           => 'nullable|string|max:200',
            'penanggung_jawab' => 'nullable|string|max:100',
            'status'           => 'required|in:Aktif,Nonaktif',
            'keterangan'       => 'nullable|string',

            'produk_id'            => 'nullable|array',
            'produk_id.*'          => 'required|uuid|exists:produks,id',
            'detail_supplier_id'   => 'nullable|array',
            'detail_supplier_id.*' => 'nullable|uuid|exists:suppliers,id',
            'detail_id'            => 'nullable|array',
            'detail_id.*'          => 'nullable|uuid',

            'no_batch'             => 'nullable|array',
            'no_batch.*'           => 'nullable|string|max:50',
            'stock_gudang'         => 'nullable|array',
            'stock_gudang.*'       => 'required|integer|min:0',
            'min_persediaan'       => 'nullable|array',
            'min_persediaan.*'     => 'required|integer|min:0',
            'tanggal_masuk'        => 'nullable|array',
            'tanggal_masuk.*'      => 'nullable|date',
            'tanggal_produksi'     => 'nullable|array',
            'tanggal_produksi.*'   => 'nullable|date',
            'tanggal_kadaluarsa'   => 'nullable|array',
            'tanggal_kadaluarsa.*' => 'nullable|date',
            'lokasi_rak'           => 'nullable|array',
            'lokasi_rak.*'         => 'nullable|string|max:50',
            'kondisi'              => 'nullable|array',
            'kondisi.*'            => 'required|in:Baik,Rusak,Kadaluarsa',
        ]);

        DB::beginTransaction();
        try {
            $gudang->update([
                'kode_gudang'      => $request->kode_gudang,
                'nama_gudang'      => $request->nama_gudang,
                'lokasi'           => $request->lokasi,
                'penanggung_jawab' => $request->penanggung_jawab,
                'keterangan'       => $request->keterangan,
                'status'           => $request->status,
            ]);

            $submittedDetailIds = array_filter($request->detail_id ?? []);

            $gudang->details()
                ->whereNotIn('id', $submittedDetailIds)
                ->each(function ($detail) {
                    if ($detail->stock_gudang > 0) {
                        StockMovement::create([
                            'gudang_id'      => $detail->gudang_id,
                            'produk_id'      => $detail->produk_id,
                            'tipe'           => 'penyesuaian_keluar',
                            'referensi_tipe' => 'gudangs',
                            'referensi_id'   => $detail->gudang_id,
                            'referensi_no'   => optional($detail->gudang)->kode_gudang,
                            'qty_sebelum'    => $detail->stock_gudang,
                            'qty_perubahan'  => -$detail->stock_gudang,
                            'qty_sesudah'    => 0,
                            'no_batch'       => $detail->no_batch,
                            'id_karyawan'    => Auth::user()->id_karyawan,
                            'catatan'        => 'Detail dihapus saat edit gudang',
                        ]);
                    }
                    $detail->delete();
                });

            if ($request->filled('produk_id')) {
                foreach ($request->produk_id as $index => $produkId) {
                    $detailId = $request->detail_id[$index] ?? null;
                    $stokBaru = (int) ($request->stock_gudang[$index] ?? 0);

                    if ($detailId) {
                        $detail   = DetailGudang::findOrFail($detailId);
                        $stokLama = $detail->stock_gudang;

                        $detail->update([
                            'supplier_id'        => $request->detail_supplier_id[$index] ?? null,
                            'no_batch'           => $request->no_batch[$index] ?? null,
                            'stock_gudang'       => $stokBaru,
                            'min_persediaan'     => (int) ($request->min_persediaan[$index] ?? 0),
                            'tanggal_masuk'      => $request->tanggal_masuk[$index] ?? null,
                            'tanggal_produksi'   => $request->tanggal_produksi[$index] ?? null,
                            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa[$index] ?? null,
                            'lokasi_rak'         => $request->lokasi_rak[$index] ?? null,
                            'kondisi'            => $request->kondisi[$index] ?? 'Baik',
                        ]);

                        $selisih = $stokBaru - $stokLama;
                        if ($selisih !== 0) {
                            StockMovement::create([
                                'gudang_id'      => $gudang->id,
                                'produk_id'      => $produkId,
                                'tipe'           => $selisih > 0 ? 'penyesuaian_masuk' : 'penyesuaian_keluar',
                                'referensi_tipe' => 'gudangs',
                                'referensi_id'   => $gudang->id,
                                'referensi_no'   => $gudang->kode_gudang,
                                'qty_sebelum'    => $stokLama,
                                'qty_perubahan'  => $selisih,
                                'qty_sesudah'    => $stokBaru,
                                'no_batch'       => $request->no_batch[$index] ?? null,
                                'id_karyawan'    => Auth::user()->id_karyawan,
                                'catatan'        => 'Penyesuaian stok saat edit gudang',
                            ]);
                        }
                    } else {
                        $detail = DetailGudang::create([
                            'gudang_id'          => $gudang->id,
                            'produk_id'          => $produkId,
                            'supplier_id'        => $request->detail_supplier_id[$index] ?? null,
                            'no_batch'           => $request->no_batch[$index] ?? null,
                            'stock_gudang'       => $stokBaru,
                            'min_persediaan'     => (int) ($request->min_persediaan[$index] ?? 0),
                            'tanggal_masuk'      => $request->tanggal_masuk[$index] ?? null,
                            'tanggal_produksi'   => $request->tanggal_produksi[$index] ?? null,
                            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa[$index] ?? null,
                            'lokasi_rak'         => $request->lokasi_rak[$index] ?? null,
                            'kondisi'            => $request->kondisi[$index] ?? 'Baik',
                        ]);

                        if ($stokBaru > 0) {
                            StockMovement::create([
                                'gudang_id'      => $gudang->id,
                                'produk_id'      => $produkId,
                                'tipe'           => 'penyesuaian_masuk',
                                'referensi_tipe' => 'gudangs',
                                'referensi_id'   => $gudang->id,
                                'referensi_no'   => $gudang->kode_gudang,
                                'qty_sebelum'    => 0,
                                'qty_perubahan'  => $stokBaru,
                                'qty_sesudah'    => $stokBaru,
                                'no_batch'       => $request->no_batch[$index] ?? null,
                                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa[$index] ?? null,
                                'id_karyawan'    => Auth::user()->id_karyawan,
                                'catatan'        => 'Stok awal produk baru ditambahkan ke gudang',
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            Alert::info('Berhasil', 'Gudang berhasil diperbarui!');
            return redirect()->route('gudangs.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    // ──────────────────────────────────────────────────────────────
    // DESTROY
    // ──────────────────────────────────────────────────────────────
    public function destroy(Gudang $gudang)
    {
        $masihAdaStok = $gudang->details()->where('stock_gudang', '>', 0)->exists();
        if ($masihAdaStok) {
            Alert::error('Gagal', 'Gudang tidak dapat dihapus karena masih memiliki stok aktif.');
            return redirect()->back();
        }

        try {
            $gudang->details()->delete();
            $gudang->delete();
            Alert::success('Berhasil', 'Gudang berhasil dihapus!');
        } catch (\Exception $e) {
            Alert::error('Gagal', 'Tidak dapat menghapus gudang: ' . $e->getMessage());
        }
        return redirect()->route('gudangs.index');
    }

    // ──────────────────────────────────────────────────────────────
    // API: SEARCH PRODUK
    // ──────────────────────────────────────────────────────────────
    public function searchSupplierProducts(Request $request)
    {
        $query      = $request->get('q', '');
        $supplierId = $request->get('supplier_id');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = Produk::when(
                $supplierId,
                fn($q) => $q->whereHas('detailSuppliers', fn($q2) => $q2->where('supplier_id', $supplierId))
            )
            ->where(function ($q) use ($query) {
                $q->where('nama_produk', 'LIKE', "%{$query}%")
                  ->orWhere('kode_produk', 'LIKE', "%{$query}%")
                  ->orWhere('merk', 'LIKE', "%{$query}%")
                  ->orWhere('jenis', 'LIKE', "%{$query}%");
            })
            ->with(['produkSatuans.satuan'])
            ->limit(30)
            ->get()
            ->map(function ($produk) {
                return [
                    'id'          => $produk->id,
                    'kode_produk' => $produk->kode_produk,
                    'nama_produk' => $produk->nama_produk,
                    'merk'        => $produk->merk,
                    'jenis'       => $produk->jenis,
                    'nie'         => $produk->nie,
                    'satuans'     => $produk->produkSatuans->map(fn($ps) => [
                        'id'           => $ps->id,
                        'nama_satuan'  => $ps->satuan->nama_satuan ?? '-',
                        'konversi'     => $ps->konversi,
                        'is_default'   => $ps->is_default,
                        'kode_barcode' => $ps->kode_barcode,
                    ]),
                ];
            });

        return response()->json($results);
    }

    // ──────────────────────────────────────────────────────────────
    // API: DATA DETAIL GUDANG (DataTables)
    // ──────────────────────────────────────────────────────────────
    public function detailsData($id)
    {
        $details = DetailGudang::where('gudang_id', $id)
            ->with(['produk.produkSatuans.satuan', 'supplier'])
            ->get();

        return DataTables::of($details)
            ->addIndexColumn()
            ->addColumn('kode_produk', fn($d) => $d->produk->kode_produk ?? '-')
            ->addColumn('nama_produk', fn($d) => $d->produk->nama_produk ?? '-')
            ->addColumn('merk',        fn($d) => $d->produk->merk ?? '-')
            ->addColumn('jenis',       fn($d) => $d->produk->jenis ?? '-')
            ->addColumn('nie',         fn($d) => $d->produk->nie ?? '-')
            ->addColumn('supplier',    fn($d) => $d->supplier->nama_supplier ?? '-')
            ->addColumn('stok_tampil', fn($d) => $d->stok_dalam_satuan)
            ->addColumn('jumlah_keluar', fn($d) => $d->jumlahKeluar())
            ->addColumn('jumlah_retur',  fn($d) => $d->jumlahRetur())
            ->addColumn('kondisi', function ($d) {
                $map = [
                    'Baik'       => '<span class="badge bg-success">Baik</span>',
                    'Rusak'      => '<span class="badge bg-danger">Rusak</span>',
                    'Kadaluarsa' => '<span class="badge bg-warning text-dark">Kadaluarsa</span>',
                ];
                return $map[$d->kondisi] ?? '<span class="badge bg-secondary">' . $d->kondisi . '</span>';
            })
            ->addColumn('stok_status', function ($d) {
                if ($d->isBelowMinimum()) {
                    return '<span class="badge bg-danger">Stok Menipis</span>';
                }
                if ($d->isNearExpiry()) {
                    return '<span class="badge bg-warning text-dark">Segera Kadaluarsa</span>';
                }
                return '<span class="badge bg-success">Normal</span>';
            })
            ->rawColumns(['kondisi', 'stok_status'])
            ->make(true);
    }

    // ──────────────────────────────────────────────────────────────
    // API: DETAIL GUDANG BERDASARKAN PRODUK
    // ──────────────────────────────────────────────────────────────
    public function getDetailGudangByProduk($produkId, Request $request)
    {
        $query = DetailGudang::where('produk_id', $produkId)
            ->with(['supplier:id,nama_supplier']);

        if ($request->filled('gudang_id')) {
            $query->where('gudang_id', $request->gudang_id);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->boolean('include_produk')) {
            $query->with([
                'produk:id,kode_produk,nama_produk,merk,jenis,nie,deskripsi',
                'produk.produkSatuans.satuan',
            ]);
        }

        $details = $query->orderBy('tanggal_kadaluarsa')->get()
            ->map(function ($d) {
                $arr = $d->toArray();
                $arr['stok_dalam_satuan'] = $d->stok_dalam_satuan;
                foreach (['tanggal_masuk', 'tanggal_produksi', 'tanggal_kadaluarsa'] as $col) {
                    if ($d->$col) $arr[$col] = $d->$col->format('Y-m-d');
                }
                return $arr;
            });

        return response()->json($details);
    }

    // ──────────────────────────────────────────────────────────────
    // API: BATCH CRUD
    // ──────────────────────────────────────────────────────────────

    /** POST /api/gudang/batch */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'gudang_id'          => 'required|uuid|exists:gudangs,id',
            'produk_id'          => 'required|uuid|exists:produks,id',
            'supplier_id'        => 'nullable|uuid|exists:suppliers,id',
            'no_batch'           => 'nullable|string|max:50',
            'stock_gudang'       => 'required|integer|min:0',
            'min_persediaan'     => 'required|integer|min:0',
            'tanggal_masuk'      => 'nullable|date',
            'tanggal_produksi'   => 'nullable|date',
            'tanggal_kadaluarsa' => 'nullable|date',
            'lokasi_rak'         => 'nullable|string|max:50',
            'kondisi'            => 'required|in:Baik,Rusak,Kadaluarsa',
        ]);

        DB::beginTransaction();
        try {
            $detail = DetailGudang::create([
                'gudang_id'          => $request->gudang_id,
                'produk_id'          => $request->produk_id,
                'supplier_id'        => $request->supplier_id,
                'no_batch'           => $request->no_batch,
                'stock_gudang'       => $request->stock_gudang,
                'min_persediaan'     => $request->min_persediaan,
                'tanggal_masuk'      => $request->tanggal_masuk,
                'tanggal_produksi'   => $request->tanggal_produksi,
                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
                'lokasi_rak'         => $request->lokasi_rak,
                'kondisi'            => $request->kondisi,
            ]);

            if ($request->stock_gudang > 0) {
                StockMovement::create([
                    'gudang_id'          => $request->gudang_id,
                    'produk_id'          => $request->produk_id,
                    'tipe'               => 'penyesuaian_masuk',
                    'referensi_tipe'     => 'detail_gudangs',
                    'referensi_id'       => $detail->id,
                    'referensi_no'       => $request->no_batch ?? 'batch-baru',
                    'qty_sebelum'        => 0,
                    'qty_perubahan'      => $request->stock_gudang,
                    'qty_sesudah'        => $request->stock_gudang,
                    'no_batch'           => $request->no_batch,
                    'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
                    'id_karyawan'        => Auth::user()->id_karyawan,
                    'catatan'            => 'Batch baru ditambahkan via modal detail gudang',
                ]);
            }

            DB::commit();

            $detail->load('supplier');
            $arr = $detail->toArray();
            $arr['stok_dalam_satuan'] = $detail->stok_dalam_satuan;
            foreach (['tanggal_masuk', 'tanggal_produksi', 'tanggal_kadaluarsa'] as $col) {
                if ($detail->$col) $arr[$col] = $detail->$col->format('Y-m-d');
            }

            return response()->json(['success' => true, 'data' => $arr]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** PUT /api/gudang/batch/{detail} */
    public function updateBatch(Request $request, DetailGudang $detail)
    {
        $request->validate([
            'supplier_id'        => 'nullable|uuid|exists:suppliers,id',
            'no_batch'           => 'nullable|string|max:50',
            'stock_gudang'       => 'required|integer|min:0',
            'min_persediaan'     => 'required|integer|min:0',
            'tanggal_masuk'      => 'nullable|date',
            'tanggal_produksi'   => 'nullable|date',
            'tanggal_kadaluarsa' => 'nullable|date',
            'lokasi_rak'         => 'nullable|string|max:50',
            'kondisi'            => 'required|in:Baik,Rusak,Kadaluarsa',
        ]);

        DB::beginTransaction();
        try {
            $stokLama = $detail->stock_gudang;
            $stokBaru = $request->stock_gudang;

            $detail->update([
                'supplier_id'        => $request->supplier_id,
                'no_batch'           => $request->no_batch,
                'stock_gudang'       => $stokBaru,
                'min_persediaan'     => $request->min_persediaan,
                'tanggal_masuk'      => $request->tanggal_masuk,
                'tanggal_produksi'   => $request->tanggal_produksi,
                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
                'lokasi_rak'         => $request->lokasi_rak,
                'kondisi'            => $request->kondisi,
            ]);

            $selisih = $stokBaru - $stokLama;
            if ($selisih !== 0) {
                StockMovement::create([
                    'gudang_id'          => $detail->gudang_id,
                    'produk_id'          => $detail->produk_id,
                    'tipe'               => $selisih > 0 ? 'penyesuaian_masuk' : 'penyesuaian_keluar',
                    'referensi_tipe'     => 'detail_gudangs',
                    'referensi_id'       => $detail->id,
                    'referensi_no'       => $request->no_batch ?? $detail->no_batch ?? 'penyesuaian',
                    'qty_sebelum'        => $stokLama,
                    'qty_perubahan'      => $selisih,
                    'qty_sesudah'        => $stokBaru,
                    'no_batch'           => $request->no_batch ?? $detail->no_batch,
                    'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
                    'id_karyawan'        => Auth::user()->id_karyawan,
                    'catatan'            => 'Penyesuaian stok via modal detail gudang',
                ]);
            }

            DB::commit();

            $detail->refresh()->load('supplier');
            $arr = $detail->toArray();
            $arr['stok_dalam_satuan'] = $detail->stok_dalam_satuan;
            foreach (['tanggal_masuk', 'tanggal_produksi', 'tanggal_kadaluarsa'] as $col) {
                if ($detail->$col) $arr[$col] = $detail->$col->format('Y-m-d');
            }

            return response()->json(['success' => true, 'data' => $arr]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** DELETE /api/gudang/batch/{detail} */
    public function destroyBatch(DetailGudang $detail)
    {
        DB::beginTransaction();
        try {
            if ($detail->stock_gudang > 0) {
                StockMovement::create([
                    'gudang_id'      => $detail->gudang_id,
                    'produk_id'      => $detail->produk_id,
                    'tipe'           => 'penyesuaian_keluar',
                    'referensi_tipe' => 'detail_gudangs',
                    'referensi_id'   => $detail->id,
                    'referensi_no'   => $detail->no_batch ?? 'hapus-batch',
                    'qty_sebelum'    => $detail->stock_gudang,
                    'qty_perubahan'  => -$detail->stock_gudang,
                    'qty_sesudah'    => 0,
                    'no_batch'       => $detail->no_batch,
                    'id_karyawan'    => Auth::user()->id_karyawan,
                    'catatan'        => 'Batch dihapus via modal detail gudang',
                ]);
            }
            $detail->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // PROSES PENERIMAAN (dari PO pembelian)
    // ──────────────────────────────────────────────────────────────
    public function prosesPenerimaan(Request $request)
    {
        $request->validate([
            'gudang_id'    => 'required|uuid|exists:gudangs,id',
            'referensi_id' => 'required|uuid',
            'referensi_no' => 'required|string',
            'items'        => 'required|array|min:1',
            'items.*.produk_id'          => 'required|uuid|exists:produks,id',
            'items.*.supplier_id'        => 'required|uuid|exists:suppliers,id',
            'items.*.no_batch'           => 'required|string|max:50',
            'items.*.qty'                => 'required|integer|min:1',
            'items.*.tanggal_kadaluarsa' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $gudangId   = $request->gudang_id;
            $totalMasuk = 0;

            foreach ($request->items as $item) {
                $produkId   = $item['produk_id'];
                $supplierId = $item['supplier_id'];
                $noBatch    = $item['no_batch'];
                $qty        = (int) $item['qty'];
                $expDate    = $item['tanggal_kadaluarsa'] ?? null;

                $detail = DetailGudang::firstOrNew([
                    'gudang_id'   => $gudangId,
                    'produk_id'   => $produkId,
                    'supplier_id' => $supplierId,
                    'no_batch'    => $noBatch,
                ]);

                $stokLama = $detail->stock_gudang ?? 0;
                $stokBaru = $stokLama + $qty;

                if (!$detail->exists) {
                    $detail->min_persediaan     = 0;
                    $detail->tanggal_masuk      = now();
                    $detail->tanggal_kadaluarsa = $expDate;
                    $detail->kondisi            = 'Baik';
                } else {
                    if ($expDate) {
                        $detail->tanggal_kadaluarsa = $expDate;
                    }
                }

                $detail->stock_gudang = $stokBaru;
                $detail->save();

                StockMovement::create([
                    'gudang_id'          => $gudangId,
                    'produk_id'          => $produkId,
                    'tipe'               => 'pembelian',
                    'referensi_tipe'     => 'purchase_orders',
                    'referensi_id'       => $request->referensi_id,
                    'referensi_no'       => $request->referensi_no,
                    'qty_sebelum'        => $stokLama,
                    'qty_perubahan'      => $qty,
                    'qty_sesudah'        => $stokBaru,
                    'no_batch'           => $noBatch,
                    'tanggal_kadaluarsa' => $expDate,
                    'id_karyawan'        => Auth::user()->id_karyawan,
                    'catatan'            => "Penerimaan dari PO {$request->referensi_no}",
                ]);

                $totalMasuk += $qty;
            }

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Penerimaan barang berhasil diproses',
                'total_item' => count($request->items),
                'total_qty'  => $totalMasuk,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses penerimaan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function mutasiStok(Request $request, string $gudangId)
    {
        $query = StockMovement::where('gudang_id', $gudangId)
            ->with([
                'produk:id,kode_produk,nama_produk,merk',
                'karyawan:id_karyawan,nama_lengkap',
            ])
            ->orderByDesc('created_at');

        if ($request->filled('produk_id')) {
            $query->where('produk_id', $request->produk_id);
        }
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        // FIX 1: Filter supplier tanpa syarat no_batch agar
        //        semua tipe transaksi (termasuk penjualan) tetap muncul
        if ($request->filled('supplier_id')) {
            $supplierId = $request->supplier_id;
            $query->whereExists(function ($sub) use ($supplierId) {
                $sub->select(DB::raw(1))
                    ->from('detail_gudangs')
                    ->whereColumn('detail_gudangs.gudang_id', 'stock_movements.gudang_id')
                    ->whereColumn('detail_gudangs.produk_id', 'stock_movements.produk_id')
                    ->where('detail_gudangs.supplier_id', $supplierId);
            });
        }

        $paginated = $query->paginate(15);

        // FIX 2: Attach supplier_nama dengan fallback ke batch lain
        //        jika batch spesifik sudah tidak ada di detail_gudangs
        $paginated->getCollection()->transform(function ($movement) use ($gudangId) {
            // Coba cocokkan no_batch spesifik terlebih dahulu
            $detail = DetailGudang::where('gudang_id', $gudangId)
                ->where('produk_id', $movement->produk_id)
                ->when(
                    $movement->no_batch,
                    fn($q) => $q->where('no_batch', $movement->no_batch)
                )
                ->with('supplier:id,nama_supplier')
                ->first();

            // Fallback: ambil supplier dari batch manapun jika batch spesifik
            // sudah tidak ada (misalnya stok terjual habis lalu baris dihapus)
            if (!$detail || !$detail->supplier) {
                $detail = DetailGudang::where('gudang_id', $gudangId)
                    ->where('produk_id', $movement->produk_id)
                    ->whereNotNull('supplier_id')
                    ->with('supplier:id,nama_supplier')
                    ->first();
            }

            $movement->supplier_nama = $detail?->supplier?->nama_supplier ?? null;
            $movement->supplier_id   = $detail?->supplier_id ?? null;

            return $movement;
        });

        return response()->json($paginated);
    }
}