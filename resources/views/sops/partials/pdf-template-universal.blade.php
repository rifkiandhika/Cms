<div class="pdf-document" style="position: relative; min-height: 250mm; box-sizing: border-box; font-family: {{ $fontSettings['fontFamily'] ?? 'Arial, sans-serif' }}; font-size: {{ $fontSettings['baseFontSize'] ?? 11 }}pt; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }}; color: #000; background: #fff; padding: {{ $fontSettings['marginTop'] ?? 20 }}mm {{ $fontSettings['marginRight'] ?? 20 }}mm 0 {{ $fontSettings['marginLeft'] ?? 20 }}mm;">
    
    {{-- HEADER TABLE --}}
    <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 0; font-size: {{ $fontSettings['headerFontSize'] ?? 10 }}pt;">
        <tbody>
            <tr>
                {{-- Logo Cell - Rowspan 5 --}}
                <td rowspan="5" style="width: 120px; padding: 5px; border-right: 1px solid #000; text-align: center; vertical-align: middle;">
                    @if($sop->logo_path)
                        @php
                            $logoPath = $sop->logo_path;
                            if (filter_var($logoPath, FILTER_VALIDATE_URL)) {
                                $imageSrc = $logoPath;
                            } else {
                                $fullPath = public_path('storage/' . $logoPath);
                                if (file_exists($fullPath)) {
                                    $imageData = base64_encode(file_get_contents($fullPath));
                                    $imageSrc = 'data:image/' . pathinfo($fullPath, PATHINFO_EXTENSION) . ';base64,' . $imageData;
                                } else {
                                    $imageSrc = null;
                                }
                            }
                        @endphp
                        
                        @if(isset($imageSrc) && $imageSrc)
                            <img src="{{ $imageSrc }}" alt="Logo" style="max-width: 100px; max-height: 100px; display: block; margin: 0 auto;">
                        @else
                            <div style="width: 80px; height: 90px; background: #e74c3c; display: inline-block; position: relative;">
                                <span style="color: white; font-size: 45px; font-weight: bold; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">PT</span>
                            </div>
                        @endif
                    @else
                        <div style="width: 80px; height: 90px; background: #e74c3c; display: inline-block; position: relative;">
                            <span style="color: white; font-size: 45px; font-weight: bold; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">PT</span>
                        </div>
                    @endif
                </td>
                
                {{-- Nama SOP --}}
                <td style="width: 100px; padding: 4px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000;  font-weight: bold;">Nama SOP</td>
                <td style="padding: 4px 5px; border-bottom: 1px solid #000; font-weight: bold; text-align: center;">{{ $sop->nama_sop ?? '' }}</td>
            </tr>
            
            <tr>
                <td style="width: 100px; padding: 4px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000; vertical-align: middle; font-weight: bold;">No. SOP</td>
                <td style="padding: 4px 5px; border-bottom: 1px solid #000; vertical-align: middle;">{{ $sop->no_sop ?? '' }}</td>
            </tr>
            
            <tr>
                <td style="width: 100px; padding: 4px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000; vertical-align: middle; font-weight: bold;">Tanggal Dibuat</td>
                <td style="padding: 4px 5px; border-bottom: 1px solid #000; vertical-align: middle;">{{ isset($sop->tanggal_dibuat) ? date('d F Y', strtotime($sop->tanggal_dibuat)) : '1 September 2023' }}</td>
            </tr>
            
            <tr>
                <td style="width: 100px; padding: 4px 5px; border-right: 1px solid #000; border-bottom: 1px solid #000; vertical-align: middle; font-weight: bold;">Tanggal Efektif</td>
                <td style="padding: 4px 5px; border-bottom: 1px solid #000; vertical-align: middle;">{{ isset($sop->tanggal_efektif) ? date('d F Y', strtotime($sop->tanggal_efektif)) : '1 September 2023' }}</td>
            </tr>
            
            <tr>
                <td style="width: 100px; padding: 4px 5px; border-right: 1px solid #000; vertical-align: middle; font-weight: bold;">Revisi</td>
                <td style="padding: 4px 5px; vertical-align: middle;">{{ $sop->revisi ?? '00' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- CONTENT SECTIONS --}}
    {{-- Border kiri & kanan menyambung dari header ke footer, tanpa border atas & bawah --}}
    <div style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 12px 15px; height: 750px; font-size: {{ $fontSettings['contentFontSize'] ?? 11 }}pt;">
        @if(isset($sop->sections) && $sop->sections->count() > 0)
            @foreach($sop->sections as $section)
                <div style="margin-bottom: 12px;">
                    {{-- Section Title --}}
                    <p style="margin: 0 0 6px 0; font-weight: bold; font-size: {{ $fontSettings['sectionTitleSize'] ?? 11 }}pt;">
                        {{ $section->section_code ?? $loop->iteration }}. {{ $section->section_title ?? '' }}
                    </p>
                    
                    {{-- Section Items --}}
                    <div>
                        @if(isset($section->items) && $section->items->count() > 0)
                            @foreach($section->items as $item)
                                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">
                                    {{ $item->order ?? $loop->iteration }}. {{ $item->content ?? '' }}
                                </p>
                            @endforeach
                        @else
                            <p style="margin: 4px 0 4px 25px; text-align: justify;">-</p>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            {{-- Default content --}}
            <!-- A. Tujuan -->
            <div style="margin-bottom: 12px;">
                <p style="margin: 0 0 6px 0; font-weight: bold;">A.  Tujuan</p>
                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">1.  Agar semua karyawan dapat memenuhi standar kompetensi yang disyaratkan untuk posisi yang dimilikinya.</p>
                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">2.  Untuk meningkatkan kemampuan/skill yang dimiliki karyawan sebagai aset perusahaan di masa yang akan datang.</p>
            </div>
            
            <!-- B. Ruang Lingkup -->
            <div style="margin-bottom: 12px;">
                <p style="margin: 0 0 6px 0; font-weight: bold;">B.  Ruang Lingkup</p>
                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">1.  Prosedur ini mengatur prosedur pelaksanaan pelatihan yang dilakukan di PT.</p>
            </div>
            
            <!-- C. Penanggung Jawab -->
            <div style="margin-bottom: 12px;">
                <p style="margin: 0 0 6px 0; font-weight: bold;">C.  Penanggung Jawab</p>
                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">1.  Direkt.</p>
                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">2.  General Manager PT.</p>
                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">3.  PJT Alkes PT.</p>
            </div>
            
            <!-- D. Prosedur -->
            <div style="margin-bottom: 12px;">
                <p style="margin: 0 0 6px 0; font-weight: bold;">D.  Prosedur</p>
                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">1.  Laksanakan program pelatihan secara periodik, sedikitnya sekali dalam setahun.</p>
                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">2.  Berikan pelatihan oleh atasan yang bersangkutan.</p>
                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">3.  Penilaian pelatihan dapat dilakukan dengan memberikan pertanyaan sebelum dan sesudah pelatihan atau dengan cara lain yang diperlukan.</p>
                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">4.  Pelatihan dievaluasi dengan sharing, presentasi atau dengan cara lain yang diperlukan.</p>
                <p style="margin: 4px 0 4px 25px; text-align: justify; line-height: {{ $fontSettings['lineHeight'] ?? 1.6 }};">5.  Pelatihan dan evaluasi pelatihan didokumentasikan.</p>
            </div>
        @endif
    </div>

    {{-- APPROVAL TABLE - FIXED FOOTER --}}
    {{-- Border atas approval table sekaligus menjadi penutup bawah dari content section --}}
    <div style="position: absolute; bottom: {{ $fontSettings['marginBottom'] ?? 20 }}mm; left: {{ $fontSettings['marginLeft'] ?? 20 }}mm; right: {{ $fontSettings['marginRight'] ?? 20 }}mm;">
        <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; font-size: {{ $fontSettings['headerFontSize'] ?? 10 }}pt;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; border-top: none; padding: 6px; text-align: center; font-weight: bold;">Keterangan</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold;">Nama</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold;">Jabatan</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($sop->approvals) && $sop->approvals->count() > 0)
                    @foreach($sop->approvals as $approval)
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;">{{ $approval->keterangan ?? '' }}</td>
                            <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;">{{ $approval->nama ?? '' }}</td>
                            <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;">{{ $approval->jabatan ?? '' }}</td>
                            <td style="border: 1px solid #000; padding: 8px;"></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;">Dibuat Oleh</td>
                        <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;"></td>
                        <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;"></td>
                        <td style="border: 1px solid #000; padding: 8px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;">Diperiksa Oleh</td>
                        <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;"></td>
                        <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;"></td>
                        <td style="border: 1px solid #000; padding: 8px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;">Disetujui Oleh</td>
                        <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;"></td>
                        <td style="border: 1px solid #000; padding: 8px; vertical-align: middle;"></td>
                        <td style="border: 1px solid #000; padding: 8px;"></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>