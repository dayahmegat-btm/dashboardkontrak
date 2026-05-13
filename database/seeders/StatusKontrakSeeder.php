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
                'kod' => 'BARU',
                'nama' => 'Baru',
                'warna' => 'blue',
                'urutan' => 2,
                'is_active' => true,
            ],
            [
                'kod' => 'AKTIF',
                'nama' => 'Aktif',
                'warna' => 'green',
                'urutan' => 3,
                'is_active' => true,
            ],
            [
                'kod' => 'HAMPIR',
                'nama' => 'Hampir Tamat',
                'warna' => 'yellow',
                'urutan' => 4,
                'is_active' => true,
            ],
            [
                'kod' => 'TAMAT',
                'nama' => 'Tamat',
                'warna' => 'orange',
                'urutan' => 5,
                'is_active' => true,
            ],
            [
                'kod' => 'LANJUT',
                'nama' => 'Dalam Proses Lanjutan',
                'warna' => 'purple',
                'urutan' => 6,
                'is_active' => true,
            ],
            [
                'kod' => 'DITAMAT',
                'nama' => 'Ditamatkan Awal',
                'warna' => 'red',
                'urutan' => 7,
                'is_active' => true,
            ],
            [
                'kod' => 'LENGKAP',
                'nama' => 'Lengkap',
                'warna' => 'emerald',
                'urutan' => 8,
                'is_active' => true,
            ],
            [
                'kod' => 'BATAL',
                'nama' => 'Dibatalkan',
                'warna' => 'red',
                'urutan' => 9,
                'is_active' => true,
            ],
        ];

        foreach ($statusKontrak as $status) {
            \App\Models\StatusKontrak::create($status);
        }
    }
}
