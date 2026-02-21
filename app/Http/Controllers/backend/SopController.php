<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\AttendanceForm;
use App\Models\EvaluationProgram;
use App\Models\Gallery;
use App\Models\KontrolGudang;
use App\Models\PengendalianHama;
use App\Models\Sop;
use App\Models\SopApproval;
use App\Models\SopSection;
use App\Models\SopSectionItem;
use App\Models\TrainingCategory;
use App\Models\TrainingMainCategory;
use App\Models\TrainingProgram;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class SopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sops = Sop::withCount('sections')->latest()->get();
        $trainingPrograms = TrainingProgram::with([
            'mainCategories.subCategories.trainingItems.details',
            'mainCategories.subCategories.trainingItems.images',
            'mainCategories.subCategories.trainingItems.metadata'
        ])
        ->latest()
        ->get();
        $programs = EvaluationProgram::with(['items', 'responses'])
            ->latest()
            ->get();
        $attendanceForms = AttendanceForm::withCount('participants')
            ->latest()
            ->get();
    
        $galleries = Gallery::withCount('images')  
            ->latest()
            ->get();
        $kontrolGudang = KontrolGudang::with('catatanSuhu')
            ->latest()
            ->get();
        $pengendalianHamaList = PengendalianHama::withCount('details')
            ->with('gambar')
            ->latest()
            ->get();
        return view('sops.index', compact('sops', 'trainingPrograms', 'programs', 'galleries', 'kontrolGudang', 'pengendalianHamaList', 'attendanceForms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sops.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sop' => 'required|string|max:255',
            'no_sop' => 'required|string|max:255|unique:sops,no_sop',
            'tanggal_dibuat' => 'required|date',
            'tanggal_efektif' => 'required|date',
            'revisi' => 'nullable|string|max:255',
            'judul_header' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,active,archived',
            
            // Sections
            'sections.*.section_code' => 'required|string',
            'sections.*.section_title' => 'required|string',
            'sections.*.items.*.content' => 'required|string',
            'sections.*.items.*.level' => 'nullable|integer',
            
            // Approvals
            'approvals.*.keterangan' => 'required|string',
            'approvals.*.nama' => 'nullable|string',
            'approvals.*.jabatan' => 'nullable|string',
            'approvals.*.tanda_tangan' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Upload logo jika ada
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('sop-logos', 'public');
            }

            // Create SOP
            $sop = Sop::create([
                'nama_sop' => $validated['nama_sop'],
                'no_sop' => $validated['no_sop'],
                'tanggal_dibuat' => $validated['tanggal_dibuat'],
                'tanggal_efektif' => $validated['tanggal_efektif'],
                'revisi' => $validated['revisi'] ?? '00',
                'logo_path' => $logoPath,
                'judul_header' => $validated['judul_header'] ?? 'PROSEDUR TETAP (PROTAP) PELATIHAN KARYAWAN',
                'status' => $validated['status'],
            ]);

            // Create Sections & Items
            if (isset($request->sections)) {
                foreach ($request->sections as $sectionIndex => $sectionData) {
                    $section = SopSection::create([
                        'sop_id' => $sop->id,
                        'section_code' => $sectionData['section_code'],
                        'section_title' => $sectionData['section_title'],
                        'order' => $sectionIndex + 1,
                    ]);

                    // Create Items
                    if (isset($sectionData['items'])) {
                        foreach ($sectionData['items'] as $itemIndex => $itemData) {
                            SopSectionItem::create([
                                'sop_section_id' => $section->id,
                                'content' => $itemData['content'],
                                'order' => $itemIndex + 1,
                                'level' => $itemData['level'] ?? 1,
                                'parent_item_id' => $itemData['parent_item_id'] ?? null,
                            ]);
                        }
                    }
                }
            }

            // Create Approvals
            if (isset($request->approvals)) {
                foreach ($request->approvals as $approvalIndex => $approvalData) {
                    SopApproval::create([
                        'sop_id' => $sop->id,
                        'keterangan' => $approvalData['keterangan'],
                        'nama' => $approvalData['nama'] ?? null,
                        'jabatan' => $approvalData['jabatan'] ?? null,
                        'tanda_tangan' => $approvalData['tanda_tangan'] ?? null,
                        'order' => $approvalIndex + 1,
                    ]);
                }
            }

            DB::commit();

            Alert::success('success', 'SOP berhasil ditambahkan!');
            return redirect()->route('sops.index');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Hapus logo jika upload gagal
            if (isset($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan SOP: ' . $e->getMessage());
        }
    }

    public function previewEdit($id)
    {
        $sop = Sop::with(['sections.items', 'approvals'])->findOrFail($id);
        return view('sops.preview-final', compact('sop'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Sop $sop)
    {
        $sop->load(['sections.items', 'approvals']);
        return view('sops.show', compact('sop'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sop $sop)
    {
        $sop->load(['sections.items', 'approvals']);
        return view('sops.edit', compact('sop'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sop $sop)
    {
        $validated = $request->validate([
            'nama_sop' => 'required|string|max:255',
            'no_sop' => 'required|string|max:255|unique:sops,no_sop,' . $sop->id,
            'tanggal_dibuat' => 'required|date',
            'tanggal_efektif' => 'required|date',
            'revisi' => 'nullable|string|max:255',
            'judul_header' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,active,archived',
            
            // Sections
            'sections.*.section_code' => 'required|string',
            'sections.*.section_title' => 'required|string',
            'sections.*.items.*.content' => 'required|string',
            'sections.*.items.*.level' => 'nullable|integer',
            
            // Approvals
            'approvals.*.keterangan' => 'required|string',
            'approvals.*.nama' => 'nullable|string',
            'approvals.*.jabatan' => 'nullable|string',
            'approvals.*.tanda_tangan' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Upload logo baru jika ada
            $logoPath = $sop->logo_path;
            if ($request->hasFile('logo')) {
                // Hapus logo lama
                if ($sop->logo_path) {
                    Storage::disk('public')->delete($sop->logo_path);
                }
                $logoPath = $request->file('logo')->store('sop-logos', 'public');
            }

            // Update SOP
            $sop->update([
                'nama_sop' => $validated['nama_sop'],
                'no_sop' => $validated['no_sop'],
                'tanggal_dibuat' => $validated['tanggal_dibuat'],
                'tanggal_efektif' => $validated['tanggal_efektif'],
                'revisi' => $validated['revisi'] ?? '00',
                'logo_path' => $logoPath,
                'judul_header' => $validated['judul_header'] ?? 'PROSEDUR TETAP (PROTAP) PELATIHAN KARYAWAN',
                'status' => $validated['status'],
            ]);

            // Delete existing sections & items
            $sop->sections()->delete();

            // Recreate Sections & Items
            if (isset($request->sections)) {
                foreach ($request->sections as $sectionIndex => $sectionData) {
                    $section = SopSection::create([
                        'sop_id' => $sop->id,
                        'section_code' => $sectionData['section_code'],
                        'section_title' => $sectionData['section_title'],
                        'order' => $sectionIndex + 1,
                    ]);

                    if (isset($sectionData['items'])) {
                        foreach ($sectionData['items'] as $itemIndex => $itemData) {
                            SopSectionItem::create([
                                'sop_section_id' => $section->id,
                                'content' => $itemData['content'],
                                'order' => $itemIndex + 1,
                                'level' => $itemData['level'] ?? 1,
                                'parent_item_id' => $itemData['parent_item_id'] ?? null,
                            ]);
                        }
                    }
                }
            }

            // Delete & Recreate Approvals
            $sop->approvals()->delete();
            if (isset($request->approvals)) {
                foreach ($request->approvals as $approvalIndex => $approvalData) {
                    SopApproval::create([
                        'sop_id' => $sop->id,
                        'keterangan' => $approvalData['keterangan'],
                        'nama' => $approvalData['nama'] ?? null,
                        'jabatan' => $approvalData['jabatan'] ?? null,
                        'tanda_tangan' => $approvalData['tanda_tangan'] ?? null,
                        'order' => $approvalIndex + 1,
                    ]);
                }
            }

            DB::commit();

            Alert::info('success', 'SOP berhasil diperbarui!');
            return redirect()->route('sops.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui SOP: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sop $sop)
    {
        try {
            // Hapus logo jika ada
            if ($sop->logo_path) {
                Storage::disk('public')->delete($sop->logo_path);
            }
            
            $sop->delete();
            
            return redirect()->route('sops.index')->with('success', 'SOP berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus SOP: ' . $e->getMessage());
        }
    }

    /**
     * Update header via AJAX
     */
    public function updateHeader(Request $request, Sop $sop)
    {
        $validated = $request->validate([
            'nama_sop' => 'required|string|max:255',
            'no_sop' => 'required|string|max:255|unique:sops,no_sop,' . $sop->id,
            'tanggal_dibuat' => 'required|date',
            'tanggal_efektif' => 'required|date',
            'revisi' => 'nullable|string|max:255',
            'judul_header' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,active,archived',
        ]);

        try {
            // Upload logo baru jika ada
            $logoPath = $sop->logo_path;
            if ($request->hasFile('logo')) {
                if ($sop->logo_path) {
                    Storage::disk('public')->delete($sop->logo_path);
                }
                $logoPath = $request->file('logo')->store('sop-logos', 'public');
            }

            $sop->update([
                'nama_sop' => $validated['nama_sop'],
                'no_sop' => $validated['no_sop'],
                'tanggal_dibuat' => $validated['tanggal_dibuat'],
                'tanggal_efektif' => $validated['tanggal_efektif'],
                'revisi' => $validated['revisi'] ?? '00',
                'logo_path' => $logoPath,
                'judul_header' => $validated['judul_header'] ?? 'PROSEDUR TETAP (PROTAP) PELATIHAN KARYAWAN',
                'status' => $validated['status'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Header berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui header: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update section via AJAX
     */
    public function updateSection(Request $request, Sop $sop, SopSection $section)
    {
        $validated = $request->validate([
            'section_code' => 'required|string',
            'section_title' => 'required|string',
            'items' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Update section
            $section->update([
                'section_code' => $validated['section_code'],
                'section_title' => $validated['section_title'],
            ]);

            // Update items
            if (isset($validated['items'])) {
                foreach ($validated['items'] as $itemId => $content) {
                    if (strpos($itemId, 'new_') === 0) {
                        // New item
                        $order = $section->items()->max('order') + 1;
                        SopSectionItem::create([
                            'sop_section_id' => $section->id,
                            'content' => $content,
                            'order' => $order,
                            'level' => 1,
                        ]);
                    } else {
                        // Update existing item
                        SopSectionItem::where('id', $itemId)->update(['content' => $content]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Section berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui section: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update approval via AJAX
     */
    public function updateApproval(Request $request, Sop $sop, SopApproval $approval)
    {
        $validated = $request->validate([
            'keterangan' => 'required|string',
            'nama' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'tanda_tangan' => 'nullable|date',
        ]);

        try {
            $approval->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Approval berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui approval: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete item via AJAX
     */
    public function deleteItem(Sop $sop, SopSectionItem $item)
    {
        try {
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete section via AJAX
     */
    public function deleteSection(Sop $sop, SopSection $section)
    {
        try {
            $section->delete();

            return response()->json([
                'success' => true,
                'message' => 'Section berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus section: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete approval via AJAX
     */
    public function deleteApproval(Sop $sop, SopApproval $approval)
    {
        try {
            $approval->delete();

            return response()->json([
                'success' => true,
                'message' => 'Approval berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus approval: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add new section via AJAX
     */
    public function addSection(Request $request, Sop $sop)
    {
        $validated = $request->validate([
            'section_code' => 'required|string',
            'section_title' => 'required|string',
        ]);

        try {
            $order = $sop->sections()->max('order') + 1;
            
            $section = SopSection::create([
                'sop_id' => $sop->id,
                'section_code' => $validated['section_code'],
                'section_title' => $validated['section_title'],
                'order' => $order,
            ]);

            // Add default item
            SopSectionItem::create([
                'sop_section_id' => $section->id,
                'content' => '',
                'order' => 1,
                'level' => 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Section berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan section: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add new approval via AJAX
     */
    public function addApproval(Request $request, Sop $sop)
    {
        $validated = $request->validate([
            'keterangan' => 'required|string',
            'nama' => 'nullable|string',
            'jabatan' => 'nullable|string',
        ]);

        try {
            $order = $sop->approvals()->max('order') + 1;
            
            SopApproval::create([
                'sop_id' => $sop->id,
                'keterangan' => $validated['keterangan'],
                'nama' => $validated['nama'] ?? null,
                'jabatan' => $validated['jabatan'] ?? null,
                'order' => $order,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Approval berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan approval: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get preview HTML via AJAX
     */
    public function getPreview(Sop $sop)
    {
        $sop->load(['sections.items', 'approvals']);
        return view('sops.partials.pdf-template', compact('sop'))->render();
    }

    /**
     * Download PDF
     */
    public function downloadPdf(Sop $sop)
    {
        $sop->load(['sections.items', 'approvals']);
        
        $pdf = Pdf::loadView('sops.partials.pdf-template', compact('sop'));
        
        return $pdf->download('SOP-' . $sop->no_sop . '.pdf');
    }
}
