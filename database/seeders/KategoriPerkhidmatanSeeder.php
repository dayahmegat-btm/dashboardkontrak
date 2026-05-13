<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriPerkhidmatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriPerkhidmatan = [
            [
                'kod' => 'PER',
                'nama' => 'Perkhidmatan',
                'penerangan' => 'Perkhidmatan seperti pembersihan, keselamatan, penyelenggaraan',
                'is_active' => true,
            ],
            [
                'kod' => 'BEK',
                'nama' => 'Bekalan',
                'penerangan' => 'Bekalan barangan dan peralatan',
                'is_active' => true,
            ],
            [
                'kod' => 'KERJA',
                'nama' => 'Kerja',
                'penerangan' => 'Kerja pembinaan dan penyelenggaraan',
                'is_active' => true,
            ],
            [
                'kod' => 'IT',
                'nama' => 'Perkhidmatan IT',
                'penerangan' => 'Perkhidmatan teknologi maklumat dan sistem',
                'is_active' => true,
            ],
            [
                'kod' => 'KONST',
                'nama' => 'Konsultansi',
                'penerangan' => 'Perkhidmatan konsultansi dan nasihat profesional',
                'is_active' => true,
            ],
            [
                'kod' => 'SEWA',
                'nama' => 'Sewaan',
                'penerangan' => 'Sewaan peralatan, kenderaan, dan aset',
                'is_active' => true,
            ],
        ];

        foreach ($kategoriPerkhidmatan as $kategori) {
            \App\Models\KategoriPerkhidmatan::create($kategori);
        }
    }
}
