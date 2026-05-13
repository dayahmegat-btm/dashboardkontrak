<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KaedahPerolehanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kaedahPerolehan = [
            [
                'kod' => 'SST',
                'nama' => 'Surat Setuju Terima (SST)',
                'penerangan' => 'Kaedah perolehan langsung melalui Surat Setuju Terima',
                'is_active' => true,
            ],
            [
                'kod' => 'TH',
                'nama' => 'Tender Terhad',
                'penerangan' => 'Tender yang terhad kepada pembekal berdaftar',
                'is_active' => true,
            ],
            [
                'kod' => 'TB',
                'nama' => 'Tender Terbuka',
                'penerangan' => 'Tender terbuka kepada semua pembekal yang layak',
                'is_active' => true,
            ],
            [
                'kod' => 'SH',
                'nama' => 'Sebut Harga',
                'penerangan' => 'Perolehan melalui kaedah sebut harga',
                'is_active' => true,
            ],
            [
                'kod' => 'RN',
                'nama' => 'Rundingan Terus',
                'penerangan' => 'Rundingan terus dengan pembekal untuk kes khas',
                'is_active' => true,
            ],
        ];

        foreach ($kaedahPerolehan as $kaedah) {
            \App\Models\KaedahPerolehan::create($kaedah);
        }
    }
}
