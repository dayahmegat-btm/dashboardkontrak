<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            [
                'kod_jabatan' => 'SUK',
                'nama_jabatan' => 'Pejabat Setiausaha Kerajaan Negeri',
                'penerangan' => 'Pejabat Setiausaha Kerajaan Negeri Kedah',
                'is_active' => true,
            ],
            [
                'kod_jabatan' => 'BPP',
                'nama_jabatan' => 'Bahagian Perolehan dan Pembangunan',
                'penerangan' => 'Menguruskan perolehan dan pembangunan aset kerajaan',
                'is_active' => true,
            ],
            [
                'kod_jabatan' => 'BPKK',
                'nama_jabatan' => 'Bahagian Pengurusan Kewangan dan Khidmat',
                'penerangan' => 'Menguruskan kewangan dan perkhidmatan pentadbiran',
                'is_active' => true,
            ],
            [
                'kod_jabatan' => 'BKD',
                'nama_jabatan' => 'Bahagian Khidmat Domestik',
                'penerangan' => 'Menguruskan perkhidmatan sokongan domestik',
                'is_active' => true,
            ],
            [
                'kod_jabatan' => 'BPNK',
                'nama_jabatan' => 'Bahagian Pembangunan Negeri Kedah',
                'penerangan' => 'Menguruskan pembangunan infrastruktur negeri',
                'is_active' => true,
            ],
            [
                'kod_jabatan' => 'BPA',
                'nama_jabatan' => 'Bahagian Pentadbiran Am',
                'penerangan' => 'Menguruskan pentadbiran am dan sumber manusia',
                'is_active' => true,
            ],
            [
                'kod_jabatan' => 'BICT',
                'nama_jabatan' => 'Bahagian ICT',
                'penerangan' => 'Menguruskan teknologi maklumat dan komunikasi',
                'is_active' => true,
            ],
            [
                'kod_jabatan' => 'AUDIT',
                'nama_jabatan' => 'Unit Audit Dalam',
                'penerangan' => 'Menjalankan audit dalaman dan pematuhan',
                'is_active' => true,
            ],
        ];

        foreach ($jabatans as $jabatan) {
            \App\Models\Jabatan::create($jabatan);
        }
    }
}
