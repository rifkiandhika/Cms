<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $sop->nama_sop }} - SOP</title>
    <style>
        /* Minimal CSS - hanya untuk @page settings */
        @page {
            size: A4 portrait;
            margin: 0mm; /* Margin dihandle di inline styles */
        }
        
        /* Helper untuk page breaks */
        .page-break {
            page-break-after: always;
        }
        
        .page-break-avoid {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    @include('sops.partials.pdf-template')
</body>
</html>