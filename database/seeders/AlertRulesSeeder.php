<?php

namespace Database\Seeders;

use App\Models\AlertRule;
use Illuminate\Database\Seeder;

class AlertRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            // ALR-001: Kategori 1 Contract Alert
            [
                'kod_alert' => 'ALR-001',
                'nama_alert' => 'Amaran Kontrak Kategori 1',
                'penerangan' => 'SST dikeluarkan, deraf tidak dihantar ke PUU, kontrak akan tamat dalam 6 bulan',
                'trigger_type' => 'kategori_1_contract',
                'trigger_conditions' => [
                    'has_sst' => true,
                    'no_draft_to_puu' => true,
                    'months_until_expiry' => 6,
                ],
                'days_before' => null,
                'schedule' => 'daily',
                'recipient_roles' => ['pic', 'ketua-unit'],
                'recipient_emails' => null,
                'email_subject' => '[KRITIKAL] Kontrak Kategori 1: {no_kontrak}',
                'email_body' => "Amaran Kontrak Kategori 1\n\nNo. Kontrak: {no_kontrak}\nNo. SST: {no_sst}\nPembekal: {pembekal}\nTarikh Tamat: {tarikh_tamat}\nHari Lagi: {days_until_expiry} hari\n\nTindakan diperlukan: Sila hantar deraf kontrak ke Pejabat Undang-Undang (PUU) segera.",
                'notification_message' => 'Kontrak {no_kontrak} adalah Kategori 1. Deraf perlu dihantar ke PUU. Kontrak akan tamat dalam {days_until_expiry} hari.',
                'priority' => 'critical',
                'is_active' => true,
            ],

            // ALR-002: Kategori 2 Contract Alert
            [
                'kod_alert' => 'ALR-002',
                'nama_alert' => 'Amaran Kontrak Kategori 2',
                'penerangan' => 'SST dikeluarkan (4+ bulan), tiada deraf ke PUU',
                'trigger_type' => 'kategori_2_contract',
                'trigger_conditions' => [
                    'has_sst' => true,
                    'no_draft_to_puu' => true,
                    'months_since_sst_created' => 4,
                ],
                'days_before' => null,
                'schedule' => 'daily',
                'recipient_roles' => ['pic', 'ketua-unit', 'pengarah'],
                'recipient_emails' => null,
                'email_subject' => '[TINGGI] Kontrak Kategori 2: {no_kontrak}',
                'email_body' => "Amaran Kontrak Kategori 2\n\nNo. Kontrak: {no_kontrak}\nNo. SST: {no_sst}\nPembekal: {pembekal}\nTarikh SST Didaftar: {tarikh_sst}\nBulan Sejak SST: {months_since_sst} bulan\n\nTindakan diperlukan: SST telah didaftarkan lebih 4 bulan. Sila hantar deraf kontrak ke PUU dengan segera.",
                'notification_message' => 'Kontrak {no_kontrak} adalah Kategori 2. SST didaftarkan sejak {months_since_sst} bulan lalu. Deraf perlu dihantar ke PUU.',
                'priority' => 'high',
                'is_active' => true,
            ],

            // ALR-003: Bond Expiry - 180 days
            [
                'kod_alert' => 'ALR-003',
                'nama_alert' => 'Bon Pelaksanaan Akan Tamat - 180 Hari',
                'penerangan' => 'Bon pelaksanaan akan tamat dalam 180 hari',
                'trigger_type' => 'bond_expiry',
                'trigger_conditions' => [
                    'status' => 'aktif',
                    'days_before_expiry' => 180,
                ],
                'days_before' => 180,
                'schedule' => 'daily',
                'recipient_roles' => ['pic'],
                'recipient_emails' => null,
                'email_subject' => '[MAKLUMAT] Bon Pelaksanaan Akan Tamat - {no_bon}',
                'email_body' => "Peringatan Bon Pelaksanaan\n\nNo. Bon: {no_bon}\nNo. Kontrak: {no_kontrak}\nJenis Bon: {jenis_bon}\nNilai: RM {nilai_bon}\nInstitusi: {institusi}\nTarikh Tamat: {tarikh_tamat}\nHari Lagi: {days_until_expiry} hari (6 bulan)\n\nTindakan: Sila pastikan pembaharuan bon diproses sekiranya diperlukan.",
                'notification_message' => 'Bon {no_bon} akan tamat dalam {days_until_expiry} hari. Nilai: RM {nilai_bon}',
                'priority' => 'medium',
                'is_active' => true,
            ],

            // ALR-004: Bond Expiry - 90 days
            [
                'kod_alert' => 'ALR-004',
                'nama_alert' => 'Bon Pelaksanaan Akan Tamat - 90 Hari',
                'penerangan' => 'Bon pelaksanaan akan tamat dalam 90 hari',
                'trigger_type' => 'bond_expiry',
                'trigger_conditions' => [
                    'status' => 'aktif',
                    'days_before_expiry' => 90,
                ],
                'days_before' => 90,
                'schedule' => 'daily',
                'recipient_roles' => ['pic', 'ketua-unit'],
                'recipient_emails' => null,
                'email_subject' => '[AMARAN] Bon Pelaksanaan Akan Tamat - {no_bon}',
                'email_body' => "Amaran Bon Pelaksanaan\n\nNo. Bon: {no_bon}\nNo. Kontrak: {no_kontrak}\nJenis Bon: {jenis_bon}\nNilai: RM {nilai_bon}\nInstitusi: {institusi}\nTarikh Tamat: {tarikh_tamat}\nHari Lagi: {days_until_expiry} hari (3 bulan)\n\nTindakan Segera: Pastikan pembaharuan bon sedang diproses atau maklumkan kepada pembekal.",
                'notification_message' => 'AMARAN: Bon {no_bon} akan tamat dalam {days_until_expiry} hari. Tindakan diperlukan.',
                'priority' => 'high',
                'is_active' => true,
            ],

            // ALR-005: Bond Expiry - 30 days
            [
                'kod_alert' => 'ALR-005',
                'nama_alert' => 'Bon Pelaksanaan Akan Tamat - 30 Hari',
                'penerangan' => 'Bon pelaksanaan akan tamat dalam 30 hari',
                'trigger_type' => 'bond_expiry',
                'trigger_conditions' => [
                    'status' => 'aktif',
                    'days_before_expiry' => 30,
                ],
                'days_before' => 30,
                'schedule' => 'daily',
                'recipient_roles' => ['pic', 'ketua-unit', 'pengarah'],
                'recipient_emails' => null,
                'email_subject' => '[TINGGI] Bon Pelaksanaan Akan Tamat - {no_bon}',
                'email_body' => "Amaran Tinggi Bon Pelaksanaan\n\nNo. Bon: {no_bon}\nNo. Kontrak: {no_kontrak}\nJenis Bon: {jenis_bon}\nNilai: RM {nilai_bon}\nInstitusi: {institusi}\nTarikh Tamat: {tarikh_tamat}\nHari Lagi: {days_until_expiry} hari (1 bulan)\n\nTindakan Kritikal: Pastikan bon diperbaharui SEGERA atau kontrak mungkin tergendala.",
                'notification_message' => 'TINGGI: Bon {no_bon} akan tamat dalam {days_until_expiry} hari sahaja!',
                'priority' => 'high',
                'is_active' => true,
            ],

            // ALR-006: Bond Expiry - 7 days
            [
                'kod_alert' => 'ALR-006',
                'nama_alert' => 'Bon Pelaksanaan Akan Tamat - 7 Hari',
                'penerangan' => 'Bon pelaksanaan akan tamat dalam 7 hari',
                'trigger_type' => 'bond_expiry',
                'trigger_conditions' => [
                    'status' => 'aktif',
                    'days_before_expiry' => 7,
                ],
                'days_before' => 7,
                'schedule' => 'daily',
                'recipient_roles' => ['pic', 'ketua-unit', 'pengarah', 'sk-exec'],
                'recipient_emails' => null,
                'email_subject' => '[KRITIKAL] Bon Pelaksanaan Akan Tamat - {no_bon}',
                'email_body' => "AMARAN KRITIKAL BON PELAKSANAAN\n\nNo. Bon: {no_bon}\nNo. Kontrak: {no_kontrak}\nJenis Bon: {jenis_bon}\nNilai: RM {nilai_bon}\nInstitusi: {institusi}\nTarikh Tamat: {tarikh_tamat}\nHari Lagi: {days_until_expiry} hari sahaja!\n\nTINDAKAN SEGERA DIPERLUKAN: Bon akan tamat dalam masa terdekat. Hubungi pembekal dan institusi penjamin dengan segera.",
                'notification_message' => 'KRITIKAL: Bon {no_bon} akan tamat dalam {days_until_expiry} hari sahaja! TINDAKAN SEGERA DIPERLUKAN.',
                'priority' => 'critical',
                'is_active' => true,
            ],

            // ALR-007: Bond Return - 30 days after completion
            [
                'kod_alert' => 'ALR-007',
                'nama_alert' => 'Penyerahan Balik Bon - 30 Hari Selepas Siap',
                'penerangan' => 'Bon belum diserahkan balik selepas 30 hari kontrak siap',
                'trigger_type' => 'bond_return',
                'trigger_conditions' => [
                    'contract_completed' => true,
                    'bond_not_returned' => true,
                    'days_after_completion' => 30,
                ],
                'days_before' => null,
                'schedule' => 'daily',
                'recipient_roles' => ['pic'],
                'recipient_emails' => null,
                'email_subject' => '[PERINGATAN] Penyerahan Balik Bon - {no_bon}',
                'email_body' => "Peringatan Penyerahan Balik Bon\n\nNo. Bon: {no_bon}\nNo. Kontrak: {no_kontrak}\nTarikh Kontrak Siap: {tarikh_siap}\nHari Selepas Siap: {days_after_completion} hari\nNilai Bon: RM {nilai_bon}\nInstitusi: {institusi}\n\nTindakan: Sila proses penyerahan balik bon kepada pembekal.",
                'notification_message' => 'Kontrak {no_kontrak} telah siap {days_after_completion} hari lalu. Bon {no_bon} perlu diserahkan balik.',
                'priority' => 'medium',
                'is_active' => true,
            ],

            // ALR-008: Bond Return - 60 days after completion
            [
                'kod_alert' => 'ALR-008',
                'nama_alert' => 'Penyerahan Balik Bon - 60 Hari Selepas Siap',
                'penerangan' => 'Bon belum diserahkan balik selepas 60 hari kontrak siap',
                'trigger_type' => 'bond_return',
                'trigger_conditions' => [
                    'contract_completed' => true,
                    'bond_not_returned' => true,
                    'days_after_completion' => 60,
                ],
                'days_before' => null,
                'schedule' => 'daily',
                'recipient_roles' => ['pic', 'ketua-unit'],
                'recipient_emails' => null,
                'email_subject' => '[AMARAN] Penyerahan Balik Bon Lewat - {no_bon}',
                'email_body' => "Amaran Penyerahan Balik Bon\n\nNo. Bon: {no_bon}\nNo. Kontrak: {no_kontrak}\nTarikh Kontrak Siap: {tarikh_siap}\nHari Selepas Siap: {days_after_completion} hari\nNilai Bon: RM {nilai_bon}\nInstitusi: {institusi}\n\nTindakan Segera: Penyerahan balik bon sudah lewat. Sila proses segera untuk mengelakkan isu audit.",
                'notification_message' => 'AMARAN: Bon {no_bon} belum diserahkan balik selepas {days_after_completion} hari. Tindakan segera diperlukan.',
                'priority' => 'high',
                'is_active' => true,
            ],

            // ALR-009: Bond Return - 90 days after completion
            [
                'kod_alert' => 'ALR-009',
                'nama_alert' => 'Penyerahan Balik Bon - 90 Hari Selepas Siap',
                'penerangan' => 'Bon belum diserahkan balik selepas 90 hari kontrak siap',
                'trigger_type' => 'bond_return',
                'trigger_conditions' => [
                    'contract_completed' => true,
                    'bond_not_returned' => true,
                    'days_after_completion' => 90,
                ],
                'days_before' => null,
                'schedule' => 'daily',
                'recipient_roles' => ['pic', 'ketua-unit', 'pengarah', 'sk-exec'],
                'recipient_emails' => null,
                'email_subject' => '[KRITIKAL] Penyerahan Balik Bon Lewat - {no_bon}',
                'email_body' => "AMARAN KRITIKAL PENYERAHAN BALIK BON\n\nNo. Bon: {no_bon}\nNo. Kontrak: {no_kontrak}\nTarikh Kontrak Siap: {tarikh_siap}\nHari Selepas Siap: {days_after_completion} hari\nNilai Bon: RM {nilai_bon}\nInstitusi: {institusi}\n\nTINDAKAN KRITIKAL: Bon belum diserahkan balik selepas 90 hari. Ini adalah isu audit. Sila ambil tindakan segera dan laporkan status.",
                'notification_message' => 'KRITIKAL: Bon {no_bon} belum diserahkan balik selepas {days_after_completion} hari. ISU AUDIT!',
                'priority' => 'critical',
                'is_active' => true,
            ],

            // ALR-010: Performance Evaluation Monthly Reminder
            [
                'kod_alert' => 'ALR-010',
                'nama_alert' => 'Peringatan Penilaian Prestasi Bulanan',
                'penerangan' => 'Peringatan bulanan untuk menilai prestasi pembekal kontrak aktif',
                'trigger_type' => 'performance_evaluation',
                'trigger_conditions' => [
                    'contract_active' => true,
                    'no_evaluation_this_month' => true,
                ],
                'days_before' => null,
                'schedule' => 'monthly',
                'recipient_roles' => ['pic', 'ketua-unit'],
                'recipient_emails' => null,
                'email_subject' => '[PERINGATAN] Penilaian Prestasi Bulan {bulan}',
                'email_body' => "Peringatan Penilaian Prestasi Pembekal\n\nNo. Kontrak: {no_kontrak}\nNo. SST: {no_sst}\nPembekal: {pembekal}\nBulan: {bulan}\n\nTindakan: Sila lakukan penilaian prestasi pembekal untuk kontrak ini bagi bulan semasa. Penilaian perlu dilengkapkan sebelum akhir bulan.",
                'notification_message' => 'Peringatan: Sila nilai prestasi pembekal untuk kontrak {no_kontrak} bagi bulan {bulan}.',
                'priority' => 'medium',
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            AlertRule::updateOrCreate(
                ['kod_alert' => $rule['kod_alert']],
                $rule
            );
        }

        $this->command->info('✅ ' . count($rules) . ' alert rules seeded successfully');
    }
}
