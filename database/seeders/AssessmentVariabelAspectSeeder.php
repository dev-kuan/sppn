<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentAspect;
use App\Models\AssessmentVariabel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssessmentVariabelAspectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============================================
        // 1. PEMBINAAN KEPRIBADIAN
        // ============================================
        $pembinaanKepribadian = AssessmentVariabel::create([
            'nama' => 'Pembinaan Kepribadian',
        ]);

        $aspects1 = [
            'Kesadaran Beragama',
            'Kesadaran Hukum & Kebangsaan',
            'Kemampuan Intelektual',
            'Kesehatan Jasmani',
            'Konseling & Rehabilitasi',
        ];

        foreach ($aspects1 as $aspect) {
            AssessmentAspect::create([
                'nama' => $aspect,
                'assessment_variabel_id' => $pembinaanKepribadian->id,
            ]);
        }

        // ============================================
        // 2. PEMBINAAN KEMANDIRIAN
        // ============================================
        $pembinaanKemandirian = AssessmentVariabel::create([
            'nama' => 'Pembinaan Kemandirian',
        ]);

        $aspects2 = [
            'Pelatihan Keterampilan',
            'Produksi Barang/Jasa',
        ];

        foreach ($aspects2 as $aspect) {
            AssessmentAspect::create([
                'nama' => $aspect,
                'assessment_variabel_id' => $pembinaanKemandirian->id,
            ]);
        }

        // ============================================
        // 3. SIKAP NARAPIDANA
        // ============================================
        $sikapNarapidana = AssessmentVariabel::create([
            'nama' => 'Penilaian Sikap',
        ]);

        $aspects3 = [
            'Keberfungsian & Rutinitas',
            'Agresi',
            'Pelanggaran Hukum',
            'Kemampuan Mempengaruhi',
            'Ekspresi Simbolik',
        ];

        foreach ($aspects3 as $aspect) {
            AssessmentAspect::create([
                'nama' => $aspect,
                'assessment_variabel_id' => $sikapNarapidana->id,
            ]);
        }

        // ============================================
        // 4. KONDISI MENTAL NARAPIDANA
        // ============================================
        $kondisiMental = AssessmentVariabel::create([
            'nama' => 'Penilaian Kondisi Mental',
        ]);

        $aspects4 = [
            'Depresi',
            'Kecemasan',
            'Psikosomatis',
            'Malingering',
            'Potensi Bunuh Diri',
        ];

        foreach ($aspects4 as $aspect) {
            AssessmentAspect::create([
                'nama' => $aspect,
                'assessment_variabel_id' => $kondisiMental->id,
            ]);
        }

        $this->command->info('Assessment Variabel & Aspect seeded successfully!');
        $this->command->info("- 4 Variabel created");
        $this->command->info("- " . AssessmentAspect::count() . " Aspek created");
    }
}
