<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\AttendanceForm;
use App\Models\AttendanceParticipant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceFormController extends Controller
{
   public function index()
    {
        $attendanceForms = AttendanceForm::withCount('participants')->latest()->get();
        return view('attendance-forms.index', compact('attendanceForms'));
    }

    public function create()
    {
        // Auto-load kolom custom dari form terakhir
        $latestForm = AttendanceForm::latest()->first();
        $defaultColumns = $latestForm ? $latestForm->getCustomColumnLabels() : [];

        return view('attendance-forms.create', compact('defaultColumns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'topik_pelatihan'    => 'required|string|max:255',
            'tanggal'            => 'nullable|date',
            'tempat'             => 'nullable|string|max:255',
            'instruktur'         => 'nullable|string|max:255',
            'catatan'            => 'nullable|string',
            'custom_columns'     => 'nullable|array',
            'custom_columns.*'   => 'nullable|string|max:100',
            'participants'       => 'required|array|min:1',
            'participants.*.nama_karyawan' => 'nullable|string|max:255',
        ]);

        // Bersihkan kolom custom dari nilai kosong
        $customColumns = collect($request->custom_columns ?? [])
            ->filter(fn($col) => !empty(trim($col)))
            ->values()
            ->toArray();

        // Simpan header form
        $form = AttendanceForm::create([
            'topik_pelatihan' => $request->topik_pelatihan,
            'tanggal'         => $request->tanggal,
            'tempat'          => $request->tempat,
            'instruktur'      => $request->instruktur,
            'catatan'         => $request->catatan,
            'custom_columns'  => count($customColumns) > 0 ? $customColumns : null,
        ]);

        // Simpan peserta
        $urutan = 1;
        foreach ($request->participants as $p) {
            // Skip baris yang nama_karyawan-nya kosong
            if (empty(trim($p['nama_karyawan'] ?? ''))) continue;

            // Ambil nilai custom per kolom [nilai_k1, nilai_k2, ...]
            $customValues = [];
            foreach ($customColumns as $i => $label) {
                $customValues[] = $p['custom'][$i] ?? '';
            }

            AttendanceParticipant::create([
                'attendance_form_id' => $form->id,
                'nama_karyawan'      => $p['nama_karyawan'],
                'jabatan'            => $p['jabatan'] ?? null,
                'lokasi_kerja'       => $p['lokasi_kerja'] ?? null,
                'urutan'             => $urutan++,
                'custom_values'      => count($customValues) > 0 ? $customValues : null,
            ]);
        }

        return redirect()->route('attendance-forms.show', $form->id)
            ->with('success', 'Daftar hadir berhasil disimpan.');
    }

    public function show(AttendanceForm $attendanceForm)
    {
        $attendanceForm->load(['participants' => fn($q) => $q->orderBy('urutan')]);
        return view('attendance-forms.show', compact('attendanceForm'));
    }

    public function edit(AttendanceForm $attendanceForm)
    {
        $attendanceForm->load(['participants' => fn($q) => $q->orderBy('urutan')]);
        return view('attendance-forms.edit', compact('attendanceForm'));
    }

    public function update(Request $request, AttendanceForm $attendanceForm)
    {
        $request->validate([
            'topik_pelatihan'    => 'required|string|max:255',
            'tanggal'            => 'nullable|date',
            'tempat'             => 'nullable|string|max:255',
            'instruktur'         => 'nullable|string|max:255',
            'catatan'            => 'nullable|string',
            'custom_columns'     => 'nullable|array',
            'custom_columns.*'   => 'nullable|string|max:100',
            'participants'       => 'required|array|min:1',
        ]);

        $customColumns = collect($request->custom_columns ?? [])
            ->filter(fn($col) => !empty(trim($col)))
            ->values()
            ->toArray();

        $attendanceForm->update([
            'topik_pelatihan' => $request->topik_pelatihan,
            'tanggal'         => $request->tanggal,
            'tempat'          => $request->tempat,
            'instruktur'      => $request->instruktur,
            'catatan'         => $request->catatan,
            'custom_columns'  => count($customColumns) > 0 ? $customColumns : null,
        ]);

        // Hapus semua peserta lama, ganti yang baru
        $attendanceForm->participants()->delete();

        $urutan = 1;
        foreach ($request->participants as $p) {
            if (empty(trim($p['nama_karyawan'] ?? ''))) continue;

            $customValues = [];
            foreach ($customColumns as $i => $label) {
                $customValues[] = $p['custom'][$i] ?? '';
            }

            AttendanceParticipant::create([
                'attendance_form_id' => $attendanceForm->id,
                'nama_karyawan'      => $p['nama_karyawan'],
                'jabatan'            => $p['jabatan'] ?? null,
                'lokasi_kerja'       => $p['lokasi_kerja'] ?? null,
                'urutan'             => $urutan++,
                'custom_values'      => count($customValues) > 0 ? $customValues : null,
            ]);
        }

        return redirect()->route('attendance-forms.show', $attendanceForm->id)
            ->with('success', 'Daftar hadir berhasil diperbarui.');
    }

    public function destroy(AttendanceForm $attendanceForm)
    {
        $attendanceForm->participants()->delete();
        $attendanceForm->delete();

        return redirect()->route('attendance-forms.index')
            ->with('success', 'Daftar hadir berhasil dihapus.');
    }

    public function exportPdf(AttendanceForm $attendanceForm)
    {
        $attendanceForm->load([
            'participants' => fn($q) => $q->orderBy('urutan')
        ]);

        $pdf = Pdf::loadView('attendance-forms.pdf', compact('attendanceForm'))
            ->setPaper('a4', 'landscape') 
            ->setOption([
                'dpi'                  => 120,
                'defaultFont'          => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
            ]);

        return $pdf->download('daftar-hadir.pdf');
    }
}
