<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 9pt;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    th, td {
        border: 1px solid #000;
        padding: 4px;
        word-wrap: break-word;
    }

    .title {
        text-align: center;
        font-weight: bold;
        font-size: 12pt;
        background: #d9d9d9;
    }
    .header-column {
        background: #d9d9d9;
    }

    .center { text-align: center; }

</style>
</head>
<body>

@php
    $customCols = $attendanceForm->custom_columns ?? [];
    $participants = $attendanceForm->participants->sortBy('urutan');

    $minRows = 15;
    $totalRows = max($minRows, $participants->count());

    // Hitung total kolom dinamis
    $totalColumns = 5 + count($customCols); 
@endphp

<table>

    <tr>
        <td colspan="{{ $totalColumns }}" class="title">
            DAFTAR HADIR PELATIHAN
        </td>
    </tr>

    <tr>
        <td width="15%">Topik</td>
        <td colspan="{{ $totalColumns-1 }}">{{ $attendanceForm->topik_pelatihan }}</td>
    </tr>

    <tr>
        <td>Tanggal</td>
        <td colspan="{{ $totalColumns-1 }}">
            {{ $attendanceForm->tanggal ? \Carbon\Carbon::parse($attendanceForm->tanggal)->format('d M Y') : '' }}
        </td>
    </tr>

    <tr>
        <td>Tempat</td>
        <td colspan="{{ $totalColumns-1 }}">{{ $attendanceForm->tempat }}</td>
    </tr>

    <tr>
        <td>Instruktur</td>
        <td colspan="{{ $totalColumns-1 }}">{{ $attendanceForm->instruktur }}</td>
    </tr>

    {{-- HEADER KOLOM --}}
    <tr class="header-column">
        <th width="5%" class="center">No</th>
        <th width="20%">Nama Karyawan</th>
        <th width="15%">Jabatan</th>
        <th width="15%">Lokasi Kerja</th>

        @foreach($customCols as $col)
            <th class="center">{{ $col }}</th>
        @endforeach

        <th width="10%" class="center">Paraf</th>
    </tr>

    {{-- DATA PESERTA --}}
    @for($i=0;$i<$totalRows;$i++)
        @php
            $p = $participants->values()->get($i);
        @endphp
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td>{{ $p->nama_karyawan ?? '' }}</td>
            <td>{{ $p->jabatan ?? '' }}</td>
            <td>{{ $p->lokasi_kerja ?? '' }}</td>

            @foreach($customCols as $ci => $col)
                <td class="center">
                    {{ $p->custom_values[$ci] ?? '' }}
                </td>
            @endforeach

            <td></td>
        </tr>
    @endfor

</table>

</body>
</html>