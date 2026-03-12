{{--
    resources/views/gallery/preview.blade.php
    Khusus Live Preview — bukan untuk download PDF.
    Variabel: $gallery, $sop
--}}
@php
    $images = $gallery->images->sortBy('order');
    $chunks = $images->chunk(2);
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

    {{-- ═══════════ JUDUL GALERI ═══════════ --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: -1px;">
        <tr>
            <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 12pt; letter-spacing: 1px;">
                DOKUMENTASI / GALERI
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; border-top: none; padding: 5px 8px; font-size: 9pt;">
                <strong>Judul:</strong> {{ $gallery->title ?? '-' }}
                &nbsp;&nbsp;&nbsp;
                <strong>Tanggal:</strong> {{ $gallery->tanggal ? \Carbon\Carbon::parse($gallery->tanggal)->format('d M Y') : '-' }}
                &nbsp;&nbsp;&nbsp;
                <strong>Lokasi:</strong> {{ $gallery->lokasi ?? '-' }}
            </td>
        </tr>
        @if($gallery->deskripsi)
        <tr>
            <td style="border: 1px solid #000; border-top: none; padding: 5px 8px; font-size: 9pt;">
                <strong>Deskripsi:</strong> {{ $gallery->deskripsi }}
            </td>
        </tr>
        @endif
    </table>

    {{-- ═══════════ GRID FOTO ═══════════ --}}
    @if($images->isEmpty())
        <table style="width: 100%; border-collapse: collapse; margin-top: -1px;">
            <tr>
                <td style="border: 1px solid #000; padding: 40px; text-align: center; color: #888; font-style: italic;">
                    Belum ada foto dalam galeri ini.
                </td>
            </tr>
        </table>
    @else
        <table style="width: 100%; border-collapse: collapse; margin-top: -1px;">
            @foreach($chunks as $chunk)
            <tr>
                @foreach($chunk as $img)
                <td style="border: 1px solid #000; padding: 8px; width: 50%; vertical-align: top; text-align: center;">
                    <img src="{{ asset('storage/' . $img->image_path) }}"
                         alt="{{ $img->caption ?? 'Foto' }}"
                         style="max-width: 100%; max-height: 160px; object-fit: contain; display: block; margin: 0 auto;">
                    @if($img->caption)
                        <p style="margin: 5px 0 0 0; font-size: 8pt; font-style: italic; color: #444;">{{ $img->caption }}</p>
                    @endif
                    @if($img->keterangan)
                        <p style="margin: 2px 0 0 0; font-size: 8pt; color: #666;">{{ $img->keterangan }}</p>
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