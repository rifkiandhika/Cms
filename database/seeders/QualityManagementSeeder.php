<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QualityManagementSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::table('questions')->delete();
        DB::table('sub_categories')->delete();
        DB::table('categories')->delete();

        // Category 1: Sistem Manajemen Mutu
        $category1 = DB::table('categories')->insertGetId([
            'number' => '1',
            'name' => 'Sistem Manajemen Mutu',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sub Category 1a: Persyaratan Umum
        $subCat1a = DB::table('sub_categories')->insertGetId([
            'category_id' => $category1,
            'label' => 'a',
            'name' => 'Persyaratan Umum',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Questions for 1a
        DB::table('questions')->insert([
            [
                'sub_category_id' => $subCat1a,
                'number' => '1.1',
                'question' => 'Apakah memiliki struktur organisasi yang mencantumkan posisi pimpinan perusahaan dan Penanggung Jawab Teknis (PJT)?',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat1a,
                'number' => '1.2',
                'question' => 'Apakah memiliki Uraian Tugas Direktur, PJT, dan setiap bagian yang sesuai struktur organisasi?',
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat1a,
                'number' => '1.3',
                'question' => 'Apakah memiliki Pedoman Mutu yang memuat kebijakan perusahaan sesuai dengan 13 aspek CDAKB?',
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat1a,
                'number' => '1.4',
                'question' => 'Apakah memiliki Perencanaan dan Monitoring Sasaran Mutu?',
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat1a,
                'number' => '1.5',
                'question' => 'Apakah PJT memiliki sertifikat pelatihan CDAKB yang diterbitkan oleh Kemenkes?',
                'order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Sub Category 1b: Persyaratan Dokumen
        $subCat1b = DB::table('sub_categories')->insertGetId([
            'category_id' => $category1,
            'label' => 'b',
            'name' => 'Persyaratan Dokumen',
            'order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Questions for 1b
        DB::table('questions')->insert([
            [
                'sub_category_id' => $subCat1b,
                'number' => '1.7',
                'question' => 'Apakah memiliki Prosedur Tetap (SOP) Pengendalian Dokumen?',
                'order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat1b,
                'number' => '1.8',
                'question' => 'Jika menggunakan sistem komputerisasi, apakah tersedia deskripsi dari sistem tersebut dan cara memvalidasi keakuratan sistem komputerisasi untuk mengetahui akurasi sistem?',
                'order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat1b,
                'number' => '1.9',
                'question' => 'Apakah memiliki Daftar Induk Dokumen yang mencakup daftar rekaman?',
                'order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat1b,
                'number' => '1.10',
                'question' => 'Apakah memiliki Daftar Distribusi Dokumen?',
                'order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat1b,
                'number' => '1.11',
                'question' => 'Apakah memiliki Form Laporan Pemusnahan Catatan Mutu?',
                'order' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat1b,
                'number' => '1.12',
                'question' => 'Apakah perusahaan memiliki daftar waktu retensi setiap rekaman sesuai dengan umur guna (lifetime) alat kesehatan atau minimal 2 (dua) tahun?',
                'order' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Category 2: Pengelolaan Sumber Daya
        $category2 = DB::table('categories')->insertGetId([
            'number' => '2',
            'name' => 'Pengelolaan Sumber Daya',
            'order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sub Category 2a: Personil
        $subCat2a = DB::table('sub_categories')->insertGetId([
            'category_id' => $category2,
            'label' => 'a',
            'name' => 'Personil',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            'sub_category_id' => $subCat2a,
            'number' => '2.1',
            'question' => 'Apakah memiliki Surat Penunjukkan Wakil Manajemen?',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sub Category 2b: Pelatihan
        $subCat2b = DB::table('sub_categories')->insertGetId([
            'category_id' => $category2,
            'label' => 'b',
            'name' => 'Pelatihan',
            'order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            [
                'sub_category_id' => $subCat2b,
                'number' => '2.2',
                'question' => 'Apakah memiliki Prosedur Tetap (SOP) Pelatihan Karyawan?',
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat2b,
                'number' => '2.3',
                'question' => 'Apakah memiliki daftar hadir pelatihan dan form Evaluasi Pelatihan Karyawan?',
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Category 3: Bangunan Dan Fasilitas
        $category3 = DB::table('categories')->insertGetId([
            'number' => '3',
            'name' => 'Bangunan Dan Fasilitas',
            'order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sub Category 3a: Penjelasan Umum
        $subCat3a = DB::table('sub_categories')->insertGetId([
            'category_id' => $category3,
            'label' => 'a',
            'name' => 'Penjelasan Umum',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            [
                'sub_category_id' => $subCat3a,
                'number' => '3.1',
                'question' => 'Apakah tersedia alat pelindung diri (APD) bagi personil yang terlibat dalam kegiatan distribusi sesuai sifat produk yang didistribusikan?',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat3a,
                'number' => '3.2',
                'question' => 'Apakah alat kesehatan diletakkan di atas rak/palet?',
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat3a,
                'number' => '3.3',
                'question' => 'Apakah memiliki alat pemadam kebakaran seperti APAR, hydrant dan atau sprinkler?',
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat3a,
                'number' => '3.4',
                'question' => 'Apakah tersedia peralatan yang memadai dan aman untuk memindahkan produk alkes?',
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat3a,
                'number' => '3.5',
                'question' => 'Jika menggunakan forklift, apakah operator memiliki Sertifikat Izin Operasional (SIO) Forklift dan masih berlaku?',
                'order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat3a,
                'number' => '3.6',
                'question' => 'Apakah forklift yang digunakan di dalam gudang menggunakan sumber penggerak listrik atau baterai?',
                'order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Sub Category 3b: Kebersihan
        $subCat3b = DB::table('sub_categories')->insertGetId([
            'category_id' => $category3,
            'label' => 'b',
            'name' => 'Kebersihan',
            'order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            [
                'sub_category_id' => $subCat3b,
                'number' => '3.8',
                'question' => 'Apakah memiliki SOP/Prosedur Tetap Kebersihan Area dan Bangunan yang mencakup tahapan pelaksanaan, evaluasi, dan pemeliharaan catatan/rekaman kebersihan area dan bangunan?',
                'order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat3b,
                'number' => '3.9',
                'question' => 'Apakah memiliki tanda peringatan Kesehatan dan Keselamatan Kerja (K3), minimal tanda larangan makan, minum, meludah, dan merokok di ruang penyimpanan?',
                'order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Sub Category 3c: Kontrol Hama
        $subCat3c = DB::table('sub_categories')->insertGetId([
            'category_id' => $category3,
            'label' => 'c',
            'name' => 'Kontrol Hama',
            'order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            'sub_category_id' => $subCat3c,
            'number' => '3.10',
            'question' => 'Apakah memiliki SOP/Prosedur Tetap Kontrol Hama pada ruang penyimpanan yang mencakup pelaksanaan, monitoring, evaluasi, dan pemeliharaan catatan/rekaman kegiatan, serta lampirkan foto kontrol hama yang dimiliki?',
            'order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Category 4: Penyimpanan dan Penanganan Persediaan
        $category4 = DB::table('categories')->insertGetId([
            'number' => '4',
            'name' => 'Penyimpanan dan Penanganan Persediaan',
            'order' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sub Category 4a: Ketentuan Umum
        $subCat4a = DB::table('sub_categories')->insertGetId([
            'category_id' => $category4,
            'label' => 'a',
            'name' => 'Ketentuan Umum',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            [
                'sub_category_id' => $subCat4a,
                'number' => '4.1',
                'question' => 'Apakah memiliki SOP/Prosedur Tetap penanganan produk alkes setelah terjadi kegawatdaruratan?',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat4a,
                'number' => '4.2',
                'question' => 'Apakah memiliki SOP/Prosedur Tetap Keselamatan dan Kesehatan Kerja (Penanggulangan Kebakaran, Penanggulangan Listrik Padam, Gempa Bumi, dll)?',
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Sub Category 4b: Penerimaan Barang
        $subCat4b = DB::table('sub_categories')->insertGetId([
            'category_id' => $category4,
            'label' => 'b',
            'name' => 'Penerimaan Barang',
            'order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            [
                'sub_category_id' => $subCat4b,
                'number' => '4.3',
                'question' => 'Apakah memiliki SOP/Prosedur Tetap Penerimaan produk alkes yang mencakup tahapan pelaksanaan, termasuk tahapan pemastian penandaan sesuai yang disetujui Kemenkes, dan tahapan pemeliharaan catatan/rekaman penerimaan produk alkes?',
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat4b,
                'number' => '4.4',
                'question' => 'Apakah memiliki form daftar produk alkes reject atau produk karantina yang mencantumkan sekurang-kurangnya nama produk alkes, tipe, nomor izin edar, kode produksi/serial number, jumlah/volume alkes, kondisi produk alkes (alasan mengapa dimasukkan dalam produk reject/karantina)?',
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Sub Category 4c: Kalibrasi
        $subCat4c = DB::table('sub_categories')->insertGetId([
            'category_id' => $category4,
            'label' => 'c',
            'name' => 'Kalibrasi',
            'order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            'sub_category_id' => $subCat4c,
            'number' => '4.5',
            'question' => 'Apakah alat ukur yang digunakan (seperti alat ukur suhu, kelembapan atau alat ukur lainnya) untuk menjamin penyimpanan dan distribusi alat kesehatan yang baik telah dikalibrasi secara rutin? Lampirkan bukti kalibrasi yang telah dilakukan',
            'order' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sub Category 4d: Penyimpanan
        $subCat4d = DB::table('sub_categories')->insertGetId([
            'category_id' => $category4,
            'label' => 'd',
            'name' => 'Penyimpanan',
            'order' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            [
                'sub_category_id' => $subCat4d,
                'number' => '4.7',
                'question' => 'Apakah memiliki SOP/Prosedur Tetap Penyimpanan produk alkes yang dapat menjamin produk alkes disimpan sesuai dengan karakteristik produk yang tercantum pada kemasan produk dan tidak terjadi kontaminasi silang?',
                'order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat4d,
                'number' => '4.8',
                'question' => 'Apakah memiliki Form Penyimpanan Barang (kartu stok)?',
                'order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat4d,
                'number' => '4.9',
                'question' => 'Apakah tersedia ruang/area penyimpanan khusus, dengan penandaan yang jelas, untuk bahan berbahaya dan sensitif seperti bahan bersifat mudah terbakar (flamable), gas bertekanan, bahan beracun, dan produk yang mengandung radiasi? (jika menerapkan)',
                'order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat4d,
                'number' => '4.10',
                'question' => 'Apakah tersedia prosedur pengendalian suhu dan kelembapan ruang penyimpanan? Pengendalian yang dimaksud dilaksanakan secara rutin, minimal 2 kali per hari?',
                'order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat4d,
                'number' => '4.11',
                'question' => 'Apakah thermohygrometer ditempatkan di ruang yang bersuhu paling fluktuatif, misalnya di depan pintu untuk jalur keluar masuk?',
                'order' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat4d,
                'number' => '4.12',
                'question' => 'Apakah perusahaan memiliki SOP/Prosedur Tetap pengurusan Izin Edar Produk? (bagi pemilik izin edar)',
                'order' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat4d,
                'number' => '4.13',
                'question' => 'Apakah memilikiSOP/ Prosedur Tetap Pembelian Alkes, yang mencakup juga daftar form yang digunakan dalam implementasi SOP tersebut, minimal form perencanaan pembelian dan form permintaan pembelian?',
                'order' => 13,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat4d,
                'number' => '4.14',
                'question' => 'Apakah telah melakukan pelaporan distribusi alat kesehatan melalui e-report pada tahun berjalan setiap minimal 6 bulan sekali?',
                'order' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat4d,
                'number' => '4.15',
                'question' => 'Apakah memiliki Form Surat Pesanan yang mencantumkan sekurang-kurangnya nama produk, NIE, jenis/tipe, nomor dan tanggal pesanan?',
                'order' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat4d,
                'number' => '4.16',
                'question' => 'Apakah memiliki SOP/Prosedur Tetap Stock Opname yang mencakup juga daftar form yang digunakan dalam implementasi SOP tersebut, minimal form stock opname dan form Berita Acara Stock Opname?',
                'order' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Sub Category 4e: Pengiriman dan Penyerahan Kepada Konsumen
        $subCat4e = DB::table('sub_categories')->insertGetId([
            'category_id' => $category4,
            'label' => 'e',
            'name' => 'Pengiriman dan Penyerahan Kepada Konsumen',
            'order' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            'sub_category_id' => $subCat4e,
            'number' => '4.17',
            'question' => 'Apakah memiliki SOP/Prosedur Tetap Distribusi/Penyaluran produk alkes yang dapat memastikan kondisi distribusi/penyaluran dapat menjaga keamanan dan mutu produk alkes tersebut, SOP mencakup juga daftar form yang digunakan dalam implementasi SOP tersebut, minimal form pengiriman dan form surat jalan?',
            'order' => 17,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Category 5: Mampu Telusur Produk (Traceability)
        $category5 = DB::table('categories')->insertGetId([
            'number' => '5',
            'name' => 'Mampu Telusur Produk (Traceability)',
            'order' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subCat5 = DB::table('sub_categories')->insertGetId([
            'category_id' => $category5,
            'label' => '',
            'name' => 'Mampu Telusur Produk',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            [
                'sub_category_id' => $subCat5,
                'number' => '5.1',
                'question' => 'Apakah memiliki SOP/Prosedur Tetap atau mekanisme Ketertelusuran Produk sesuai dengan ruang lingkupnya?',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat5,
                'number' => '5.2',
                'question' => 'Jika memiliki Alkes implan, apakah memiliki sistem penelusuran hingga ke fasilitas pengguna sekurang-kurangnya meliputi tanggal alkes diimplankan pada pasien?',
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Category 6: Penanganan Keluhan
        $category6 = DB::table('categories')->insertGetId([
            'number' => '6',
            'name' => 'Penanganan Keluhan',
            'order' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subCat6 = DB::table('sub_categories')->insertGetId([
            'category_id' => $category6,
            'label' => '',
            'name' => 'Penanganan Keluhan',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            [
                'sub_category_id' => $subCat6,
                'number' => '6.1',
                'question' => 'Apakah memiliki SOP/Prosedur Tetap Penanganan Keluhan yang juga mencakup daftar form yang digunakan dalam implementasi SOP tersebut, minimal form keluhan pelanggan, form monitoring keluhan pelanggan, dan form laporan hasil evaluasi keluhan pelanggan?',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat6,
                'number' => '6.2',
                'question' => 'Apakah memiliki SOP/Prosedur Tetap Penanganan Kejadian Tidak Diinginkan (KTD), yang juga mencakup tahapan pelaporan KTD ke Kementerian Kesehatan?',
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Category 7: Tindakan Perbaikan Keamanan Di Lapangan (Field Safety Corrective Action/FSCA)
        $category7 = DB::table('categories')->insertGetId([
            'number' => '7',
            'name' => 'Tindakan Perbaikan Keamanan Di Lapangan (Field Safety Corrective Action/FSCA)',
            'order' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subCat7 = DB::table('sub_categories')->insertGetId([
            'category_id' => $category7,
            'label' => '',
            'name' => 'Tindakan Perbaikan Keamanan Di Lapangan',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            [
                'sub_category_id' => $subCat7,
                'number' => '7.1',
                'question' => 'Apakah memiliki SOP/Prosedur Tetap Tindakan Perbaikan Keamanan di Lapangan (Field Safety Corrective Action/FSCA) yang juga mencakup daftar form yang digunakan dalam implementasi SOP tersebut, minimal form FSCA?',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat7,
                'number' => '7.2',
                'question' => 'Apakah memiliki SOP/Prosedur Tetap penanganan mandatory recall dan voluntary recall (penarikan kembali) alkes yang mencantumkan juga tahapan koordinasi dengan produsen dan tahapan pelaporan recall tersebut ke Kementerian Kesehatan serta bagian/petugas yang ditunjuk untuk menangani recall tersebut?',
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat7,
                'number' => '7.3',
                'question' => 'Apakah memiliki Form Pemberitahuan recall kepada konsumen, yang mencakup sekurang-kurangnya nama produk, NIE, tipe/jenis, kode produksi/serial number, tanggal kadaluarsa, jumlah/volume, kondisi produk, tanggal pelaksanaan recall, dan tempat asal recall?',
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_category_id' => $subCat7,
                'number' => '7.4',
                'question' => 'Apakah memiliki mekanisme pemberitahuan tindakan perbaikan kepada konsumen yang telah menerima produk?',
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Category 8: Pengembalian/Retur Alat Kesehatan
        $category8 = DB::table('categories')->insertGetId([
            'number' => '8',
            'name' => 'Pengembalian/Retur Alat Kesehatan',
            'order' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subCat8 = DB::table('sub_categories')->insertGetId([
            'category_id' => $category8,
            'label' => '',
            'name' => 'Pengembalian/Retur Alat Kesehatan',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            'sub_category_id' => $subCat8,
            'number' => '8.1',
            'question' => 'Apakah memiliki SOP/Prosedur Tetap Penanganan Alkes Kembalian (retur) yang mencakup juga kriteria produk kembalian, tahapan penanganan setiap kriteria dan ketentuan merekam setiap aktivitas?',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Category 9: Pemusnahan Alat Kesehatan
        $category9 = DB::table('categories')->insertGetId([
            'number' => '9',
            'name' => 'Pemusnahan Alat Kesehatan',
            'order' => 9,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subCat9 = DB::table('sub_categories')->insertGetId([
            'category_id' => $category9,
            'label' => '',
            'name' => 'Pemusnahan Alat Kesehatan',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            'sub_category_id' => $subCat9,
            'number' => '9.1',
            'question' => 'Apakah memiliki SOP/Prosedur Tetap Pemusnahan Alkes yang mencakup juga kriteria produk yang akan dimusnahkan dan sesuai dengan ketentuan pemusnahan yang ditetapkan oleh Kemenkes?',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Category 10: Alat Kesehatan Ilegal Dan Tidak Memenuhi Syarat (TMS)
        $category10 = DB::table('categories')->insertGetId([
            'number' => '10',
            'name' => 'Alat Kesehatan Ilegal Dan Tidak Memenuhi Syarat (TMS)',
            'order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subCat10 = DB::table('sub_categories')->insertGetId([
            'category_id' => $category10,
            'label' => '',
            'name' => 'Alat Kesehatan Ilegal Dan Tidak Memenuhi Syarat',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            'sub_category_id' => $subCat10,
            'number' => '10.1',
            'question' => 'Apakah memiliki SOP/Protap Penanganan Produk Ilegal dan TMS yang mencakup diantaranya kriteria produk ilegal dan TMS, tahapan penanganan setiap kriteria tersebut, serta pemeliharaan rekaman aktivitas penanganan produk ilegal dan TMS?',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Category 11: Audit Internal
        $category11 = DB::table('categories')->insertGetId([
            'number' => '11',
            'name' => 'Audit Internal',
            'order' => 11,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subCat11 = DB::table('sub_categories')->insertGetId([
            'category_id' => $category11,
            'label' => '',
            'name' => 'Audit Internal',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            'sub_category_id' => $subCat11,
            'number' => '11.1',
            'question' => 'Apakah memiliki SOP/Prosedur Tetap Audit Mutu Internal yang mencakup diantaranya tanggung jawab, persyaratan, perencanaan, pelaporan dan pemeliharaan rekaman pelaksanaan audit internal serta mencantumkan frekuensi pelaksanaan audit internal (minimal 1 tahun sekali)?',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Category 12: Kajian Manajemen
        $category12 = DB::table('categories')->insertGetId([
            'number' => '12',
            'name' => 'Kajian Manajemen',
            'order' => 12,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subCat12 = DB::table('sub_categories')->insertGetId([
            'category_id' => $category12,
            'label' => '',
            'name' => 'Kajian Manajemen',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('questions')->insert([
            'sub_category_id' => $subCat12,
            'number' => '12.1',
            'question' => 'Apakah memiliki SOP/Prosedur Tetap Tinjauan Manajemen yang mencantumkan diantaranya tanggung jawab, persyaratan, perencanaan, pelaporan dan pemeliharaan rekaman pelaksanaan serta mencantumkan frekuensi pelaksanaan tinjauan manajemen (minimal 1 tahun sekali) dan 8 (delapan) input/materi yang wajib dibahas sesuai dengan CDAKB?',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Category 13: Aktifitas Pihak Ketiga (Outsourcing Activity)
        $category13 = DB::table('categories')->insertGetId([
            'number' => '13',
            'name' => 'Aktifitas Pihak Ketiga (Outsourcing Activity)',
            'order' => 13,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subCat13 = DB::table('sub_categories')->insertGetId([
            'category_id' => $category13,
            'label' => '',
            'name' => 'Aktifitas Pihak Ketiga',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Note: Question 13.1 and beyond are not visible in the images provided
        // Add them if you have more images

        echo "Seeder executed successfully!\n";
    }
}