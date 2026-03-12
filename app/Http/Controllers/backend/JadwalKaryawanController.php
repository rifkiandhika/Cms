<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\JadwalKaryawan;
use App\Models\PesertaJadwal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JadwalKaryawanController extends Controller
{
    /**
     * Get jadwal for calendar (AJAX)
     */
    public function getJadwalCalendar(Request $request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $jadwals = JadwalKaryawan::whereBetween('tanggal', [$start, $end])
            ->withCount('peserta')
            ->get()
            ->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'title' => $jadwal->nama_acara . ($jadwal->peserta_count > 0 ? ' (' . $jadwal->peserta_count . ')' : ''),
                    'start' => $jadwal->tanggal->format('Y-m-d'),
                    'backgroundColor' => $this->getStatusColor($jadwal->status),
                    'borderColor' => $this->getStatusColor($jadwal->status),
                    'textColor' => '#fff',
                    'extendedProps' => [
                        'peserta_count' => $jadwal->peserta_count,
                        'status' => $jadwal->status,
                        'lokasi' => $jadwal->lokasi,
                        'waktu_mulai' => $jadwal->waktu_mulai ? substr($jadwal->waktu_mulai, 0, 5) : null,
                    ]
                ];
            });

        return response()->json($jadwals);
    }

    /**
     * Get jadwal by date (AJAX)
     */
    public function getJadwalByDate(Request $request)
    {
        try {
            $date = $request->input('date');
            
            $jadwals = JadwalKaryawan::whereDate('tanggal', $date)
                ->with('peserta')
                ->orderBy('waktu_mulai', 'asc')
                ->get();

            return response()->json($jadwals);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading jadwal by date: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store jadwal (AJAX) - WITH FILE UPLOAD SUPPORT
     */
    public function store(Request $request)
    {
        // dd($request->sop_id, $request->all());
        // dd($request->all());
        $validated = $request->validate([
            'sop_id'      => 'nullable|exists:sops,id',
            'tanggal' => 'required|date',
            'nama_acara' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'nullable|in:scheduled,ongoing,completed,cancelled',
            'peserta' => 'nullable|array',
            'peserta.*.nama_karyawan' => 'nullable|string|max:255',
            'peserta.*.catatan' => 'nullable|string',
            'peserta.*.nilai' => 'nullable|integer|min:0|max:100',
            'peserta.*.status_kehadiran' => 'nullable|in:hadir,tidak_hadir,izin,sakit',
            'peserta.*.bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Create jadwal
            $jadwal = JadwalKaryawan::create([
                'sop_id' => $validated['sop_id'] ?? null,
                'tanggal' => $validated['tanggal'],
                'nama_acara' => $validated['nama_acara'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'waktu_mulai' => $validated['waktu_mulai'] ?? null,
                'waktu_selesai' => $validated['waktu_selesai'] ?? null,
                'lokasi' => $validated['lokasi'] ?? null,
                'status' => $validated['status'] ?? 'scheduled',
            ]);

            // Create peserta dengan upload bukti
            if (isset($validated['peserta']) && is_array($validated['peserta'])) {
                foreach ($validated['peserta'] as $index => $pesertaData) {
                    // Skip jika nama karyawan kosong
                    if (empty($pesertaData['nama_karyawan'])) {
                        continue;
                    }
                    
                    $buktiPath = null;
                    
                    // Handle file upload
                    if ($request->hasFile("peserta.{$index}.bukti")) {
                        $file = $request->file("peserta.{$index}.bukti");
                        $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                        $buktiPath = $file->storeAs('bukti-peserta', $filename, 'public');
                    }
                    
                    PesertaJadwal::create([
                        'jadwal_karyawan_id' => $jadwal->id,
                        'nama_karyawan' => $pesertaData['nama_karyawan'],
                        'catatan' => $pesertaData['catatan'] ?? null,
                        'nilai' => $pesertaData['nilai'] ?? null,
                        'status_kehadiran' => $pesertaData['status_kehadiran'] ?? 'hadir',
                        'bukti' => $buktiPath,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil ditambahkan!',
                'data' => $jadwal->load('peserta')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update jadwal (AJAX) - WITH FILE UPLOAD SUPPORT
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sop_id'      => 'nullable|exists:sops,id',
            'tanggal' => 'required|date',
            'nama_acara' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'nullable|in:scheduled,ongoing,completed,cancelled',
            'peserta' => 'nullable|array',
            'peserta.*.id' => 'nullable|integer',
            'peserta.*.nama_karyawan' => 'nullable|string|max:255',
            'peserta.*.catatan' => 'nullable|string',
            'peserta.*.nilai' => 'nullable|integer|min:0|max:100',
            'peserta.*.status_kehadiran' => 'nullable|in:hadir,tidak_hadir,izin,sakit',
            'peserta.*.bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'peserta.*.remove_bukti' => 'nullable|boolean',
        ]);

        try {
            $jadwal = JadwalKaryawan::findOrFail($id);
            
            DB::beginTransaction();

            // Update jadwal
            $jadwal->update([
                'sop_id' => $validated['sop_id'] ?? null,
                'tanggal' => $validated['tanggal'],
                'nama_acara' => $validated['nama_acara'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'waktu_mulai' => $validated['waktu_mulai'] ?? null,
                'waktu_selesai' => $validated['waktu_selesai'] ?? null,
                'lokasi' => $validated['lokasi'] ?? null,
                'status' => $validated['status'] ?? 'scheduled',
            ]);

            // Get existing peserta untuk referensi bukti lama
            $existingPeserta = $jadwal->peserta()->get()->keyBy('id');

            // Simpan path bukti lama untuk di-preserve
            $oldBuktiPaths = [];
            foreach ($existingPeserta as $peserta) {
                if ($peserta->bukti) {
                    $oldBuktiPaths[$peserta->id] = $peserta->bukti;
                }
            }

            // Delete existing peserta records (tapi jangan hapus file dulu)
            $jadwal->peserta()->delete();

            // Recreate peserta
            if (isset($validated['peserta']) && is_array($validated['peserta'])) {
                $usedBuktiPaths = [];
                
                foreach ($validated['peserta'] as $index => $pesertaData) {
                    // Skip jika nama karyawan kosong
                    if (empty($pesertaData['nama_karyawan'])) {
                        continue;
                    }
                    
                    $buktiPath = null;
                    
                    // 1. Cek apakah ada file baru yang diupload
                    if ($request->hasFile("peserta.{$index}.bukti")) {
                        $file = $request->file("peserta.{$index}.bukti");
                        $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                        $buktiPath = $file->storeAs('bukti-peserta', $filename, 'public');
                    }
                    // 2. Cek apakah ada flag remove_bukti
                    elseif (isset($pesertaData['remove_bukti']) && $pesertaData['remove_bukti']) {
                        // Bukti dihapus, set null
                        $buktiPath = null;
                    }
                    // 3. Preserve bukti lama jika ada
                    elseif (isset($pesertaData['id']) && isset($oldBuktiPaths[$pesertaData['id']])) {
                        $buktiPath = $oldBuktiPaths[$pesertaData['id']];
                        $usedBuktiPaths[] = $buktiPath;
                    }
                    
                    PesertaJadwal::create([
                        'jadwal_karyawan_id' => $jadwal->id,
                        'nama_karyawan' => $pesertaData['nama_karyawan'],
                        'catatan' => $pesertaData['catatan'] ?? null,
                        'nilai' => $pesertaData['nilai'] ?? null,
                        'status_kehadiran' => $pesertaData['status_kehadiran'] ?? 'hadir',
                        'bukti' => $buktiPath,
                    ]);
                }
                
                // Hapus file bukti lama yang tidak digunakan lagi
                foreach ($oldBuktiPaths as $pesertaId => $oldPath) {
                    if (!in_array($oldPath, $usedBuktiPaths)) {
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                }
            } else {
                // Jika tidak ada peserta baru, hapus semua file bukti lama
                foreach ($oldBuktiPaths as $oldPath) {
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diperbarui!',
                'data' => $jadwal->load('peserta')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete jadwal (AJAX)
     */
    public function destroy($id)
    {
        try {
            $jadwal = JadwalKaryawan::with('peserta')->findOrFail($id);
            
            // Delete all bukti files
            foreach ($jadwal->peserta as $peserta) {
                if ($peserta->bukti && Storage::disk('public')->exists($peserta->bukti)) {
                    Storage::disk('public')->delete($peserta->bukti);
                }
            }
            
            $jadwal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail jadwal (AJAX)
     */
    public function show($id)
    {
        try {
            $jadwal = JadwalKaryawan::with('peserta')->findOrFail($id);
            return response()->json($jadwal);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Helper: Get status color
     */
    private function getStatusColor($status)
    {
        $colors = [
            'scheduled' => '#0d6efd',
            'ongoing' => '#ffc107',
            'completed' => '#198754',
            'cancelled' => '#dc3545',
        ];

        return $colors[$status] ?? '#6c757d';
    }
}