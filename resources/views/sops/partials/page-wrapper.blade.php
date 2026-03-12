@if($pageType === 'sop')
    {{-- Halaman SOP Utama: langsung render, sudah ada padding di dalam --}}
    {!! $contentHtml !!}
@else
    {{-- Halaman data (training, attendance, dll):
         blade preview sudah self-contained dengan header+footer SOP,
         cukup render langsung tanpa wrapper padding tambahan --}}
    {!! $contentHtml !!}
@endif