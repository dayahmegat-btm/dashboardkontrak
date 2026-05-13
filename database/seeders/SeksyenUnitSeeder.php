<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeksyenUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all jabatan IDs
        $jabatans = \App\Models\Jabatan::all()->keyBy('kod_jabatan');

        $seksyenUnits = [
            // SUK - Pejabat Setiausaha Kerajaan Negeri
            [
                'jabatan_id' => $jabatans['SUK']->id,
                'kod_seksyen_unit' => 'SUK-EKS',
                'nama_seksyen_unit' => 'Unit Eksekutif',
                'penerangan' => 'Unit pentadbiran eksekutif',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['SUK']->id,
                'kod_seksyen_unit' => 'SUK-PER',
                'nama_seksyen_unit' => 'Unit Perancangan',
                'penerangan' => 'Unit perancangan strategik',
                'is_active' => true,
            ],

            // BPP - Bahagian Perolehan dan Pembangunan
            [
                'jabatan_id' => $jabatans['BPP']->id,
                'kod_seksyen_unit' => 'BPP-PER',
                'nama_seksyen_unit' => 'Unit Perolehan',
                'penerangan' => 'Unit perolehan dan kontrak',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['BPP']->id,
                'kod_seksyen_unit' => 'BPP-PEM',
                'nama_seksyen_unit' => 'Unit Pembangunan',
                'penerangan' => 'Unit pembangunan projek',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['BPP']->id,
                'kod_seksyen_unit' => 'BPP-AST',
                'nama_seksyen_unit' => 'Unit Pengurusan Aset',
                'penerangan' => 'Unit pengurusan aset kerajaan',
                'is_active' => true,
            ],

            // BPKK - Bahagian Pengurusan Kewangan dan Khidmat
            [
                'jabatan_id' => $jabatans['BPKK']->id,
                'kod_seksyen_unit' => 'BPKK-KEW',
                'nama_seksyen_unit' => 'Unit Kewangan',
                'penerangan' => 'Unit pengurusan kewangan',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['BPKK']->id,
                'kod_seksyen_unit' => 'BPKK-AKN',
                'nama_seksyen_unit' => 'Unit Akaun',
                'penerangan' => 'Unit akaun dan perakaunan',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['BPKK']->id,
                'kod_seksyen_unit' => 'BPKK-BYR',
                'nama_seksyen_unit' => 'Unit Bayaran',
                'penerangan' => 'Unit pemprosesan bayaran',
                'is_active' => true,
            ],

            // BKD - Bahagian Khidmat Domestik
            [
                'jabatan_id' => $jabatans['BKD']->id,
                'kod_seksyen_unit' => 'BKD-PEM',
                'nama_seksyen_unit' => 'Unit Pembersihan',
                'penerangan' => 'Unit perkhidmatan pembersihan',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['BKD']->id,
                'kod_seksyen_unit' => 'BKD-KES',
                'nama_seksyen_unit' => 'Unit Keselamatan',
                'penerangan' => 'Unit perkhidmatan keselamatan',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['BKD']->id,
                'kod_seksyen_unit' => 'BKD-SEL',
                'nama_seksyen_unit' => 'Unit Penyelenggaraan',
                'penerangan' => 'Unit penyelenggaraan bangunan',
                'is_active' => true,
            ],

            // BPNK - Bahagian Pembangunan Negeri Kedah
            [
                'jabatan_id' => $jabatans['BPNK']->id,
                'kod_seksyen_unit' => 'BPNK-INF',
                'nama_seksyen_unit' => 'Unit Infrastruktur',
                'penerangan' => 'Unit pembangunan infrastruktur',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['BPNK']->id,
                'kod_seksyen_unit' => 'BPNK-PEM',
                'nama_seksyen_unit' => 'Unit Pemantauan Projek',
                'penerangan' => 'Unit pemantauan pelaksanaan projek',
                'is_active' => true,
            ],

            // BPA - Bahagian Pentadbiran Am
            [
                'jabatan_id' => $jabatans['BPA']->id,
                'kod_seksyen_unit' => 'BPA-SDM',
                'nama_seksyen_unit' => 'Unit Sumber Manusia',
                'penerangan' => 'Unit pengurusan sumber manusia',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['BPA']->id,
                'kod_seksyen_unit' => 'BPA-LAT',
                'nama_seksyen_unit' => 'Unit Latihan',
                'penerangan' => 'Unit latihan dan pembangunan',
                'is_active' => true,
            ],

            // BICT - Bahagian ICT
            [
                'jabatan_id' => $jabatans['BICT']->id,
                'kod_seksyen_unit' => 'BICT-SIS',
                'nama_seksyen_unit' => 'Unit Sistem',
                'penerangan' => 'Unit pembangunan sistem',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['BICT']->id,
                'kod_seksyen_unit' => 'BICT-INF',
                'nama_seksyen_unit' => 'Unit Infrastruktur',
                'penerangan' => 'Unit infrastruktur IT',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['BICT']->id,
                'kod_seksyen_unit' => 'BICT-SOK',
                'nama_seksyen_unit' => 'Unit Sokongan',
                'penerangan' => 'Unit sokongan teknikal',
                'is_active' => true,
            ],

            // AUDIT - Unit Audit Dalam
            [
                'jabatan_id' => $jabatans['AUDIT']->id,
                'kod_seksyen_unit' => 'AUDIT-KEW',
                'nama_seksyen_unit' => 'Unit Audit Kewangan',
                'penerangan' => 'Unit audit kewangan',
                'is_active' => true,
            ],
            [
                'jabatan_id' => $jabatans['AUDIT']->id,
                'kod_seksyen_unit' => 'AUDIT-OPS',
                'nama_seksyen_unit' => 'Unit Audit Operasi',
                'penerangan' => 'Unit audit operasi',
                'is_active' => true,
            ],
        ];

        foreach ($seksyenUnits as $unit) {
            \App\Models\SeksyenUnit::create($unit);
        }
    }
}
