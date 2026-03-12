{{--
    resources/views/evaluation_programs/preview.blade.php
    Khusus Live Preview — bukan untuk download PDF.
    Variabel: $evaluationProgram, $sop
--}}
@php
    $participants  = $evaluationProgram->participants->sortBy('order');
    $items         = $evaluationProgram->items->sortBy('order');
    $namaPeserta   = $participants->pluck('nama_peserta')->filter()->join(', ');
    $jabatanLokasi = $participants->pluck('jabatan_lokasi_kerja')->filter()->unique()->join(', ');
    $firstResponse = $evaluationProgram->responses->first();
    $itemA = $items->where('item_label', 'A')->first();
    $itemB = $items->where('item_label', 'B')->first();
    $itemC = $items->where('item_label', 'C')->first();
    $itemD = $items->where('item_label', 'D')->first();
@endphp

<div style="box-sizing: border-box; background: white; width: 210mm; min-height: 297mm; font-family: Arial, sans-serif; font-size: 9pt; padding: 0;">

    {{-- ═══════════ HEADER SOP ═══════════ --}}
    <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td rowspan="5" style="width: 120px; padding: 5px; border-right: 1px solid #000; text-align: center; vertical-align: middle;">
                @if($sop->logo_path)
                    <img src="{{ asset('storage/' . $sop->logo_path) }}" alt="Logo"
                         style="max-width: 100px; max-height: 80px; display: block; margin: 0 auto;">
                @else
                    <div style="width: 80px; height: 70px; background: #e74c3c; display: inline-block; position: relative;">
                        <span style="color:white; font-size:36px; font-weight:bold; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);">P</span>
                    </div>
                @endif
            </td>
            <td style="padding: 3px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000; width: 100px; font-weight: bold; font-size: 8pt;">Nama SOP</td>
            <td style="padding: 3px 5px; border-bottom: 1px solid #000; font-weight: bold; text-align: center; font-size: 8pt;">{{ strtoupper($sop->nama_sop) }}</td>
        </tr>
        <tr>
            <td style="padding: 3px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold; font-size: 8pt;">No. SOP</td>
            <td style="padding: 3px 5px; border-bottom: 1px solid #000; font-size: 8pt;">{{ $sop->no_sop }}</td>
        </tr>
        <tr>
            <td style="padding: 3px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold; font-size: 8pt;">Tanggal Dibuat</td>
            <td style="padding: 3px 5px; border-bottom: 1px solid #000; font-size: 8pt;">{{ $sop->tanggal_dibuat->format('d F Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 3px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold; font-size: 8pt;">Tanggal Efektif</td>
            <td style="padding: 3px 5px; border-bottom: 1px solid #000; font-size: 8pt;">{{ $sop->tanggal_efektif->format('d F Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 3px 5px; border-right: 1px solid #000; font-weight: bold; font-size: 8pt;">Revisi</td>
            <td style="padding: 3px 5px; font-size: 8pt;">{{ $sop->revisi }}</td>
        </tr>
    </table>

    {{-- ═══════════ FORM EVALUASI ═══════════ --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: -1px; font-size: 9pt;">

        {{-- Judul --}}
        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 10px; text-align: center; font-weight: bold; font-size: 12pt; letter-spacing: 1px;">
                EVALUASI HASIL PELATIHAN
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 10pt;">
                {{ $evaluationProgram->title }}
            </td>
        </tr>

        {{-- Info Peserta --}}
        <tr>
            <td style="border: 1px solid #000; padding: 6px 8px; width: 35%; font-weight: bold;">Materi Pelatihan</td>
            <td style="border: 1px solid #000; padding: 6px 8px;">{{ $evaluationProgram->materi_pelatihan ?? '.................................' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold;">Hari/Tanggal Pelatihan</td>
            <td style="border: 1px solid #000; padding: 6px 8px;">
                {{ $evaluationProgram->hari_tanggal
                    ? \Carbon\Carbon::parse($evaluationProgram->hari_tanggal)->translatedFormat('l, d F Y')
                    : '....................................' }}
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold;">Tempat Pelatihan</td>
            <td style="border: 1px solid #000; padding: 6px 8px;">{{ $evaluationProgram->tempat_pelatihan ?? '.................................' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold;">Nama Peserta</td>
            <td style="border: 1px solid #000; padding: 6px 8px;">{{ $namaPeserta ?: '.................................' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold;">Jabatan/Lokasi Kerja</td>
            <td style="border: 1px solid #000; padding: 6px 8px;">{{ $jabatanLokasi ?: '.................................' }}</td>
        </tr>

        {{-- Item A --}}
        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 10px 8px;">
                <strong>A. Kompetensi yang diharapkan bisa ditingkatkan dengan mengikuti training ini</strong>
                <div style="margin-top: 8px; min-height: 50px; padding: 4px 0;">{{ $itemA->item_content ?? '' }}</div>
            </td>
        </tr>
        {{-- Item B --}}
        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 10px 8px;">
                <strong>B. Perilaku/hasil yang ditunjukkan peserta <u>sebelum</u> mengikuti training ini</strong>
                <div style="margin-top: 8px; min-height: 50px; padding: 4px 0;">{{ $itemB->item_content ?? '' }}</div>
            </td>
        </tr>
        {{-- Item C --}}
        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 10px 8px;">
                <strong>C. Perilaku/hasil yang ditunjukkan peserta <u>setelah</u> mengikuti training ini</strong>
                <div style="margin-top: 8px; min-height: 50px; padding: 4px 0;">{{ $itemC->item_content ?? '' }}</div>
            </td>
        </tr>
        {{-- Item D --}}
        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 10px 8px;">
                <strong>D. Menurut saya training ini efektif/tidak efektif* karena</strong>
                <div style="margin-top: 8px; min-height: 50px; padding: 4px 0;">{{ $itemD->item_content ?? '' }}</div>
            </td>
        </tr>

        {{-- Tanda Tangan --}}
        <tr>
            <td style="border: 1px solid #000; padding: 10px 8px; vertical-align: top; height: 80px;">
                <strong>Mengetahui: Atasan</strong><br><br><br>
                @if($firstResponse && $firstResponse->mengetahui_atasan_nama)
                    ( {{ $firstResponse->mengetahui_atasan_nama }} )
                    @if($firstResponse->mengetahui_atasan_tanggal)
                        <br><span style="font-size: 8pt;">{{ \Carbon\Carbon::parse($firstResponse->mengetahui_atasan_tanggal)->format('d M Y') }}</span>
                    @endif
                @else
                    ( ................................ )
                @endif
            </td>
            <td style="border: 1px solid #000; padding: 10px 8px; vertical-align: top; height: 80px;">
                <strong>Mengetahui: Bagian Personalia</strong><br><br><br>
                @if($firstResponse && $firstResponse->mengetahui_personalia_nama)
                    ( {{ $firstResponse->mengetahui_personalia_nama }} )
                    @if($firstResponse->mengetahui_personalia_tanggal)
                        <br><span style="font-size: 8pt;">{{ \Carbon\Carbon::parse($firstResponse->mengetahui_personalia_tanggal)->format('d M Y') }}</span>
                    @endif
                @else
                    ( ................................ )
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 4px 8px; font-size: 8pt; font-style: italic;">
                *Coret salah satu
            </td>
        </tr>
    </table>

    {{-- ═══════════ FOOTER APPROVAL SOP ═══════════ --}}
    @if($sop->approvals->count() > 0)
    <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold; width: 20%; font-size: 8pt;">Keterangan</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold; width: 30%; font-size: 8pt;">Nama</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold; width: 30%; font-size: 8pt;">Jabatan</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold; width: 20%; font-size: 8pt;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sop->approvals as $approval)
            <tr>
                <td style="border: 1px solid #000; padding: 8px; font-size: 8pt;">{{ $approval->keterangan }}</td>
                <td style="border: 1px solid #000; padding: 8px; font-size: 8pt;">{{ $approval->nama ?? '-' }}</td>
                <td style="border: 1px solid #000; padding: 8px; font-size: 8pt;">{{ $approval->jabatan ?? '-' }}</td>
                <td style="border: 1px solid #000; padding: 8px; font-size: 8pt; text-align: center;">{{ $approval->tanda_tangan ? $approval->tanda_tangan->format('d/m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

</div>