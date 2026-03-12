<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Print PO -
        @if($po->status === 'selesai')
            {{ $po->no_gr }}
        @else
            {{ $po->no_po }}
        @endif
    </title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 11pt; line-height: 1.4; color: #333; padding: 20px; }
        .container { max-width: 210mm; margin: 0 auto; background: white; }
        .header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #2c3e50; }
        .company-info { flex: 1; }
        .company-info h1 { color: #2c3e50; font-size: 24pt; margin-bottom: 5px; }
        .company-info p { font-size: 9pt; color: #666; line-height: 1.6; }
        .po-title { text-align: right; }
        .po-title h2 { color: #e74c3c; font-size: 28pt; margin-bottom: 5px; }
        .po-title .po-number { font-size: 14pt; color: #2c3e50; font-weight: bold; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .info-box { width: 48%; }
        .info-box h3 { background: #34495e; color: white; padding: 8px 12px; font-size: 11pt; margin-bottom: 10px; }
        .info-box table { width: 100%; font-size: 10pt; }
        .info-box table td { padding: 5px; vertical-align: top; }
        .info-box table td:first-child { width: 130px; font-weight: bold; color: #555; }
        .temp-info-box { margin-bottom: 20px; padding: 10px 15px; background: #f0f8ff; border: 1px solid #b8d4f0; border-radius: 5px; display: flex; gap: 30px; font-size: 10pt; }
        .temp-info-box .temp-item { display: flex; align-items: center; gap: 8px; }
        .temp-info-box .temp-label { font-weight: bold; color: #2c3e50; }
        .temp-info-box .temp-value { color: #2980b9; font-weight: bold; font-size: 11pt; }
        .items-section { margin-bottom: 25px; }
        .items-section h3 { background: #34495e; color: white; padding: 8px 12px; font-size: 11pt; margin-bottom: 10px; }
        .items-table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        .items-table thead { background: #ecf0f1; }
        .items-table th { padding: 10px 8px; text-align: left; font-weight: bold; color: #2c3e50; border-bottom: 2px solid #bdc3c7; }
        .items-table td { padding: 8px; border-bottom: 1px solid #ecf0f1; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-top: 20px; float: right; width: 300px; }
        .summary table { width: 100%; font-size: 10pt; }
        .summary td { padding: 8px; border-bottom: 1px solid #ecf0f1; }
        .summary td:first-child { font-weight: bold; color: #555; }
        .summary td:last-child { text-align: right; }
        .summary .grand-total { background: #2c3e50; color: white; font-size: 12pt; font-weight: bold; }
        .notes { clear: both; margin-top: 30px; padding: 15px; background: #fff9e6; border-left: 4px solid #f39c12; }
        .notes h4 { color: #f39c12; margin-bottom: 8px; font-size: 11pt; }
        .notes p { font-size: 10pt; color: #666; }
        .signatures { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { width: 30%; text-align: center; }
        .signature-box h4 { font-size: 10pt; margin-bottom: 60px; color: #555; }
        .signature-box p { border-top: 1px solid #333; padding-top: 5px; font-size: 10pt; font-weight: bold; }
        .signature-box small { display: block; color: #999; font-size: 8pt; margin-top: 3px; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 2px solid #ecf0f1; text-align: center; font-size: 8pt; color: #999; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 9pt; font-weight: bold; }
        .badge-draft { background: #95a5a6; color: white; }
        .badge-approved { background: #27ae60; color: white; }
        .badge-pending { background: #f39c12; color: white; }
        .badge-rejected { background: #e74c3c; color: white; }
        .badge-internal { background: #3498db; color: white; }
        .badge-external { background: #9b59b6; color: white; }
        .print-button { position: fixed; top: 20px; right: 20px; padding: 12px 24px; background: #3498db; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 1000; }
        .print-button:hover { background: #2980b9; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            .container { max-width: 100%; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body>

    {{-- Tombol print — langsung cetak, tidak ada modal lagi --}}
    <button class="print-button no-print" onclick="window.print()">
        Cetak / Print
    </button>

    <div class="container">

        {{-- Header --}}
        <div class="header">
            <div class="company-info">
                <h1>PT Premiere Alkes Nusindo</h1>
                <p>
                    Jl. Alamat Perusahaan No. 123<br>
                </p>
            </div>
            <div class="po-title">
                <h2>PURCHASE ORDER</h2>
                <div class="po-number">
                    @if($po->status === 'selesai')
                        {{ $po->no_gr }}
                    @else
                        {{ $po->no_po }}
                    @endif
                </div>
                <div style="margin-top: 10px;">
                    @if($po->tipe_po == 'penjualan')
                        <span class="badge badge-internal">Penjualan</span>
                    @else
                        <span class="badge badge-external">Pembelian</span>
                    @endif
                    @php
                        $statusBadge = 'badge-draft';
                        if(in_array($po->status, ['disetujui','diterima'])) $statusBadge = 'badge-approved';
                        elseif(str_contains($po->status, 'menunggu'))       $statusBadge = 'badge-pending';
                        elseif($po->status === 'ditolak')                   $statusBadge = 'badge-rejected';
                    @endphp
                    <span class="badge {{ $statusBadge }}">{{ strtoupper(str_replace('_', ' ', $po->status)) }}</span>
                </div>
            </div>
        </div>

        {{-- Info Section --}}
        <div class="info-section">
            <div class="info-box">
                <h3>DARI / FROM</h3>
                <table>
                    <tr><td>Unit Pemohon</td><td>: {{ strtoupper($po->unit_pemohon) }}</td></tr>
                    <tr><td>Pemohon</td><td>: {{ $po->karyawanPemohon->nama_lengkap }}</td></tr>
                    <tr><td>Tanggal Permintaan</td><td>: {{ $po->tanggal_permintaan->format('d F Y, H:i') }}</td></tr>
                    @if($po->tanggal_dikirim_ke_supplier)
                    <tr><td>Tanggal Kirim</td><td>: {{ $po->tanggal_dikirim_ke_supplier->format('d F Y, H:i') }}</td></tr>
                    @endif
                </table>
            </div>
            <div class="info-box">
                <h3>KEPADA / TO</h3>
                @if($po->supplier)
                <table>
                    <tr><td>Nama Supplier</td><td>: <strong>{{ $po->supplier->nama_supplier }}</strong></td></tr>
                    <tr><td>Alamat</td><td>: {{ $po->supplier->alamat ?? '-' }}</td></tr>
                    <tr><td>Telepon</td><td>: {{ $po->supplier->no_telepon ?? '-' }}</td></tr>
                    <tr><td>Email</td><td>: {{ $po->supplier->email ?? '-' }}</td></tr>
                </table>
                @elseif($po->customer)
                <table>
                    <tr><td>Nama Customer</td><td>: <strong>{{ $po->customer->nama_customer }}</strong></td></tr>
                    <tr><td>Alamat</td><td>: {{ $po->customer->alamat ?? '-' }}</td></tr>
                    <tr><td>Telepon</td><td>: {{ $po->customer->no_telepon ?? '-' }}</td></tr>
                    <tr><td>Email</td><td>: {{ $po->customer->email ?? '-' }}</td></tr>
                </table>
                @else
                <table>
                    <tr><td>Unit Tujuan</td><td>: <strong>{{ strtoupper($po->unit_tujuan) }}</strong></td></tr>
                </table>
                @endif
            </div>
        </div>

        {{-- Suhu — dibaca dari database, tampil hanya jika ada nilainya --}}
        @if($po->suhu_barang_dikirim || $po->suhu_barang_datang)
        <div class="temp-info-box">
            @if($po->suhu_barang_dikirim)
            <div class="temp-item">
                <span class="temp-label">Suhu Barang Dikirim:</span>
                <span class="temp-value">{{ $po->suhu_barang_dikirim }} °C</span>
            </div>
            @endif
            @if($po->suhu_barang_datang)
            <div class="temp-item">
                <span class="temp-label">Suhu Barang Datang:</span>
                <span class="temp-value">{{ $po->suhu_barang_datang }} °C</span>
            </div>
            @endif
        </div>
        @endif

        {{-- Items Table — hide_price dikontrol via ?hide_price=1 dari show.blade.php --}}
        @php $hidePrice = request()->query('hide_price') == '1'; @endphp

        <div class="items-section">
            <h3>DAFTAR ITEM / ITEM LIST</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Nama Produk</th>
                        <th width="80" class="text-center">Qty Diminta</th>
                        <th width="80" class="text-center">Qty Disetujui</th>
                        @if($po->status === 'selesai')
                        <th width="80" class="text-center">Qty Diterima</th>
                        @endif
                        @if(!$hidePrice)
                        <th width="100" class="text-right">Harga Satuan</th>
                        <th width="120" class="text-right">Subtotal</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($po->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->nama_produk }}</strong>
                            @if($item->kode_produk)
                                <br><small style="color:#666;">Kode: {{ $item->kode_produk }}</small>
                            @endif
                            @if($item->batch_number)
                                <br><small style="color:#666;">Batch: {{ $item->batch_number }}</small>
                            @endif
                            @if($item->tanggal_kadaluarsa)
                                <br><small style="color:#e67e22;">Exp: {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d/m/Y') }}</small>
                            @endif
                            @if(!$hidePrice && ($item->diskon_persen ?? 0) > 0)
                                <br>
                                @if($item->is_free ?? false)
                                    <small style="color:#27ae60; font-weight:bold;">★ GRATIS</small>
                                @else
                                    <small style="color:#e67e22;">
                                        Diskon {{ $item->diskon_persen }}%
                                        (- Rp {{ number_format($item->diskon_nominal, 0, ',', '.') }})
                                    </small>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">{{ $item->qty_diminta }}</td>
                        <td class="text-center">{{ $item->qty_disetujui ?? '-' }}</td>
                        @if($po->status === 'selesai')
                        <td class="text-center">{{ $item->qty_diterima ?? '-' }}</td>
                        @endif
                        @if(!$hidePrice)
                        <td class="text-right">
                            @if($item->is_free ?? false)
                                <span style="text-decoration:line-through; color:#999; font-size:9pt;">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span><br>
                                <strong style="color:#27ae60;">Rp 0</strong>
                            @elseif(($item->diskon_persen ?? 0) > 0)
                                <span style="text-decoration:line-through; color:#999; font-size:9pt;">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span><br>
                                <strong>Rp {{ number_format($item->harga_setelah_diskon ?? $item->harga_satuan, 0, ',', '.') }}</strong>
                            @else
                                Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="text-right">
                            <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary — hanya tampil jika harga tidak disembunyikan --}}
        @if(!$hidePrice)
        <div class="summary">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td>Rp {{ number_format($po->total_harga, 0, ',', '.') }}</td>
                </tr>
                @php
                    $totalDiskon = $po->items->sum(fn($i) => ($i->diskon_nominal ?? 0) * $i->qty_diminta);
                @endphp
                @if($totalDiskon > 0)
                <tr>
                    <td>Total Diskon</td>
                    <td style="color:#e74c3c;">- Rp {{ number_format($totalDiskon, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($po->pajak > 0)
                <tr>
                    <td>Pajak</td>
                    <td>Rp {{ number_format($po->pajak, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td>GRAND TOTAL</td>
                    <td>Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        @endif

        {{-- Notes --}}
        @if($po->catatan_pemohon)
        <div class="notes" style="clear:both; margin-top: 30px;">
            <h4>CATATAN / NOTES</h4>
            <p>{{ $po->catatan_pemohon }}</p>
        </div>
        @endif

        {{-- Approval Info --}}
        @if(($po->kepalaGudang || $po->kasir) && ($po->status_approval_kepala_gudang || $po->status_approval_kasir))
        <div style="margin-top: 30px; padding: 15px; background: #ecf0f1; border-radius: 5px; clear:both;">
            <h4 style="color: #2c3e50; margin-bottom: 15px; font-size: 11pt;">PERSETUJUAN / APPROVAL</h4>
            <div style="display: flex; justify-content: space-between;">
                @if($po->kepalaGudang)
                <div style="width: 48%;">
                    <strong style="color: #555;">Kepala Gudang:</strong><br>
                    {{ $po->kepalaGudang->nama_lengkap }}<br>
                    @if($po->status_approval_kepala_gudang === 'disetujui')
                        <span class="badge badge-approved">✓ DISETUJUI</span><br>
                        <small style="color: #666;">{{ $po->tanggal_approval_kepala_gudang?->format('d/m/Y H:i') }}</small>
                    @elseif($po->status_approval_kepala_gudang === 'ditolak')
                        <span class="badge badge-rejected">✗ DITOLAK</span><br>
                        <small style="color: #666;">{{ $po->tanggal_approval_kepala_gudang?->format('d/m/Y H:i') }}</small>
                    @endif
                    @if($po->catatan_kepala_gudang)
                        <br><small style="color: #666; font-style: italic;">Catatan: {{ $po->catatan_kepala_gudang }}</small>
                    @endif
                </div>
                @endif
                @if($po->kasir)
                <div style="width: 48%;">
                    <strong style="color: #555;">Kasir:</strong><br>
                    {{ $po->kasir->nama_lengkap }}<br>
                    @if($po->status_approval_kasir === 'disetujui')
                        <span class="badge badge-approved">✓ DISETUJUI</span><br>
                        <small style="color: #666;">{{ $po->tanggal_approval_kasir?->format('d/m/Y H:i') }}</small>
                    @elseif($po->status_approval_kasir === 'ditolak')
                        <span class="badge badge-rejected">✗ DITOLAK</span><br>
                        <small style="color: #666;">{{ $po->tanggal_approval_kasir?->format('d/m/Y H:i') }}</small>
                    @endif
                    @if($po->catatan_kasir)
                        <br><small style="color: #666; font-style: italic;">Catatan: {{ $po->catatan_kasir }}</small>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Signatures --}}
        <div class="signatures">
            <div class="signature-box">
                <h4>Dibuat Oleh / Prepared By</h4>
                <p>{{ $po->karyawanPemohon->nama_lengkap }}</p>
                <small>{{ $po->tanggal_permintaan->format('d/m/Y') }}</small>
            </div>
            @if($po->kepalaGudang && $po->status_approval_kepala_gudang === 'disetujui')
            <div class="signature-box">
                <h4>Disetujui Oleh / Approved By</h4>
                <p>{{ $po->kepalaGudang->nama_lengkap }}</p>
                <small>Kepala Gudang</small><br>
                <small>{{ $po->tanggal_approval_kepala_gudang?->format('d/m/Y') }}</small>
            </div>
            @endif
            @if($po->supplier)
            <div class="signature-box">
                <h4>Diterima Oleh / Received By</h4>
                <p>_______________________</p>
                <small>{{ $po->supplier->nama_supplier }}</small>
            </div>
            @elseif($po->customer)
            <div class="signature-box">
                <h4>Diterima Oleh / Received By</h4>
                <p>_______________________</p>
                <small>{{ $po->customer->nama_customer }}</small>
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>
                Dokumen ini dibuat secara elektronik dan sah tanpa tanda tangan basah.<br>
                Dicetak pada: {{ now()->format('d F Y, H:i:s') }}
            </p>
        </div>
    </div>

</body>
</html>