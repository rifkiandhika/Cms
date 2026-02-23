<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Jenis;
use App\Models\Produk;
use App\Models\Satuan;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')->paginate(10);
        return view('customer.index', compact('customers'));
    }

    public function create()
    {
        $jenis   = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuans = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();
        $customer = new Customer();

        return view('customer.create', compact('jenis', 'satuans', 'customer'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_customer'    => 'required|string|max:50|unique:customers,kode_customer',
            'nama_customer'    => 'required|string|max:100',
            'tipe_customer'    => 'required|string',
            'status'           => 'required|in:aktif,nonaktif',
            'nama_kontak'      => 'nullable|string|max:100',
            'email'            => 'nullable|email|max:100',
            'telepon'          => 'nullable|string|max:15',
            'alamat'           => 'nullable|string',
            'kota'             => 'nullable|string|max:100',
            'provinsi'         => 'nullable|string|max:100',
            'npwp'             => 'nullable|string|max:30',
            'izin_operasional' => 'nullable|string|max:100',

            // Detail produk
            'jenis'            => 'nullable|array',
            'jenis.*'          => 'required|string',
            'product_id'       => 'nullable|array',
            'product_id.*'     => 'nullable|uuid',
            'nama_manual'      => 'nullable|array',
            'nama_manual.*'    => 'nullable|string|max:200',
            'no_batch'         => 'nullable|array',
            'no_batch.*'       => 'nullable|string',
            'judul'            => 'nullable|array',
            'judul.*'          => 'nullable|string',
            'merk'             => 'nullable|array',
            'merk.*'           => 'nullable|string',
            'satuan'           => 'nullable|array',
            'satuan.*'         => 'nullable|string',
            'harga_jual'       => 'nullable|array',
            'harga_jual.*'     => 'nullable|numeric|min:0',
            'stock_live'       => 'nullable|array',
            'stock_live.*'     => 'nullable|integer|min:0',
            'stock_po'         => 'nullable|array',
            'stock_po.*'       => 'nullable|integer|min:0',
            'min_persediaan'   => 'nullable|array',
            'min_persediaan.*' => 'nullable|integer|min:0',
            'exp_date'         => 'nullable|array',
            'exp_date.*'       => 'nullable|date',
            'kode_rak'         => 'nullable|array',
            'kode_rak.*'       => 'nullable|string',
        ]);

        $customer = Customer::create([
            'kode_customer'    => $request->kode_customer,
            'nama_customer'    => $request->nama_customer,
            'tipe_customer'    => $request->tipe_customer,
            'status'           => $request->status,
            'nama_kontak'      => $request->nama_kontak,
            'email'            => $request->email,
            'telepon'          => $request->telepon,
            'alamat'           => $request->alamat,
            'kota'             => $request->kota,
            'provinsi'         => $request->provinsi,
            'npwp'             => $request->npwp,
            'izin_operasional' => $request->izin_operasional,
        ]);

        $this->saveDetails($customer, $request);

        Alert::success('Berhasil', 'Data customer berhasil ditambahkan!');
        return redirect()->route('customers.index');
    }

    public function edit(Customer $customer)
    {
        $customer->load(['detailCustomers.produk']);

        $jenis   = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuans = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();

        return view('customer.edit', compact('customer', 'jenis', 'satuans'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'kode_customer'    => 'required|string|max:50|unique:customers,kode_customer,' . $customer->id,
            'nama_customer'    => 'required|string|max:100',
            'tipe_customer'    => 'required|string',
            'status'           => 'required|in:aktif,nonaktif',
            'nama_kontak'      => 'nullable|string|max:100',
            'email'            => 'nullable|email|max:100',
            'telepon'          => 'nullable|string|max:15',
            'alamat'           => 'nullable|string',
            'kota'             => 'nullable|string|max:100',
            'provinsi'         => 'nullable|string|max:100',
            'npwp'             => 'nullable|string|max:30',
            'izin_operasional' => 'nullable|string|max:100',

            'jenis'            => 'nullable|array',
            'jenis.*'          => 'required|string',
            'product_id'       => 'nullable|array',
            'product_id.*'     => 'nullable|uuid',
            'nama_manual'      => 'nullable|array',
            'nama_manual.*'    => 'nullable|string|max:200',
            'satuan'           => 'nullable|array',
            'satuan.*'         => 'nullable|string',
        ]);

        $customer->update([
            'kode_customer'    => $request->kode_customer,
            'nama_customer'    => $request->nama_customer,
            'tipe_customer'    => $request->tipe_customer,
            'status'           => $request->status,
            'nama_kontak'      => $request->nama_kontak,
            'email'            => $request->email,
            'telepon'          => $request->telepon,
            'alamat'           => $request->alamat,
            'kota'             => $request->kota,
            'provinsi'         => $request->provinsi,
            'npwp'             => $request->npwp,
            'izin_operasional' => $request->izin_operasional,
        ]);

        // Hapus detail lama, simpan yang baru
        $customer->detailCustomers()->delete();
        $this->saveDetails($customer, $request);

        Alert::info('Berhasil', 'Data customer berhasil diperbarui!');
        return redirect()->route('customers.index');
    }

    public function destroy(Customer $customer)
    {
        $customer->detailCustomers()->delete();
        $customer->delete();

        Alert::warning('Berhasil', 'Customer berhasil dihapus!');
        return redirect()->route('customers.index');
    }

    // =========================================================
    // Private Helper
    // =========================================================

    private function saveDetails(Customer $customer, Request $request): void
    {
        if (!$request->has('jenis') || !is_array($request->jenis)) {
            return;
        }

        foreach ($request->jenis as $i => $jenis) {
            $productId  = $request->product_id[$i] ?? null;
            $namaBarang = null;
            $merk       = $request->merk[$i] ?? null;
            $satuan     = $request->satuan[$i] ?? null;
            $hargaJual  = $request->harga_jual[$i] ?? 0;

            if ($productId) {
                $produk = Produk::find($productId);
                if ($produk) {
                    $namaBarang = $produk->nama_produk;
                    $merk       = $merk   ?: $produk->merk;
                    $satuan     = $satuan ?: $produk->satuan;
                    // Gunakan harga_jual dari produk jika tidak diisi manual
                    $hargaJual  = $hargaJual ?: ($produk->harga_jual ?? 0);
                }
            } else {
                $namaBarang = $request->nama_manual[$i] ?? null;
                $productId  = null;
            }

            if (!$namaBarang) {
                continue;
            }

            $customer->detailCustomers()->create([
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
                'harga_jual'     => str_replace('.', '', $hargaJual),
                'kode_rak'       => $request->kode_rak[$i] ?? null,
            ]);
        }
    }
}
