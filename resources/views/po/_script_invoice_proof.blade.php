<script>
// ============================================
// INVOICE PROOF MODAL FUNCTIONS
// ============================================

function showInvoiceProofModal() {
    const modalElement = document.getElementById('invoiceProofModal');
    const modal = new bootstrap.Modal(modalElement);
    
    const hasInvoice = {{ $po->hasInvoiceProof() ? 'true' : 'false' }};
    const hasBarang = {{ $po->hasBarangProof() ? 'true' : 'false' }};
    
    if (hasInvoice || hasBarang) {
        document.getElementById('invoiceProofModalTitle').textContent = 'Bukti Invoice & Barang';
        document.getElementById('invoiceProofPreview').style.display = 'block';
    }
    
    modal.show();
}

function changeProof(type) {
    // Redirect ke halaman upload
    window.location.href = '{{ route("po.upload-proof", $po->id_po) }}?type=' + type;
}

function confirmDeleteProof(type, proofId = null) {
    bootstrap.Modal.getInstance(document.getElementById('invoiceProofModal')).hide();
    
    const title = type === 'invoice' ? 'Bukti Invoice' : 'Bukti Barang';
    const message = proofId 
        ? `Hapus ${title} ini?` 
        : `Hapus semua ${title}?`;
    
    Swal.fire({
        title: message,
        html: `
            <div class="text-start">
                <div class="alert alert-warning mb-3">
                    <i class="ri-alert-line me-2"></i>
                    <strong>Peringatan:</strong> File yang dihapus tidak dapat dikembalikan!
                </div>
                <p class="mb-3">Untuk melanjutkan, masukkan PIN Anda:</p>
                <div class="d-flex justify-content-center gap-2 mb-2" id="deleteOtpContainer">
                    <input type="password" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                    <input type="password" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                    <input type="password" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                    <input type="password" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                    <input type="password" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                    <input type="password" class="delete-otp-input form-control text-center" maxlength="1" style="width: 45px; height: 45px; font-size: 1.25rem; font-weight: 600;">
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ri-delete-bin-line me-1"></i> Ya, Hapus!',
        cancelButtonText: '<i class="ri-close-line me-1"></i> Batal',
        showLoaderOnConfirm: true,
        didOpen: () => {
            const deleteInputs = document.querySelectorAll('.delete-otp-input');
            
            deleteInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    if (!/^\d$/.test(value)) {
                        e.target.value = '';
                        return;
                    }
                    this.classList.add('filled');
                    if (value && index < deleteInputs.length - 1) {
                        deleteInputs[index + 1].focus();
                    }
                });
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        deleteInputs[index - 1].focus();
                        deleteInputs[index - 1].value = '';
                        deleteInputs[index - 1].classList.remove('filled');
                    }
                });
                
                input.addEventListener('focus', function() { this.select(); });
            });
            
            setTimeout(() => deleteInputs[0].focus(), 100);
        },
        preConfirm: () => {
            const deleteInputs = document.querySelectorAll('.delete-otp-input');
            const pin = Array.from(deleteInputs).map(input => input.value).join('');
            
            if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
                Swal.showValidationMessage('PIN harus 6 digit angka');
                return false;
            }
            
            const requestData = { 
                pin: pin, 
                type: type 
            };
            
            if (proofId) {
                requestData.id_proof = proofId;
            }
            
            return fetch(`/po/{{ $po->id_po }}/delete-proof`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json().then(data => ({ status: response.status, data: data })))
            .then(({status, data}) => {
                if (status === 403) throw new Error('PIN tidak valid');
                if (status !== 200) throw new Error(data.error || 'Gagal menghapus file');
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(error.message);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.value.message,
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            // Kembali ke modal preview jika batal
            setTimeout(() => showInvoiceProofModal(), 100);
        }
    });
}

// Function untuk menampilkan carousel slide tertentu
function showInCarousel(carouselId, slideIndex) {
    const carousel = document.getElementById(carouselId);
    if (carousel) {
        const bsCarousel = bootstrap.Carousel.getOrCreateInstance(carousel);
        bsCarousel.to(slideIndex);
        
        // Scroll ke carousel
        carousel.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}
</script>