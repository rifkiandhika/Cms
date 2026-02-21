<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\AuditResponse;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuditController extends Controller
{
    /**
     * Display listing of audits
     */
    public function index()
    {
        $audits = Audit::latest()->paginate(10);
        return view('audits.index', compact('audits'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('audits.create');
    }

    /**
     * Store new audit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'audit_date' => 'required|date',
            'auditor_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $audit = Audit::create($validated);

        // Create empty responses for all questions
        $questions = Question::all();
        foreach ($questions as $question) {
            AuditResponse::create([
                'audit_id' => $audit->id,
                'question_id' => $question->id,
            ]);
        }

        return redirect()->route('audits.show', $audit)
            ->with('success', 'Audit berhasil dibuat');
    }

    /**
     * Display audit form
     */
    public function show(Audit $audit)
    {
        $categories = Category::with([
            'subCategories.questions.auditResponses' => function ($query) use ($audit) {
                $query->where('audit_id', $audit->id);
            }
        ])->orderBy('order')->get();

        return view('audits.show', compact('audit', 'categories'));
    }

    /**
     * Update audit response
     */
    public function updateResponse(Request $request, Audit $audit, Question $question)
    {
        $validated = $request->validate([
            'response' => 'nullable|in:yes,no,na,partial',
            'evidence' => 'nullable|string',
            'notes' => 'nullable|string',
            'evidence_date' => 'nullable|date',
            'temperature' => 'nullable|numeric|min:-100|max:200', // suhu dalam range -100°C sampai 200°C
            'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB max
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max untuk gambar
        ]);

        $response = AuditResponse::where('audit_id', $audit->id)
            ->where('question_id', $question->id)
            ->first();

        if (!$response) {
            $response = new AuditResponse([
                'audit_id' => $audit->id,
                'question_id' => $question->id,
            ]);
        }

        $response->response = $validated['response'] ?? null;
        $response->evidence = $validated['evidence'] ?? null;
        $response->notes = $validated['notes'] ?? null;
        $response->evidence_date = $validated['evidence_date'] ?? null;
        $response->temperature = $validated['temperature'] ?? null;

        // Handle document upload
        if ($request->hasFile('document')) {
            // Hapus file lama jika ada
            if ($response->document_path && Storage::disk('public')->exists($response->document_path)) {
                Storage::disk('public')->delete($response->document_path);
            }
            $path = $request->file('document')->store('audit-documents', 'public');
            $response->document_path = $path;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($response->image_path && Storage::disk('public')->exists($response->image_path)) {
                Storage::disk('public')->delete($response->image_path);
            }
            $imagePath = $request->file('image')->store('audit-images', 'public');
            $response->image_path = $imagePath;
        }

        $response->save();

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan',
            'response' => $response,
        ]);
    }

    /**
     * Delete uploaded document
     */
    public function deleteDocument(Audit $audit, Question $question)
    {
        $response = AuditResponse::where('audit_id', $audit->id)
            ->where('question_id', $question->id)
            ->first();

        if ($response && $response->document_path) {
            if (Storage::disk('public')->exists($response->document_path)) {
                Storage::disk('public')->delete($response->document_path);
            }
            $response->document_path = null;
            $response->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil dihapus',
        ]);
    }

    /**
     * Delete uploaded image
     */
    public function deleteImage(Audit $audit, Question $question)
    {
        $response = AuditResponse::where('audit_id', $audit->id)
            ->where('question_id', $question->id)
            ->first();

        if ($response && $response->image_path) {
            if (Storage::disk('public')->exists($response->image_path)) {
                Storage::disk('public')->delete($response->image_path);
            }
            $response->image_path = null;
            $response->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Gambar berhasil dihapus',
        ]);
    }

    /**
     * Complete audit
     */
    public function complete(Audit $audit)
    {
        $audit->update(['status' => 'completed']);

        return redirect()->route('audits.report', $audit)
            ->with('success', 'Audit berhasil diselesaikan');
    }

    /**
     * Show audit report
     */
    public function report(Audit $audit)
    {
        $categories = Category::with([
            'subCategories.questions.auditResponses' => function ($query) use ($audit) {
                $query->where('audit_id', $audit->id);
            }
        ])->orderBy('order')->get();

        $summary = $audit->summary;

        return view('audits.report', compact('audit', 'categories', 'summary'));
    }

    /**
     * Delete audit
     */
    public function destroy(Audit $audit)
    {
        // Delete all related files
        foreach ($audit->responses as $response) {
            if ($response->document_path && Storage::disk('public')->exists($response->document_path)) {
                Storage::disk('public')->delete($response->document_path);
            }
            if ($response->image_path && Storage::disk('public')->exists($response->image_path)) {
                Storage::disk('public')->delete($response->image_path);
            }
        }

        $audit->delete();

        return redirect()->route('audits.index')
            ->with('success', 'Audit berhasil dihapus');
    }
}
