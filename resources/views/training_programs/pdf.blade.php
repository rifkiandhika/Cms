<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Program Pelatihan Karyawan - {{ $trainingProgram->program_number }}</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 7pt;
            color: #000;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #000;
            padding: 3px 4px;
        }

        /* ================= HEADER ================= */

        .header-table {
            /* border: 1px solid #000; */
            margin-bottom: 0;
        }

        .header-title {
            font-weight: bold;
            font-size: 10pt;
            text-align: center;
            letter-spacing: 1px;
        }

        .doc-info {
            width: 100%;
            font-size: 7pt;
        }

        .doc-info td {
            /* border: 1px solid #000; */
            padding: 3px;
        }

        .doc-info td:first-child {
            width: 45%;
        }

        /* ================= MAIN TABLE ================= */

        .training-table {
            border: 1px solid #000;
            margin-top: -1px;
        }

        .training-table thead th {
            font-weight: bold;
            text-align: center;
            font-size: 7pt;
            padding: 5px 3px;
        }

        .col-no { width: 4%; text-align: center; }
        .col-pelatihan { width: 34%; }
        .col-peserta { width: 10%; text-align: center; }
        .col-instruktur { width: 14%; text-align: center; }
        .col-metode { width: 13%; text-align: center; }
        .col-jadwal { width: 12%; text-align: center; }
        .col-penilaian { width: 13%; text-align: center; }

        /* ===== CATEGORY ROWS ===== */

        .row-main-category td {
            font-weight: bold;
            text-transform: uppercase;
            background: #e6e6e6;
        }

        .row-sub-category td {
            font-weight: bold;
            text-transform: uppercase;
            background: #f2f2f2;
        }

        .row-training-item td {
            font-size: 7pt;
        }

        .row-detail td {
            font-size: 7pt;
        }

        .detail-indent {
            padding-left: 12px;
        }

        .cell-no {
            text-align: center;
            vertical-align: middle;
        }

        .cell-grouped {
            text-align: center;
            vertical-align: middle;
            font-size: 7pt;
        }

        /* Repeat header on every page */
        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        /* Footer Page Number */
        .footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
        }
    </style>

</head>
<body>
<div class="page">

    {{-- ========================
         HEADER
         ======================== --}}
    <table class="header-table">
        <tr>
            <td style="width:70%;" class="header-title">
                PROGRAM PELATIHAN KARYAWAN
            </td>
            <td style="width:30%; padding:0;">
                <table class="doc-info">
                    <tr>
                        <td>Hal</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>No. Dok</td>
                        <td><strong>{{ $trainingProgram->program_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>Tgl. Efektif</td>
                        <td><strong>
                            {{ $trainingProgram->effective_date ? $trainingProgram->effective_date->format('d M Y') : '-' }}
                        </strong></td>
                    </tr>
                    <tr>
                        <td>Revisi</td>
                        <td><strong>{{ $trainingProgram->revision }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================
         TRAINING TABLE
         ======================== --}}
    <table class="training-table">
        {{-- Table Header --}}
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-pelatihan">PELATIHAN</th>
                <th class="col-peserta">PESERTA</th>
                <th class="col-instruktur">INSTRUKTUR</th>
                <th class="col-metode">METODE</th>
                <th class="col-jadwal">JADWAL</th>
                <th class="col-penilaian">METODE<br>PENILAIAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trainingProgram->mainCategories->sortBy('order') as $mainCat)
                {{-- ===== MAIN CATEGORY ROW (I, II, III) ===== --}}
                <tr class="row-main-category">
                    <td class="cell-no">{{ $mainCat->roman_number }}</td>
                    <td colspan="6">{{ strtoupper($mainCat->name) }}</td>
                </tr>

                @foreach($mainCat->subCategories->sortBy('order') as $subCat)
                    {{-- ===== SUB CATEGORY ROW (A, B, C) ===== --}}
                    <tr class="row-sub-category">
                        <td class="cell-no">{{ $subCat->letter }}</td>
                        <td colspan="6">{{ strtoupper($subCat->name) }}</td>
                    </tr>

                    @php
                        $items = $subCat->trainingItems->sortBy('order');
                        $itemCount = $items->count();
                        
                        $itemsArray = $items->values()->toArray();
                        $rendered = [];
                        $i = 0;
                    @endphp

                    @foreach($items as $itemIndex => $item)
                        @php
                            // Count how many consecutive items share the same peserta/instruktur/metode/jadwal/penilaian
                            $rowspan = 1;
                            $currentItems = $items->values();
                            $currentIdx = $currentItems->search(fn($it) => $it->id === $item->id);
                            
                            // Check next items for matching grouped data
                            for ($j = $currentIdx + 1; $j < $currentItems->count(); $j++) {
                                $nextItem = $currentItems[$j];
                                if (
                                    $nextItem->peserta === $item->peserta &&
                                    $nextItem->instruktur === $item->instruktur &&
                                    $nextItem->metode === $item->metode &&
                                    $nextItem->jadwal === $item->jadwal &&
                                    $nextItem->metode_penilaian === $item->metode_penilaian &&
                                    (!empty($item->peserta) || !empty($item->instruktur))
                                ) {
                                    $rowspan++;
                                } else {
                                    break;
                                }
                            }
                            
                            // Check if this item's grouped cells are already rendered
                            $isGrouped = false;
                            if ($currentIdx > 0) {
                                $prevItem = $currentItems[$currentIdx - 1];
                                if (
                                    $prevItem->peserta === $item->peserta &&
                                    $prevItem->instruktur === $item->instruktur &&
                                    $prevItem->metode === $item->metode &&
                                    $prevItem->jadwal === $item->jadwal &&
                                    $prevItem->metode_penilaian === $item->metode_penilaian &&
                                    (!empty($item->peserta) || !empty($item->instruktur))
                                ) {
                                    $isGrouped = true;
                                }
                            }
                            
                            $hasDetails = $item->details && $item->details->count() > 0;
                        @endphp

                        {{-- Training Item Row --}}
                        <tr class="row-training-item">
                            <td class="cell-no">{{ $item->number }}</td>
                            <td class="cell-data">{{ $item->nama_pelatihan }}</td>

                            @if(!$isGrouped)
                                <td class="cell-grouped" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $item->peserta ?? '' }}
                                </td>
                                <td class="cell-grouped" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $item->instruktur ?? '' }}
                                </td>
                                <td class="cell-grouped" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $item->metode ?? '' }}
                                </td>
                                <td class="cell-grouped" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $item->jadwal ?? '' }}
                                </td>
                                <td class="cell-grouped" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>
                                    {{ $item->metode_penilaian ?? '' }}
                                </td>
                            @endif
                        </tr>

                        {{-- Detail Rows (a, b, c) --}}
                        @if($hasDetails)
                            @foreach($item->details->sortBy('order') as $detail)
                                <tr class="row-detail">
                                    <td class="cell-no"></td>
                                    <td class="cell-data detail-indent">
                                        {{ $detail->letter }}. {{ $detail->content }}
                                    </td>
                                    @if(!$isGrouped && $loop->first && $rowspan > 1)
                                        {{-- grouped cells already rendered with rowspan --}}
                                    @elseif(!$isGrouped && $loop->first && $rowspan == 1)
                                        {{-- no grouped data needed, cells already rendered in item row --}}
                                    @endif
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        @endif

                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>

</div>
</body>
</html>