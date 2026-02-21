{{-- Preview --}}
<div class="pdf-document" style="box-sizing: border-box; background: white; position: relative; min-height: 297mm; padding: 20mm; padding-bottom: 0;">
    
    {{-- HEADER TABLE --}}
    <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            {{-- Logo Cell --}}
            <td rowspan="5" style="width: 120px; padding: 5px; border-right: 1px solid #000; text-align: center; vertical-align: middle;">
                @if($sop->logo_path)
                    <img src="{{ asset('storage/' . $sop->logo_path) }}" alt="Logo" style="max-width: 100px; max-height: 100px; display: block; margin: 0 auto;">
                @else
                    <div style="width: 80px; height: 90px; background: #e74c3c; display: inline-block; margin: 0 auto; position: relative;">
                        <span style="color: white; font-size: 45px; font-weight: bold; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">P</span>
                    </div>
                @endif
            </td>
            
            {{-- Nama SOP Row --}}
            <td style="padding: 4px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000; width: 100px; vertical-align: middle; font-weight: bold;">
                Nama SOP
            </td>
            <td style="padding: 4px 5px; border-bottom: 1px solid #000; font-weight: bold; text-align: center">
                {{ strtoupper($sop->nama_sop) }}
            </td>
        </tr>
        
        <tr>
            <td style="padding: 4px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000; vertical-align: middle; font-weight: bold;">No. SOP</td>
            <td style="padding: 4px 5px; border-bottom: 1px solid #000; vertical-align: middle;">{{ $sop->no_sop }}</td>
        </tr>
        
        <tr>
            <td style="padding: 4px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000; vertical-align: middle; font-weight: bold;">Tanggal Dibuat</td>
            <td style="padding: 4px 5px; border-bottom: 1px solid #000; vertical-align: middle;">{{ $sop->tanggal_dibuat->format('d F Y') }}</td>
        </tr>
        
        <tr>
            <td style="padding: 4px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000; vertical-align: middle; font-weight: bold;">Tanggal Efektif</td>
            <td style="padding: 4px 5px; border-bottom: 1px solid #000; vertical-align: middle;">{{ $sop->tanggal_efektif->format('d F Y') }}</td>
        </tr>
        
        <tr>
            <td style="padding: 4px 5px; border-right: 1px solid #000; vertical-align: middle; font-weight: bold;">Revisi</td>
            <td style="padding: 4px 5px; vertical-align: middle;">{{ $sop->revisi }}</td>
        </tr>
    </table>

    {{-- CONTENT SECTIONS --}}
    {{-- height: 750px agar border kiri & kanan memanjang penuh dari header ke footer --}}
    <div class="content-wrapper" style="border-left: 1px solid #000; border-right: 1px solid #000; height: 750px; padding: 12px 15px; overflow: hidden;">
        @foreach($sop->sections as $section)
            <div class="section-block" style="margin-bottom: 18px;">
                {{-- Section Title --}}
                <p class="section-title" style="margin: 0 0 8px 0; font-weight: bold;">
                    {{ $section->section_code }}. {{ $section->section_title }}
                </p>
                
                {{-- Section Items --}}
                <div class="section-content">
                    @foreach($section->items as $item)
                        <p style="margin: 6px 0 6px 25px; text-align: justify; line-height: 1.6;">
                            {{ $item->order }}. {{ $item->content }}
                        </p>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- APPROVAL TABLE - FIXED FOOTER --}}
    {{-- Border atas table otomatis menutup bagian bawah content section --}}
    @if($sop->approvals->count() > 0)
    <div style="position: absolute; bottom: 20mm; left: 20mm; right: 20mm;">
        <table style="width: 100%; border: 1px solid #000; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; width: 20%; font-size: 10pt;">Keterangan</th>
                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; width: 30%; font-size: 10pt;">Nama</th>
                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; width: 30%; font-size: 10pt;">Jabatan</th>
                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; width: 20%; font-size: 10pt;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sop->approvals as $approval)
                    <tr>
                        <td style="border: 1px solid #000; padding: 10px; vertical-align: middle; font-size: 10pt;">
                            {{ $approval->keterangan }}
                        </td>
                        <td style="border: 1px solid #000; padding: 10px; vertical-align: middle; font-size: 10pt;">
                            {{ $approval->nama ?? '-' }}
                        </td>
                        <td style="border: 1px solid #000; padding: 10px; vertical-align: middle; font-size: 10pt;">
                            {{ $approval->jabatan ?? '-' }}
                        </td>
                        <td style="border: 1px solid #000; padding: 10px;">
                            {{-- @if($approval->tanda_tangan) --}}
                                <span class="text-center" style="font-size: 9pt;">{{ $approval->tanda_tangan->format('d/m/Y') }}</span>
                            {{-- @else
                                <span style="color: #999;">-</span>
                            @endif --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>