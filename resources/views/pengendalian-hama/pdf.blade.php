<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengendalian Hama</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 15px;
        }
        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        .header .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .subtitle {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 0 0 5px 0;
            color: #444;
        }
        .info-lokasi {
            margin-bottom: 15px;
            font-size: 11px;
            border: 1px solid #aaa;
            padding: 8px 10px;
            background-color: #f5f5f5;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .info-lokasi p {
            margin: 3px 0;
            flex: 1 1 30%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
            table-layout: fixed;
        }
        th {
            background-color: #d3d3d3;
            font-weight: bold;
            text-align: center;
            padding: 6px 2px;
            border: 1px solid #000;
            vertical-align: middle;
            font-size: 9px;
        }
        td {
            padding: 5px 2px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }
        td:first-child {
            text-align: center;
            padding-left: 2px;
            font-weight: 500;
        }
        .check-symbol {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            font-weight: bold;
            color: #000;
        }
        .treatment-cell {
            text-align: center;
            font-size: 12px;
        }
        .keterangan-legend {
            margin: 15px 0 20px 0;
            font-size: 9px;
            border: 1px solid #999;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .legend-row {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
        }

        .legend-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px 15px;
        }

        .legend-title {
            font-weight: bold;
            font-size: 10px;
            margin-right: 5px;
            background-color: #e0e0e0;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .legend-item {
            margin-right: 5px;
            white-space: nowrap;
        }

        .legend-item strong {
            margin-right: 2px;
            font-weight: 700;
        }
        
        .gambar-page {
            page-break-before: always;
            margin-top: 20px;
        }
        
        .gambar-section {
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .gambar-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 20px;
            text-decoration: underline;
            background-color: #f0f0f0;
            padding: 6px 12px;
            display: inline-block;
            text-align: center;
            width: 100%;
        }
        .gambar-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }
        .gambar-item {
            border: 1px solid #aaa;
            padding: 8px;
            width: 200px;
            text-align: center;
            page-break-inside: avoid;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .gambar-item img {
            max-width: 100%;
            height: auto;
            max-height: 140px;
            object-fit: contain;
            border: 1px solid #ddd;
        }
        .gambar-nama {
            font-size: 9px;
            margin-top: 6px;
            color: #555;
            font-style: italic;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
        }
        .signature {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
        }
        .signature div {
            text-align: center;
            width: 220px;
        }
        .signature .line {
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 6px;
            font-weight: 500;
        }
        .text-left {
            text-align: left;
            padding-left: 5px;
        }
        
        /* Menyesuaikan dengan screenshot: header tabel lebih gelap, tata letak lebih rapat */
        table th {
            background-color: #c0c0c0;
            border: 1px solid #222;
        }
        
        table td {
            border: 1px solid #222;
        }
        
        /* Styling untuk baris data */
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Lebar kolom disesuaikan */
        th:first-child { width: 8%; }
        th:nth-child(2) { width: 5%; }
        th:nth-child(3) { width: 6%; }
        /* Kolom treatment C B F I total 12% */
        th:nth-child(8) { width: 8%; } /* Perangkap/Perlakuan */
        th:nth-child(9) { width: 6%; } /* Jumlah Hama */
        th:nth-child(10) { width: 8%; } /* Evaluasi */
        th:nth-child(11) { width: 8%; } /* Nama Petugas */
        th:nth-child(12) { width: 6%; } /* Paraf */
        th:nth-child(13) { width: 9%; } /* Keterangan */
        
        /* Halaman gambar di page terpisah */
        .gambar-header {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- HALAMAN 1: Data Laporan -->
    <div class="header">
        <div class="title">PEST CONTROL</div>
        <div class="subtitle">LAPORAN PENGENDALIAN HAMA</div>
    </div>

    <div class="info-lokasi">
        <p><strong>Lokasi :</strong> {{ $lokasi }}</p>
        <p><strong>Bulan :</strong> {{ $bulan }}</p>
        <p><strong>Tahun :</strong> {{ $tahun }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">Tgl</th>
                <th rowspan="2">Hari</th>
                <th rowspan="2">Waktu</th>
                <th colspan="4">Treatment</th>
                <th rowspan="2">Perangkap/<br>Perlakuan</th>
                <th rowspan="2">Jumlah<br>Hama</th>
                <th rowspan="2">Evaluasi</th>
                <th colspan="2">Petugas</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>C</th>
                <th>B</th>
                <th>F</th>
                <th>I</th>
                <th>Nama</th>
                <th>Paraf</th>
            </tr>
        </thead>
        <tbody>
            @forelse($details as $detail)
            <tr>
                <td>{{ Carbon\Carbon::parse($detail->tanggal)->format('d M') }}</td>
                <td>{{ $detail->hari }}</td>
                <td>{{ $detail->waktu ? substr($detail->waktu, 0, 5) : '-' }}</td>
                <td class="treatment-cell">
                    @if($detail->treatment_c)
                        <span class="check-symbol">✓</span>
                    @endif
                </td>
                <td class="treatment-cell">
                    @if($detail->treatment_b)
                        <span class="check-symbol">✓</span>
                    @endif
                </td>
                <td class="treatment-cell">
                    @if($detail->treatment_f)
                        <span class="check-symbol">✓</span>
                    @endif
                </td>
                <td class="treatment-cell">
                    @if($detail->treatment_i)
                        <span class="check-symbol">✓</span>
                    @endif
                </td>
                <td>{{ $detail->perangkap_perlakuan ?? '-' }}</td>
                <td>{{ $detail->jumlah_hama ?? '0' }}</td>
                <td>{{ $detail->evaluasi ?? '-' }}</td>
                <td>{{ $detail->nama_petugas ?? '-' }}</td>
                <td>
                    @if($detail->paraf_petugas)
                        <span class="check-symbol">✓</span>
                    @endif
                </td>
                <td>{{ $detail->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="13" style="text-align: center; padding: 15px; background-color: #f0f0f0;">
                    Tidak ada data pengendalian hama untuk periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Keterangan / Legend sesuai screenshot -->
    <div class="keterangan-legend">
        <div class="legend-row">
            <div class="legend-group">
                <span class="legend-title">Ket:</span>
                <span class="legend-item"><strong>C:</strong> Coolfog</span>
                <span class="legend-item"><strong>B:</strong> Baiting</span>
                <span class="legend-item"><strong>F:</strong> Fogging</span>
                <span class="legend-item"><strong>I:</strong> Inspeksi</span>
            </div>

            <div class="legend-group">
                <span class="legend-title">Perangkap</span>
                <span class="legend-item"><strong>K:</strong> Cockroach (Kecoa)</span>
                <span class="legend-item"><strong>MS:</strong> Mosq Trap (Nyamuk)</span>
                <span class="legend-item"><strong>L:</strong> Fly Trap (Lalat)</span>
                <span class="legend-item"><strong>RB:</strong> Rat Box (Tikus)</span>
            </div>
        </div>
    </div>

    <!-- Footer dengan Tanda Tangan (masih di halaman 1) -->
    <div class="footer">
        <div style="margin-bottom: 20px; font-weight: bold;">Menyetujui,</div>
        
        <div class="signature">
            <div>
                <div style="margin-bottom: 30px;">&nbsp;</div>
                <div class="line">{{ $penanggungJawab }}</div>
                <div>Penanggung Jawab Teknis</div>
            </div>
        </div>
    </div>

    <!-- HALAMAN 2: Gambar Dokumentasi (jika ada) - Page terpisah -->
    @if(count($gambarBase64) > 0)
    <div class="page-break"></div>
    <div class="gambar-page">
        <div class="gambar-header">
            DOKUMENTASI PENGENDALIAN HAMA
        </div>
        <div class="gambar-section">
            <div class="gambar-title">Lokasi: {{ $lokasi }} | Bulan: {{ $bulan }} {{ $tahun }}</div>
            <div class="gambar-container">
                @foreach($gambarBase64 as $index => $gambar)
                <div class="gambar-item">
                    <img src="{{ $gambar }}" alt="Gambar {{ $index + 1 }}">
                    <div class="gambar-nama">Dokumentasi {{ $index + 1 }}</div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Footer kecil di halaman gambar -->
        <div style="margin-top: 30px; text-align: center; font-size: 8px; color: #666;">
            Halaman Dokumentasi - {{ $lokasi }} ({{ $bulan }} {{ $tahun }})
        </div>
    </div>
    @endif
</body>
</html>