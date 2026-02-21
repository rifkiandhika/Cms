<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailPengendalianHama;
use App\Models\PengendalianHama;
use App\Models\PengendalianHamaGambar;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengendalianHamaController extends Controller
{
    public function index()
    {
        $data = PengendalianHama::withCount('details')
            ->with('gambar')
            ->latest()
            ->get();

        return view('pengendalian-hama.index', compact('data'));
    }

    public function create()
    {
        return view('pengendalian-hama.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'lokasi'          => 'required|string|max:255',
            'bulan'           => 'required|string|max:20',
            'tahun'           => 'required|digits:4',
            'penanggung_jawab'=> 'nullable|string|max:255',
            'gambar.*'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // Detail rows
            'rows.*.tanggal'            => 'required|date',
            'rows.*.waktu'              => 'nullable|date_format:H:i',
            'rows.*.perangkap_perlakuan'=> 'nullable|string|max:10',
            'rows.*.jumlah_hama'        => 'nullable|integer|min:0',
            'rows.*.evaluasi'           => 'nullable|string|max:255',
            'rows.*.nama_petugas'       => 'nullable|string|max:255',
            'rows.*.keterangan'         => 'nullable|string|max:255',
        ]);

        // Simpan header
        $header = PengendalianHama::create([
            'lokasi'           => $request->lokasi,
            'bulan'            => $request->bulan,
            'tahun'            => $request->tahun,
            'penanggung_jawab' => $request->penanggung_jawab,
        ]);

        // Simpan detail baris
        if ($request->has('rows')) {
            foreach ($request->rows as $row) {
                if (empty($row['tanggal'])) continue;

                DetailPengendalianHama::create([
                    'pengendalian_hama_id' => $header->id,
                    'tanggal'              => $row['tanggal'],
                    'hari'                 => Carbon::parse($row['tanggal'])->locale('id')->dayName,
                    'waktu'                => $row['waktu'] ?? null,
                    'treatment_c'          => isset($row['treatment_c']) ? 1 : 0,
                    'treatment_b'          => isset($row['treatment_b']) ? 1 : 0,
                    'treatment_f'          => isset($row['treatment_f']) ? 1 : 0,
                    'treatment_i'          => isset($row['treatment_i']) ? 1 : 0,
                    'perangkap_perlakuan'  => $row['perangkap_perlakuan'] ?? null,
                    'jumlah_hama'          => $row['jumlah_hama'] ?? 0,
                    'evaluasi'             => $row['evaluasi'] ?? null,
                    'nama_petugas'         => $row['nama_petugas'] ?? null,
                    'paraf_petugas'        => isset($row['paraf_petugas']) ? 1 : 0,
                    'keterangan'           => $row['keterangan'] ?? null,
                ]);
            }
        }

        // Simpan gambar
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $file) {
                $path = $file->store('pengendalian-hama', 'public');
                PengendalianHamaGambar::create([
                    'pengendalian_hama_id' => $header->id,
                    'path_gambar'          => $path,
                    'nama_file'            => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('pengendalian-hama.show', $header->id)
            ->with('success', 'Data pengendalian hama berhasil disimpan.');
    }

    public function show(PengendalianHama $pengendalianHama)
    {
        $pengendalianHama->load(['details' => fn($q) => $q->orderBy('tanggal'), 'gambar']);

        return view('pengendalian-hama.show', compact('pengendalianHama'));
    }

    public function edit(PengendalianHama $pengendalianHama)
    {
        $pengendalianHama->load(['details' => fn($q) => $q->orderBy('tanggal'), 'gambar']);

        return view('pengendalian-hama.edit', compact('pengendalianHama'));
    }

    public function update(Request $request, PengendalianHama $pengendalianHama)
    {
        $request->validate([
            'lokasi'           => 'required|string|max:255',
            'bulan'            => 'required|string|max:20',
            'tahun'            => 'required|digits:4',
            'penanggung_jawab' => 'nullable|string|max:255',
            'gambar.*'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'rows.*.tanggal'   => 'required|date',
        ]);

        // Update header
        $pengendalianHama->update($request->only('lokasi', 'bulan', 'tahun', 'penanggung_jawab'));

        // Hapus detail lama, ganti dengan yang baru
        $pengendalianHama->details()->delete();

        if ($request->has('rows')) {
            foreach ($request->rows as $row) {
                if (empty($row['tanggal'])) continue;

                DetailPengendalianHama::create([
                    'pengendalian_hama_id' => $pengendalianHama->id,
                    'tanggal'              => $row['tanggal'],
                    'hari'                 => Carbon::parse($row['tanggal'])->locale('id')->dayName,
                    'waktu'                => $row['waktu'] ?? null,
                    'treatment_c'          => isset($row['treatment_c']) ? 1 : 0,
                    'treatment_b'          => isset($row['treatment_b']) ? 1 : 0,
                    'treatment_f'          => isset($row['treatment_f']) ? 1 : 0,
                    'treatment_i'          => isset($row['treatment_i']) ? 1 : 0,
                    'perangkap_perlakuan'  => $row['perangkap_perlakuan'] ?? null,
                    'jumlah_hama'          => $row['jumlah_hama'] ?? 0,
                    'evaluasi'             => $row['evaluasi'] ?? null,
                    'nama_petugas'         => $row['nama_petugas'] ?? null,
                    'paraf_petugas'        => isset($row['paraf_petugas']) ? 1 : 0,
                    'keterangan'           => $row['keterangan'] ?? null,
                ]);
            }
        }

        // Tambah gambar baru jika ada
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $file) {
                $path = $file->store('pengendalian-hama', 'public');
                PengendalianHamaGambar::create([
                    'pengendalian_hama_id' => $pengendalianHama->id,
                    'path_gambar'          => $path,
                    'nama_file'            => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('pengendalian-hama.show', $pengendalianHama->id)
            ->with('success', 'Data pengendalian hama berhasil diperbarui.');
    }

    public function destroy(PengendalianHama $pengendalianHama)
    {
        // Hapus file gambar dari storage
        foreach ($pengendalianHama->gambar as $gambar) {
            Storage::disk('public')->delete($gambar->path_gambar);
        }

        $pengendalianHama->gambar()->delete();
        $pengendalianHama->details()->delete();
        $pengendalianHama->delete();

        return redirect()->route('pengendalian-hama.index')
            ->with('success', 'Data pengendalian hama berhasil dihapus.');
    }

    public function destroyGambar(PengendalianHamaGambar $gambar)
    {
        Storage::disk('public')->delete($gambar->path_gambar);
        $hambaId = $gambar->pengendalian_hama_id;
        $gambar->delete();

        return redirect()->route('pengendalian-hama.show', $hambaId)
            ->with('success', 'Gambar berhasil dihapus.');
    }

    public function exportPdf(PengendalianHama $pengendalianHama)
    {
        $pengendalianHama->load(['details' => fn($q) => $q->orderBy('tanggal'), 'gambar']);

        // Format bulan dan tahun untuk ditampilkan
        $bulanTahun = $pengendalianHama->bulan . ' ' . $pengendalianHama->tahun;
        
        // Encode gambar ke base64 untuk ditampilkan di PDF
        $gambarBase64 = [];
        foreach ($pengendalianHama->gambar as $gambar) {
            $path = storage_path('app/public/' . $gambar->path_gambar);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $gambarBase64[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $data = [
            'pengendalianHama' => $pengendalianHama,
            'details' => $pengendalianHama->details,
            'gambarBase64' => $gambarBase64,
            'lokasi' => $pengendalianHama->lokasi,
            'bulan' => $pengendalianHama->bulan,
            'tahun' => $pengendalianHama->tahun,
            'bulanTahun' => $bulanTahun,
            'penanggungJawab' => $pengendalianHama->penanggung_jawab ?? '____________________',
            'tanggalCetak' => now()->format('d M Y'),
        ];

        $pdf = Pdf::loadView('pengendalian-hama.pdf', $data);
        $pdf->setPaper('A4', 'landscape'); // Landscape karena tabelnya lebar
        
        $filename = 'Pengendalian_Hama_' . $pengendalianHama->lokasi . '_' . $bulanTahun . '.pdf';
        
        return $pdf->download($filename);
    }
}
