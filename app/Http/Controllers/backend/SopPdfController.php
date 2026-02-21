<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Sop;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SopPdfController extends Controller
{
   /**
     * Generate and download PDF with custom font settings
     * IMPORTANT: This uses the SAME view as preview for consistency
     */
    public function downloadPdf(Request $request, $id)
    {
        $sop = Sop::with(['sections.items', 'approvals'])->findOrFail($id);
        
        // Get font settings from request or use defaults
        $fontSettings = $this->getFontSettings($request);
        
        // CRITICAL: Use the UNIVERSAL template (same as preview)
        $pdf = Pdf::loadView('sops.partials.export-wrapper', [
            'sop' => $sop,
            'fontSettings' => $fontSettings
        ]);
        
        // Set paper to A4 portrait
        $pdf->setPaper('A4', 'portrait');
        
        // DomPDF Options - Optimized for consistency
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false, // Disable remote to avoid issues
            'defaultFont' => $this->extractFontFamily($fontSettings['fontFamily']),
            'dpi' => 96,
            'isFontSubsettingEnabled' => true,
            'isPhpEnabled' => false,
            'chroot' => public_path(),
            'enable_css_float' => false, // Disable for better table rendering
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
        ]);
        
        $filename = 'SOP_' . str_replace(' ', '_', $sop->nama_sop) . '_' . date('Ymd') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Preview PDF in browser (stream)
     */
    public function previewPdf(Request $request, $id)
    {
        $sop = Sop::with(['sections.items', 'approvals'])->findOrFail($id);
        $fontSettings = $this->getFontSettings($request);
        
        // Use the same wrapper as download
        $pdf = Pdf::loadView('sops.partials.export-wrapper', [
            'sop' => $sop,
            'fontSettings' => $fontSettings
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont' => $this->extractFontFamily($fontSettings['fontFamily']),
            'dpi' => 96,
            'chroot' => public_path(),
        ]);
        
        return $pdf->stream();
    }
    
    /**
     * Get preview HTML for live preview (AJAX refresh)
     * Returns the SAME template used for PDF generation
     */
    public function getPreview($id)
    {
        $sop = Sop::with(['sections.items', 'approvals'])->findOrFail($id);
        
        // Get font settings from session/localStorage (passed via AJAX)
        $fontSettings = $this->getFontSettings(request());
        
        // Return the UNIVERSAL template (same as PDF export)
        return view('sops.partials.pdf-template', compact('sop', 'fontSettings'));
    }
    
    /**
     * Get font settings from request or return defaults
     */
    private function getFontSettings(Request $request)
    {
        $defaults = [
            'fontFamily' => 'Arial, sans-serif',
            'baseFontSize' => 11,
            'headerFontSize' => 10,
            'sectionTitleSize' => 11,
            'contentFontSize' => 11,
            'lineHeight' => 1.6,
            'marginTop' => 20,
            'marginBottom' => 20,
            'marginLeft' => 20,
            'marginRight' => 20,
        ];
        
        if ($request->has('font_settings')) {
            $customSettings = json_decode($request->font_settings, true);
            return array_merge($defaults, $customSettings ?? []);
        }
        
        return $defaults;
    }
    
    /**
     * Extract font family name from CSS font-family string
     * Map to DomPDF supported fonts
     */
    private function extractFontFamily($fontFamily)
    {
        $fonts = explode(',', $fontFamily);
        $firstFont = trim($fonts[0]);
        $firstFont = str_replace(["'", '"'], '', $firstFont);
        
        // DomPDF font mapping
        $fontMap = [
            'Times New Roman' => 'times',
            'Courier New' => 'courier',
            'Arial' => 'helvetica',
            'Helvetica' => 'helvetica',
            'Georgia' => 'times',
            'Verdana' => 'helvetica',
            'Trebuchet MS' => 'helvetica',
            'Calibri' => 'helvetica',
            'Tahoma' => 'helvetica',
        ];
        
        return $fontMap[$firstFont] ?? 'helvetica';
    }
}
