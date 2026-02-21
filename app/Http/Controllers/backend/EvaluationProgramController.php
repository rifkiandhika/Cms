<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\EvaluationImage;
use App\Models\EvaluationParticipant;
use App\Models\EvaluationProgram;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EvaluationProgramController extends Controller
{
    public function index()
    {
        $programs = EvaluationProgram::withCount(['items', 'responses', 'participants'])
            ->latest()
            ->get();

        return view('evaluation_programs.index', compact('programs'));
    }

    public function create()
    {
        return view('evaluation_programs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                              => 'required|string|max:255',
            'materi_pelatihan'                   => 'required|string|max:255',
            'hari_tanggal'                       => 'nullable|date',
            'tempat_pelatihan'                   => 'nullable|string|max:255',
            'program_number'                     => 'required|string|unique:evaluation_programs,program_number',
            'status'                             => 'required|in:draft,active,archived',
            'description'                        => 'nullable|string',

            'items'                              => 'required|array|min:1',
            'items.*.item_label'                 => 'required|string|max:50',
            'items.*.item_content'               => 'required|string',
            'items.*.order'                      => 'required|integer|min:0',

            'participants'                       => 'nullable|array',
            'participants.*.nama_peserta'        => 'required|string|max:255',
            'participants.*.jabatan_lokasi_kerja'=> 'nullable|string|max:255',

            'images'                             => 'nullable|array',
            'images.*'                           => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'captions'                           => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $program = EvaluationProgram::create([
                'title'            => $validated['title'],
                'materi_pelatihan' => $validated['materi_pelatihan'],
                'hari_tanggal'     => $validated['hari_tanggal'] ?? null,
                'tempat_pelatihan' => $validated['tempat_pelatihan'] ?? null,
                'program_number'   => $validated['program_number'],
                'status'           => $validated['status'],
                'description'      => $validated['description'] ?? null,
            ]);

            // Create Items langsung (tanpa kategori)
            foreach ($validated['items'] as $itemData) {
                $program->items()->create([
                    'item_label'   => $itemData['item_label'],
                    'item_content' => $itemData['item_content'],
                    'order'        => $itemData['order'],
                ]);
            }

            // Create Participants
            if (!empty($validated['participants'])) {
                foreach ($validated['participants'] as $index => $participantData) {
                    if (!empty(trim($participantData['nama_peserta']))) {
                        $program->participants()->create([
                            'nama_peserta'         => $participantData['nama_peserta'],
                            'jabatan_lokasi_kerja' => $participantData['jabatan_lokasi_kerja'] ?? null,
                            'order'                => $index,
                        ]);
                    }
                }
            }

            // Upload Images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imgIndex => $image) {
                    $path = $image->store('evaluation_images', 'public');
                    $program->images()->create([
                        'image_path' => $path,
                        'caption'    => $request->captions[$imgIndex] ?? null,
                        'order'      => $imgIndex,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('evaluation-programs.index')
                ->with('success', 'Program evaluasi berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(EvaluationProgram $evaluationProgram)
    {
        $evaluationProgram->load(['items', 'participants', 'responses', 'images']);

        return view('evaluation_programs.show', compact('evaluationProgram'));
    }

    public function edit(EvaluationProgram $evaluationProgram)
    {
        $evaluationProgram->load(['items', 'participants', 'images']);

        return view('evaluation_programs.edit', compact('evaluationProgram'));
    }

    public function update(Request $request, EvaluationProgram $evaluationProgram)
    {
        $validated = $request->validate([
            'title'                              => 'required|string|max:255',
            'materi_pelatihan'                   => 'required|string|max:255',
            'hari_tanggal'                       => 'nullable|date',
            'tempat_pelatihan'                   => 'nullable|string|max:255',
            'program_number'                     => 'required|string|unique:evaluation_programs,program_number,' . $evaluationProgram->id,
            'status'                             => 'required|in:draft,active,archived',
            'description'                        => 'nullable|string',

            'items'                              => 'required|array|min:1',
            'items.*.item_label'                 => 'required|string|max:50',
            'items.*.item_content'               => 'required|string',
            'items.*.order'                      => 'required|integer|min:0',

            'participants'                       => 'nullable|array',
            'participants.*.nama_peserta'        => 'required|string|max:255',
            'participants.*.jabatan_lokasi_kerja'=> 'nullable|string|max:255',

            'images'                             => 'nullable|array',
            'images.*'                           => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'captions'                           => 'nullable|array',
            'remove_images'                      => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $evaluationProgram->update([
                'title'            => $validated['title'],
                'materi_pelatihan' => $validated['materi_pelatihan'],
                'hari_tanggal'     => $validated['hari_tanggal'] ?? null,
                'tempat_pelatihan' => $validated['tempat_pelatihan'] ?? null,
                'program_number'   => $validated['program_number'],
                'status'           => $validated['status'],
                'description'      => $validated['description'] ?? null,
            ]);

            // Hapus items lama & buat ulang
            $evaluationProgram->items()->delete();
            foreach ($validated['items'] as $itemData) {
                $evaluationProgram->items()->create([
                    'item_label'   => $itemData['item_label'],
                    'item_content' => $itemData['item_content'],
                    'order'        => $itemData['order'],
                ]);
            }

            // Hapus peserta lama & buat ulang
            $evaluationProgram->participants()->delete();
            if (!empty($validated['participants'])) {
                foreach ($validated['participants'] as $index => $participantData) {
                    if (!empty(trim($participantData['nama_peserta']))) {
                        $evaluationProgram->participants()->create([
                            'nama_peserta'         => $participantData['nama_peserta'],
                            'jabatan_lokasi_kerja' => $participantData['jabatan_lokasi_kerja'] ?? null,
                            'order'                => $index,
                        ]);
                    }
                }
            }

            // Hapus gambar yang dipilih
            if ($request->has('remove_images')) {
                foreach ($request->remove_images as $imageId) {
                    $image = EvaluationImage::find($imageId);
                    if ($image && $image->evaluation_program_id == $evaluationProgram->id) {
                        Storage::disk('public')->delete($image->image_path);
                        $image->delete();
                    }
                }
            }

            // Upload gambar baru
            if ($request->hasFile('images')) {
                $maxOrder = $evaluationProgram->images()->max('order') ?? -1;
                foreach ($request->file('images') as $imgIndex => $image) {
                    $path = $image->store('evaluation_images', 'public');
                    $evaluationProgram->images()->create([
                        'image_path' => $path,
                        'caption'    => $request->captions[$imgIndex] ?? null,
                        'order'      => $maxOrder + $imgIndex + 1,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('evaluation-programs.index')
                ->with('success', 'Program evaluasi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(EvaluationProgram $evaluationProgram)
    {
        DB::beginTransaction();
        try {
            foreach ($evaluationProgram->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $evaluationProgram->delete();

            DB::commit();
            return redirect()->route('evaluation-programs.index')
                ->with('success', 'Program evaluasi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Peserta mengisi evaluasi
     */
    public function fillResponse(Request $request, EvaluationProgram $evaluationProgram)
    {
        $validated = $request->validate([
            'evaluation_participant_id'       => 'nullable|exists:evaluation_participants,id',
            'nama_peserta'                    => 'required|string|max:255',
            'jabatan_lokasi_kerja'            => 'nullable|string|max:255',
            'responses'                       => 'required|array',
            'mengetahui_atasan_nama'          => 'nullable|string|max:255',
            'mengetahui_atasan_tanggal'       => 'nullable|date',
            'mengetahui_personalia_nama'      => 'nullable|string|max:255',
            'mengetahui_personalia_tanggal'   => 'nullable|date',
        ]);

        try {
            $evaluationProgram->responses()->create([
                'evaluation_participant_id'     => $validated['evaluation_participant_id'] ?? null,
                'nama_peserta'                  => $validated['nama_peserta'],
                'jabatan_lokasi_kerja'          => $validated['jabatan_lokasi_kerja'] ?? null,
                'responses'                     => $validated['responses'],
                'mengetahui_atasan_nama'        => $validated['mengetahui_atasan_nama'] ?? null,
                'mengetahui_atasan_tanggal'     => $validated['mengetahui_atasan_tanggal'] ?? null,
                'mengetahui_personalia_nama'    => $validated['mengetahui_personalia_nama'] ?? null,
                'mengetahui_personalia_tanggal' => $validated['mengetahui_personalia_tanggal'] ?? null,
            ]);

            return back()->with('success', 'Evaluasi berhasil disubmit.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Generate PDF untuk satu peserta
     */
    public function generatePDF(EvaluationProgram $evaluationProgram)
    {
        // Load semua relasi yang dibutuhkan blade
        $evaluationProgram->load([
            'items'        => fn($q) => $q->orderBy('order'),
            'participants' => fn($q) => $q->orderBy('order'),
            'responses',   
        ]);

        $pdf = Pdf::loadView(
            'evaluation_programs.pdf',   
            compact('evaluationProgram')
        )
        ->setPaper('a4', 'landscape')     
        ->setOptions([
            'dpi' => 150,
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultMediaType' => 'print',
            'isFontSubsettingEnabled' => true,
        ]);

        $filename = 'evaluasi-' .
            Str::slug($evaluationProgram->program_number, '-') .
            '.pdf';

        return $pdf->download($filename);
        // Ganti ->download() dengan ->stream() jika ingin preview di browser
    }

    // /**
    //  * Generate PDF untuk semua peserta
    //  */
    // public function generateAllPDF(EvaluationProgram $evaluationProgram)
    // {
    //     $evaluationProgram->load(['items', 'participants', 'responses']);

    //     return view('evaluation_programs.pdf_all', compact('evaluationProgram'));
    // }

    /**
     * Export ke Excel
     */
    public function exportExcel(EvaluationProgram $evaluationProgram)
    {
        return back()->with('info', 'Fitur export Excel akan segera tersedia.');
    }
}