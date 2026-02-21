<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gallery - {{ $gallery->judul }}</title>

    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
        }

        /* Layout 2 kolom pakai table (AMAN untuk DomPDF) */
        .table-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .table-grid td {
            width: 50%;
            padding: 8px;
            vertical-align: top;
        }

        .image-box {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
            page-break-inside: avoid;
        }

        .image-box img {
            width: 100%;
            height: auto;
            object-fit: cover;
            margin-bottom: 5px;
        }

        .title {
            font-size: 12px;
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }

        .footer {
            margin-top: 10px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>

<body>

@php
    $chunks = $galleryImages->chunk(4); // 1 halaman = 4 gambar
@endphp

@foreach($chunks as $pageIndex => $chunk)

    <div class="header">
        <h1>{{ strtoupper($gallery->judul) }}</h1>
    </div>

    <table class="table-grid">
        <tr>
            @foreach($chunk as $i => $image)

                @php
                    $imagePath = public_path('storage/' . $image->image_path);
                    if (!file_exists($imagePath)) {
                        $imagePath = storage_path('app/public/' . $image->image_path);
                    }
                @endphp

                <td>
                    <div class="image-box">

                        {{-- GAMBAR --}}
                        @if(file_exists($imagePath))
                            <img src="{{ $imagePath }}">
                        @else
                            <div style="height:auto; display:flex; align-items:center; justify-content:center; background:#eee;">
                                Gambar tidak ditemukan
                            </div>
                        @endif

                        {{-- JUDUL --}}
                        <div class="title">
                            {{ strtoupper(pathinfo($image->image_name, PATHINFO_FILENAME)) }}
                        </div>

                    </div>
                </td>

                @if(($i + 1) % 2 == 0)
                    </tr><tr>
                @endif

            @endforeach
        </tr>
    </table>

    {{-- <div class="footer">
        Halaman {{ $pageIndex + 1 }} dari {{ $chunks->count() }}
    </div> --}}

    {{-- @if(!$loop->last)
        <div class="page-break"></div>
    @endif --}}

@endforeach

</body>
</html>
