<?php

use App\Http\Controllers\api\DashboardPoApiController;
use App\Http\Controllers\backend\AttendanceFormController;
use App\Http\Controllers\backend\AuditController;
use App\Http\Controllers\backend\CatatanSuhuRuanganController;
use App\Http\Controllers\backend\CategoryController;
use App\Http\Controllers\backend\CustomerController;
use App\Http\Controllers\backend\DashboardPoController;
use App\Http\Controllers\backend\EvaluationFormController;
use App\Http\Controllers\backend\EvaluationProgramController;
use App\Http\Controllers\backend\FontController;
use App\Http\Controllers\backend\GalleryController;
use App\Http\Controllers\backend\GudangController;
use App\Http\Controllers\backend\GudangReportController;
use App\Http\Controllers\backend\HistoryGudangController;
use App\Http\Controllers\backend\JadwalKaryawanController;
use App\Http\Controllers\backend\JenisController;
use App\Http\Controllers\backend\KaryawanController;
use App\Http\Controllers\backend\LoginController;
use App\Http\Controllers\backend\NotificationController;
use App\Http\Controllers\backend\PengendalianHamaController;
use App\Http\Controllers\backend\PinVerificationController;
use App\Http\Controllers\backend\PoConfirmationController;
use App\Http\Controllers\backend\PoexConfirmationController;
use App\Http\Controllers\backend\ProdukController;
use App\Http\Controllers\Backend\PurchaseOrderController;
use App\Http\Controllers\backend\PurchaseOrderReportController;
use App\Http\Controllers\backend\ReturController;
use App\Http\Controllers\backend\ReturDocumentController;
use App\Http\Controllers\backend\RolePermissionController;
use App\Http\Controllers\backend\SatuanController;
use App\Http\Controllers\backend\SopController;
use App\Http\Controllers\backend\SopPdfController;
use App\Http\Controllers\backend\SupplierController;
use App\Http\Controllers\backend\TagihanPoController;
use App\Http\Controllers\backend\TrainingCategoryController;
use App\Http\Controllers\backend\TrainingController;
use App\Http\Controllers\backend\TrainingProgramController;
use App\Http\Controllers\backend\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/forgot-password', [LoginController::class, 'forgotPassword'])->name('forgot-password');
Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('send-reset-link');

// Redirect root to login if not authenticated
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard - TANPA pin.verified middleware (modal akan muncul di sini)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Logout tetap bisa diakses tanpa PIN
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // PIN Routes
    Route::post('/pin/verify', [PinVerificationController::class, 'verify'])
        ->name('pin.verify');
    Route::post('/pin/logout', [PinVerificationController::class, 'logoutPin'])
        ->name('pin.logout');
});

Route::middleware(['auth', 'pin.verified'])->group(function () {

    // ========================================
    // DASHBOARD
    // ========================================
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');
    // Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // ========================================
    // SYSTEM MANAGEMENT
    // ========================================
    Route::resource('users', UserController::class);
    Route::patch('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::patch('users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    // ========================================
    // NOTIFICATIONS
    // ========================================
    Route::get('/notifications', [NotificationController::class, 'getNotifications']);
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/auto-cancel', [NotificationController::class, 'autoCancelPendingPO']);
    // ========================================
    // ROLE PERMISSON
    // ========================================
    Route::resource('role-permissions', RolePermissionController::class);
    Route::post('role-permissions/assign', [RolePermissionController::class, 'assignRole'])->name('role-permissions.assign');
    Route::post('role-permissions/remove', [RolePermissionController::class, 'removeRole'])->name('role-permissions.remove');
    // ========================================
    // MASTER DATA
    // ========================================
    Route::resource('karyawans', KaryawanController::class);
    Route::resource('jenis', JenisController::class);
    Route::resource('satuans', SatuanController::class);
    Route::resource('produks', ProdukController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('sops', SopController::class);
    Route::resource('training-programs', TrainingProgramController::class);
    Route::resource('attendance-forms', AttendanceFormController::class);
    Route::resource('evaluation-programs', EvaluationProgramController::class);
    Route::resource('gallery', GalleryController::class);
    // ========================================
    // Export PDF
    // ========================================
    Route::get('training-programs/{trainingProgram}/export-pdf', [TrainingProgramController::class, 'exportPdf'])
    ->name('training-programs.export-pdf');
    
    Route::get('attendance-forms/{attendanceForm}/export-pdf',
    [AttendanceFormController::class, 'exportPdf'])
    ->name('attendance-forms.export-pdf');
    // ========================================
    // Gallery
    // ========================================
    Route::delete('gallery/image/{image}', [GalleryController::class, 'deleteImage'])->name('gallery.image.delete');
    Route::get('/{gallery}/export-pdf', [GalleryController::class, 'exportPdf'])->name('gallery.export.pdf');
    Route::get('/{gallery}/export-pdf-chunked', [GalleryController::class, 'exportPdfChunked'])->name('gallery.export.pdf.chunked');
    // ========================================
    // Evaluation
    // ========================================
    Route::get('evaluation-programs/{evaluationProgram}/export-excel', 
        [EvaluationProgramController::class, 'exportExcel'])
        ->name('evaluation-programs.export-excel');
    Route::prefix('evaluation-programs')->name('evaluation-programs.')->group(function () {
        Route::post('/{evaluationProgram}/fill-response',      [EvaluationProgramController::class, 'fillResponse'])->name('fill-response');
        Route::get('/{evaluationProgram}/pdf/{participantId}', [EvaluationProgramController::class, 'generatePDF'])->name('generate-pdf');
        Route::get('/{evaluationProgram}/pdf-all',             [EvaluationProgramController::class, 'generatePDF'])->name('generate-all-pdf');
        Route::get('/{evaluationProgram}/export-excel',        [EvaluationProgramController::class, 'exportExcel'])->name('export-excel');
    });
    // ========================================
    // SOP
    // ========================================
    Route::prefix('sops/{sop}')->name('sops.')->group(function () {

        Route::get('/preview-edit', [SopController::class, 'previewEdit']);

        Route::get('/preview', [SopPdfController::class, 'getPreview'])->name('get-preview');
        
        // Download PDF with font settings
        Route::get('/download-pdf', [SopPdfController::class, 'downloadPdf'])->name('download-pdf');
        
        // Stream PDF (view in browser)
        Route::get('/stream-pdf', [SopPdfController::class, 'previewPdf'])->name('stream-pdf');
        // Update sections
        Route::post('/update-header', [SopController::class, 'updateHeader'])->name('update-header');
        Route::post('/update-section/{section}', [SopController::class, 'updateSection'])->name('update-section');
        Route::post('/update-approval/{approval}', [SopController::class, 'updateApproval'])->name('update-approval');
        
        // Delete sections
        Route::delete('/delete-item/{item}', [SopController::class, 'deleteItem'])->name('delete-item');
        Route::delete('/delete-section/{section}', [SopController::class, 'deleteSection'])->name('delete-section');
        Route::delete('/delete-approval/{approval}', [SopController::class, 'deleteApproval'])->name('delete-approval');
        
        // Add sections
        Route::post('/add-section', [SopController::class, 'addSection'])->name('add-section');
        Route::post('/add-approval', [SopController::class, 'addApproval'])->name('add-approval');
    });
    // ========================================
    // Jadwal Karyawan
    // ========================================
    Route::prefix('jadwal-karyawan')->name('jadwal.')->group(function () {
        // Calendar data
        Route::get('/calendar', [JadwalKaryawanController::class, 'getJadwalCalendar'])->name('calendar');
        
        // CRUD Operations
        Route::get('/by-date', [JadwalKaryawanController::class, 'getJadwalByDate'])->name('by-date');
        Route::get('/{jadwal}', [JadwalKaryawanController::class, 'show'])->name('show');
        Route::post('/', [JadwalKaryawanController::class, 'store'])->name('store');
        Route::put('/{jadwal}', [JadwalKaryawanController::class, 'update'])->name('update');
        Route::delete('/{jadwal}', [JadwalKaryawanController::class, 'destroy'])->name('destroy');
    });
    // ========================================
    // Catatan Suhu
    // ========================================
    Route::prefix('catatan-suhu')->name('catatan-suhu.')->group(function () {

        // Periode (parent)
        Route::get('create',[CatatanSuhuRuanganController::class, 'create'])->name('create');
        Route::post('periode',[CatatanSuhuRuanganController::class, 'storePeriode'])->name('periode.store');
        Route::get('{kontrolGudang}/edit',[CatatanSuhuRuanganController::class, 'editPeriode'])->name('edit-periode');
        Route::put('{kontrolGudang}/periode',[CatatanSuhuRuanganController::class, 'updatePeriode'])->name('update-periode');
        Route::delete('{kontrolGudang}/periode',[CatatanSuhuRuanganController::class, 'destroyPeriode'])->name('periode.destroy');

        Route::get('{kontrolGudang}',[CatatanSuhuRuanganController::class, 'show'])->name('show');

        Route::post('/',[CatatanSuhuRuanganController::class, 'store'])->name('store');
        Route::put('catatan/{catatanSuhu}',[CatatanSuhuRuanganController::class, 'update'])->name('update');
        Route::delete('catatan/{catatanSuhu}',[CatatanSuhuRuanganController::class, 'destroy'])->name('destroy');
        Route::get('/catatan-suhu/{kontrolGudang}/export-pdf', [CatatanSuhuRuanganController::class, 'exportPdf'])
        ->name('export-pdf');
    });
    // ========================================
    // Pengendalian hama
    // ========================================
    Route::resource('pengendalian-hama', PengendalianHamaController::class);
    Route::get('/pengendalian-hama/{pengendalianHama}/export-pdf', [PengendalianHamaController::class, 'exportPdf'])
    ->name('pengendalian-hama.export-pdf');
    Route::delete('pengendalian-hama-gambar/{gambar}', [PengendalianHamaController::class, 'destroyGambar'])
        ->name('pengendalian-hama.gambar.destroy');
    // ========================================
    // Supplier
    // ========================================
    Route::resource('suppliers', SupplierController::class);
    // ========================================
    // GUDANG
    // ========================================
    Route::resource('gudangs', GudangController::class);
    Route::get('/gudang/barang/{barangId}/detail', [GudangController::class, 'getDetailGudangByBarang']);
    Route::post('/gudang/penerimaan', [GudangController::class, 'prosesPenerimaan'])->name('gudangs.penerimaan');
    Route::get('/gudang/{id}/details/data', [GudangController::class, 'detailsData']);
    Route::get('/supplier/{supplier}/details', [GudangController::class, 'getSupplierDetails']);
    Route::get('/gudang/{gudangId}/history', [HistoryGudangController::class, 'index'])
        ->name('gudang.history');
    Route::post('/history-gudang/filter', [HistoryGudangController::class, 'filter'])
        ->name('history-gudang.filter');
    Route::get('/history-gudang/export-excel', [HistoryGudangController::class, 'exportExcel'])
        ->name('history-gudang.export-excel');
    Route::get('/history-gudang/export-pdf', [HistoryGudangController::class, 'exportPdf'])
        ->name('history-gudang.export-pdf');
    Route::get('/history-gudang/{id}', [HistoryGudangController::class, 'show'])
        ->name('history-gudang.show');
    Route::post('/history-gudang/summary', [HistoryGudangController::class, 'summary'])
        ->name('history-gudang.summary');
    Route::get('/stock', [GudangController::class, 'stockGudang'])->name('gudang.stock');
    // ========================================
    // PURCHASE ORDER
    // ========================================
    Route::resource('po', PurchaseOrderController::class)->parameters([
        'po' => 'id_po'
    ]);

    Route::prefix('po')->name('po.')->group(function () {
        Route::post('{id_po}/submit', [PurchaseOrderController::class, 'submit'])->name('submit');
        Route::post('{id_po}/approve-kepala-gudang', [PurchaseOrderController::class, 'approveKepalaGudang'])->name('approve.kepala-gudang');
        Route::post('{id_po}/approve-kasir', [PurchaseOrderController::class, 'approveKasir'])->name('approve.kasir');
        Route::post('{id_po}/send-to-supplier', [PurchaseOrderController::class, 'sendToSupplier'])->name('send-to-supplier');
        Route::get('{id_po}/print', [PurchaseOrderController::class, 'print'])->name('print');
        Route::get('/po/{id_po}/print-invoice', [PurchaseOrderController::class, 'printInvoice'])->name('print-invoice');
        Route::get('{id_po}/confirm-receipt-internal', [PoConfirmationController::class, 'showConfirmation'])
            ->name('show-confirmation');
        Route::post('{id_po}/confirm-receipt-internal', [PoConfirmationController::class, 'confirmReceipt'])
            ->name('confirm-receipt');
        Route::get('{id_po}/confirm-receipt-external', [PoexConfirmationController::class, 'showConfirmation'])
            ->name('showex-confirmation');
        Route::post('{id_po}/confirm-receipt-external', [PoexConfirmationController::class, 'confirmReceipt'])
            ->name('confirmex-receipt');
        Route::get('/po/{id_po}/invoice-form', [PoexConfirmationController::class, 'showInvoiceForm'])
            ->name('invoice-form');
        Route::post('/po/{id_po}/store-invoice', [PoexConfirmationController::class, 'storeInvoice'])
            ->name('store-invoice');
        Route::post('/po/{id_po}/mark-received', [PurchaseOrderController::class, 'markAsReceived'])
        ->name('mark-received');
        Route::post('/{id_po}/upload-proof', [PurchaseOrderController::class, 'uploadProof'])
            ->name('upload-proof');
        Route::post('/po/{id_po}/delete-proof', [PurchaseOrderController::class, 'deleteProof'])
            ->name('delete-invoice-proof');
    });

    // ========================================
    // Audit
    // ========================================
    Route::prefix('audits')->name('audits.')->group(function () {
        // List & Create
        Route::get('/', [AuditController::class, 'index'])->name('index');
        Route::get('/create', [AuditController::class, 'create'])->name('create');
        Route::post('/', [AuditController::class, 'store'])->name('store');
        
        // Show & Update Response (Halaman utama untuk mengisi audit)
        Route::get('/{audit}', [AuditController::class, 'show'])->name('show');
        Route::post('/{audit}/questions/{question}/response', [AuditController::class, 'updateResponse'])->name('updateResponse');
        
        // Delete files
        Route::delete('/{audit}/questions/{question}/document', [AuditController::class, 'deleteDocument'])->name('deleteDocument');
        Route::delete('/{audit}/questions/{question}/image', [AuditController::class, 'deleteImage'])->name('deleteImage');
        
        // Complete & Report
        Route::post('/{audit}/complete', [AuditController::class, 'complete'])->name('complete');
        Route::get('/{audit}/report', [AuditController::class, 'report'])->name('report');
        
        // Delete
        Route::delete('/{audit}', [AuditController::class, 'destroy'])->name('destroy');
    });
    // ========================================
    // Retur
    // ========================================
    Route::prefix('returs')->name('returs.')->group(function () {
        // Resource routes (index, create, store, show, edit, update, destroy)
        Route::get('/', [ReturController::class, 'index'])->name('index');
        Route::get('/create', [ReturController::class, 'create'])->name('create');
        Route::post('/', [ReturController::class, 'store'])->name('store');
        Route::get('/{id}', [ReturController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ReturController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ReturController::class, 'update'])->name('update');
        Route::delete('/{id}', [ReturController::class, 'destroy'])->name('destroy');
        
        // Action routes
        Route::post('/{id}/submit', [ReturController::class, 'submit'])->name('submit');
        Route::post('/{id}/approve', [ReturController::class, 'approve'])->name('approve');
        Route::post('/{id}/process', [ReturController::class, 'process'])->name('process');
        Route::post('/{id}/complete', [ReturController::class, 'complete'])->name('complete');
        Route::post('/{id}/cancel', [ReturController::class, 'cancel'])->name('cancel');
        
        // Documents
        Route::post('/{id}/documents/upload', [ReturDocumentController::class, 'upload'])->name('documents.upload');
        Route::get('/{id}/documents', [ReturDocumentController::class, 'index'])->name('documents.index');
        Route::delete('/documents/{id}', [ReturDocumentController::class, 'destroy'])->name('documents.destroy');
    });
    // ========================================
    // Tagihan
    // ========================================
    Route::prefix('tagihan')->name('tagihan.')->middleware(['auth'])->group(function () {
        Route::get('/', [TagihanPoController::class, 'index'])->name('index');
        Route::get('/load-tab', [TagihanPoController::class, 'loadTabData'])->name('load-tab');
        Route::get('/{id_tagihan}', [TagihanPoController::class, 'show'])->name('show');
        Route::get('/{id_tagihan}/payment', [TagihanPoController::class, 'showPaymentForm'])->name('payment.form');
        Route::post('/{id_tagihan}/payment', [TagihanPoController::class, 'processPayment'])->name('payment.process');
        Route::get('/{id_tagihan}/payment-history', [TagihanPoController::class, 'paymentHistory'])->name('payment.history');
        // Route::post('/payment/{id_pembayaran}/verify', [TagihanPoController::class, 'verifyPayment'])->name('payment.verify');
        Route::get('/payment/{id_pembayaran}/download', [TagihanPoController::class, 'downloadBukti'])->name('payment.download');
        Route::get('/{id_tagihan}/print', [TagihanPoController::class, 'print'])->name('print');
    });
    Route::prefix('reports')->name('reports.')->middleware(['auth'])->group(function () {
        // Purchase Order Reports
        Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
            Route::get('/', [PurchaseOrderReportController::class, 'index'])->name('index');
            Route::get('/{id}', [PurchaseOrderReportController::class, 'show'])->name('show');
            Route::get('/summary', [PurchaseOrderReportController::class, 'summary'])->name('summary');
        });
        Route::prefix('gudangs')->name('gudangs.')->group(function () {
            Route::get('/gudang', [GudangReportController::class, 'index'])->name('index');
            Route::get('/gudang/print', [GudangReportController::class, 'print'])->name('print');
            Route::get('/gudang/export-excel', [GudangReportController::class, 'exportExcel'])->name('export-excel');
            Route::get('/gudang/export-pdf', [GudangReportController::class, 'exportPdf'])->name('export-pdf');
        });
    });
    // ==========================================
    // DASHBOARD Purchase Order
    // ==========================================
    Route::prefix('dashboardpo')->name('dashboardpo.')->group(function () {
        Route::get('/', [DashboardPoController::class, 'index'])->name('index');
        Route::get('/realtime-stats', [DashboardPoController::class, 'getRealtimeStats'])->name('realtime-stats');
        Route::get('/export', [DashboardPoController::class, 'exportData'])->name('export');
    });
    Route::get('/purchase-orders/pending', [PurchaseOrderController::class, 'pending'])->name('po.pending');
    Route::get('/tagihan/overdue', [TagihanPoController::class, 'overdue'])->name('tagihan.overdue');
    Route::get('/tagihan/due-soon', [TagihanPoController::class, 'dueSoon'])->name('tagihan.due-soon');
    Route::get('/purchase-orders/need-invoice', [PurchaseOrderController::class, 'needInvoice'])->name('po.need-invoice');
    Route::get('/purchase-orders/need-confirmation', [PurchaseOrderController::class, 'needConfirmation'])->name('po.need-confirmation');
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('po.index');
    Route::get('/tagihan', [TagihanPoController::class, 'index'])->name('tagihan.index');
    
    // Route::get('/pembayaran', [PembayaranTagihanController::class, 'index'])->name('pembayaran.index');
    Route::get('/purchase-orders/{id}', [PurchaseOrderController::class, 'show'])->name('po.show');
    Route::get('/tagihan/{id}', [TagihanPoController::class, 'show'])->name('tagihan.show');
    // Route::get('/pembayaran/{id}', [PembayaranTagihanController::class, 'show'])->name('pembayaran.show');
    Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/stats', [DashboardPoApiController::class, 'getStats'])->name('stats');
        Route::get('/chart-data', [DashboardPoApiController::class, 'getChartData'])->name('chart-data');
    });
});