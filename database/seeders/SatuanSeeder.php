<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SatuanSeeder extends Seeder
{
    public function run(): void
    {
        $satuans = [

            // ── Satuan Dasar / Umum ──────────────────────────────────────────
            ['nama_satuan' => 'Pcs',     'deskripsi' => 'Pieces / satuan buah'],
            ['nama_satuan' => 'Unit',    'deskripsi' => 'Unit / satu kesatuan alat'],
            ['nama_satuan' => 'Set',     'deskripsi' => 'Set / satu paket kelengkapan'],
            ['nama_satuan' => 'Lusin',   'deskripsi' => '12 buah'],
            ['nama_satuan' => 'Gross',   'deskripsi' => '144 buah (12 lusin)'],
            ['nama_satuan' => 'Pasang',  'deskripsi' => 'Sepasang (2 buah)'],
            ['nama_satuan' => 'Paket',   'deskripsi' => 'Satu paket / bundle'],
            ['nama_satuan' => 'Buah',    'deskripsi' => 'Satuan buah tunggal'],

            // ── Satuan Kemasan Obat / Farmasi ────────────────────────────────
            ['nama_satuan' => 'Tablet',   'deskripsi' => 'Satuan tablet obat'],
            ['nama_satuan' => 'Kapsul',   'deskripsi' => 'Satuan kapsul obat'],
            ['nama_satuan' => 'Strip',    'deskripsi' => 'Strip / blister (umumnya 10 tablet)'],
            ['nama_satuan' => 'Blister',  'deskripsi' => 'Kemasan blister obat'],
            ['nama_satuan' => 'Box',      'deskripsi' => 'Kotak / dus kemasan'],
            ['nama_satuan' => 'Dus',      'deskripsi' => 'Dus / karton kemasan'],
            ['nama_satuan' => 'Karton',   'deskripsi' => 'Karton besar (master box)'],
            ['nama_satuan' => 'Sachet',   'deskripsi' => 'Sachet / kemasan sekali pakai'],
            ['nama_satuan' => 'Ampul',    'deskripsi' => 'Ampul injeksi (gelas kecil tertutup)'],
            ['nama_satuan' => 'Vial',     'deskripsi' => 'Vial injeksi (botol kecil multi-dose)'],
            ['nama_satuan' => 'Flakon',   'deskripsi' => 'Flakon / botol injeksi'],
            ['nama_satuan' => 'Tube',     'deskripsi' => 'Tube salep / gel / krim'],
            ['nama_satuan' => 'Pot',      'deskripsi' => 'Pot / wadah krim atau salep'],
            ['nama_satuan' => 'Suppositoria', 'deskripsi' => 'Satuan suppositoria rektal'],
            ['nama_satuan' => 'Patch',    'deskripsi' => 'Koyo / plester transdermal'],
            ['nama_satuan' => 'Inhaler',  'deskripsi' => 'Satuan inhaler / alat hirup'],
            ['nama_satuan' => 'Pen',      'deskripsi' => 'Pen injeksi (mis. insulin pen)'],
            ['nama_satuan' => 'Cartridge','deskripsi' => 'Cartridge untuk pen injeksi'],

            // ── Satuan Volume Cairan ─────────────────────────────────────────
            ['nama_satuan' => 'mL',      'deskripsi' => 'Mililiter'],
            ['nama_satuan' => 'L',       'deskripsi' => 'Liter'],
            ['nama_satuan' => 'cc',      'deskripsi' => 'Cubic centimeter (setara mL)'],
            ['nama_satuan' => 'Botol',   'deskripsi' => 'Botol (volume sesuai spesifikasi produk)'],
            ['nama_satuan' => 'Galon',   'deskripsi' => 'Galon (±19 liter)'],
            ['nama_satuan' => 'Jerigen', 'deskripsi' => 'Jerigen (5–30 liter)'],
            ['nama_satuan' => 'Drum',    'deskripsi' => 'Drum besar (±200 liter)'],
            ['nama_satuan' => 'Infus',   'deskripsi' => 'Kantong / botol cairan infus'],
            ['nama_satuan' => 'Kolf',    'deskripsi' => 'Kolf cairan infus (500 mL / 1000 mL)'],

            // ── Satuan Berat ─────────────────────────────────────────────────
            ['nama_satuan' => 'mg',      'deskripsi' => 'Miligram'],
            ['nama_satuan' => 'g',       'deskripsi' => 'Gram'],
            ['nama_satuan' => 'kg',      'deskripsi' => 'Kilogram'],

            // ── Satuan Panjang / Ukuran ──────────────────────────────────────
            ['nama_satuan' => 'cm',      'deskripsi' => 'Sentimeter'],
            ['nama_satuan' => 'mm',      'deskripsi' => 'Milimeter'],
            ['nama_satuan' => 'meter',   'deskripsi' => 'Meter'],
            ['nama_satuan' => 'Roll',    'deskripsi' => 'Gulungan (perban, plester, dll)'],
            ['nama_satuan' => 'Yard',    'deskripsi' => 'Yard (kain / bahan medis)'],

            // ── Satuan Alat Kesehatan ────────────────────────────────────────
            ['nama_satuan' => 'Kantong',   'deskripsi' => 'Kantong (urine bag, blood bag, dll)'],
            ['nama_satuan' => 'Lembar',    'deskripsi' => 'Lembar (kertas, kain kasa, foil, dll)'],
            ['nama_satuan' => 'Helai',     'deskripsi' => 'Helai benang bedah / suture'],
            ['nama_satuan' => 'Jarum',     'deskripsi' => 'Jarum / needle'],
            ['nama_satuan' => 'Spuit',     'deskripsi' => 'Spuit / syringe'],
            ['nama_satuan' => 'Kateter',   'deskripsi' => 'Satuan kateter'],
            ['nama_satuan' => 'Selang',    'deskripsi' => 'Selang / tubing medis'],
            ['nama_satuan' => 'Masker',    'deskripsi' => 'Masker (per buah atau per box)'],
            ['nama_satuan' => 'Sarung Tangan', 'deskripsi' => 'Sarung tangan medis (per buah)'],
            ['nama_satuan' => 'Pak',       'deskripsi' => 'Pak / pack kemasan produk medis'],
            ['nama_satuan' => 'Canister',  'deskripsi' => 'Canister (tabung sentrifugal, dll)'],
            ['nama_satuan' => 'Kaset',     'deskripsi' => 'Kaset reagen / test strip'],
            ['nama_satuan' => 'Reagen',    'deskripsi' => 'Satuan reagen laboratorium'],
            ['nama_satuan' => 'Tes',       'deskripsi' => 'Satuan tes / test kit (rapid test, dll)'],

            // ── Satuan Gas Medis ─────────────────────────────────────────────
            ['nama_satuan' => 'Tabung',    'deskripsi' => 'Tabung gas medis (O2, CO2, dll)'],

        ];

        foreach ($satuans as $satuan) {
            DB::table('satuans')->insert([
                'id'          => Str::uuid(),
                'nama_satuan' => $satuan['nama_satuan'],
                'deskripsi'   => $satuan['deskripsi'],
                'status'      => 'Aktif',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}