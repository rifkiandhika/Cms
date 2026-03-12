<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::withCount('images')->latest()->get();
        return view('gallery.index', compact('galleries'));
    }

    public function create()
    {
        return view('gallery.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            
            'sop_id' => 'nullable|exists:sops,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $gallery = Gallery::create([
            'sop_id' => $request->sop_id ?? null,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('gallery', $imageName, 'public');

                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $imagePath,
                    'image_name' => $image->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('gallery.index')
            ->with('success', 'Gallery berhasil ditambahkan!');
    }

    public function edit(Gallery $gallery)
    {
        $gallery->load('images');
        return view('gallery.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'sop_id' => 'nullable|exists:sops,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $gallery->update([
            'sop_id' => $request->sop_id ?? null,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('gallery', $imageName, 'public');

                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $imagePath,
                    'image_name' => $image->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('gallery.index')
            ->with('success', 'Gallery berhasil diupdate!');
    }

    public function destroy(Gallery $gallery)
    {
        // Hapus semua file gambar
        foreach ($gallery->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $gallery->delete();

        return redirect()->route('gallery.index')
            ->with('success', 'Gallery berhasil dihapus!');
    }

    public function deleteImage($imageId)
    {
        $image = GalleryImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function exportPdf(Gallery $gallery)
    {
        // Load images
        $gallery->load('images');
        
        // Ambil semua images
        $galleryImages = $gallery->images;
        
        // Hitung total halaman
        $totalPages = ceil($galleryImages->count() / 4);
        
        // Generate PDF
        $pdf = Pdf::loadView('gallery.pdf', compact('gallery', 'galleryImages', 'totalPages'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true, // Untuk mengizinkan gambar dari storage
                'isFontSubsettingEnabled' => true,
                'isJavascriptEnabled' => false,
                'isPhpEnabled' => true,
                'chroot' => [public_path(), storage_path()] // Izinkan akses ke public dan storage
            ]);
        
        $filename = 'gallery-' . Str::slug($gallery->judul) . '-' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
        // return $pdf->stream($filename); // Untuk preview
    }

    /**
     * Alternative method dengan chunking manual
     */
    public function exportPdfChunked(Gallery $gallery)
    {
        $gallery->load('images');
        
        // Kelompokkan images per halaman (4 per halaman)
        $imageChunks = $gallery->images->chunk(4);
        $totalChunks = $imageChunks->count();
        
        $pdf = Pdf::loadView('gallery.pdf_chunked', [
            'gallery' => $gallery,
            'imageChunks' => $imageChunks,
            'totalChunks' => $totalChunks
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'dpi' => 150,
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        
        return $pdf->stream('gallery-' . Str::slug($gallery->judul) . '.pdf');
    }

    /**
     * Extract EXIF data dari gambar
     */
    private function extractExifData($imagePath)
    {
        try {
            $fullPath = storage_path('app/public/' . $imagePath);
            if (file_exists($fullPath)) {
                $exif = @exif_read_data($fullPath);
                return $exif;
            }
        } catch (\Exception $e) {
            // Log error jika perlu
        }
        return null;
    }
}
