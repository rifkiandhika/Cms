<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\TrainingCategory;
use App\Models\TrainingMainCategory;
use App\Models\TrainingSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TrainingCategoryController extends Controller
{
    public function index()
    {
        $mainCategories = TrainingMainCategory::with([
            'subCategories.trainingItems.details',
            'subCategories.trainingItems.images',
            'subCategories.trainingItems.metadata'
        ])
        ->orderBy('order')
        ->get();

        return view('training_programs.index', compact('mainCategories'));
    }

    public function create()
    {
        return view('training_programs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'roman_number' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            
            'sub_categories' => 'required|array|min:1',
            'sub_categories.*.letter' => 'required|string|max:10',
            'sub_categories.*.name' => 'required|string|max:255',
            'sub_categories.*.order' => 'required|integer|min:0',
            
            'sub_categories.*.training_items' => 'required|array|min:1',
            'sub_categories.*.training_items.*.number' => 'required|string|max:10',
            'sub_categories.*.training_items.*.nama_pelatihan' => 'required|string',
            'sub_categories.*.training_items.*.peserta' => 'nullable|string',
            'sub_categories.*.training_items.*.instruktur' => 'nullable|string',
            'sub_categories.*.training_items.*.metode' => 'nullable|string',
            'sub_categories.*.training_items.*.jadwal' => 'nullable|string',
            'sub_categories.*.training_items.*.metode_penilaian' => 'nullable|string',
            'sub_categories.*.training_items.*.order' => 'required|integer|min:0',
            
            'sub_categories.*.training_items.*.details' => 'nullable|array',
            'sub_categories.*.training_items.*.details.*.letter' => 'required|string|max:10',
            'sub_categories.*.training_items.*.details.*.content' => 'required|string',
            'sub_categories.*.training_items.*.details.*.order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Create Main Category
            $mainCategory = TrainingMainCategory::create([
                'roman_number' => $validated['roman_number'],
                'name' => $validated['name'],
                'order' => $validated['order'],
            ]);

            // Create Sub Categories
            foreach ($validated['sub_categories'] as $subCatIndex => $subCatData) {
                $subCategory = $mainCategory->subCategories()->create([
                    'letter' => $subCatData['letter'],
                    'name' => $subCatData['name'],
                    'order' => $subCatData['order'],
                ]);

                // Create Training Items
                if (isset($subCatData['training_items'])) {
                    foreach ($subCatData['training_items'] as $itemIndex => $itemData) {
                        $trainingItem = $subCategory->trainingItems()->create([
                            'number' => $itemData['number'],
                            'nama_pelatihan' => $itemData['nama_pelatihan'],
                            'peserta' => $itemData['peserta'] ?? null,
                            'instruktur' => $itemData['instruktur'] ?? null,
                            'metode' => $itemData['metode'] ?? null,
                            'jadwal' => $itemData['jadwal'] ?? null,
                            'metode_penilaian' => $itemData['metode_penilaian'] ?? null,
                            'order' => $itemData['order'],
                        ]);

                        // Create Details (a, b, c)
                        if (isset($itemData['details'])) {
                            foreach ($itemData['details'] as $detailData) {
                                $trainingItem->details()->create([
                                    'letter' => $detailData['letter'],
                                    'content' => $detailData['content'],
                                    'order' => $detailData['order'],
                                ]);
                            }
                        }

                        // Upload Images
                        if ($request->hasFile("sub_categories.{$subCatIndex}.training_items.{$itemIndex}.images")) {
                            foreach ($request->file("sub_categories.{$subCatIndex}.training_items.{$itemIndex}.images") as $imgIndex => $image) {
                                $path = $image->store('training_images', 'public');
                                
                                $trainingItem->images()->create([
                                    'image_path' => $path,
                                    'caption' => $itemData['captions'][$imgIndex] ?? null,
                                    'order' => $imgIndex
                                ]);
                            }
                        }

                        // Create Metadata
                        if (isset($itemData['tanggal_mulai']) || isset($itemData['lokasi']) || isset($itemData['catatan'])) {
                            $trainingItem->metadata()->create([
                                'tanggal_mulai' => $itemData['tanggal_mulai'] ?? null,
                                'tanggal_selesai' => $itemData['tanggal_selesai'] ?? null,
                                'lokasi' => $itemData['lokasi'] ?? null,
                                'catatan' => $itemData['catatan'] ?? null,
                            ]);
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

    public function edit(TrainingMainCategory $trainingProgram)
    {
        $trainingProgram->load([
            'subCategories.trainingItems.details',
            'subCategories.trainingItems.images',
            'subCategories.trainingItems.metadata'
        ]);
        
        return view('training_programs.edit', compact('trainingProgram'));
    }

    public function update(Request $request, TrainingMainCategory $trainingProgram)
    {
        // Similar validation as store
        $validated = $request->validate([
            'roman_number' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            
            'sub_categories' => 'required|array|min:1',
            'sub_categories.*.letter' => 'required|string|max:10',
            'sub_categories.*.name' => 'required|string|max:255',
            'sub_categories.*.order' => 'required|integer|min:0',
            
            'sub_categories.*.training_items' => 'required|array|min:1',
            'sub_categories.*.training_items.*.number' => 'required|string|max:10',
            'sub_categories.*.training_items.*.nama_pelatihan' => 'required|string',
            'sub_categories.*.training_items.*.peserta' => 'nullable|string',
            'sub_categories.*.training_items.*.instruktur' => 'nullable|string',
            'sub_categories.*.training_items.*.metode' => 'nullable|string',
            'sub_categories.*.training_items.*.jadwal' => 'nullable|string',
            'sub_categories.*.training_items.*.metode_penilaian' => 'nullable|string',
            'sub_categories.*.training_items.*.order' => 'required|integer|min:0',
            
            'sub_categories.*.training_items.*.details' => 'nullable|array',
            'sub_categories.*.training_items.*.details.*.letter' => 'required|string|max:10',
            'sub_categories.*.training_items.*.details.*.content' => 'required|string',
            'sub_categories.*.training_items.*.details.*.order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update main category
            $trainingProgram->update([
                'roman_number' => $validated['roman_number'],
                'name' => $validated['name'],
                'order' => $validated['order'],
            ]);

            // Update sub categories, items, details...
            // (Similar logic to store but with update)

            DB::commit();
            return redirect()->route('training-programs.index')
                ->with('success', 'Program pelatihan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(TrainingMainCategory $trainingProgram)
    {
        DB::beginTransaction();
        try {
            // Delete all images
            foreach ($trainingProgram->subCategories as $subCat) {
                foreach ($subCat->trainingItems as $item) {
                    foreach ($item->images as $image) {
                        Storage::disk('public')->delete($image->image_path);
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
}
