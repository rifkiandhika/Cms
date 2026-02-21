<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\CatatanSuhuRuangan;
use App\Models\KontrolGudang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CatatanSuhuRuanganController extends Controller
{
     /**
     * Halaman create periode baru.
     */
    public function create()
    {
        return view('catatan-suhu.create');
    }

    /**
     * Simpan periode baru (parent).
     */
    public function storePeriode(Request $request)
    {
        $request->validate([
            'periode'     => 'required|string|max:20',
            'nama_gudang' => 'required|string|max:255',
        ]);

        KontrolGudang::create($request->only('periode', 'nama_gudang'));

        return redirect()->route('sops.index')
            ->with('success', 'Periode gudang berhasil ditambahkan.');
    }

    /**
     * Halaman detail - tampilkan catatan harian per periode.
     */
    public function show(KontrolGudang $kontrolGudang)
    {
        $catatanSuhu = $kontrolGudang->catatanSuhu()->orderBy('tanggal')->get();

        return view('catatan-suhu.show', compact('kontrolGudang', 'catatanSuhu'));
    }

    /**
     * Halaman edit periode.
     */
    public function editPeriode(KontrolGudang $kontrolGudang)
    {
        return view('catatan-suhu.edit-periode', compact('kontrolGudang'));
    }

    /**
     * Update periode.
     */
    public function updatePeriode(Request $request, KontrolGudang $kontrolGudang)
    {
        $request->validate([
            'periode'     => 'required|string|max:20',
            'nama_gudang' => 'required|string|max:255',
        ]);

        $kontrolGudang->update($request->only('periode', 'nama_gudang'));

        return redirect()->route('catatan-suhu.show', $kontrolGudang->id)
            ->with('success', 'Periode berhasil diperbarui.');
    }

    /**
     * Hapus periode beserta semua catatan suhu-nya.
     */
    public function destroyPeriode(KontrolGudang $kontrolGudang)
    {
        $kontrolGudang->catatanSuhu()->delete();
        $kontrolGudang->delete();

        return redirect()->route('sops.index')
            ->with('success', 'Periode gudang beserta catatan suhu berhasil dihapus.');
    }

    /**
     * Simpan catatan suhu harian baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kontrol_gudang_id' => 'required|exists:kontrol_gudang,id',
            'tanggal'           => 'required|date',
            'kebersihan'        => 'required|boolean',
            'suhu_refrigerator' => 'required|numeric',
            'suhu_ruangan'      => 'required|numeric',
            'kelembapan'        => 'required|numeric|min:0|max:100',
            'keamanan'          => 'required|boolean',
        ]);

        CatatanSuhuRuangan::create($request->all());

        return redirect()->route('catatan-suhu.show', $request->kontrol_gudang_id)
            ->with('success', 'Catatan suhu berhasil ditambahkan.');
    }

    /**
     * Update catatan suhu harian.
     */
    public function update(Request $request, CatatanSuhuRuangan $catatanSuhu)
    {
        $request->validate([
            'tanggal'           => 'required|date',
            'kebersihan'        => 'required|boolean',
            'suhu_refrigerator' => 'required|numeric',
            'suhu_ruangan'      => 'required|numeric',
            'kelembapan'        => 'required|numeric|min:0|max:100',
            'keamanan'          => 'required|boolean',
        ]);

        $catatanSuhu->update($request->all());

        return redirect()->route('catatan-suhu.show', $catatanSuhu->kontrol_gudang_id)
            ->with('success', 'Catatan suhu berhasil diperbarui.');
    }

    /**
     * Hapus catatan suhu harian (soft delete).
     */
    public function destroy(CatatanSuhuRuangan $catatanSuhu)
    {
        $kontrolGudangId = $catatanSuhu->kontrol_gudang_id;
        $catatanSuhu->delete();

        return redirect()->route('catatan-suhu.show', $kontrolGudangId)
            ->with('success', 'Catatan suhu berhasil dihapus.');
    }

    public function exportPdf(KontrolGudang $kontrolGudang)
    {
        $catatanSuhu = $kontrolGudang->catatanSuhu()
            ->orderBy('tanggal')
            ->get();

        $data = [
            'kontrolGudang' => $kontrolGudang,
            'catatanSuhu' => $catatanSuhu,
            'periode' => $kontrolGudang->periode,
            'nama_gudang' => $kontrolGudang->nama_gudang,
            'tanggal_cetak' => now()->format('d M Y'),
        ];

        $pdf = Pdf::loadView('catatan-suhu.pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        // Download PDF dengan nama file yang dinamis
        $filename = 'Catatan_Suhu_' . $kontrolGudang->periode . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}
