{{--
    resources/views/catatan-suhu/preview.blade.php
    Khusus Live Preview — bukan untuk download PDF.
    Variabel: $kontrolGudang, $catatanSuhu, $nama_gudang, $periode, $tanggal_cetak, $sop
--}}
@php
    use Carbon\Carbon;
    $groupedByTanggal = $catatanSuhu->groupBy(fn($item) => Carbon::parse($item->tanggal)->format('Y-m-d'));
    $allDates = $groupedByTanggal->keys()->sort()->values();

    // Kumpulkan semua jam unik, urutkan
    $allJam = $catatanSuhu->pluck('jam')->unique()->sort()->values();

    // Batas baris per halaman (estimasi)
    $minRows = max(20, $allDates->count());
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

    {{-- ═══════════ JUDUL DOKUMEN ═══════════ --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: -1px;">
        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 12pt; letter-spacing: 1px;">
                CATATAN SUHU RUANGAN
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; border-top: none; padding: 5px 8px; width: 50%; font-size: 9pt;">
                <strong>Nama Gudang:</strong> {{ $nama_gudang }}
            </td>
            <td style="border: 1px solid #000; border-top: none; border-left: none; padding: 5px 8px; font-size: 9pt;">
                <strong>Periode:</strong> {{ $periode }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #000; border-top: none; padding: 5px 8px; font-size: 9pt;">
                <strong>Tanggal Cetak:</strong> {{ $tanggal_cetak }}
            </td>
        </tr>
    </table>

    {{-- ═══════════ TABEL CATATAN SUHU ═══════════ --}}
    @if($catatanSuhu->isEmpty())
        <table style="width: 100%; border-collapse: collapse; margin-top: -1px;">
            <tr>
                <td style="border: 1px solid #000; padding: 30px; text-align: center; color: #888; font-style: italic;">
                    Belum ada data catatan suhu.
                </td>
            </tr>
        </table>
    @else
        <table style="width: 100%; border-collapse: collapse; margin-top: -1px; font-size: 7.5pt;">
            <thead>
                <tr style="background: #d9d9d9;">
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 5%;">No</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 12%;">Tanggal</th>
                    @foreach($allJam as $jam)
                        <th style="border: 1px solid #000; padding: 4px; text-align: center;">
                            {{ $jam }}<br><span style="font-size: 6.5pt; font-weight: normal;">(°C)</span>
                        </th>
                    @endforeach
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 15%;">Keterangan</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 12%;">Paraf</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allDates as $idx => $tgl)
                    @php
                        $rowData     = $groupedByTanggal[$tgl] ?? collect();
                        $keterangan  = $rowData->pluck('keterangan')->filter()->unique()->join(', ');
                    @endphp
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ $idx + 1 }}</td>
                        <td style="border: 1px solid #000; padding: 3px; text-align: center;">
                            {{ Carbon::parse($tgl)->format('d/m/Y') }}
                        </td>
                        @foreach($allJam as $jam)
                            @php
                                $entry = $rowData->firstWhere('jam', $jam);
                            @endphp
                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">
                                {{ $entry ? number_format($entry->suhu, 1) : '' }}
                            </td>
                        @endforeach
                        <td style="border: 1px solid #000; padding: 3px;">{{ $keterangan }}</td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                @endforeach

                {{-- Baris kosong pelengkap --}}
                @for($i = $allDates->count(); $i < $minRows; $i++)
                <tr>
                    <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ $i + 1 }}</td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    @foreach($allJam as $jam)
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    @endforeach
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                    <td style="border: 1px solid #000; padding: 3px;"></td>
                </tr>
                @endfor
            </tbody>
        </table>

        {{-- Batas suhu --}}
        @if($kontrolGudang->batas_suhu_min || $kontrolGudang->batas_suhu_max)
        <table style="width: 100%; border-collapse: collapse; margin-top: -1px;">
            <tr>
                <td style="border: 1px solid #000; padding: 5px 8px; font-size: 8pt;">
                    <strong>Batas Suhu:</strong>
                    Min {{ $kontrolGudang->batas_suhu_min ?? '-' }}°C
                    — Maks {{ $kontrolGudang->batas_suhu_max ?? '-' }}°C
                </td>
            </tr>
        </table>
        @endif
    @endif

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
                <td style="border: 1px solid #000; padding: 8px; font-size: 8pt; text-align: center;">
                    {{ $approval->tanda_tangan ? $approval->tanda_tangan->format('d/m/Y') : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

</div>