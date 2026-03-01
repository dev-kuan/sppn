<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ObservationItem;
use App\Models\AssessmentVariabel;
use App\Models\AssessmentAspect;

class ObservationItemSeeder extends Seeder
{
    public function run(): void
    {
        $sortOrder = 1;

        // Get Variabel IDs
        $pembinaanKepribadian = AssessmentVariabel::where('nama', 'Pembinaan Kepribadian')->first();
        $pembinaanKemandirian = AssessmentVariabel::where('nama', 'Pembinaan Kemandirian')->first();
        $sikapNarapidana = AssessmentVariabel::where('nama', 'Penilaian Sikap')->first();
        $kondisiMental = AssessmentVariabel::where('nama', 'Penilaian Kondisi Mental')->first();
        $pernyataanKomitmen = AssessmentVariabel::where('nama', 'Pernyataan Komitmen')->first();

        $this->seedPembinaanKepribadian($pembinaanKepribadian->id, $sortOrder);
        $this->seedPembinaanKemandirian($pembinaanKemandirian->id, $sortOrder);
        $this->seedSikapNarapidana($sikapNarapidana->id, $sortOrder);
        $this->seedKondisiMental($kondisiMental->id, $sortOrder);
        $this->seedPernyataanKomitmen($pernyataanKomitmen->id, $sortOrder);

        $this->command->info('All Observation Items seeded successfully!');
    }

    private function seedPembinaanKepribadian($variabelId, &$sortOrder)
    {
        // Get Aspek IDs
        $kesadaranBeragama = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Kesadaran Beragama')->first();
        $kesadaranHukum = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Kesadaran Hukum & Kebangsaan')->first();
        $kemampuanIntelektual = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Kemampuan Intelektual')->first();
        $kesehatanJasmani = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Kesehatan Jasmani')->first();
        $konselingRehab = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Konseling & Rehabilitasi')->first();

        // 1. Kesadaran Beragama
        $kesadaranBeragamaItems = [
            ['kode' => "PK-KB-01", 'nama_item' => 'Membaca dan/atau belajar Kitab Suci', 'bobot' => 1.04, 'jenis_frekuensi' => 'Mingguan2',],
            ['kode' => "PK-KB-02", 'nama_item' => 'Ibadah tepat waktu/rutin', 'bobot' => 1.08, 'jenis_frekuensi' => 'Harian',],
            ['kode' => "PK-KB-03", 'nama_item' => 'Membaca dan/atau belajar Kitab Suci', 'bobot' => 0.87, 'jenis_frekuensi' => 'Mingguan1',],
            ['kode' => "PK-KB-04", 'nama_item' => 'Melaksanakan ibadah di luar yang wajib', 'bobot' => 0.97, 'jenis_frekuensi' => 'Mingguan1',],
            ['kode' => "PK-KB-05", 'nama_item' => 'Mengikuti ibadah secara berkelompok', 'bobot' => 1.03, 'jenis_frekuensi' => 'Fix',],
        ];

        foreach($kesadaranBeragamaItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $kesadaranBeragama->id,
                'nama_item' => $item['nama_item'],
                'bobot' => $item['bobot'],
                'bobot_default' => $item['bobot'], // ✅ Set bobot_default
                'use_dynamic_frequency' => true,
                'jenis_frekuensi' => $item['jenis_frekuensi'],
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        // 2. Kesadaran Hukum & Kebangsaan
        $kesadaranHukumItems = [
            ['kode' => "PK-KH-01", 'nama_item' => 'Mengikuti penyuluhan wawasan nusantara', 'bobot' => 1.04,],
            ['kode' => "PK-KH-02", 'nama_item' => 'Mengikuti penyuluhan hukum dampak dan bahaya tindak pidana', 'bobot' => 1.08,],
            ['kode' => "PK-KH-03", 'nama_item' => 'Memperoleh nilai evaluasi materi penyuluhan', 'bobot' => 0.87,],
            ['kode' => "PK-KH-04", 'nama_item' => 'Mengikuti upacara', 'bobot' => 1.00,],
            ['kode' => "PK-KH-05", 'nama_item' => 'Hormati bendera saat upacara', 'bobot' => 1.04,],
        ];

        foreach($kesadaranHukumItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $kesadaranHukum->id,
                'nama_item' => $item['nama_item'],
                'bobot' => $item['bobot'],
                'bobot_default' => $item['bobot'], // ✅ Set bobot_default
                'jenis_frekuensi' => 'Fix',
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        // ✅ ITEM KONDISIONAL #1: Mengisi lembar self-assessment
        ObservationItem::create([
            'kode' => 'PK-KH-06',
            'variabel_id' => $variabelId,
            'aspect_id' => $kesadaranHukum->id,
            'nama_item' => 'Mengisi lembar self-assessment',
            'bobot' => 1.00, // ✅ Default 1.00, akan di-override jadi 0 atau 1 via modal
            'bobot_default' => 1.00, // ✅ Simpan default value
            'is_conditional_weight' => true,
            'use_dynamic_frequency' => true,
            'jenis_frekuensi' => 'Kondisional',
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        // ✅ ITEM KONDISIONAL #2: Mengikuti pramuka
        ObservationItem::create([
            'kode' => 'PK-KH-07',
            'variabel_id' => $variabelId,
            'aspect_id' => $kesadaranHukum->id,
            'nama_item' => 'Mengikuti pramuka',
            'bobot' => 1.00, // ✅ Default 1.00
            'bobot_default' => 1.00,
            'is_conditional_weight' => true,
            'use_dynamic_frequency' => true,
            'jenis_frekuensi' => 'Kondisional',
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        // 3. Kemampuan Intelektual
        ObservationItem::create([
            'kode' => 'PK-KI-01',
            'variabel_id' => $variabelId,
            'aspect_id' => $kemampuanIntelektual->id,
            'nama_item' => 'Membaca buku di perpustakaan',
            'bobot' => 1.01,
            'bobot_default' => 1.01, // ✅ Set bobot_default
            'jenis_frekuensi' => 'Fix',
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        // ✅ ITEM KONDISIONAL #3: Mengikuti pendidikan Paket A/B/C
        ObservationItem::create([
            'kode' => 'PK-KI-02',
            'variabel_id' => $variabelId,
            'aspect_id' => $kemampuanIntelektual->id,
            'nama_item' => 'Mengikuti pendidikan Paket A/B/C',
            'bobot' => 1.00, // ✅ Default 1.00
            'bobot_default' => 1.00,
            'is_conditional_weight' => true,
            'jenis_frekuensi' => 'Kondisional',
            'use_dynamic_frequency' => true,
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        ObservationItem::create([
            'kode' => 'PK-KI-03',
            'variabel_id' => $variabelId,
            'aspect_id' => $kemampuanIntelektual->id,
            'nama_item' => 'Mengikuti materi CMT & LST',
            'bobot' => 1.01,
            'bobot_default' => 1.01, // ✅ Set bobot_default
            'jenis_frekuensi' => 'Fix',
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        // 4. Kesehatan Jasmani
        ObservationItem::create([
            'kode' => 'PK-KJ-01',
            'variabel_id' => $variabelId,
            'aspect_id' => $kesehatanJasmani->id,
            'nama_item' => 'Mengikuti kegiatan rekreasi',
            'bobot' => 0.95,
            'bobot_default' => 0.95, // ✅ Set bobot_default
            'use_dynamic_frequency' => true,
            'jenis_frekuensi' => 'Harian',
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        ObservationItem::create([
            'kode' => 'PK-KJ-02',
            'variabel_id' => $variabelId,
            'aspect_id' => $kesehatanJasmani->id,
            'nama_item' => 'Mengikuti olahraga luar ruangan (komunal)',
            'bobot' => 1.06,
            'bobot_default' => 1.06, // ✅ Set bobot_default
            'jenis_frekuensi' => 'Mingguan1',
            'use_dynamic_frequency' => true,
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        ObservationItem::create([
            'kode' => 'PK-KJ-03',
            'variabel_id' => $variabelId,
            'aspect_id' => $kesehatanJasmani->id,
            'nama_item' => 'Mengikuti kegiatan kesenian',
            'bobot' => 0.99,
            'bobot_default' => 0.99, // ✅ Set bobot_default
            'jenis_frekuensi' => 'Mingguan1',
            'use_dynamic_frequency' => true,
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        // 5. Konseling & Rehabilitasi - ✅ SEMUA ITEM KONDISIONAL
        $konselingItems = [
            ['kode' => "PK-KR-01", 'nama_item' => 'Mengikuti konseling psikologi'],
            ['kode' => "PK-KR-02", 'nama_item' => 'Mengikuti rehabilitasi sosial'],
            ['kode' => "PK-KR-03", 'nama_item' => 'Mengikuti rehabilitasi medis'],
        ];

        foreach ($konselingItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $konselingRehab->id,
                'nama_item' => $item['nama_item'],
                'bobot' => 1.00, // ✅ Default 1.00
                'bobot_default' => 1.00,
                'is_conditional_weight' => true,
                'jenis_frekuensi' => 'Kondisional',
                'use_dynamic_frequency' => true,
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        $this->command->info('Observation Items - Pembinaan Kepribadian seeded!');
    }

    private function seedPembinaanKemandirian($variabelId, &$sortOrder)
    {
        // Get Aspek IDs
        $pelatihanKeterampilan = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Pelatihan Keterampilan')->first();
        $produksiBarang = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Produksi Barang/Jasa')->first();

        // 1. Pelatihan Keterampilan
        $pelatihanItems = [
            ['kode' => 'PM-PK-01', 'nama' => 'Hadir tepat waktu', 'bobot' => 1.03,],
            ['kode' => 'PM-PK-02', 'nama' => 'Mengikuti seluruh kegiatan pelatihan', 'bobot' => 1.04],
            ['kode' => 'PM-PK-03', 'nama' => 'Mematuhi peraturan sesuai prosedur kegiatan', 'bobot' => 1.04],
            ['kode' => 'PM-PK-04', 'nama' => 'Mematuhi peraturan dalam hubungan kerja', 'bobot' => 1.05],
            ['kode' => 'PM-PK-07', 'nama' => 'Menerapkan prosedur K3 dengan baik', 'bobot' => 1.00],
        ];

        foreach ($pelatihanItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $pelatihanKeterampilan->id,
                'nama_item' => $item['nama'],
                'bobot' => $item['bobot'],
                'bobot_default' => $item['bobot'], // ✅ Set bobot_default
                'jenis_frekuensi' => 'Mingguan3',
                'use_dynamic_frequency' => true,
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        ObservationItem::create([
            'kode' => 'PM-PK-05',
            'variabel_id' => $variabelId,
            'aspect_id' => $pelatihanKeterampilan->id,
            'nama_item' => 'Mendapatkan skor post test pengetahuan minimal 60',
            'bobot' => 0.93,
            'bobot_default' => 0.93, // ✅ Set bobot_default
            'jenis_frekuensi' => 'Fix',
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        ObservationItem::create([
            'kode' => 'PM-PK-06',
            'variabel_id' => $variabelId,
            'aspect_id' => $pelatihanKeterampilan->id,
            'nama_item' => 'Mendapatkan skor tes keterampilan minimal 60',
            'bobot' => 0.92,
            'bobot_default' => 0.92, // ✅ Set bobot_default
            'jenis_frekuensi' => 'Fix',
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        // 2. Produksi Barang/Jasa
        $produksiItems = [
            ['kode' => 'PM-PB-01', 'nama' => 'Hadir tepat waktu', 'bobot' => 0.95],
            ['kode' => 'PM-PB-02', 'nama' => 'Mengikuti kegiatan produksi kerja', 'bobot' => 1.01],
            ['kode' => 'PM-PB-03', 'nama' => 'Mematuhi peraturan produksi barang/jasa yang berlaku', 'bobot' => 1.01],
            ['kode' => 'PM-PB-04', 'nama' => 'Mematuhi peraturan dalam hubungan kerja', 'bobot' => 1.00],
            ['kode' => 'PM-PB-05', 'nama' => 'Menghasilkan barang/jasa sesuai dengan standar', 'bobot' => 1.03],
            ['kode' => 'PM-PB-06', 'nama' => 'Menerapkan prosedur K3 dengan baik', 'bobot' => 1.00],
        ];

        foreach ($produksiItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $produksiBarang->id,
                'nama_item' => $item['nama'],
                'bobot' => $item['bobot'],
                'bobot_default' => $item['bobot'], // ✅ Set bobot_default
                'jenis_frekuensi' => 'Mingguan3',
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        $this->command->info('Observation Items - Pembinaan Kemandirian seeded!');
    }

    private function seedSikapNarapidana($variabelId, &$sortOrder)
    {
        // Get Aspek IDs
        $keberfungsian = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Keberfungsian & Rutinitas')->first();
        $agresi = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Agresi')->first();
        $pelanggaran = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Pelanggaran Hukum')->first();
        $mempengaruhi = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Kemampuan Mempengaruhi')->first();
        $ekspresi = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Ekspresi Simbolik')->first();

        // 1. Keberfungsian & Rutinitas
        $rutinitas = [
            ['kode' => 'SN-KR-01', 'nama' => 'Menerima dan mengkonsumsi makanan dan minuman', 'bobot' => 0.98],
            ['kode' => 'SN-KR-02', 'nama' => 'Menggunakan baju yang bersih dan rapi', 'bobot' => 1.04],
            ['kode' => 'SN-KR-03', 'nama' => 'Menggunakan baju seragam', 'bobot' => 1.05],
            ['kode' => 'SN-KR-04', 'nama' => 'Membersihkan kamar hunian', 'bobot' => 1.06],
            ['kode' => 'SN-KR-06', 'nama' => 'Mematuhi tata tertib lapas', 'bobot' => 1.08],
            ['kode' => 'SN-KR-07', 'nama' => 'Menjawab salam dari petugas', 'bobot' => 0.97],
            ['kode' => 'SN-KR-08', 'nama' => 'Mengucapkan salam kepada petugas', 'bobot' => 0.95],
            ['kode' => 'SN-KR-09', 'nama' => 'Tersenyum kepada petugas', 'bobot' => 0.94],
            ['kode' => 'SN-KR-10', 'nama' => 'Menyapa petugas', 'bobot' => 0.93],
            ['kode' => 'SN-KR-11', 'nama' => 'Berbincang dengan petugas', 'bobot' => 0.87]
        ];

        foreach ($rutinitas as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $keberfungsian->id,
                'nama_item' => $item['nama'],
                'bobot' => $item['bobot'],
                'bobot_default' => $item['bobot'], // ✅ Set bobot_default
                'jenis_frekuensi' => 'Harian',
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        ObservationItem::create([
            'kode' => 'SN-KR-05',
            'variabel_id' => $variabelId,
            'aspect_id' => $keberfungsian->id,
            'nama_item' => 'Ikut kerja bakti',
            'bobot' => 1.06,
            'bobot_default' => 1.06, // ✅ Set bobot_default
            'use_dynamic_frequency' => true,
            'jenis_frekuensi' => 'Mingguan1',
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        ObservationItem::create([
            'kode' => 'SN-KR-12',
            'variabel_id' => $variabelId,
            'aspect_id' => $keberfungsian->id,
            'nama_item' => 'Menerima kunjungan keluarga',
            'bobot' => 1.06,
            'bobot_default' => 1.06, // ✅ Set bobot_default
            'jenis_frekuensi' => 'Fix',
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        ObservationItem::create([
            'kode' => 'SN-KR-13',
            'variabel_id' => $variabelId,
            'aspect_id' => $keberfungsian->id,
            'nama_item' => 'Menerima kunjungan dinas',
            'bobot' => 1.01,
            'bobot_default' => 1.01, // ✅ Set bobot_default
            'jenis_frekuensi' => 'Fix',
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        // ✅ ITEM KONDISIONAL #7: Mau merapikan rambut, janggut, dan kuku
        ObservationItem::create([
            'kode' => 'SN-KR-14',
            'variabel_id' => $variabelId,
            'aspect_id' => $keberfungsian->id,
            'nama_item' => 'Mau merapikan rambut, janggut, dan kuku',
            'bobot' => 1.00, // ✅ Default 1.00
            'bobot_default' => 1.00,
            'is_conditional_weight' => true,
            'use_dynamic_frequency' => true,
            'jenis_frekuensi' => 'Kondisional',
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        // 2. Agresi
        $agresiItems = [
            ['kode' => 'SN-AG-01', 'nama' => 'Melakukan pemukulan tembok', 'bobot' => 0.92],
            ['kode' => 'SN-AG-02', 'nama' => 'Membanting barang-barang', 'bobot' => 0.92],
            ['kode' => 'SN-AG-03', 'nama' => 'Menunjukan sikap marah-marah', 'bobot' => 0.99],
            ['kode' => 'SN-AG-04', 'nama' => 'Berteriak-teriak', 'bobot' => 1.01],
            ['kode' => 'SN-AG-05', 'nama' => 'Merusak CCTV/Inventaris lain', 'bobot' => 1.07],
            ['kode' => 'SN-AG-06', 'nama' => 'Mengguncang atau menendang teralis', 'bobot' => 1.02],
            ['kode' => 'SN-AG-07', 'nama' => 'Memanjat teralis', 'bobot' => 1.06],
        ];

        foreach ($agresiItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $agresi->id,
                'nama_item' => $item['nama'],
                'bobot' => $item['bobot'],
                'bobot_default' => $item['bobot'], // ✅ Set bobot_default
                'jenis_frekuensi' => 'Harian',
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        // 3. Pelanggaran Hukum - ✅ ITEMS dengan conditional_weight tapi bukan Kondisional frequency
        // Note: Items ini punya is_conditional_weight = true tapi jenis_frekuensi = Harian
        // Berbeda dengan 9 item kondisional utama
        $pelanggaranItems = [
            ['kode' => 'SN-PH-01', 'nama' => 'Berupaya melarikan diri'],
            ['kode' => 'SN-PH-02', 'nama' => 'Mengancam/menyerang petugas'],
            ['kode' => 'SN-PH-03', 'nama' => 'Berkelahi dengan narapidana lain'],
            ['kode' => 'SN-PH-04', 'nama' => 'Melakukan dugaan tindak pidana lain'],
        ];

        foreach ($pelanggaranItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $pelanggaran->id,
                'nama_item' => $item['nama'],
                'bobot' => 0.00,
                'bobot_default' => 0.00, // ✅ Default 0 (penalty items)
                'is_conditional_weight' => true,
                'jenis_frekuensi' => 'Harian', // ⚠️ BUKAN Kondisional
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        // 4. Kemampuan Mempengaruhi
        $mempengaruhiItems = [
            ['kode' => 'SN-KM-01', 'nama' => 'Membujuk petugas Pemasyarakatan melakukan pelanggaran secara langsung', 'bobot' => 0.99],
            ['kode' => 'SN-KM-02', 'nama' => 'Menggunakan jaringan untuk membujuk petugas Pemasyarakatan melakukan pelanggaran', 'bobot' => 1.01],
            ['kode' => 'SN-KM-03', 'nama' => 'Membujuk atau mengajak narapidana lain melakukan pelanggaran', 'bobot' => 1.00],
        ];

        foreach ($mempengaruhiItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $mempengaruhi->id,
                'nama_item' => $item['nama'],
                'bobot' => $item['bobot'],
                'bobot_default' => $item['bobot'], // ✅ Set bobot_default
                'jenis_frekuensi' => 'Harian',
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        // 5. Ekspresi Simbolik
        $ekspresiItems = [
            ['kode' => 'SN-ES-01', 'nama' => 'Menggambar simbol yang berkaitan dengan ideologi ekstrimisme kekerasan', 'bobot' => 0.98],
            ['kode' => 'SN-ES-02', 'nama' => 'Meminta sesuatu yang berkaitan dengan ideologi ekstrimisme kekerasan', 'bobot' => 1.01],
            ['kode' => 'SN-ES-03', 'nama' => 'Membuat pernyataan yang menunjukkan niat untuk melakukan aksi teror seperti memberikan doktrin', 'bobot' => 1.03],
            ['kode' => 'SN-ES-04', 'nama' => 'Menggunakan kata "kami" dan "mereka" dalam maksud memisahkan antara kelompoknya dengan petugas', 'bobot' => 0.99],
            ['kode' => 'SN-ES-05', 'nama' => 'Menggunakan sandi untuk menghina petugas', 'bobot' => 0.99],
        ];

        foreach ($ekspresiItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $ekspresi->id,
                'nama_item' => $item['nama'],
                'bobot' => $item['bobot'],
                'bobot_default' => $item['bobot'], // ✅ Set bobot_default
                'jenis_frekuensi' => 'Harian',
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        $this->command->info('Observation Items - Sikap Narapidana seeded!');
    }

    private function seedKondisiMental($variabelId, &$sortOrder)
    {
        // Get Aspek IDs
        $depresi = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Depresi')->first();
        $kecemasan = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Kecemasan')->first();
        $psikosomatis = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Psikosomatis')->first();
        $malingering = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Malingering')->first();
        $bunuhDiri = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Potensi Bunuh Diri')->first();

        // 1. Depresi
        $depresiItems = [
            ['kode' => 'KM-DP-01', 'nama' => 'Tidak mau bangun dari tempat tidur', 'bobot' => 1.00],
            ['kode' => 'KM-DP-02', 'nama' => 'Sulit tidur', 'bobot' => 1.05],
            ['kode' => 'KM-DP-03', 'nama' => 'Tidak mau mandi', 'bobot' => 1.01],
            ['kode' => 'KM-DP-04', 'nama' => 'Tidak mau makan/minum', 'bobot' => 1.04],
            ['kode' => 'KM-DP-05', 'nama' => 'Murung terus-menerus', 'bobot' => 1.03],
            ['kode' => 'KM-DP-06', 'nama' => 'Menangis terus-menerus', 'bobot' => 0.98],
            ['kode' => 'KM-DP-07', 'nama' => 'Menatap dinding dengan lama', 'bobot' => 0.93],
            ['kode' => 'KM-DP-08', 'nama' => 'Tidak mau berbicara', 'bobot' => 0.95],
        ];

        foreach ($depresiItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $depresi->id,
                'nama_item' => $item['nama'],
                'bobot' => $item['bobot'],
                'bobot_default' => $item['bobot'], // ✅ Set bobot_default
                'jenis_frekuensi' => 'Harian',
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        // 2. Kecemasan
        $kecemasanItems = [
            ['kode' => 'KM-KC-01', 'nama' => 'Melakukan perilaku berulang-ulang', 'bobot' => 1.04],
            ['kode' => 'KM-KC-02', 'nama' => 'Tidak bisa fokus terhadap banyak hal', 'bobot' => 1.00],
            ['kode' => 'KM-KC-03', 'nama' => 'Takut ditempatkan di ruang sendiri', 'bobot' => 0.96],
        ];

        foreach ($kecemasanItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $kecemasan->id,
                'nama_item' => $item['nama'],
                'bobot' => $item['bobot'],
                'bobot_default' => $item['bobot'], // ✅ Set bobot_default
                'jenis_frekuensi' => 'Harian',
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        // 3. Psikosomatis - ⚠️ conditional_weight tapi bukan Kondisional frequency
        ObservationItem::create([
            'kode' => 'KM-PS-01',
            'variabel_id' => $variabelId,
            'aspect_id' => $psikosomatis->id,
            'nama_item' => 'Mengalami gejala fisik pada saat situasi di bawah tekanan',
            'bobot' => 1.00,
            'bobot_default' => 1.00,
            'is_conditional_weight' => true,
            'use_dynamic_frequency' => true,
            'jenis_frekuensi' => 'Harian', // ⚠️ BUKAN Kondisional
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        // 4. Malingering - ⚠️ conditional_weight tapi bukan Kondisional frequency
        ObservationItem::create([
            'kode' => 'KM-MG-01',
            'variabel_id' => $variabelId,
            'aspect_id' => $malingering->id,
            'nama_item' => 'Mengeluhkan sesuatu secara terus-menerus untuk kepentingan diri sendiri untuk menghindari kewajiban',
            'bobot' => 1.00,
            'bobot_default' => 1.00,
            'is_conditional_weight' => true,
            'use_dynamic_frequency' => true,
            'jenis_frekuensi' => 'Harian', // ⚠️ BUKAN Kondisional
            'sort_order' => $sortOrder++,
            'aktif' => true,
        ]);

        // 5. Potensi Bunuh Diri
        $bunuhDiriItems = [
            ['kode' => 'KM-BS-01', 'nama' => 'Menyakiti diri sendiri', 'bobot' => 0.99],
            ['kode' => 'KM-BS-02', 'nama' => 'Membenturkan kepala ke benda keras', 'bobot' => 0.96],
            ['kode' => 'KM-BS-03', 'nama' => 'Melakukan usaha untuk bunuh diri', 'bobot' => 1.03],
            ['kode' => 'KM-BS-04', 'nama' => 'Mengatakan ingin bunuh diri', 'bobot' => 1.02],
        ];

        foreach ($bunuhDiriItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $bunuhDiri->id,
                'nama_item' => $item['nama'],
                'bobot' => $item['bobot'],
                'bobot_default' => $item['bobot'], // ✅ Set bobot_default
                'jenis_frekuensi' => 'Harian',
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        $this->command->info('Observation Items - Kondisi Mental seeded!');
    }

    private function seedPernyataanKomitmen($variabelId, &$sortOrder)
    {
        // Get Aspek IDs
        $pernyataanKomitmen = AssessmentAspect::where('assessment_variabel_id', $variabelId)
            ->where('nama', 'Pernyataan Komitmen')->first();

        // ✅ ITEM KONDISIONAL #8 & #9: Pernyataan Komitmen
        $pernyataanKomitmenItems = [
            ['kode' => 'PK-KM-01', 'nama' => 'Menandatangani pernyataan kesetiaan terhadap NKRI'],
            ['kode' => 'PK-KM-02', 'nama' => 'Menandatangani pernyataan tidak terlibat dalam jaringan narkoba'],
        ];

        foreach ($pernyataanKomitmenItems as $item) {
            ObservationItem::create([
                'kode' => $item['kode'],
                'variabel_id' => $variabelId,
                'aspect_id' => $pernyataanKomitmen->id,
                'nama_item' => $item['nama'],
                'bobot' => 1.00, // ✅ Default 1.00
                'bobot_default' => 1.00,
                'is_conditional_weight' => true,
                'jenis_frekuensi' => 'Kondisional',
                'sort_order' => $sortOrder++,
                'aktif' => true,
            ]);
        }

        $this->command->info('Observation Items - Pernyataan Komitmen seeded!');
    }
}
