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
        $jenis    = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuans  = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();
        $customer = new Customer();

        return view('customer.create', compact('jenis', 'satuans', 'customer'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Data utama customer
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

            // Detail produk — sesuai kolom schema detail_customers
            'produk_id'            => 'nullable|array',
            'produk_id.*'          => 'nullable|uuid',
            'produk_satuan_id'     => 'nullable|array',
            'produk_satuan_id.*'   => 'nullable|uuid',
            'harga_jual'           => 'nullable|array',
            'harga_jual.*'         => 'nullable|numeric|min:0',
            'catatan'              => 'nullable|array',
            'catatan.*'            => 'nullable|string',
            'is_aktif'             => 'nullable|array',
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
        // Load relasi produk dan produkSatuan untuk ditampilkan di blade
        $customer->load([
            'detailCustomers.produk.produkSatuans',
            'detailCustomers.produkSatuan',
        ]);

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

            'produk_id'            => 'nullable|array',
            'produk_id.*'          => 'nullable|uuid',
            'produk_satuan_id'     => 'nullable|array',
            'produk_satuan_id.*'   => 'nullable|uuid',
            'harga_jual'           => 'nullable|array',
            'harga_jual.*'         => 'nullable|numeric|min:0',
            'catatan'              => 'nullable|array',
            'catatan.*'            => 'nullable|string',
            'is_aktif'             => 'nullable|array',
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

        // Hapus detail lama, simpan ulang
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

    /**
     * Simpan detail produk customer.
     * Kolom yang disimpan sesuai schema detail_customers:
     *   customer_id, produk_id, produk_satuan_id, harga_jual, is_aktif, catatan
     *
     * Constraint unique: (customer_id, produk_id, produk_satuan_id)
     * → pakai updateOrCreate agar tidak duplikat.
     */
    private function saveDetails(Customer $customer, Request $request): void
    {
        $produkIds = $request->input('produk_id', []);

        if (empty($produkIds)) {
            return;
        }

        foreach ($produkIds as $i => $produkId) {
            // Hanya proses baris yang memiliki produk_id
            if (empty($produkId)) {
                continue;
            }

            $produkSatuanId = $request->input("produk_satuan_id.{$i}") ?: null;
            $hargaJual      = $request->input("harga_jual.{$i}", 0);
            $catatan        = $request->input("catatan.{$i}") ?: null;

            // Bersihkan format rupiah (titik ribuan)
            $hargaJual = str_replace('.', '', $hargaJual);

            // Checkbox is_aktif: hanya terkirim jika dicentang
            $isAktif = isset($request->is_aktif[$i]) ? true : false;

            // updateOrCreate untuk menghindari duplicate unique constraint
            // (customer_id, produk_id, produk_satuan_id)
            $customer->detailCustomers()->updateOrCreate(
                [
                    'produk_id'        => $produkId,
                    'produk_satuan_id' => $produkSatuanId,
                ],
                [
                    'harga_jual' => is_numeric($hargaJual) ? $hargaJual : 0,
                    'is_aktif'   => $isAktif,
                    'catatan'    => $catatan,
                ]
            );
        }
    }
}