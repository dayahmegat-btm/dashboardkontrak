<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusKontrakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statusKontrak = [
            [
                'kod' => 'DERAF',
                'nama' => 'Deraf',
                'warna' => 'gray',
                'urutan' => 1,
                'is_active' => true,
            ],
            [
                'kod' => 'HANTAR',
                'nama' => 'Dihantar Untuk Kelulusan',
                'warna' => 'info',
                'urutan' => 2,
                'is_active' => true,
            ],
            [
                'kod' => 'SEMAK',
                'nama' => 'Dalam Semakan',
                'warna' => 'warning',
                'urutan' => 3,
                'is_active' => true,
            ],
            [
                'kod' => 'LULUS',
                'nama' => 'Diluluskan',
                'warna' => 'success',
                'urutan' => 4,
                'is_active' => true,
            ],
            [
                'kod' => 'TOLAK',
                'nama' => 'Ditolak',
                'warna' => 'danger',
                'urutan' => 5,
                'is_active' => true,
            ],
            [
                'kod' => 'BARU',
                'nama' => 'Baru',
                'warna' => 'blue',
                'urutan' => 6,
                'is_active' => true,
            ],
            [
                'kod' => 'AKTIF',
                'nama' => 'Aktif',
                'warna' => 'green',
                'urutan' => 7,
                'is_active' => true,
            ],
            [
                'kod' => 'HAMPIR',
                'nama' => 'Hampir Tamat',
                'warna' => 'yellow',
                'urutan' => 8,
                'is_active' => true,
            ],
            [
                'kod' => 'TAMAT',
                'nama' => 'Tamat',
                'warna' => 'orange',
                'urutan' => 9,
                'is_active' => true,
            ],
            [
                'kod' => 'LANJUT',
                'nama' => 'Dalam Proses Lanjutan',
                'warna' => 'purple',
                'urutan' => 10,
                'is_active' => true,
            ],
            [
                'kod' => 'DITAMAT',
                'nama' => 'Ditamatkan Awal',
                'warna' => 'red',
                'urutan' => 11,
                'is_active' => true,
            ],
            [
                'kod' => 'LENGKAP',
                'nama' => 'Lengkap',
                'warna' => 'emerald',
                'urutan' => 12,
                'is_active' => true,
            ],
            [
                'kod' => 'BATAL',
                'nama' => 'Dibatalkan',
                'warna' => 'red',
                'urutan' => 13,
                'is_active' => true,
            ],
        ];

        foreach ($statusKontrak as $status) {
            \App\Models\StatusKontrak::updateOrCreate(
                ['kod' => $status['kod']],
                $status
            );
        }
    }
}
