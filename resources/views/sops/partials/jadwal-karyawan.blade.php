{{-- Jadwal Karyawan Section --}}
<div class="row">
    {{-- LEFT: Calendar --}}
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="ri-calendar-line me-2"></i>Kalender Jadwal</h6>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-light btn-sm" id="prevMonth">
                        <i class="ri-arrow-left-s-line"></i>
                    </button>
                    <button type="button" class="btn btn-light btn-sm" id="todayBtn">Hari Ini</button>
                    <button type="button" class="btn btn-light btn-sm" id="nextMonth">
                        <i class="ri-arrow-right-s-line"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
            <div class="card-footer bg-light">
                <small class="text-muted d-block mb-2"><strong>Keterangan Warna:</strong></small>
                <div class="d-flex gap-3 flex-wrap">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2">●</span>
                        <small>Scheduled</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-warning me-2">●</span>
                        <small>Ongoing</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">●</span>
                        <small>Completed</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-danger me-2">●</span>
                        <small>Cancelled</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Form --}}
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0" id="formTitle">
                    <i class="ri-add-circle-line me-2"></i>Tambah Jadwal Baru
                </h6>
            </div>
            <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                <form id="jadwalForm" enctype="multipart/form-data">
                    <input type="hidden" id="jadwalId" name="jadwal_id">
                    <input type="hidden" id="formMode" value="create">
                    
                    {{-- Info Acara --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Acara <span class="text-danger">*</span></label>
                        <input type="text" name="nama_acara" id="nama_acara" class="form-control" 
                               placeholder="e.g. Training Karyawan Baru" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2" 
                                  placeholder="Deskripsi singkat acara..."></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" id="waktu_mulai" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" id="waktu_selesai" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Lokasi</label>
                            <input type="text" name="lokasi" id="lokasi" class="form-control" 
                                   placeholder="e.g. Ruang Meeting A">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="scheduled">Scheduled</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    {{-- Daftar Karyawan --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label fw-bold mb-0">Daftar Karyawan (Opsional)</label>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addKaryawan">
                            <i class="ri-add-line me-1"></i>Tambah Karyawan
                        </button>
                    </div>

                    <div id="karyawanContainer">
                        {{-- Karyawan items will be added here --}}
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-3">
                        <small><i class="ri-information-line me-1"></i>
                            Karyawan dapat ditambahkan sekarang atau nanti setelah jadwal dibuat.
                        </small>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="ri-save-line me-1"></i>Simpan Jadwal
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="resetFormBtn">
                            <i class="ri-refresh-line me-1"></i>Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Preview Bukti --}}
<div class="modal fade" id="buktiModal" tabindex="-1" aria-labelledby="buktiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buktiModalLabel">
                    <i class="ri-image-line me-2"></i>Preview Bukti - <span id="modalKaryawanName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalBuktiImage" class="img-fluid" style="max-height: 70vh; object-fit: contain;">
            </div>
            <div class="modal-footer">
                <a href="" id="downloadBukti" class="btn btn-primary" download>
                    <i class="ri-download-line me-1"></i>Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    #calendar {
        max-width: 100%;
        margin: 0 auto;
    }

    .fc-event {
        cursor: pointer;
        padding: 2px 4px;
        font-size: 0.85rem;
    }

    .fc-daygrid-day:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .fc-day-today {
        background-color: #fff3cd !important;
    }

    .schedule-count-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        line-height: 18px;
        text-align: center;
        font-size: 10px;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        margin-right: 4px;
        vertical-align: middle;
    }

    .schedule-count-badge.status-scheduled {
        background: #0d6efd;
    }

    .schedule-count-badge.status-ongoing {
        background: #ffc107;
    }

    .schedule-count-badge.status-completed {
        background: #198754;
    }

    .schedule-count-badge.status-cancelled {
        background: #dc3545;
    }

    .schedule-count-badge.status-multiple {
        background: linear-gradient(135deg, #0d6efd, #ffc107, #198754);
    }

    .fc-daygrid-day.has-schedule {
        background-color: #f0f4ff !important;
        position: relative;
    }

    .fc-daygrid-day.has-schedule .fc-daygrid-day-number {
        font-weight: 600;
    }

    .karyawan-item {
        border-left: 3px solid #0d6efd;
        transition: all 0.3s ease;
    }

    .karyawan-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .karyawan-item .card-body {
        background-color: #f8f9fa;
    }

    .bukti-thumbnail {
        transition: transform 0.2s;
        border: 2px solid #dee2e6;
    }

    .bukti-thumbnail:hover {
        transform: scale(1.05);
        border-color: #0d6efd;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .bukti-preview-container {
        margin-top: 8px;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .swal2-popup {
        font-size: 0.9rem;
    }

    .jadwal-list-item {
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 8px;
    }

    .jadwal-list-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    @media (max-width: 768px) {
        .schedule-count-badge {
            width: 16px;
            height: 16px;
            line-height: 16px;
            font-size: 9px;
        }
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
$(document).ready(function() {
    let calendar;
    let karyawanCounter = 0;
    let scheduleCounts = {};

    // Initialize Calendar
    initCalendar();

    // Set default date
    if ($('#formMode').val() === 'create' && !$('#tanggal').val()) {
        const today = new Date().toISOString().split('T')[0];
        $('#tanggal').val(today);
    }

    function initCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'title',
                center: '',
                right: ''
            },
            eventDisplay: 'none',
            locale: 'id',
            firstDay: 1,
            height: 'auto',
            events: function(info, successCallback, failureCallback) {
                loadJadwalCalendar(info.start, info.end, successCallback, failureCallback);
            },
            dateClick: function(info) {
                handleDateClick(info.dateStr);
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                handleEventClick(info.event.id);
            },
            loading: function(isLoading) {
                if (!isLoading) {
                    setTimeout(renderAllBadges, 100);
                }
            }
        });

        calendar.render();
    }

    function loadJadwalCalendar(startDate, endDate, successCallback, failureCallback) {
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        $.ajax({
            url: '/jadwal-karyawan/calendar',
            method: 'GET',
            data: { 
                start: formatDate(startDate),
                end: formatDate(endDate)
            },
            success: function(events) {
                calculateScheduleCounts(events);
                successCallback([]);
                setTimeout(renderAllBadges, 50);
            },
            error: function(xhr) {
                console.error('Error loading calendar:', xhr);
                scheduleCounts = {};
                failureCallback();
            }
        });
    }

    function calculateScheduleCounts(events) {
        scheduleCounts = {};
        
        events.forEach(function(event) {
            const dateStr = event.start;
            
            if (!scheduleCounts[dateStr]) {
                scheduleCounts[dateStr] = {
                    count: 0,
                    statuses: []
                };
            }
            
            scheduleCounts[dateStr].count++;
            scheduleCounts[dateStr].statuses.push(event.extendedProps.status);
        });
    }

    function renderAllBadges() {
        $('.schedule-count-badge').remove();
        $('.fc-daygrid-day').removeClass('has-schedule');
        
        $('.fc-daygrid-day').each(function() {
            const dateStr = $(this).attr('data-date');
            
            if (!dateStr) return;
            
            if (scheduleCounts[dateStr] && scheduleCounts[dateStr].count > 0) {
                const count = scheduleCounts[dateStr].count;
                const statuses = scheduleCounts[dateStr].statuses;
                
                const uniqueStatuses = [...new Set(statuses)];
                let statusClass = 'status-multiple';
                
                if (uniqueStatuses.length === 1) {
                    statusClass = 'status-' + uniqueStatuses[0];
                }
                
                $(this).addClass('has-schedule');
                
                const badge = $('<span>')
                    .addClass('schedule-count-badge')
                    .addClass(statusClass)
                    .text(count)
                    .attr('title', count + ' jadwal');
                
                const dayNumber = $(this).find('.fc-daygrid-day-number');
                if (dayNumber.length > 0) {
                    dayNumber.prepend(badge);
                }
            }
        });
    }

    function handleDateClick(dateStr) {
        $.ajax({
            url: '/jadwal-karyawan/by-date',
            method: 'GET',
            data: { date: dateStr },
            success: function(jadwals) {
                if (jadwals.length > 0) {
                    showJadwalList(jadwals, dateStr);
                } else {
                    resetForm();
                    $('#tanggal').val(dateStr);
                    $('#formMode').val('create');
                    $('#formTitle').html('<i class="ri-add-circle-line me-2"></i>Tambah Jadwal Baru');
                    $('.card-header').removeClass('bg-warning').addClass('bg-success');
                }
            },
            error: function() {
                resetForm();
                $('#tanggal').val(dateStr);
            }
        });
    }

    function showJadwalList(jadwals, dateStr) {
        const formatDate = new Date(dateStr).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        let listHTML = `<div class="mb-3"><strong>Jadwal pada ${formatDate}:</strong></div>`;
        
        jadwals.forEach(function(jadwal) {
            const statusBadge = getStatusBadge(jadwal.status);
            const waktu = jadwal.waktu_mulai ? `<small class="text-muted"><i class="ri-time-line"></i> ${jadwal.waktu_mulai.substring(0,5)}</small>` : '';
            const peserta = jadwal.peserta && jadwal.peserta.length > 0 ? 
                `<small class="text-muted"><i class="ri-team-line"></i> ${jadwal.peserta.length} orang</small>` : 
                '<small class="text-muted"><i class="ri-team-line"></i> Belum ada peserta</small>';
            
            listHTML += `
                <div class="jadwal-list-item border rounded p-3 mb-2" data-jadwal-id="${jadwal.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${jadwal.nama_acara}</h6>
                            <div class="d-flex gap-3">
                                ${waktu}
                                ${peserta}
                            </div>
                        </div>
                        <div>
                            ${statusBadge}
                        </div>
                    </div>
                </div>
            `;
        });

        listHTML += `
            <div class="text-center mt-3">
                <button type="button" class="btn btn-sm btn-outline-primary" id="createNewOnDate" data-date="${dateStr}">
                    <i class="ri-add-line me-1"></i>Buat Jadwal Baru di Tanggal Ini
                </button>
            </div>
        `;

        Swal.fire({
            title: '<strong>Pilih Jadwal</strong>',
            html: listHTML,
            showConfirmButton: false,
            showCloseButton: true,
            width: '600px'
        });

        $(document).on('click', '.jadwal-list-item', function() {
            const jadwalId = $(this).data('jadwal-id');
            Swal.close();
            handleEventClick(jadwalId);
        });

        $(document).on('click', '#createNewOnDate', function() {
            const date = $(this).data('date');
            Swal.close();
            resetForm();
            $('#tanggal').val(date);
        });
    }

    function getStatusBadge(status) {
        const badges = {
            'scheduled': '<span class="badge bg-primary">Scheduled</span>',
            'ongoing': '<span class="badge bg-warning">Ongoing</span>',
            'completed': '<span class="badge bg-success">Completed</span>',
            'cancelled': '<span class="badge bg-danger">Cancelled</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
    }

    function handleEventClick(eventId) {
        $.ajax({
            url: `/jadwal-karyawan/${eventId}`,
            method: 'GET',
            success: function(jadwal) {
                fillFormWithData(jadwal);
                
                $('html, body').animate({
                    scrollTop: $('#jadwalForm').offset().top - 100
                }, 500);
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal memuat data jadwal',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    function fillFormWithData(jadwal) {
        $('#formMode').val('edit');
        $('#jadwalId').val(jadwal.id);
        
        let tanggalValue = jadwal.tanggal;
        if (tanggalValue && tanggalValue.length === 10) {
            $('#tanggal').val(tanggalValue);
        }
        
        $('#nama_acara').val(jadwal.nama_acara);
        $('#deskripsi').val(jadwal.deskripsi);
        $('#waktu_mulai').val(jadwal.waktu_mulai ? jadwal.waktu_mulai.substring(0, 5) : '');
        $('#waktu_selesai').val(jadwal.waktu_selesai ? jadwal.waktu_selesai.substring(0, 5) : '');
        $('#lokasi').val(jadwal.lokasi);
        $('#status').val(jadwal.status);

        $('#formTitle').html('<i class="ri-edit-line me-2"></i>Edit Jadwal');
        $('.card-header').removeClass('bg-success').addClass('bg-warning');

        $('#karyawanContainer').html('');
        karyawanCounter = 0;
        
        if (jadwal.peserta && jadwal.peserta.length > 0) {
            jadwal.peserta.forEach(function(peserta) {
                addKaryawanItem(peserta);
            });
        }
    }

    $('#addKaryawan').click(function() {
        addKaryawanItem();
    });

    function addKaryawanItem(data = null) {
        const index = karyawanCounter;
        const itemHTML = `
            <div class="karyawan-item card mb-2" data-index="${index}">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-primary">Karyawan ${index + 1}</strong>
                        <button type="button" class="btn btn-sm btn-outline-danger removeKaryawan">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Nama Karyawan</label>
                        <input type="text" name="peserta[${index}][nama_karyawan]" class="form-control form-control-sm" 
                               value="${data ? data.nama_karyawan : ''}" placeholder="Nama lengkap karyawan">
                        ${data ? `<input type="hidden" name="peserta[${index}][id]" value="${data.id}">` : ''}
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Catatan</label>
                        <textarea name="peserta[${index}][catatan]" class="form-control form-control-sm" rows="2" 
                                  placeholder="Catatan evaluasi...">${data ? data.catatan || '' : ''}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold">Nilai Kinerja (1-100)</label>
                            <input type="number" name="peserta[${index}][nilai]" class="form-control form-control-sm" 
                                   value="${data ? data.nilai || '' : ''}" min="0" max="100" placeholder="0-100">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold">Status Kehadiran</label>
                            <select name="peserta[${index}][status_kehadiran]" class="form-select form-select-sm">
                                <option value="hadir" ${data && data.status_kehadiran === 'hadir' ? 'selected' : ''}>Hadir</option>
                                <option value="tidak_hadir" ${data && data.status_kehadiran === 'tidak_hadir' ? 'selected' : ''}>Tidak Hadir</option>
                                <option value="izin" ${data && data.status_kehadiran === 'izin' ? 'selected' : ''}>Izin</option>
                                <option value="sakit" ${data && data.status_kehadiran === 'sakit' ? 'selected' : ''}>Sakit</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Upload Bukti (Gambar)</label>
                        <input type="file" name="peserta[${index}][bukti]" class="form-control form-control-sm bukti-upload" 
                               accept="image/*" data-index="${index}">
                        <small class="text-muted">Format: JPG, PNG, GIF (Max: 2MB)</small>
                        ${data && data.bukti ? `
                            <div class="mt-2 bukti-preview-container" id="bukti-preview-${index}">
                                <img src="/storage/${data.bukti}" class="img-thumbnail bukti-thumbnail" 
                                    style="max-width: 150px; cursor: pointer;" 
                                    onclick="showBuktiModal('/storage/${data.bukti}', '${data.nama_karyawan}')">
                                <button type="button" class="btn btn-sm btn-danger mt-1 remove-bukti" data-index="${index}">
                                    <i class="ri-delete-bin-line"></i> Hapus Bukti
                                </button>
                            </div>
                        ` : `
                            <div class="mt-2 bukti-preview-container" id="bukti-preview-${index}" style="display: none;">
                                <img src="" class="img-thumbnail bukti-thumbnail" style="max-width: 150px; cursor: pointer;">
                                <button type="button" class="btn btn-sm btn-danger mt-1 remove-bukti" data-index="${index}">
                                    <i class="ri-delete-bin-line"></i> Hapus Bukti
                                </button>
                            </div>
                        `}
                    </div>
                </div>
            </div>
        `;

        $('#karyawanContainer').append(itemHTML);
        karyawanCounter++;
        updateKaryawanNumbers();
    }

    // Handle bukti upload preview
    $(document).on('change', '.bukti-upload', function(e) {
        const index = $(this).data('index');
        const file = e.target.files[0];
        
        if (file) {
            // Validasi ukuran file (max 2MB)
            if (file.size > 2048000) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 2MB',
                    timer: 2000,
                    showConfirmButton: false
                });
                $(this).val('');
                return;
            }
            
            // Validasi tipe file
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Salah',
                    text: 'Hanya menerima file gambar (JPG, PNG, GIF)',
                    timer: 2000,
                    showConfirmButton: false
                });
                $(this).val('');
                return;
            }
            
            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewContainer = $(`#bukti-preview-${index}`);
                const img = previewContainer.find('.bukti-thumbnail');
                
                img.attr('src', e.target.result);
                img.attr('onclick', `showBuktiModal('${e.target.result}', 'Preview Bukti')`);
                previewContainer.show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle remove bukti
    $(document).on('click', '.remove-bukti', function() {
        const index = $(this).data('index');
        
        Swal.fire({
            title: 'Hapus Bukti?',
            text: 'Bukti gambar akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Clear file input
                $(`input[name="peserta[${index}][bukti]"]`).val('');
                
                // Hide preview
                $(`#bukti-preview-${index}`).hide();
                $(`#bukti-preview-${index} img`).attr('src', '');
                
                // Set flag untuk hapus bukti di backend (untuk mode edit)
                if ($('#formMode').val() === 'edit') {
                    $(`<input type="hidden" name="peserta[${index}][remove_bukti]" value="1">`).insertAfter(`input[name="peserta[${index}][bukti]"]`);
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Bukti dihapus!',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });

    // Function to show modal (global function)
    window.showBuktiModal = function(imageUrl, karyawanName) {
        $('#modalKaryawanName').text(karyawanName);
        $('#modalBuktiImage').attr('src', imageUrl);
        $('#downloadBukti').attr('href', imageUrl);
        
        const modal = new bootstrap.Modal(document.getElementById('buktiModal'));
        modal.show();
    };

    $(document).on('click', '.removeKaryawan', function() {
        $(this).closest('.karyawan-item').fadeOut(300, function() {
            $(this).remove();
            updateKaryawanNumbers();
        });
    });

    function updateKaryawanNumbers() {
        $('.karyawan-item').each(function(index) {
            $(this).find('strong').first().text(`Karyawan ${index + 1}`);
        });
    }

    // Submit form with FormData for file upload
    // Submit form
    $('#jadwalForm').submit(function(e) {
        e.preventDefault();
        
        const mode = $('#formMode').val();
        const jadwalId = $('#jadwalId').val();
        
        // PERBAIKAN: Gunakan FormData untuk handle file upload
        const formData = new FormData();
        
        // Add basic fields
        formData.append('tanggal', $('#tanggal').val());
        formData.append('nama_acara', $('#nama_acara').val());
        formData.append('deskripsi', $('#deskripsi').val());
        formData.append('waktu_mulai', $('#waktu_mulai').val());
        formData.append('waktu_selesai', $('#waktu_selesai').val());
        formData.append('lokasi', $('#lokasi').val());
        formData.append('status', $('#status').val());
        
        // Add peserta data with files
        let pesertaIndex = 0;
        $('.karyawan-item').each(function() {
            const index = $(this).data('index');
            const namaKaryawan = $(`input[name="peserta[${index}][nama_karyawan]"]`).val();
            
            if (namaKaryawan) {
                formData.append(`peserta[${pesertaIndex}][nama_karyawan]`, namaKaryawan);
                formData.append(`peserta[${pesertaIndex}][catatan]`, $(`textarea[name="peserta[${index}][catatan]"]`).val() || '');
                formData.append(`peserta[${pesertaIndex}][nilai]`, $(`input[name="peserta[${index}][nilai]"]`).val() || '');
                formData.append(`peserta[${pesertaIndex}][status_kehadiran]`, $(`select[name="peserta[${index}][status_kehadiran]"]`).val());
                
                // IMPORTANT: Add peserta ID if exists (for edit mode)
                const pesertaId = $(`input[name="peserta[${index}][id]"]`).val();
                if (pesertaId) {
                    formData.append(`peserta[${pesertaIndex}][id]`, pesertaId);
                }
                
                // IMPORTANT: Add file if exists
                const fileInput = $(`input[name="peserta[${index}][bukti]"]`)[0];
                if (fileInput && fileInput.files && fileInput.files[0]) {
                    formData.append(`peserta[${pesertaIndex}][bukti]`, fileInput.files[0]);
                }
                
                // Add remove_bukti flag if exists
                const removeBukti = $(`input[name="peserta[${index}][remove_bukti]"]`).val();
                if (removeBukti) {
                    formData.append(`peserta[${pesertaIndex}][remove_bukti]`, '1');
                }
                
                pesertaIndex++;
            }
        });
        
        const url = mode === 'edit' ? `/jadwal-karyawan/${jadwalId}` : '/jadwal-karyawan';
        
        // IMPORTANT: Add _method for PUT request (Laravel)
        if (mode === 'edit') {
            formData.append('_method', 'PUT');
        }

        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: url,
            method: 'POST', // ALWAYS POST when using FormData
            data: formData,
            processData: false, // CRITICAL: Don't process the data
            contentType: false, // CRITICAL: Don't set content type
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                calendar.refetchEvents();
                resetForm();
            },
            error: function(xhr) {
                let errorMessage = 'Gagal menyimpan jadwal';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // Tampilkan error validation jika ada
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    let errorList = '<ul style="text-align: left;">';
                    Object.keys(errors).forEach(function(key) {
                        errorList += '<li>' + errors[key][0] + '</li>';
                    });
                    errorList += '</ul>';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal!',
                        html: errorList,
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    });

    $('#resetFormBtn').click(function() {
        Swal.fire({
            title: 'Reset Form?',
            text: 'Semua data yang belum disimpan akan hilang',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                resetForm();
                Swal.fire({
                    icon: 'success',
                    title: 'Form direset!',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });

    function resetForm() {
        $('#jadwalForm')[0].reset();
        $('#formMode').val('create');
        $('#jadwalId').val('');
        $('#formTitle').html('<i class="ri-add-circle-line me-2"></i>Tambah Jadwal Baru');
        $('.card-header').removeClass('bg-warning').addClass('bg-success');
        
        $('#karyawanContainer').html('');
        karyawanCounter = 0;
        
        // Set default date
        const today = new Date().toISOString().split('T')[0];
        $('#tanggal').val(today);
    }

    $('#prevMonth').click(function() {
        calendar.prev();
    });

    $('#nextMonth').click(function() {
        calendar.next();
    });

    $('#todayBtn').click(function() {
        calendar.today();
    });

    window.refreshCalendar = function() {
        if (calendar) {
            calendar.refetchEvents();
        }
    };
});
</script>
@endpush