{{--
    resources/views/training_programs/preview.blade.php
    Khusus untuk Live Preview di halaman SOP — bukan untuk download PDF.
    Variabel: $trainingProgram, $sop
--}}
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

    {{-- ═══════════ JUDUL DOKUMEN ═══════════ --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: -1px;">
        <tr>
            <td style="width: 70%; padding: 8px 10px; border: 1px solid #000; font-weight: bold; font-size: 11pt; text-align: center; letter-spacing: 1px;">
                PROGRAM PELATIHAN KARYAWAN
            </td>
            <td style="width: 30%; padding: 0; border: 1px solid #000; border-left: none;">
                <table style="width: 100%; border-collapse: collapse; font-size: 7pt;">
                    <tr>
                        <td style="padding: 2px 4px; border-bottom: 1px solid #000;">Hal</td>
                        <td style="padding: 2px 4px; border-bottom: 1px solid #000; border-left: 1px solid #000;"></td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px; border-bottom: 1px solid #000;">No. Dok</td>
                        <td style="padding: 2px 4px; border-bottom: 1px solid #000; border-left: 1px solid #000;"><strong>{{ $trainingProgram->program_number }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px; border-bottom: 1px solid #000;">Tgl. Efektif</td>
                        <td style="padding: 2px 4px; border-bottom: 1px solid #000; border-left: 1px solid #000;"><strong>{{ $trainingProgram->effective_date ? $trainingProgram->effective_date->format('d M Y') : '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px;">Revisi</td>
                        <td style="padding: 2px 4px; border-left: 1px solid #000;"><strong>{{ $trainingProgram->revision }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ═══════════ TABEL TRAINING ═══════════ --}}
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; margin-top: -1px; font-size: 7pt;">
        <thead>
            <tr style="background: #f2f2f2;">
                <th style="border: 1px solid #000; padding: 4px 3px; text-align: center; width: 4%;">NO</th>
                <th style="border: 1px solid #000; padding: 4px 3px; text-align: center; width: 34%;">PELATIHAN</th>
                <th style="border: 1px solid #000; padding: 4px 3px; text-align: center; width: 10%;">PESERTA</th>
                <th style="border: 1px solid #000; padding: 4px 3px; text-align: center; width: 14%;">INSTRUKTUR</th>
                <th style="border: 1px solid #000; padding: 4px 3px; text-align: center; width: 13%;">METODE</th>
                <th style="border: 1px solid #000; padding: 4px 3px; text-align: center; width: 12%;">JADWAL</th>
                <th style="border: 1px solid #000; padding: 4px 3px; text-align: center; width: 13%;">METODE PENILAIAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trainingProgram->mainCategories->sortBy('order') as $mainCat)
                <tr style="background: #e6e6e6;">
                    <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">{{ $mainCat->roman_number }}</td>
                    <td colspan="6" style="border: 1px solid #000; padding: 3px; font-weight: bold; text-transform: uppercase;">{{ $mainCat->name }}</td>
                </tr>
                @foreach($mainCat->subCategories->sortBy('order') as $subCat)
                    <tr style="background: #f2f2f2;">
                        <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">{{ $subCat->letter }}</td>
                        <td colspan="6" style="border: 1px solid #000; padding: 3px; font-weight: bold; text-transform: uppercase;">{{ $subCat->name }}</td>
                    </tr>
                    @php
                        $items = $subCat->trainingItems->sortBy('order');
                    @endphp
                    @foreach($items as $itemIndex => $item)
                        @php
                            $currentItems = $items->values();
                            $currentIdx   = $currentItems->search(fn($it) => $it->id === $item->id);
                            $rowspan = 1;
                            for ($j = $currentIdx + 1; $j < $currentItems->count(); $j++) {
                                $next = $currentItems[$j];
                                if ($next->peserta === $item->peserta && $next->instruktur === $item->instruktur &&
                                    $next->metode === $item->metode && $next->jadwal === $item->jadwal &&
                                    $next->metode_penilaian === $item->metode_penilaian &&
                                    (!empty($item->peserta) || !empty($item->instruktur))) {
                                    $rowspan++;
                                } else { break; }
                            }
                            $isGrouped = false;
                            if ($currentIdx > 0) {
                                $prev = $currentItems[$currentIdx - 1];
                                if ($prev->peserta === $item->peserta && $prev->instruktur === $item->instruktur &&
                                    $prev->metode === $item->metode && $prev->jadwal === $item->jadwal &&
                                    $prev->metode_penilaian === $item->metode_penilaian &&
                                    (!empty($item->peserta) || !empty($item->instruktur))) {
                                    $isGrouped = true;
                                }
                            }
                            $hasDetails = $item->details && $item->details->count() > 0;
                        @endphp
                        <tr>
                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ $item->number }}</td>
                            <td style="border: 1px solid #000; padding: 3px;">{{ $item->nama_pelatihan }}</td>
                            @if(!$isGrouped)
                                <td style="border: 1px solid #000; padding: 3px; text-align: center; vertical-align: middle;" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ $item->peserta ?? '' }}</td>
                                <td style="border: 1px solid #000; padding: 3px; text-align: center; vertical-align: middle;" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ $item->instruktur ?? '' }}</td>
                                <td style="border: 1px solid #000; padding: 3px; text-align: center; vertical-align: middle;" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ $item->metode ?? '' }}</td>
                                <td style="border: 1px solid #000; padding: 3px; text-align: center; vertical-align: middle;" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ $item->jadwal ?? '' }}</td>
                                <td style="border: 1px solid #000; padding: 3px; text-align: center; vertical-align: middle;" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ $item->metode_penilaian ?? '' }}</td>
                            @endif
                        </tr>
                        @if($hasDetails)
                            @foreach($item->details->sortBy('order') as $detail)
                                <tr>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                    <td style="border: 1px solid #000; padding: 3px; padding-left: 14px;">{{ $detail->letter }}. {{ $detail->content }}</td>
                                    <td style="border: 1px solid #000;"></td>
                                    <td style="border: 1px solid #000;"></td>
                                    <td style="border: 1px solid #000;"></td>
                                    <td style="border: 1px solid #000;"></td>
                                    <td style="border: 1px solid #000;"></td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
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