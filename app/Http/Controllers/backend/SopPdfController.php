<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\AttendanceForm;
use App\Models\EvaluationProgram;
use App\Models\Gallery;
use App\Models\KontrolGudang;
use App\Models\PengendalianHama;
use App\Models\Sop;
use App\Models\TrainingProgram;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SopPdfController extends Controller
{
    /**
     * Ambil daftar semua halaman SOP
     */
    private function getPages(Sop $sop): array
    {
        $pages = [];

        // Halaman 1: SOP Utama
        $pages[] = [
            'type'  => 'sop',
            'label' => 'SOP Utama',
            'id'    => $sop->id,
        ];

        // Training Programs
        foreach ($sop->trainingPrograms ?? [] as $item) {
            $pages[] = [
                'type'  => 'training',
                'label' => 'Pelatihan: ' . ($item->nama_program ?? $item->id),
                'id'    => $item->id,
            ];
        }

        // Attendance Forms
        foreach ($sop->attendanceForms ?? [] as $item) {
            $pages[] = [
                'type'  => 'attendance',
                'label' => 'Daftar Hadir: ' . ($item->topik_pelatihan ?? $item->id),
                'id'    => $item->id,
            ];
        }

        // Evaluation Programs
        foreach ($sop->evaluationPrograms ?? [] as $item) {
            $pages[] = [
                'type'  => 'evaluation',
                'label' => 'Evaluasi: ' . ($item->nama_program ?? $item->id),
                'id'    => $item->id,
            ];
        }

        // Galleries
        foreach ($sop->galleries ?? [] as $item) {
            $pages[] = [
                'type'  => 'gallery',
                'label' => 'Galeri: ' . ($item->nama ?? $item->id),
                'id'    => $item->id,
            ];
        }

        // Kontrol Gudang / Suhu
        foreach ($sop->kontrolGudang ?? [] as $item) {
            $pages[] = [
                'type'  => 'suhu',
                'label' => 'Suhu: ' . ($item->nama_gudang ?? $item->id),
                'id'    => $item->id,
            ];
        }

        // Pengendalian Hama
        foreach ($sop->pengendalianHama ?? [] as $item) {
            $pages[] = [
                'type'  => 'hama',
                'label' => 'Hama: ' . ($item->nama ?? $item->id),
                'id'    => $item->id,
            ];
        }

        return $pages;
    }

    /**
     * Render konten blade khusus untuk PDF download
     */
    private function renderPageContentForPdf(string $type, $dataId, Sop $sop): string
    {
        switch ($type) {

            case 'sop':
                return view('sops.partials.pdf-template', compact('sop'))->render();

            case 'training':
                $trainingProgram = TrainingProgram::with([
                    'mainCategories.subCategories.trainingItems.details',
                    'mainCategories.subCategories.trainingItems.images',
                    'mainCategories.subCategories.trainingItems.metadata',
                ])->findOrFail($dataId);
                return view('training_programs.pdf', compact('trainingProgram', 'sop'))->render();

            case 'attendance':
                $attendanceForm = AttendanceForm::with('participants')->findOrFail($dataId);
                return view('attendance-form.pdf', compact('attendanceForm', 'sop'))->render();

            case 'evaluation':
                $evaluationProgram = EvaluationProgram::with([
                    'items',
                    'responses',
                    'participants',
                ])->findOrFail($dataId);
                return view('evaluation_programs.pdf', compact('evaluationProgram', 'sop'))->render();

            case 'gallery':
                $gallery = Gallery::with('images')->findOrFail($dataId);
                $galleryImages = $gallery->images; 
                return view('gallery.pdf', compact('gallery', 'galleryImages', 'sop'))->render();

            case 'suhu':
                $kontrolGudang = KontrolGudang::with('catatanSuhu')->findOrFail($dataId);
                $catatanSuhu   = $kontrolGudang->catatanSuhu;
                $nama_gudang   = $kontrolGudang->nama_gudang ?? '-';
                $periode       = $kontrolGudang->periode     ?? '-';
                $tanggal_cetak = now()->format('d M Y');
                return view('catatan-suhu.pdf', compact(
                    'kontrolGudang', 'catatanSuhu', 'nama_gudang', 'periode', 'tanggal_cetak', 'sop'
                ))->render();

            case 'hama':
                $pengendalianHama = PengendalianHama::with(['details', 'gambar'])
                    ->findOrFail($dataId);
                $lokasi          = $pengendalianHama->lokasi          ?? '-';
                $bulan           = $pengendalianHama->bulan           ?? '-';
                $tahun           = $pengendalianHama->tahun           ?? now()->year;
                $details         = $pengendalianHama->details;
                $penanggungJawab = $pengendalianHama->penanggung_jawab ?? '-'; // ← tambah ini
                
                // Konversi gambar ke base64 jika ada
                $gambarBase64 = [];
                foreach ($pengendalianHama->gambar ?? [] as $gambar) {
                    $path = storage_path('app/public/' . $gambar->path);
                    if (file_exists($path)) {
                        $mime = mime_content_type($path);
                        $gambarBase64[] = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                    }
                }

                return view('pengendalian-hama.pdf', compact(
                    'pengendalianHama', 'lokasi', 'bulan', 'tahun', 'details',
                    'penanggungJawab', 'gambarBase64', 'sop'  // ← tambah keduanya
                ))->render();

            default:
                return '<p class="text-center text-muted py-5">Tipe halaman tidak dikenali.</p>';
        }
    }

    /**
     * Download semua halaman SOP dalam 1 PDF
     */
    public function downloadPdf(Request $request, Sop $sop)
    {
        $sop->load([
            'sections.items',
            'approvals',
            'trainingPrograms.mainCategories.subCategories.trainingItems.details',
            'trainingPrograms.mainCategories.subCategories.trainingItems.images',
            'trainingPrograms.mainCategories.subCategories.trainingItems.metadata',
            'evaluationPrograms.items',
            'evaluationPrograms.responses',
            'evaluationPrograms.participants',
            'attendanceForms.participants',
            'galleries.images',
            'kontrolGudang.catatanSuhu',
            'pengendalianHama.details',
            'pengendalianHama.gambar',
        ]);

        $fontSettings = $this->getFontSettings($request);
        $pages        = $this->getPages($sop);
        $allHtml      = '';

        foreach ($pages as $index => $page) {
            $contentHtml = $this->renderPageContentForPdf($page['type'], $page['id'], $sop);

            $pageHtml = view('sops.partials.page-wrapper', [
                'sop'         => $sop,
                'contentHtml' => $contentHtml,
                'showHeader'  => true,
                'showFooter'  => true,
                'pageType'    => $page['type'],
            ])->render();

            $allHtml .= $pageHtml;

            if ($index < count($pages) - 1) {
                $allHtml .= '<div style="page-break-after: always;"></div>';
            }
        }

        $fullHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body {
                    font-family: Arial, sans-serif;
                    font-size: ' . $fontSettings['baseFontSize'] . 'pt;
                    line-height: ' . $fontSettings['lineHeight'] . ';
                }
                table { border-collapse: collapse; width: 100%; }
                td, th { vertical-align: middle; }
                img { max-width: 100%; height: auto; }
                @page {
                    size: A4;
                    margin: ' . $fontSettings['marginTop'] . 'mm
                            ' . $fontSettings['marginRight'] . 'mm
                            ' . $fontSettings['marginBottom'] . 'mm
                            ' . $fontSettings['marginLeft'] . 'mm;
                }
            </style>
        </head>
        <body>' . $allHtml . '</body>
        </html>';

        $pdf = Pdf::loadHTML($fullHtml);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled'    => true,
            'isRemoteEnabled'         => false,
            'defaultFont'             => $this->extractFontFamily($fontSettings['fontFamily']),
            'dpi'                     => 96,
            'isFontSubsettingEnabled' => true,
            'chroot'                  => public_path(),
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

        $pdf = Pdf::loadView('sops.partials.export-wrapper', [
            'sop'          => $sop,
            'fontSettings' => $fontSettings,
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultFont'          => $this->extractFontFamily($fontSettings['fontFamily']),
            'dpi'                  => 96,
            'chroot'               => public_path(),
        ]);

        return $pdf->stream();
    }

    /**
     * Get preview HTML for live preview (AJAX refresh)
     */
    public function getPreview($id)
    {
        $sop          = Sop::with(['sections.items', 'approvals'])->findOrFail($id);
        $fontSettings = $this->getFontSettings(request());

        return view('sops.partials.pdf-template', compact('sop', 'fontSettings'));
    }

    /**
     * Get font settings from request or return defaults
     */
    private function getFontSettings(Request $request): array
    {
        $defaults = [
            'fontFamily'      => 'Arial, sans-serif',
            'baseFontSize'    => 11,
            'headerFontSize'  => 10,
            'sectionTitleSize'=> 11,
            'contentFontSize' => 11,
            'lineHeight'      => 1.6,
            'marginTop'       => 20,
            'marginBottom'    => 20,
            'marginLeft'      => 20,
            'marginRight'     => 20,
        ];

        if ($request->has('font_settings')) {
            $customSettings = json_decode($request->font_settings, true);
            return array_merge($defaults, $customSettings ?? []);
        }

        return $defaults;
    }

    /**
     * Extract font family name untuk DomPDF
     */
    private function extractFontFamily(string $fontFamily): string
    {
        $firstFont = trim(explode(',', $fontFamily)[0]);
        $firstFont = str_replace(["'", '"'], '', $firstFont);

        $fontMap = [
            'Times New Roman' => 'times',
            'Courier New'     => 'courier',
            'Arial'           => 'helvetica',
            'Helvetica'       => 'helvetica',
            'Georgia'         => 'times',
            'Verdana'         => 'helvetica',
            'Trebuchet MS'    => 'helvetica',
            'Calibri'         => 'helvetica',
            'Tahoma'          => 'helvetica',
        ];

        return $fontMap[$firstFont] ?? 'helvetica';
    }
}