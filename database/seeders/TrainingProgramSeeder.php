<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingProgram;
use App\Models\TrainingMainCategory;
use App\Models\TrainingSubCategory;
use App\Models\TrainingItem;
use App\Models\TrainingDetail;

class TrainingProgramSeeder extends Seeder
{
    public function run()
    {
        // Create Training Program
        $program = TrainingProgram::create([
            'title' => 'PROSEDUR TETAP (PROTAP) PELATIHAN KARYAWAN PT. PREMIERE ALKES NUSINDO',
            'program_number' => '01.PROTAP.CDAKB.8',
            'effective_date' => '2026-02-12',
            'revision' => 'Rev. 00',
            'status' => 'draft',
            'description' => 'Program pelatihan komprehensif untuk seluruh karyawan PT. Premiere Alkes Nusindo'
        ]);

        // ============================================
        // I. PELATIHAN UMUM
        // ============================================
        $pelatihanUmum = $program->mainCategories()->create([
            'roman_number' => 'I',
            'name' => 'PELATIHAN UMUM',
            'order' => 1
        ]);

        // A. ORIENTASI UMUM
        $orientasiUmum = $pelatihanUmum->subCategories()->create([
            'letter' => 'A',
            'name' => 'ORIENTASI UMUM',
            'order' => 1
        ]);

        // 1. Pengenalan Perusahaan
        $pengenalanPerusahaan = $orientasiUmum->trainingItems()->create([
            'number' => '1',
            'nama_pelatihan' => 'Pengenalan Perusahaan',
            'peserta' => null,
            'instruktur' => null,
            'metode' => null,
            'jadwal' => null,
            'metode_penilaian' => null,
            'order' => 1
        ]);

        // Details untuk Pengenalan Perusahaan
        $pengenalanPerusahaan->details()->createMany([
            ['letter' => 'a', 'content' => 'Sejarah Perusahaan', 'order' => 1],
            ['letter' => 'b', 'content' => 'Struktur Organisasi', 'order' => 2],
            ['letter' => 'c', 'content' => 'Peraturan/Tata Tertib', 'order' => 3],
        ]);

        // 2. Pengenalan Layanan Fasilitas Distribusi
        $orientasiUmum->trainingItems()->create([
            'number' => '2',
            'nama_pelatihan' => 'Pengenalan Layanan Fasilitas Distribusi',
            'peserta' => 'Karyawan baru',
            'instruktur' => 'Atasan ybs',
            'metode' => 'Penjelasan lisan/presentasi',
            'jadwal' => 'Mulai masuk kerja',
            'metode_penilaian' => 'Pertanyaan lisan',
            'order' => 2
        ]);

        // 3. Uraian tugas karyawan ybs.
        $orientasiUmum->trainingItems()->create([
            'number' => '3',
            'nama_pelatihan' => 'Uraian tugas karyawan ybs.',
            'peserta' => null,
            'instruktur' => null,
            'metode' => null,
            'jadwal' => null,
            'metode_penilaian' => null,
            'order' => 3
        ]);

        // 4. Pengenalan tempat bekerja
        $orientasiUmum->trainingItems()->create([
            'number' => '4',
            'nama_pelatihan' => 'Pengenalan tempat bekerja, toilet, kantin,gudang, dll',
            'peserta' => null,
            'instruktur' => null,
            'metode' => null,
            'jadwal' => null,
            'metode_penilaian' => null,
            'order' => 4
        ]);

        // B. PELATIHAN CDAKB
        $pelatihanCDAKB = $pelatihanUmum->subCategories()->create([
            'letter' => 'B',
            'name' => 'PELATIHAN CDAKB',
            'order' => 2
        ]);

        // 1. Pengenalan industri Distribusi IVD
        $pelatihanCDAKB->trainingItems()->create([
            'number' => '1',
            'nama_pelatihan' => 'Pengenalan industri Distribusi IVD',
            'peserta' => 'Karyawan baru, lama',
            'instruktur' => 'Penanggung Jawab Teknis IVD',
            'metode' => 'Presentasi',
            'jadwal' => 'Mulai masuk kerja, berkala 1 kali setahun',
            'metode_penilaian' => 'Pertanyaan sebelum dan sesudah pelatihan',
            'order' => 1
        ]);

        // 2. Proses Distribusi
        $pelatihanCDAKB->trainingItems()->create([
            'number' => '2',
            'nama_pelatihan' => 'Proses Distribusi',
            'peserta' => null,
            'instruktur' => null,
            'metode' => null,
            'jadwal' => null,
            'metode_penilaian' => null,
            'order' => 2
        ]);

        // 3. Tujuan CDAKB
        $pelatihanCDAKB->trainingItems()->create([
            'number' => '3',
            'nama_pelatihan' => 'Tujuan CDAKB',
            'peserta' => null,
            'instruktur' => null,
            'metode' => null,
            'jadwal' => null,
            'metode_penilaian' => null,
            'order' => 3
        ]);

        // 4. Prinsip-prinsip CDAKB
        $pelatihanCDAKB->trainingItems()->create([
            'number' => '4',
            'nama_pelatihan' => 'Prinsip-prinsip CDAKB',
            'peserta' => null,
            'instruktur' => null,
            'metode' => null,
            'jadwal' => null,
            'metode_penilaian' => null,
            'order' => 4
        ]);

        // C. PELATIHAN K3L
        $pelatihanK3L = $pelatihanUmum->subCategories()->create([
            'letter' => 'C',
            'name' => 'PELATIHAN K3L',
            'order' => 3
        ]);

        // 1. Keadaan tanggap darurat
        $pelatihanK3L->trainingItems()->create([
            'number' => '1',
            'nama_pelatihan' => 'Keadaan tanggap darurat',
            'peserta' => 'Karyawan baru, lama, sub-kontraktor',
            'instruktur' => 'Bagian Pemelihara Mutu/ Instruktur Pelatihan',
            'metode' => 'Presentasi',
            'jadwal' => 'Mulai masuk kerja, berkala 1 kali setahun',
            'metode_penilaian' => 'Pertanyaan sebelum dan sesudah pelatihan, praktek di tempat',
            'order' => 1
        ]);

        // 2. P3K
        $pelatihanK3L->trainingItems()->create([
            'number' => '2',
            'nama_pelatihan' => 'P3K',
            'peserta' => null,
            'instruktur' => null,
            'metode' => null,
            'jadwal' => null,
            'metode_penilaian' => null,
            'order' => 2
        ]);

        // 3. Hygiene
        $pelatihanK3L->trainingItems()->create([
            'number' => '3',
            'nama_pelatihan' => 'Hygiene',
            'peserta' => null,
            'instruktur' => null,
            'metode' => null,
            'jadwal' => null,
            'metode_penilaian' => null,
            'order' => 3
        ]);

        // ============================================
        // II. PELATIHAN KHUSUS
        // ============================================
        $pelatihanKhusus = $program->mainCategories()->create([
            'roman_number' => 'II',
            'name' => 'PELATIHAN KHUSUS',
            'order' => 2
        ]);

        // A. ORIENTASI UMUM
        $orientasiUmumKhusus = $pelatihanKhusus->subCategories()->create([
            'letter' => 'A',
            'name' => 'ORIENTASI UMUM',
            'order' => 1
        ]);

        // 1. Penjelasan tentang teknis pelaksanaan tugas dan tanggung jawab
        $orientasiUmumKhusus->trainingItems()->create([
            'number' => '1',
            'nama_pelatihan' => 'Penjelasan tentang teknis pelaksanaan tugas dan tanggung jawab',
            'peserta' => 'Karyawan baru',
            'instruktur' => 'Atasan yang bersangkutan',
            'metode' => 'Penjelasan n lisan, presentasi, dokumen kerja',
            'jadwal' => 'Mulai masuk kerja',
            'metode_penilaian' => 'Pertanyaan sebelum dan sesudah pelatihan tertulis, praktek',
            'order' => 1
        ]);

        // 2. Penjelasan tentang Bagian-Bagian
        $penjelasanBagian = $orientasiUmumKhusus->trainingItems()->create([
            'number' => '2',
            'nama_pelatihan' => 'Penjelasan tentang Bagian-Bagian dan yang terkait dengan tugas dan tanggung jawab',
            'peserta' => 'Karyawan baru',
            'instruktur' => 'Atasan yang bersangkutan',
            'metode' => 'Penjelasan n lisan, presentasi, dokumen kerja',
            'jadwal' => 'Mulai masuk kerja',
            'metode_penilaian' => 'Pertanyaan sebelum dan sesudah pelatihan tertulis, praktek',
            'order' => 2
        ]);

        $this->command->info('✅ Training program seeded successfully!');
    }
}