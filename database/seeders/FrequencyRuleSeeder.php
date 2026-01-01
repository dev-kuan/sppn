<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FrequencyRule;

class FrequencyRuleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membaca Kitab Suci
        FrequencyRule::create([
            'nama_aturan' => 'Membaca Kitab Suci',
            'deskripsi' => 'Frekuensi untuk membaca dan/atau belajar Kitab Suci',
            'formula' => [
                ['max_days' => 7, 'frequency' => 8],
                ['max_days' => 14, 'frequency' => 6],
                ['max_days' => 21, 'frequency' => 4],
                ['max_days' => 28, 'frequency' => 2],
            ],
            'aktif' => true,
        ]);

        // 2. Kegiatan Berkala
        FrequencyRule::create([
            'nama_aturan' => 'Kegiatan Berkala',
            'deskripsi' => 'Ibadah tambahan, ceramah/khotbah, olahraga komunal, kesenian, kerja bakti',
            'formula' => [
                ['max_days' => 7, 'frequency' => 4],
                ['max_days' => 14, 'frequency' => 3],
                ['max_days' => 21, 'frequency' => 2],
                ['max_days' => 31, 'frequency' => 1],
            ],
            'aktif' => true,
        ]);

        // 3. Pelatihan dan Produksi
        FrequencyRule::create([
            'nama_aturan' => 'Pelatihan dan Produksi',
            'deskripsi' => 'Frekuensi untuk kegiatan pelatihan dan produksi kerja',
            'formula' => [
                ['max_days' => 7, 'frequency' => 20],
                ['max_days' => 14, 'frequency' => 15],
                ['max_days' => 21, 'frequency' => 10],
                ['max_days' => 28, 'frequency' => 2],
            ],
            'aktif' => true,
        ]);

        // 4. Selenggarakan?
        FrequencyRule::create([
            'nama_aturan' => 'Kegiatan diselenggarakan',
            'deskripsi' => 'Frekuensi untuk kegiatan yang diselenggarakan',
            'formula' => [
                ['diselenggarakan' => true, 'frequency' => 1],
            ],
            'aktif' => true,
        ]);

        $this->command->info('Frequency Rules seeded successfully!');
    }
}
