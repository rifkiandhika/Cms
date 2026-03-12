{{--
    resources/views/attendance-forms/preview.blade.php
    Khusus Live Preview — bukan untuk download PDF.
    Variabel: $attendanceForm, $sop
--}}
@php
    $customCols  = $attendanceForm->custom_columns ?? [];
    $participants = $attendanceForm->participants->sortBy('urutan');
    $minRows     = 15;
    $totalRows   = max($minRows, $participants->count());
    $totalColumns = 5 + count($customCols);
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

    {{-- ═══════════ TABEL DAFTAR HADIR ═══════════ --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: -1px; font-size: 8pt;">

        {{-- Judul --}}
        <tr>
            <td colspan="{{ $totalColumns }}" style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold; font-size: 11pt; background: #d9d9d9; letter-spacing: 1px;">
                DAFTAR HADIR PELATIHAN
            </td>
        </tr>

        {{-- Info Pelatihan --}}
        <tr>
            <td style="border: 1px solid #000; padding: 4px 6px; width: 15%; font-weight: bold;">Topik</td>
            <td colspan="{{ $totalColumns - 1 }}" style="border: 1px solid #000; padding: 4px 6px;">{{ $attendanceForm->topik_pelatihan }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 4px 6px; font-weight: bold;">Tanggal</td>
            <td colspan="{{ $totalColumns - 1 }}" style="border: 1px solid #000; padding: 4px 6px;">
                {{ $attendanceForm->tanggal ? \Carbon\Carbon::parse($attendanceForm->tanggal)->format('d M Y') : '' }}
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 4px 6px; font-weight: bold;">Tempat</td>
            <td colspan="{{ $totalColumns - 1 }}" style="border: 1px solid #000; padding: 4px 6px;">{{ $attendanceForm->tempat }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 4px 6px; font-weight: bold;">Instruktur</td>
            <td colspan="{{ $totalColumns - 1 }}" style="border: 1px solid #000; padding: 4px 6px;">{{ $attendanceForm->instruktur }}</td>
        </tr>

        {{-- Header Kolom --}}
        <tr style="background: #d9d9d9;">
            <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 5%;">No</th>
            <th style="border: 1px solid #000; padding: 4px; width: 22%;">Nama Karyawan</th>
            <th style="border: 1px solid #000; padding: 4px; width: 17%;">Jabatan</th>
            <th style="border: 1px solid #000; padding: 4px; width: 17%;">Lokasi Kerja</th>
            @foreach($customCols as $col)
                <th style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $col }}</th>
            @endforeach
            <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 12%;">Paraf</th>
        </tr>

        {{-- Data Peserta --}}
        @for($i = 0; $i < $totalRows; $i++)
            @php $p = $participants->values()->get($i); @endphp
            <tr>
                <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $i + 1 }}</td>
                <td style="border: 1px solid #000; padding: 4px;">{{ $p->nama_karyawan ?? '' }}</td>
                <td style="border: 1px solid #000; padding: 4px;">{{ $p->jabatan ?? '' }}</td>
                <td style="border: 1px solid #000; padding: 4px;">{{ $p->lokasi_kerja ?? '' }}</td>
                @foreach($customCols as $ci => $col)
                    <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $p->custom_values[$ci] ?? '' }}</td>
                @endforeach
                <td style="border: 1px solid #000; padding: 4px; height: 22px;"></td>
            </tr>
        @endfor

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