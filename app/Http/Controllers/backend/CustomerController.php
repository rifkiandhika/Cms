<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_customer' => 'required|string|max:255|unique:customers,kode_customer',
            'nama_customer' => 'required|string|max:255',
            'tipe_customer' => 'required|in:rumah_sakit,klinik,laboratorium,apotek,lainnya',
            'nama_kontak' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:255',
            'izin_operasional' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'kode_customer.required' => 'Kode customer wajib diisi',
            'kode_customer.unique' => 'Kode customer sudah digunakan',
            'nama_customer.required' => 'Nama customer wajib diisi',
            'tipe_customer.required' => 'Tipe customer wajib dipilih',
            'tipe_customer.in' => 'Tipe customer tidak valid',
            'email.email' => 'Format email tidak valid',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        try {
            Customer::create($validated);

            Alert::success('success', 'Customer berhasil ditambahkan!');
            return redirect()->route('customers.index');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambahkan customer: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('customer.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customer.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'kode_customer' => 'required|string|max:255|unique:customers,kode_customer,' . $customer->id,
            'nama_customer' => 'required|string|max:255',
            'tipe_customer' => 'required|in:rumah_sakit,klinik,laboratorium,apotek,lainnya',
            'nama_kontak' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:255',
            'izin_operasional' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'kode_customer.required' => 'Kode customer wajib diisi',
            'kode_customer.unique' => 'Kode customer sudah digunakan',
            'nama_customer.required' => 'Nama customer wajib diisi',
            'tipe_customer.required' => 'Tipe customer wajib dipilih',
            'tipe_customer.in' => 'Tipe customer tidak valid',
            'email.email' => 'Format email tidak valid',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        try {
            $customer->update($validated);

            Alert::success('success', 'Customer berhasil diupdate!');
            return redirect()->route('customers.index');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal mengupdate customer: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();

            Alert::success('success', 'Customer berhasil dihapus!');
            return redirect()->route('customers.index');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus customer: ' . $e->getMessage());
        }
    }
}
