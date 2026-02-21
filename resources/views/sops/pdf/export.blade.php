<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $sop->nama_sop }} - SOP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4 portrait;
            margin: {{ $fontSettings['marginTop'] }}mm {{ $fontSettings['marginRight'] }}mm {{ $fontSettings['marginBottom'] }}mm {{ $fontSettings['marginLeft'] }}mm;
        }
        
        body {
            font-family: {{ $fontSettings['fontFamily'] }};
            font-size: {{ $fontSettings['baseFontSize'] }}pt;
            line-height: {{ $fontSettings['lineHeight'] }};
            color: #000;
            background: #fff;
        }
        
        .pdf-document {
            width: 100%;
        }
        
        /* Header Table Styling - Matching Word Example */
        .header-table {
            width: 100%;
            border: 2px solid #000;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: {{ $fontSettings['headerFontSize'] }}pt;
        }
        
        .header-table td {
            vertical-align: middle;
        }
        
        .header-table .logo-cell {
            width: 120px;
            padding: 10px;
            border-right: 2px solid #000;
            text-align: center;
            vertical-align: middle;
        }
        
        .header-table .logo-cell img {
            max-width: 100px;
            max-height: 100px;
            display: block;
            margin: 0 auto;
        }
        
        .header-table .label-cell {
            width: 100px;
            padding: 8px 10px;
            border-right: 2px solid #000;
            vertical-align: middle;
            font-weight: bold;
        }
        
        .header-table .value-cell {
            padding: 8px 10px;
            vertical-align: middle;
        }
        
        .header-table tr:not(:last-child) .label-cell,
        .header-table tr:not(:last-child) .value-cell {
            border-bottom: 1px solid #000;
        }
        
        .header-table .nama-sop-value {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        /* Logo Placeholder */
        .logo-placeholder {
            width: 80px;
            height: 90px;
            background: #e74c3c;
            display: inline-block;
            position: relative;
        }
        
        .logo-placeholder span {
            color: white;
            font-size: 45px;
            font-weight: bold;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        /* Content Sections */
        .content-wrapper {
            margin-bottom: 20px;
        }
        
        .section-block {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        
        .section-title {
            margin: 0 0 10px 0;
            font-weight: bold;
            font-size: {{ $fontSettings['sectionTitleSize'] }}pt;
        }
        
        .section-content {
            font-size: {{ $fontSettings['contentFontSize'] }}pt;
        }
        
        .section-content p {
            margin: 6px 0 6px 25px;
            text-align: justify;
            line-height: {{ $fontSettings['lineHeight'] }};
        }
        
        /* Approval Table - Footer */
        .approval-table {
            width: 100%;
            border: 2px solid #000;
            border-collapse: collapse;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .approval-table thead tr {
            background-color: #f0f0f0;
        }
        
        .approval-table th,
        .approval-table td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
            vertical-align: middle;
        }
        
        .approval-table th {
            text-align: center;
            font-weight: bold;
        }
        
        .approval-table .signature-cell {
            height: 70px;
            text-align: center;
            vertical-align: bottom;
        }
        
        .approval-table .signature-cell span {
            font-size: 10pt;
        }
        
        /* Column widths */
        .approval-table .col-keterangan { width: 20%; }
        .approval-table .col-nama { width: 30%; }
        .approval-table .col-jabatan { width: 30%; }
        .approval-table .col-ttd { width: 20%; }
        
        /* Utilities */
        strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="pdf-document">
        {{-- HEADER TABLE --}}
        <table class="header-table">
            <tbody>
                <tr>
                    {{-- Logo Cell - Rowspan 5 --}}
                    <td rowspan="5" class="logo-cell">
                        @if($sop->logo_path)
                            @php
                                $logoPath = public_path('storage/' . $sop->logo_path);
                                // Fallback jika file tidak ada
                                if (!file_exists($logoPath)) {
                                    $logoPath = null;
                                }
                            @endphp
                            
                            @if($logoPath)
                                <img src="{{ $logoPath }}" alt="Logo">
                            @else
                                <div class="logo-placeholder">
                                    <span>P</span>
                                </div>
                            @endif
                        @else
                            <div class="logo-placeholder">
                                <span>P</span>
                            </div>
                        @endif
                    </td>
                    
                    {{-- Nama SOP --}}
                    <td class="label-cell">Nama SOP</td>
                    <td class="value-cell nama-sop-value">{{ strtoupper($sop->nama_sop) }}</td>
                </tr>
                
                <tr>
                    {{-- No. SOP --}}
                    <td class="label-cell">No. SOP</td>
                    <td class="value-cell">{{ $sop->no_sop }}</td>
                </tr>
                
                <tr>
                    {{-- Tanggal Dibuat --}}
                    <td class="label-cell">Tanggal Dibuat</td>
                    <td class="value-cell">{{ $sop->tanggal_dibuat->format('d F Y') }}</td>
                </tr>
                
                <tr>
                    {{-- Tanggal Efektif --}}
                    <td class="label-cell">Tanggal Efektif</td>
                    <td class="value-cell">{{ $sop->tanggal_efektif->format('d F Y') }}</td>
                </tr>
                
                <tr>
                    {{-- Revisi --}}
                    <td class="label-cell">Revisi</td>
                    <td class="value-cell">{{ $sop->revisi }}</td>
                </tr>
            </tbody>
        </table>

        {{-- CONTENT SECTIONS --}}
        <div class="content-wrapper">
            @foreach($sop->sections as $section)
                <div class="section-block">
                    <p class="section-title">{{ $section->section_code }}. {{ $section->section_title }}</p>
                    
                    <div class="section-content">
                        @foreach($section->items as $item)
                            <p>{{ $item->order }}. {{ $item->content }}</p>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- APPROVAL TABLE --}}
        @if($sop->approvals->count() > 0)
            <table class="approval-table">
                <thead>
                    <tr>
                        <th class="col-keterangan">Keterangan</th>
                        <th class="col-nama">Nama</th>
                        <th class="col-jabatan">Jabatan</th>
                        <th class="col-ttd">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sop->approvals as $approval)
                        <tr>
                            <td>{{ $approval->keterangan }}</td>
                            <td>{{ $approval->nama ?? '-' }}</td>
                            <td>{{ $approval->jabatan ?? '-' }}</td>
                            <td class="signature-cell">
                                @if($approval->tanda_tangan)
                                    <span>{{ $approval->tanda_tangan->format('d/m/Y') }}</span>
                                @else
                                    <span style="color: #999;">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>