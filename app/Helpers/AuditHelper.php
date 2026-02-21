<?php

namespace App\Helpers;

class AuditHelper
{
    public static function getSopLink(string $questionNumber): ?array
    {
        $baseUrl = '/sops';

        $map = [
            // ─────────────── KATEGORI 1: Sistem Manajemen Mutu ───────────────
            '1.1' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Lihat Struktur Org.',
                'icon'    => 'organization-chart',
                'new_tab' => false,
            ],
            '1.2' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Lihat Uraian Tugas',
                'icon'    => 'file-list-3-line',
                'new_tab' => false,
            ],
            '1.3' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Lihat Pedoman Mutu',
                'icon'    => 'book-2-line',
                'new_tab' => false,
            ],
            '1.4' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Sasaran Mutu',
                'icon'    => 'focus-3-line',
                'new_tab' => false,
            ],
            '1.5' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Sertifikat PJT',
                'icon'    => 'award-line',
                'new_tab' => false,
            ],
            '1.7' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Pengendalian Dok.',
                'icon'    => 'file-text-line',
                'new_tab' => false,
            ],
            '1.8' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Sistem Komputerisasi',
                'icon'    => 'computer-line',
                'new_tab' => false,
            ],
            '1.9' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Daftar Induk Dokumen',
                'icon'    => 'file-list-2-line',
                'new_tab' => false,
            ],
            '1.10' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Daftar Distribusi Dok.',
                'icon'    => 'share-line',
                'new_tab' => false,
            ],
            '1.11' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Form Pemusnahan Catatan',
                'icon'    => 'delete-bin-line',
                'new_tab' => false,
            ],
            '1.12' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Daftar Waktu Retensi',
                'icon'    => 'time-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 2: Pengelolaan Sumber Daya ───────────────
            '2.1' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SK Wakil Manajemen',
                'icon'    => 'user-star-line',
                'new_tab' => false,
            ],
            '2.2' => [
                'url'     => $baseUrl . '#program-pelatihan-content',
                'label'   => 'Program Pelatihan',
                'icon'    => 'book-open-line',
                'new_tab' => false,
            ],
            '2.3' => [
                'url'     => $baseUrl . '#daftar-hadir-content',
                'label'   => 'Daftar Hadir',
                'icon'    => 'file-list-2-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 3: Bangunan Dan Fasilitas ───────────────
            '3.1' => [
                'url'     => $baseUrl . '#gallery-content',
                'label'   => 'Lihat Galeri APD',
                'icon'    => 'image-line',
                'new_tab' => false,
            ],
            '3.8' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Kebersihan',
                'icon'    => 'sparkling-line',
                'new_tab' => false,
            ],
            '3.9' => [
                'url'     => $baseUrl . '#gallery-content',
                'label'   => 'Foto Tanda K3',
                'icon'    => 'image-line',
                'new_tab' => false,
            ],
            '3.10' => [
                'url'     => $baseUrl . '#pengendalian-hama-content',
                'label'   => 'Pengendalian Hama',
                'icon'    => 'bug-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 4: Penyimpanan & Persediaan ───────────────
            '4.1' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Kegawatdaruratan',
                'icon'    => 'alert-line',
                'new_tab' => false,
            ],
            '4.2' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP K3',
                'icon'    => 'shield-check-line',
                'new_tab' => false,
            ],
            '4.3' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Penerimaan Barang',
                'icon'    => 'inbox-archive-line',
                'new_tab' => false,
            ],
            '4.4' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Form Produk Reject',
                'icon'    => 'file-damage-line',
                'new_tab' => false,
            ],
            '4.5' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Bukti Kalibrasi',
                'icon'    => 'scales-line',
                'new_tab' => false,
            ],
            '4.7' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Penyimpanan',
                'icon'    => 'store-2-line',
                'new_tab' => false,
            ],
            '4.8' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Kartu Stok',
                'icon'    => 'stock-line',
                'new_tab' => false,
            ],
            '4.10' => [
                'url'     => $baseUrl . '#catatan-suhu-content',
                'label'   => 'Catatan Suhu Ruangan',
                'icon'    => 'temp-hot-line',
                'new_tab' => false,
            ],
            '4.11' => [
                'url'     => $baseUrl . '#catatan-suhu-content',
                'label'   => 'Catatan Thermohygrometer',
                'icon'    => 'temp-hot-line',
                'new_tab' => false,
            ],
            '4.12' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Izin Edar',
                'icon'    => 'file-certificate-line',
                'new_tab' => false,
            ],
            '4.13' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Pembelian Alkes',
                'icon'    => 'shopping-cart-line',
                'new_tab' => false,
            ],
            '4.14' => [
                'url'     => 'https://e-regalkes.kemkes.go.id',
                'label'   => 'e-Report Kemenkes',
                'icon'    => 'external-link-line',
                'new_tab' => true,
            ],
            '4.15' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Form Surat Pesanan',
                'icon'    => 'file-text-line',
                'new_tab' => false,
            ],
            '4.16' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Stock Opname',
                'icon'    => 'clipboard-line',
                'new_tab' => false,
            ],
            '4.17' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Distribusi',
                'icon'    => 'truck-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 5: Mampu Telusur Produk ───────────────
            '5.1' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Ketertelusuran',
                'icon'    => 'search-eye-line',
                'new_tab' => false,
            ],
            '5.2' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Sistem Alkes Implan',
                'icon'    => 'health-book-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 6: Penanganan Keluhan ───────────────
            '6.1' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Penanganan Keluhan',
                'icon'    => 'customer-service-2-line',
                'new_tab' => false,
            ],
            '6.2' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Penanganan KTD',
                'icon'    => 'error-warning-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 7: FSCA ───────────────
            '7.1' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP FSCA',
                'icon'    => 'tools-line',
                'new_tab' => false,
            ],
            '7.2' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Recall',
                'icon'    => 'arrow-go-back-line',
                'new_tab' => false,
            ],
            '7.3' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Form Pemberitahuan Recall',
                'icon'    => 'notification-line',
                'new_tab' => false,
            ],
            '7.4' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'Mekanisme Pemberitahuan',
                'icon'    => 'mail-send-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 8: Retur Alkes ───────────────
            '8.1' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Retur Alkes',
                'icon'    => 'refund-2-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 9: Pemusnahan ───────────────
            '9.1' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Pemusnahan Alkes',
                'icon'    => 'delete-bin-6-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 10: Alkes Ilegal/TMS ───────────────
            '10.1' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Produk Ilegal & TMS',
                'icon'    => 'spam-2-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 11: Audit Internal ───────────────
            '11.1' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Audit Internal',
                'icon'    => 'audit-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 12: Kajian Manajemen ───────────────
            '12.1' => [
                'url'     => $baseUrl . '#sop-content',
                'label'   => 'SOP Tinjauan Manajemen',
                'icon'    => 'presentation-line',
                'new_tab' => false,
            ],

            // ─────────────── KATEGORI 13: Outsourcing ───────────────
            // Tambahkan di sini jika ada nomor soal kategori 13
        ];

        return $map[$questionNumber] ?? null;
    }
}