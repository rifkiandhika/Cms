<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount(['subCategories', 'questions'])
            ->orderBy('order')
            ->get();
        
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            
            // SubCategory validation
            'sub_categories' => 'nullable|array',
            'sub_categories.*.label' => 'required|string|max:50',
            'sub_categories.*.name' => 'required|string|max:255',
            'sub_categories.*.order' => 'required|integer|min:0',
            
            // Question validation
            'sub_categories.*.questions' => 'nullable|array',
            'sub_categories.*.questions.*.number' => 'required|string|max:50',
            'sub_categories.*.questions.*.question' => 'required|string',
            'sub_categories.*.questions.*.order' => 'required|integer|min:0',
        ], [
            'number.required' => 'Nomor kategori harus diisi',
            'name.required' => 'Nama kategori harus diisi',
            'order.required' => 'Urutan harus diisi',
            'sub_categories.*.label.required' => 'Label sub kategori harus diisi',
            'sub_categories.*.name.required' => 'Nama sub kategori harus diisi',
            'sub_categories.*.questions.*.number.required' => 'Nomor pertanyaan harus diisi',
            'sub_categories.*.questions.*.question.required' => 'Pertanyaan harus diisi',
        ]);

        DB::beginTransaction();
        try {
            // Create Category
            $category = Category::create([
                'number' => $validated['number'],
                'name' => $validated['name'],
                'order' => $validated['order'],
            ]);

            // Create SubCategories and Questions
            if (isset($validated['sub_categories'])) {
                foreach ($validated['sub_categories'] as $subCatData) {
                    $subCategory = $category->subCategories()->create([
                        'label' => $subCatData['label'],
                        'name' => $subCatData['name'],
                        'order' => $subCatData['order'],
                    ]);

                    // Create Questions for this SubCategory
                    if (isset($subCatData['questions'])) {
                        foreach ($subCatData['questions'] as $questionData) {
                            $subCategory->questions()->create([
                                'number' => $questionData['number'],
                                'question' => $questionData['question'],
                                'order' => $questionData['order'],
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('categories.index')
                ->with('success', 'Kategori beserta sub kategori dan pertanyaan berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['subCategories.questions']);
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $category->load(['subCategories.questions']);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            
            // SubCategory validation
            'sub_categories' => 'nullable|array',
            'sub_categories.*.id' => 'nullable|exists:sub_categories,id',
            'sub_categories.*.label' => 'required|string|max:50',
            'sub_categories.*.name' => 'required|string|max:255',
            'sub_categories.*.order' => 'required|integer|min:0',
            
            // Question validation
            'sub_categories.*.questions' => 'nullable|array',
            'sub_categories.*.questions.*.id' => 'nullable|exists:questions,id',
            'sub_categories.*.questions.*.number' => 'required|string|max:50',
            'sub_categories.*.questions.*.question' => 'required|string',
            'sub_categories.*.questions.*.order' => 'required|integer|min:0',
        ], [
            'number.required' => 'Nomor kategori harus diisi',
            'name.required' => 'Nama kategori harus diisi',
            'order.required' => 'Urutan harus diisi',
            'sub_categories.*.label.required' => 'Label sub kategori harus diisi',
            'sub_categories.*.name.required' => 'Nama sub kategori harus diisi',
            'sub_categories.*.questions.*.number.required' => 'Nomor pertanyaan harus diisi',
            'sub_categories.*.questions.*.question.required' => 'Pertanyaan harus diisi',
        ]);

        DB::beginTransaction();
        try {
            // Update Category
            $category->update([
                'number' => $validated['number'],
                'name' => $validated['name'],
                'order' => $validated['order'],
            ]);

            // Track existing IDs
            $existingSubCatIds = [];
            $existingQuestionIds = [];

            // Update or Create SubCategories and Questions
            if (isset($validated['sub_categories'])) {
                foreach ($validated['sub_categories'] as $subCatData) {
                    if (isset($subCatData['id'])) {
                        // Update existing SubCategory
                        $subCategory = SubCategory::findOrFail($subCatData['id']);
                        $subCategory->update([
                            'label' => $subCatData['label'],
                            'name' => $subCatData['name'],
                            'order' => $subCatData['order'],
                        ]);
                        $existingSubCatIds[] = $subCategory->id;
                    } else {
                        // Create new SubCategory
                        $subCategory = $category->subCategories()->create([
                            'label' => $subCatData['label'],
                            'name' => $subCatData['name'],
                            'order' => $subCatData['order'],
                        ]);
                        $existingSubCatIds[] = $subCategory->id;
                    }

                    // Handle Questions
                    if (isset($subCatData['questions'])) {
                        foreach ($subCatData['questions'] as $questionData) {
                            if (isset($questionData['id'])) {
                                // Update existing Question
                                $question = Question::findOrFail($questionData['id']);
                                $question->update([
                                    'number' => $questionData['number'],
                                    'question' => $questionData['question'],
                                    'order' => $questionData['order'],
                                ]);
                                $existingQuestionIds[] = $question->id;
                            } else {
                                // Create new Question
                                $question = $subCategory->questions()->create([
                                    'number' => $questionData['number'],
                                    'question' => $questionData['question'],
                                    'order' => $questionData['order'],
                                ]);
                                $existingQuestionIds[] = $question->id;
                            }
                        }
                    }
                }
            }

            // Delete removed SubCategories (and cascade delete Questions)
            $category->subCategories()->whereNotIn('id', $existingSubCatIds)->delete();

            // Delete removed Questions
            if (!empty($existingSubCatIds)) {
                Question::whereHas('subCategory', function($query) use ($category) {
                    $query->where('category_id', $category->id);
                })->whereNotIn('id', $existingQuestionIds)->delete();
            }

            DB::commit();
            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')
                ->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}
