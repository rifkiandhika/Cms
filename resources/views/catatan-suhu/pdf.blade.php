<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Catatan Suhu Ruangan Penyimpanan Produk</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 3px 0;
        }
        .header .subtitle {
            font-size: 12px;
            margin: 0;
        }
        .info-gudang {
            margin-bottom: 15px;
            font-size: 12px;
        }
        .info-gudang p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #e6f0fa;
            font-weight: bold;
            text-align: center;
            padding: 8px 5px;
            border: 1px solid #000;
            font-size: 11px;
        }
        td {
            padding: 6px 5px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }
        td:first-child {
            text-align: left;
            padding-left: 8px;
        }
        .check-symbol {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: right;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .footer .signature {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        .footer .signature div {
            text-align: center;
            width: 200px;
        }
        .footer .signature .line {
            margin-top: 35px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CATATAN SUHU RUANGAN PENYIMPANAN PRODUK</h1>
        <h2>Kontrol Kondisi Gudang:</h2>
        <div class="subtitle">
            <strong>Periode:</strong> {{ $periode }}<br>
            <strong>Ruang/Refrigerator:</strong> {{ $nama_gudang }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%">Tanggal</th>
                <th style="width: 8%">Kebersihan (✓)</th>
                <th style="width: 12%">Refrigerator</th>
                <th style="width: 12%">Ruangan</th>
                <th style="width: 12%">Kelembapan</th>
                <th style="width: 8%">Keamanan (✓)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($catatanSuhu as $catatan)
            <tr>
                <td>{{ \Carbon\Carbon::parse($catatan->tanggal)->format('d M Y') }}</td>
                <td>
                    @if($catatan->kebersihan)
                        <span class="check-symbol">✓</span>
                    @endif
                </td>
                <td>{{ $catatan->suhu_refrigerator }}°C</td>
                <td>{{ $catatan->suhu_ruangan }}°C</td>
                <td>{{ $catatan->kelembapan }}%</td>
                <td>
                    @if($catatan->keamanan)
                        <span class="check-symbol">✓</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    Tidak ada data catatan suhu untuk periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer w-100">
        <div class="w-100 text-end">
            <div>Dicetak pada: {{ $tanggal_cetak }}</div>
            
            <div class="signature">
                <div>
                    <div>Mengetahui,</div>
                    <div class="line">( ____________________ )</div>
                    <div>Penanggung Jawab Gudang</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>