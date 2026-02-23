<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\TrainingProgram;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TrainingProgramController extends Controller
{
    public function index()
    {
        $programs = TrainingProgram::withCount('mainCategories')->latest()->get();
        return view('training_programs.index', compact('programs'));
    }

    public function create()
    {
        return view('training_programs.create');
    }

    public function show(TrainingProgram $trainingProgram)
    {
        $trainingProgram->load([
            'mainCategories' => fn ($q) => $q->orderBy('order'),
            'mainCategories.subCategories' => fn ($q) => $q->orderBy('order'),
            'mainCategories.subCategories.trainingItems' => fn ($q) => $q->orderBy('order'),
            'mainCategories.subCategories.trainingItems.details' => fn ($q) => $q->orderBy('order'),
            'mainCategories.subCategories.trainingItems.images',
            'mainCategories.subCategories.trainingItems.metadata',
        ]);

        return view('training_programs.show', compact('trainingProgram'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'program_number' => 'required|string|unique:training_programs,program_number',
            'effective_date' => 'nullable|date',
            'revision' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
            'description' => 'nullable|string',
            
            'main_categories' => 'required|array|min:1',
            'main_categories.*.roman_number' => 'required|string|max:10',
            'main_categories.*.name' => 'required|string|max:255',
            'main_categories.*.order' => 'required|integer|min:0',
            
            'main_categories.*.sub_categories' => 'required|array|min:1',
            'main_categories.*.sub_categories.*.letter' => 'required|string|max:10',
            'main_categories.*.sub_categories.*.name' => 'required|string|max:255',
            'main_categories.*.sub_categories.*.order' => 'required|integer|min:0',
            
            'main_categories.*.sub_categories.*.training_items' => 'required|array|min:1',
            'main_categories.*.sub_categories.*.training_items.*.number' => 'required|string|max:10',
            'main_categories.*.sub_categories.*.training_items.*.nama_pelatihan' => 'required|string',
            'main_categories.*.sub_categories.*.training_items.*.peserta' => 'nullable|string',
            'main_categories.*.sub_categories.*.training_items.*.instruktur' => 'nullable|string',
            'main_categories.*.sub_categories.*.training_items.*.metode' => 'nullable|string',
            'main_categories.*.sub_categories.*.training_items.*.jadwal' => 'nullable|string',
            'main_categories.*.sub_categories.*.training_items.*.metode_penilaian' => 'nullable|string',
            'main_categories.*.sub_categories.*.training_items.*.order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Create Training Program
            $program = TrainingProgram::create([
                'title' => $validated['title'],
                'program_number' => $validated['program_number'],
                'effective_date' => $validated['effective_date'] ?? null,
                'revision' => $validated['revision'] ?? 'Rev. 00',
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
            ]);

            // Create Main Categories
            foreach ($validated['main_categories'] as $mcIndex => $mcData) {
                $mainCategory = $program->mainCategories()->create([
                    'roman_number' => $mcData['roman_number'],
                    'name' => $mcData['name'],
                    'order' => $mcData['order'],
                ]);

                // Create Sub Categories
                if (isset($mcData['sub_categories'])) {
                    foreach ($mcData['sub_categories'] as $scIndex => $scData) {
                        $subCategory = $mainCategory->subCategories()->create([
                            'letter' => $scData['letter'],
                            'name' => $scData['name'],
                            'order' => $scData['order'],
                        ]);

                        // Create Training Items
                        if (isset($scData['training_items'])) {
                            foreach ($scData['training_items'] as $tiIndex => $tiData) {
                                $trainingItem = $subCategory->trainingItems()->create([
                                    'number' => $tiData['number'],
                                    'nama_pelatihan' => $tiData['nama_pelatihan'],
                                    'peserta' => $tiData['peserta'] ?? null,
                                    'instruktur' => $tiData['instruktur'] ?? null,
                                    'metode' => $tiData['metode'] ?? null,
                                    'jadwal' => $tiData['jadwal'] ?? null,
                                    'metode_penilaian' => $tiData['metode_penilaian'] ?? null,
                                    'order' => $tiData['order'],
                                ]);

                                // Create Details (a, b, c)
                                if (isset($tiData['details'])) {
                                    foreach ($tiData['details'] as $detailData) {
                                        $trainingItem->details()->create([
                                            'letter' => $detailData['letter'],
                                            'content' => $detailData['content'],
                                            'order' => $detailData['order'],
                                        ]);
                                    }
                                }

                                // Upload Images
                                if ($request->hasFile("main_categories.{$mcIndex}.sub_categories.{$scIndex}.training_items.{$tiIndex}.images")) {
                                    foreach ($request->file("main_categories.{$mcIndex}.sub_categories.{$scIndex}.training_items.{$tiIndex}.images") as $imgIndex => $image) {
                                        $path = $image->store('training_images', 'public');
                                        
                                        $trainingItem->images()->create([
                                            'image_path' => $path,
                                            'caption' => $tiData['captions'][$imgIndex] ?? null,
                                            'order' => $imgIndex
                                        ]);
                                    }
                                }

                                // Create Metadata
                                if (isset($tiData['tanggal_mulai']) || isset($tiData['lokasi']) || isset($tiData['catatan'])) {
                                    $trainingItem->metadata()->create([
                                        'tanggal_mulai' => $tiData['tanggal_mulai'] ?? null,
                                        'tanggal_selesai' => $tiData['tanggal_selesai'] ?? null,
                                        'lokasi' => $tiData['lokasi'] ?? null,
                                        'catatan' => $tiData['catatan'] ?? null,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('training-programs.index')
                ->with('success', 'Program pelatihan berhasil ditambahkan');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(TrainingProgram $trainingProgram)
    {
        $trainingProgram->load([
            'mainCategories.subCategories.trainingItems.details',
            'mainCategories.subCategories.trainingItems.images',
            'mainCategories.subCategories.trainingItems.metadata'
        ]);
        
        return view('training_programs.edit', compact('trainingProgram'));
    }

    public function update(Request $request, TrainingProgram $trainingProgram)
    {
        // Similar to store but with update logic
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'program_number' => 'required|string|unique:training_programs,program_number,' . $trainingProgram->id,
            'effective_date' => 'nullable|date',
            'revision' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
            'description' => 'nullable|string',
            
            'main_categories' => 'required|array|min:1',
            'main_categories.*.roman_number' => 'required|string|max:10',
            'main_categories.*.name' => 'required|string|max:255',
            'main_categories.*.order' => 'required|integer|min:0',
            
            'main_categories.*.sub_categories' => 'required|array|min:1',
            'main_categories.*.sub_categories.*.letter' => 'required|string|max:10',
            'main_categories.*.sub_categories.*.name' => 'required|string|max:255',
            'main_categories.*.sub_categories.*.order' => 'required|integer|min:0',
            
            'main_categories.*.sub_categories.*.training_items' => 'required|array|min:1',
            'main_categories.*.sub_categories.*.training_items.*.number' => 'required|string|max:10',
            'main_categories.*.sub_categories.*.training_items.*.nama_pelatihan' => 'required|string',
            'main_categories.*.sub_categories.*.training_items.*.peserta' => 'nullable|string',
            'main_categories.*.sub_categories.*.training_items.*.instruktur' => 'nullable|string',
            'main_categories.*.sub_categories.*.training_items.*.metode' => 'nullable|string',
            'main_categories.*.sub_categories.*.training_items.*.jadwal' => 'nullable|string',
            'main_categories.*.sub_categories.*.training_items.*.metode_penilaian' => 'nullable|string',
            'main_categories.*.sub_categories.*.training_items.*.order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $trainingProgram->update([
                'title' => $validated['title'],
                'program_number' => $validated['program_number'],
                'effective_date' => $validated['effective_date'] ?? null,
                'revision' => $validated['revision'] ?? 'Rev. 00',
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
            ]);

            // Delete existing and recreate (simpler approach)
            // Or implement update logic for existing records

            DB::commit();
            return redirect()->route('training-programs.index')
                ->with('success', 'Program pelatihan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(TrainingProgram $trainingProgram)
    {
        DB::beginTransaction();
        try {
            // Delete all images
            foreach ($trainingProgram->mainCategories as $mainCat) {
                foreach ($mainCat->subCategories as $subCat) {
                    foreach ($subCat->trainingItems as $item) {
                        foreach ($item->images as $image) {
                            Storage::disk('public')->delete($image->image_path);
                        }
                    }
                }
            }
            
            $trainingProgram->delete();
            
            DB::commit();
            return redirect()->route('training-programs.index')
                ->with('success', 'Program pelatihan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function exportPdf(TrainingProgram $trainingProgram)
    {
        $trainingProgram->load([
            'mainCategories' => fn ($q) => $q->orderBy('order'),
            'mainCategories.subCategories' => fn ($q) => $q->orderBy('order'),
            'mainCategories.subCategories.trainingItems' => fn ($q) => $q->orderBy('order'),
            'mainCategories.subCategories.trainingItems.details' => fn ($q) => $q->orderBy('order'),
            'mainCategories.subCategories.trainingItems.images',
            'mainCategories.subCategories.trainingItems.metadata',
        ]);

        $pdf = Pdf::loadView('training_programs.pdf', compact('trainingProgram'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

        // WAJIB render dulu
        $pdf->render();

        // Ambil canvas
        // $canvas = $pdf->getDomPDF()->getCanvas();
        // $font = $pdf->getDomPDF()->getFontMetrics()->get_font("Arial", "normal");

        // // Posisi landscape A4 (sesuaikan jika perlu)
        // // $canvas->page_text(
        // //     720,  // X (kanan)
        // //     570,  // Y (bawah)
        // //     "Halaman {PAGE_NUM} dari {PAGE_COUNT}",
        // //     $font,
        // //     8,
        // //     [0, 0, 0]
        // // );

        $filename = 'program-pelatihan-' . str_replace(['/', '\\', ' '], '-', $trainingProgram->program_number) . '.pdf';

        return $pdf->download($filename);
    }
}
