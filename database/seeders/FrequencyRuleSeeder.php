<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FrequencyRule;

class FrequencyRuleSeeder extends Seeder
{
    public function run(): void
    {
        FrequencyRule::create([
            'nama_aturan' => 'Membaca Kitab Suci',
            'deskripsi' => 'Target frekuensi membaca dan/atau belajar Kitab Suci berdasarkan panjang periode penilaian',
            'formula' => [
                'mode' => 'THRESHOLD_BASED',
                'unit' => 'PERIOD',
                'thresholds' => [
                    ['max_days' => 7,  'frequency' => 8],
                    ['max_days' => 14, 'frequency' => 6],
                    ['max_days' => 21, 'frequency' => 4],
                    ['max_days' => 31, 'frequency' => 2],
                ],
                'fallback' => 0,
            ],
            'aktif' => true,
        ]);

        /**
         * 2. Kegiatan Berkala
         * Aktivitas komunal / berkala dengan target minimum
         */
        FrequencyRule::create([
            'nama_aturan' => 'Kegiatan Berkala',
            'deskripsi' => 'Target frekuensi kegiatan berkala (ceramah, olahraga komunal, kesenian, kerja bakti)',
            'formula' => [
                'mode' => 'THRESHOLD_BASED',
                'unit' => 'PERIOD',
                'thresholds' => [
                    ['max_days' => 7,  'frequency' => 4],
                    ['max_days' => 14, 'frequency' => 3],
                    ['max_days' => 21, 'frequency' => 2],
                    ['max_days' => 31, 'frequency' => 1],
                ],
                'fallback' => 0,
            ],
            'aktif' => true,
        ]);

        /**
         * 3. Pelatihan dan Produksi
         * Kegiatan intensif dengan target tinggi pada periode pendek
         */
        FrequencyRule::create([
            'nama_aturan' => 'Pelatihan dan Produksi',
            'deskripsi' => 'Target frekuensi kegiatan pelatihan dan produksi kerja berdasarkan durasi periode',
            'formula' => [
                'mode' => 'THRESHOLD_BASED',
                'unit' => 'PERIOD',
                'thresholds' => [
                    ['max_days' => 7,  'frequency' => 20],
                    ['max_days' => 14, 'frequency' => 15],
                    ['max_days' => 21, 'frequency' => 10],
                    ['max_days' => 31, 'frequency' => 2],
                ],
                'fallback' => 0,
            ],
            'aktif' => true,
        ]);

        /**
         * 4. Kegiatan Diselenggarakan
         * Kegiatan kondisional (hanya dinilai jika kegiatan ada)
         */
        FrequencyRule::create([
            'nama_aturan' => 'Kegiatan Diselenggarakan',
            'deskripsi' => 'Frekuensi untuk kegiatan yang hanya dinilai apabila diselenggarakan',
            'formula' => [
                'mode' => 'EVENT_BASED',
                'event' => 'diselenggarakan',
                'frequency' => 1,
            ],
            'aktif' => true,
        ]);


        $this->command->info('Frequency Rules seeded successfully!');
    }
}
