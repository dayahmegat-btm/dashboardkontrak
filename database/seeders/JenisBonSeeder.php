<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisBonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisBon = [
            [
                'kod' => 'PB',
                'nama' => 'Performance Bond',
                'penerangan' => 'Bon Pelaksanaan untuk menjamin pelaksanaan kontrak',
                'is_active' => true,
            ],
            [
                'kod' => 'MB',
                'nama' => 'Maintenance Bond',
                'penerangan' => 'Bon Penyelenggaraan untuk tempoh tanggungan kecacatan',
                'is_active' => true,
            ],
            [
                'kod' => 'JB',
                'nama' => 'Jaminan Bank',
                'penerangan' => 'Jaminan bank untuk memastikan pembayaran',
                'is_active' => true,
            ],
            [
                'kod' => 'INS',
                'nama' => 'Insurans Kontrak',
                'penerangan' => 'Insurans untuk melindungi kontrak dari risiko',
                'is_active' => true,
            ],
        ];

        foreach ($jenisBon as $jenis) {
            \App\Models\JenisBon::create($jenis);
        }
    }
}
