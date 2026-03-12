{{--
    resources/views/pengendalian-hama/preview.blade.php
    Khusus Live Preview — bukan untuk download PDF.
    Variabel: $pengendalianHama, $lokasi, $bulan, $tahun, $details, $sop
    Gambar diload langsung via asset() — tidak perlu base64
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
            <td colspan="4" style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 12pt; letter-spacing: 1px;">
                LAPORAN PENGENDALIAN HAMA
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; border-top: none; padding: 5px 8px; width: 20%; font-weight: bold; font-size: 9pt;">Lokasi</td>
            <td style="border: 1px solid #000; border-top: none; border-left: none; padding: 5px 8px; width: 30%; font-size: 9pt;">{{ $lokasi }}</td>
            <td style="border: 1px solid #000; border-top: none; border-left: none; padding: 5px 8px; width: 20%; font-weight: bold; font-size: 9pt;">Bulan / Tahun</td>
            <td style="border: 1px solid #000; border-top: none; border-left: none; padding: 5px 8px; width: 30%; font-size: 9pt;">{{ $bulan }} / {{ $tahun }}</td>
        </tr>
        @if($pengendalianHama->penanggung_jawab ?? false)
        <tr>
            <td style="border: 1px solid #000; border-top: none; padding: 5px 8px; font-weight: bold; font-size: 9pt;">Penanggung Jawab</td>
            <td colspan="3" style="border: 1px solid #000; border-top: none; border-left: none; padding: 5px 8px; font-size: 9pt;">{{ $pengendalianHama->penanggung_jawab }}</td>
        </tr>
        @endif
    </table>

    {{-- ═══════════ TABEL DETAIL PENGENDALIAN HAMA ═══════════ --}}
    @if($details->isEmpty())
        <table style="width: 100%; border-collapse: collapse; margin-top: -1px;">
            <tr>
                <td style="border: 1px solid #000; padding: 30px; text-align: center; color: #888; font-style: italic;">
                    Belum ada data detail pengendalian hama.
                </td>
            </tr>
        </table>
    @else
        <table style="width: 100%; border-collapse: collapse; margin-top: -1px; font-size: 8pt;">
            <thead>
                <tr style="background: #d9d9d9;">
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 4%;">No</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 15%;">Tanggal</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 18%;">Jenis Hama</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 15%;">Lokasi Temuan</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 20%;">Tindakan</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 15%;">Bahan/Produk</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 13%;">Hasil/Ket.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $idx => $detail)
                <tr>
                    <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ $idx + 1 }}</td>
                    <td style="border: 1px solid #000; padding: 3px; text-align: center;">
                        {{ $detail->tanggal ? \Carbon\Carbon::parse($detail->tanggal)->format('d/m/Y') : '' }}
                    </td>
                    <td style="border: 1px solid #000; padding: 3px;">{{ $detail->jenis_hama ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 3px;">{{ $detail->lokasi_temuan ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 3px;">{{ $detail->tindakan ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 3px;">{{ $detail->bahan_produk ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 3px;">{{ $detail->hasil_keterangan ?? $detail->keterangan ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ═══════════ FOTO DOKUMENTASI (langsung via asset) ═══════════ --}}
    @php
        $gambar = $pengendalianHama->gambar ?? collect();
        $gambarChunks = $gambar->chunk(2);
    @endphp
    @if($gambar->isNotEmpty())
        <table style="width: 100%; border-collapse: collapse; margin-top: 8px;">
            <tr>
                <td colspan="2" style="border: 1px solid #000; padding: 5px 8px; font-weight: bold; background: #f2f2f2;">
                    Dokumentasi Foto
                </td>
            </tr>
            @foreach($gambarChunks as $chunk)
            <tr>
                @foreach($chunk as $foto)
                <td style="border: 1px solid #000; padding: 8px; width: 50%; text-align: center; vertical-align: top;">
                    <img src="{{ asset('storage/' . $foto->gambar_path) }}"
                         alt="Foto Hama"
                         style="max-width: 100%; max-height: 140px; object-fit: contain; display: block; margin: 0 auto;">
                    @if($foto->keterangan ?? false)
                        <p style="margin: 4px 0 0 0; font-size: 7.5pt; font-style: italic; color: #555;">{{ $foto->keterangan }}</p>
                    @endif
                </td>
                @endforeach
                @if($chunk->count() < 2)
                <td style="border: 1px solid #000; padding: 8px; width: 50%;"></td>
                @endif
            </tr>
            @endforeach
        </table>
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