@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">

    {{-- Judul --}}
    <div class="col-md-12 mb-3">
        <label class="form-label fw-semibold">
            Judul <span class="text-danger">*</span>
        </label>
        
        <input type="hidden" name="sop_id" value="{{ old('sop_id', request('sop_id') ?? $gallery->sop_id ?? '') }}">
        <input type="text"
               name="judul"
               class="form-control"
               value="{{ old('judul', $gallery->judul ?? '') }}"
               placeholder="Contoh: Area Pengiriman Barang"
               required>
    </div>

    {{-- Deskripsi --}}
    <div class="col-md-12 mb-3">
        <label class="form-label fw-semibold">
            Deskripsi (Opsional)
        </label>
        <textarea name="deskripsi"
                  class="form-control"
                  rows="3"
                  placeholder="Tambahkan deskripsi gallery...">{{ old('deskripsi', $gallery->deskripsi ?? '') }}</textarea>
    </div>

    {{-- Upload Area --}}
    <div class="col-md-12">
        <label class="form-label fw-semibold">
            Upload Gambar
            @unless(isset($gallery))
                <span class="text-danger">*</span>
            @endunless
        </label>
    </div>
    <div id="dropArea" class="upload-box position-relative">
        <i class="ri-image-add-line upload-icon"></i>
        <p class="fw-semibold mb-1">Klik atau Drag & Drop Gambar</p>
        <small class="text-muted">JPG, PNG, GIF, WEBP (Max 2MB)</small>
    </div>

    <input type="file"
        name="images[]"
        id="imageInput"
        accept="image/*"
        multiple
        hidden
        @unless(isset($gallery)) required @endunless>


        <div id="imagePreview" class="row mt-3"></div>
    </div>

    {{-- Existing Images (Edit Mode) --}}
    @isset($gallery)
        <div class="col-md-12 mt-4">
            <label class="fw-bold mb-2">Gambar Saat Ini</label>
            <div class="row">
                @forelse($gallery->images as $image)
                    <div class="col-md-3 mb-3" id="image-{{ $image->id }}">
                        <div class="card border-0 shadow-sm">
                            <img src="{{ asset('storage/'.$image->image_path) }}"
                                 class="card-img-top"
                                 style="height:150px; object-fit:cover;">

                            <div class="card-body p-2">
                                <button type="button"
                                        class="btn btn-danger btn-sm w-100 delete-existing-image"
                                        data-id="{{ $image->id }}">
                                    <i class="ri-delete-bin-line me-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light text-center">
                            Belum ada gambar pada galeri ini.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    @endisset

</div>
@push('styles')
<style>
.upload-box {
    border: 2px dashed #d0d5dd;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: 0.3s;
    background: #f9fafb;
}

.upload-box:hover {
    border-color: #6366f1;
    background: #eef2ff;
}

.upload-icon {
    font-size: 40px;
    color: #6366f1;
    margin-bottom: 10px;
}

.preview-card {
    position: relative;
}

.preview-card img {
    width: 100%;
    height: auto;
    object-fit: cover;
    border-radius: 8px;
}

.remove-preview {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(220, 53, 69, 0.9);
    border: none;
    color: white;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    cursor: pointer;
}
</style>
@endpush
@push('scripts')
<script>
$(document).ready(function() {

    const dropArea = $('#dropArea');
    const input = $('#imageInput');
    let fileList = [];

    dropArea.on('click', function() {
        input.click();
    });

    dropArea.on('dragover', function(e) {
        e.preventDefault();
        dropArea.addClass('border-primary');
    });

    dropArea.on('dragleave', function() {
        dropArea.removeClass('border-primary');
    });

    dropArea.on('drop', function(e) {
        e.preventDefault();
        dropArea.removeClass('border-primary');
        handleFiles(e.originalEvent.dataTransfer.files);
    });

    input.on('change', function(e) {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {

            if(file.size > 2048000){
                alert('Ukuran maksimal 2MB per gambar');
                return;
            }

            fileList.push(file);

            const reader = new FileReader();
            reader.onload = function(e) {

                const preview = `
                    <div class="col-md-3 preview-card mb-3">
                        <img src="${e.target.result}">
                        <button type="button" class="remove-preview">×</button>
                        <p class="small text-muted mt-1">${file.name}</p>
                    </div>
                `;

                $('#imagePreview').append(preview);
            };

            reader.readAsDataURL(file);
        });

        syncFiles();
    }

    function syncFiles(){
        const dataTransfer = new DataTransfer();
        fileList.forEach(file => dataTransfer.items.add(file));
        input[0].files = dataTransfer.files;
    }

    $(document).on('click', '.remove-preview', function() {
        const index = $(this).closest('.preview-card').index();
        fileList.splice(index, 1);
        $(this).closest('.preview-card').remove();
        syncFiles();
    });

    // Delete existing image (edit mode)
    $(document).on('click', '.delete-existing-image', function() {

        const id = $(this).data('id');
        const card = $('#image-' + id);

        if(confirm('Hapus gambar ini?')) {

            $.ajax({
                url: '/gallery/image/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(){
                    card.fadeOut(300, function(){
                        $(this).remove();
                    });
                }
            });

        }

    });

});
</script>
@endpush
