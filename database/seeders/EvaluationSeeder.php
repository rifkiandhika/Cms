<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EvaluationSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------
        // 1. Evaluation Programs
        // -------------------------
        $programs = [
            [
                'id'                => 1,
                'title'             => 'Pelatihan Keselamatan & Kesehatan Kerja (K3)',
                'materi_pelatihan'  => 'Dasar-dasar K3, Penggunaan APD, Prosedur Evakuasi',
                'hari_tanggal'      => '2025-03-10',
                'tempat_pelatihan'  => 'Aula Pelatihan Lantai 2',
                'program_number'    => 'PRG-2025-001',
                'status'            => 'active',
                'description'       => 'Program pelatihan rutin tahunan mengenai keselamatan dan kesehatan kerja untuk seluruh karyawan.',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'id'                => 2,
                'title'             => 'Pelatihan Pelayanan Pelanggan',
                'materi_pelatihan'  => 'Komunikasi Efektif, Penanganan Komplain, Customer Experience',
                'hari_tanggal'      => '2025-04-05',
                'tempat_pelatihan'  => 'Ruang Rapat Utama',
                'program_number'    => 'PRG-2025-002',
                'status'            => 'draft',
                'description'       => 'Pelatihan untuk meningkatkan kualitas pelayanan kepada pelanggan internal maupun eksternal.',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ];

        DB::table('evaluation_programs')->insert($programs);

        // -------------------------
        // 2. Evaluation Items
        // -------------------------
        $items = [
            // Program 1 - K3
            [
                'evaluation_program_id' => 1,
                'item_label'            => 'A',
                'item_content'          => 'Materi pelatihan yang disampaikan mudah dipahami dan relevan dengan pekerjaan saya.',
                'order'                 => 1,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'evaluation_program_id' => 1,
                'item_label'            => 'B',
                'item_content'          => 'Instruktur/fasilitator menyampaikan materi dengan baik dan interaktif.',
                'order'                 => 2,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'evaluation_program_id' => 1,
                'item_label'            => 'C',
                'item_content'          => 'Fasilitas dan sarana pelatihan memadai dan mendukung proses belajar.',
                'order'                 => 3,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'evaluation_program_id' => 1,
                'item_label'            => 'D',
                'item_content'          => 'Pelatihan ini memberikan manfaat nyata yang dapat saya terapkan di tempat kerja.',
                'order'                 => 4,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],

            // Program 2 - Pelayanan Pelanggan
            [
                'evaluation_program_id' => 2,
                'item_label'            => 'A',
                'item_content'          => 'Materi pelatihan sesuai dengan kebutuhan dan tantangan kerja sehari-hari saya.',
                'order'                 => 1,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'evaluation_program_id' => 2,
                'item_label'            => 'B',
                'item_content'          => 'Metode penyampaian materi (studi kasus, role-play, dll.) efektif dan menarik.',
                'order'                 => 2,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'evaluation_program_id' => 2,
                'item_label'            => 'C',
                'item_content'          => 'Durasi pelatihan sudah cukup untuk memahami materi yang disampaikan.',
                'order'                 => 3,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'evaluation_program_id' => 2,
                'item_label'            => 'D',
                'item_content'          => 'Saya merasa lebih percaya diri dalam melayani pelanggan setelah mengikuti pelatihan ini.',
                'order'                 => 4,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
        ];

        DB::table('evaluation_items')->insert($items);

        // Ambil item IDs per program untuk dipakai di responses
        $itemsProgram1 = DB::table('evaluation_items')
            ->where('evaluation_program_id', 1)
            ->orderBy('order')
            ->pluck('id', 'item_label');

        $itemsProgram2 = DB::table('evaluation_items')
            ->where('evaluation_program_id', 2)
            ->orderBy('order')
            ->pluck('id', 'item_label');

        // -------------------------
        // 3. Evaluation Participants
        // -------------------------
        $participants = [
            // Program 1
            ['evaluation_program_id' => 1, 'nama_peserta' => 'Budi Santoso',      'jabatan_lokasi_kerja' => 'Operator Produksi / Pabrik Utama',  'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['evaluation_program_id' => 1, 'nama_peserta' => 'Siti Rahayu',       'jabatan_lokasi_kerja' => 'Teknisi Mesin / Pabrik Utama',       'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['evaluation_program_id' => 1, 'nama_peserta' => 'Ahmad Fauzi',       'jabatan_lokasi_kerja' => 'Supervisor Produksi / Pabrik Utama',  'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['evaluation_program_id' => 1, 'nama_peserta' => 'Dewi Lestari',      'jabatan_lokasi_kerja' => 'Staff Gudang / Logistik',             'order' => 4, 'created_at' => now(), 'updated_at' => now()],

            // Program 2
            ['evaluation_program_id' => 2, 'nama_peserta' => 'Rina Wulandari',    'jabatan_lokasi_kerja' => 'Customer Service / Kantor Pusat',     'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['evaluation_program_id' => 2, 'nama_peserta' => 'Dian Permatasari',  'jabatan_lokasi_kerja' => 'Admin Penjualan / Kantor Pusat',      'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['evaluation_program_id' => 2, 'nama_peserta' => 'Hendra Kusuma',     'jabatan_lokasi_kerja' => 'Sales Representative / Area Surabaya', 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('evaluation_participants')->insert($participants);

        $participantsProgram1 = DB::table('evaluation_participants')
            ->where('evaluation_program_id', 1)
            ->orderBy('order')
            ->get();

        $participantsProgram2 = DB::table('evaluation_participants')
            ->where('evaluation_program_id', 2)
            ->orderBy('order')
            ->get();

        // -------------------------
        // 4. Evaluation Responses
        // -------------------------
        $responses = [];

        // Jawaban acak dari skala: Sangat Baik, Baik, Cukup, Kurang
        $optionSets = [
            ['Sangat Baik', 'Baik', 'Sangat Baik', 'Baik'],
            ['Baik', 'Baik', 'Cukup', 'Sangat Baik'],
            ['Sangat Baik', 'Sangat Baik', 'Baik', 'Sangat Baik'],
            ['Cukup', 'Baik', 'Baik', 'Baik'],
        ];

        // Responses Program 1
        foreach ($participantsProgram1 as $i => $participant) {
            $answers   = $optionSets[$i % count($optionSets)];
            $itemLabels = ['A', 'B', 'C', 'D'];
            $responseJson = [];
            foreach ($itemLabels as $j => $label) {
                $responseJson[$itemsProgram1[$label]] = $answers[$j];
            }

            $responses[] = [
                'evaluation_program_id'       => 1,
                'evaluation_participant_id'   => $participant->id,
                'nama_peserta'                => $participant->nama_peserta,
                'jabatan_lokasi_kerja'        => $participant->jabatan_lokasi_kerja,
                'responses'                   => json_encode($responseJson),
                'mengetahui_atasan_nama'      => 'Ir. Suharto, M.T.',
                'mengetahui_atasan_tanggal'   => '2025-03-10',
                'mengetahui_personalia_nama'  => 'Ani Suryani, S.H.',
                'mengetahui_personalia_tanggal' => '2025-03-10',
                'created_at'                  => now(),
                'updated_at'                  => now(),
            ];
        }

        // Responses Program 2 (hanya 2 dari 3 peserta yang sudah submit)
        $submittedParticipants = $participantsProgram2->take(2);
        foreach ($submittedParticipants as $i => $participant) {
            $answers   = $optionSets[($i + 1) % count($optionSets)];
            $itemLabels = ['A', 'B', 'C', 'D'];
            $responseJson = [];
            foreach ($itemLabels as $j => $label) {
                $responseJson[$itemsProgram2[$label]] = $answers[$j];
            }

            $responses[] = [
                'evaluation_program_id'       => 2,
                'evaluation_participant_id'   => $participant->id,
                'nama_peserta'                => $participant->nama_peserta,
                'jabatan_lokasi_kerja'        => $participant->jabatan_lokasi_kerja,
                'responses'                   => json_encode($responseJson),
                'mengetahui_atasan_nama'      => 'Drs. Bambang Wijaya',
                'mengetahui_atasan_tanggal'   => '2025-04-05',
                'mengetahui_personalia_nama'  => 'Sari Indah, S.Psi.',
                'mengetahui_personalia_tanggal' => '2025-04-05',
                'created_at'                  => now(),
                'updated_at'                  => now(),
            ];
        }

        DB::table('evaluation_responses')->insert($responses);
    }
}