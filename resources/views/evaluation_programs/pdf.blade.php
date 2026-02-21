<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
@page {
    margin: 15mm 15mm 15mm 15mm;
    size: A4 landscape;
}

body {
    font-size: 12pt;
    line-height: 1.3;
    margin: 0;
}

.wrapper {
    width: 100%;
    max-width: 267mm; /* 297mm - 30mm margin */
    margin: 0 auto;
}

.header-title {
    text-align: center;
    font-weight: bold;
    font-size: 14pt;
    text-transform: uppercase;
    margin-bottom: 15px;
    letter-spacing: 1px;
}

table {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid #000;
}

td {
    border: 1px solid #000;
    padding: 8px 10px;
    vertical-align: top;
}

.main-title td {
    text-align: center;
    font-weight: bold;
    font-size: 14pt;
    text-transform: uppercase;
    background: #ffffff;
    border: 2px solid #000;
    padding: 12px;
}

.label {
    width: 30%;
    font-weight: normal;
}

.value {
    width: 70%;
}

.section-label {
    font-weight: bold;
    font-size: 12pt;
}

.answer-cell {
    height: 60px;
}

.signature-cell {
    height: 80px;
}

.footer-note {
    font-size: 10pt;
    font-style: italic;
}

.empty-line {
    display: inline-block;
    border-bottom: 1px dotted #000;
    min-width: 200px;
    margin-left: 5px;
}
</style>
</head>

<body>

@php
$participants  = $evaluationProgram->participants->sortBy('order');
$items         = $evaluationProgram->items->sortBy('order');
$namaPeserta   = $participants->pluck('nama_peserta')->filter()->join(', ');
$jabatanLokasi = $participants->pluck('jabatan_lokasi_kerja')->filter()->unique()->join(', ');
$firstResponse = $evaluationProgram->responses->first();

// Inisialisasi items untuk A, B, C, D
$itemA = $items->where('item_label', 'A')->first();
$itemB = $items->where('item_label', 'B')->first();
$itemC = $items->where('item_label', 'C')->first();
$itemD = $items->where('item_label', 'D')->first();
@endphp

<div class="wrapper">

<div class="header-title">EVALUASI HASIL PELATIHAN</div>

<table>
<tr class="main-title">
<td colspan="2">{{ $evaluationProgram->title }}</td>
</tr>

<tr>
<td class="label">Materi Pelatihan</td>
<td class="value">{{ $evaluationProgram->materi_pelatihan ?? '.................................' }}</td>
</tr>

<tr>
<td class="label">Hari/tanggal Pelatihan</td>
<td class="value">
{{ $evaluationProgram->hari_tanggal
? \Carbon\Carbon::parse($evaluationProgram->hari_tanggal)->translatedFormat('l, d F Y')
: '....................................' }}
</td>
</tr>

<tr>
<td class="label">Tempat Pelatihan</td>
<td class="value">{{ $evaluationProgram->tempat_pelatihan ?? '.................................' }}</td>
</tr>

<tr>
<td class="label">Nama Peserta</td>
<td class="value">{{ $namaPeserta ?: '.................................' }}</td>
</tr>

<tr>
<td class="label">Jabatan/lokasi kerja</td>
<td class="value">{{ $jabatanLokasi ?: '.................................' }}</td>
</tr>

<tr>
<td colspan="2" style="padding: 15px 10px;">
    <strong>A. Kompetensi yang diharapkan bisa ditingkatkan dengan mengikuti training ini</strong>
    <div style="margin-top: 10px; min-height: 60px;">{{ $itemA->item_content ?? '' }}</div>
</td>
</tr>

<tr>
<td colspan="2" style="padding: 15px 10px;">
    <strong>B. Perilaku/hasil yang ditunjukkan peserta sebelum mengikuti training ini (konteks kompetensi di atas)</strong>
    <div style="margin-top: 10px; min-height: 60px;">{{ $itemB->item_content ?? '' }}</div>
</td>
</tr>

<tr>
<td colspan="2" style="padding: 15px 10px;">
    <strong>C. Perilaku/hasil yang ditunjukkan peserta setelah mengikuti training ini (konteks kompetensi di atas)</strong>
    <div style="margin-top: 10px; min-height: 60px;">{{ $itemC->item_content ?? '' }}</div>
</td>
</tr>

<tr>
<td colspan="2" style="padding: 15px 10px;">
    <strong>D. Menurut saya training ini efektif/tidak efektif karena</strong>
    <div style="margin-top: 10px; min-height: 60px;">{{ $itemD->item_content ?? '' }}</div>
</td>
</tr>

<tr>
<td style="width: 50%; padding: 15px 10px; height: 100px;">
    <strong>Mengetahui:</strong><br>
    <strong>Atasan</strong><br><br><br>
    @if($firstResponse && $firstResponse->mengetahui_atasan_nama)
    ( {{ $firstResponse->mengetahui_atasan_nama }} )<br>
    @if($firstResponse->mengetahui_atasan_tanggal)
    <span style="font-size: 10pt;">{{ \Carbon\Carbon::parse($firstResponse->mengetahui_atasan_tanggal)->format('d M Y') }}</span>
    @endif
    @else
    ( ................................ )
    @endif
</td>

<td style="width: 50%; padding: 15px 10px; height: 100px;">
    <strong>Mengetahui:</strong><br>
    <strong>Bagian Personalia</strong><br><br><br>
    @if($firstResponse && $firstResponse->mengetahui_personalia_nama)
    ( {{ $firstResponse->mengetahui_personalia_nama }} )<br>
    @if($firstResponse->mengetahui_personalia_tanggal)
    <span style="font-size: 10pt;">{{ \Carbon\Carbon::parse($firstResponse->mengetahui_personalia_tanggal)->format('d M Y') }}</span>
    @endif
    @else
    ( ................................ )
    @endif
</td>
</tr>

<tr>
<td colspan="2" style="font-size: 10pt; font-style: italic; padding: 5px 10px;">
*Coret salah satu
</td>
</tr>

</table>
</div>

</body>
</html>