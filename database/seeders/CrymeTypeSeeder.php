<?php

namespace Database\Seeders;

use App\Models\CrymeType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CrymeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crymeTypes = [
            'Farmasi dan Kesehatan',
            'Fidusia (Pengalihan hak kepemilikan)',
            'Illegal Logging/Pembakaran liar',
            'ITE (Informasi dan Transaksi Elektronik)',
            'Jinayat',
            'KDRT (Kekerasan dalam Rumah Tangga)',
            'Kealfaan mengakibatkan kematian/luka',
            'Kehutanan',
            'Keimigrasian',
            'Kejahatan Hak Cipta',
            'Kejahatan Merek',
            'Kejahatan terhadap asal usul perkawinan',
            'Kejahatan terhadap HAM',
            'Kejahatan terhadap keamanan negara (subversi)',
            'Kejahatan terhadap kemerdekaan orang lain',
            'Kejahatan terhadap Kesusilaan',
            'Kejahatan terhadap Keteriban Umum',
            'Kepabeanan',
            'Ketenagakerjaan',
            'Konservasi Sumber Daya Alam',
            'Korupsi',
            'Lalu Lintas',
            'Lingkungan Hidup',
            'Mata Uang',
            'Migas',
            'Narkotika & Psikotropika',
            'Pangan',
            'Paten',
            'Pelayaran',
            'Pemalsuan',
            'Pembunuhan',
            'Pemerasaan, Pengancaman, Penculikan, Pengeroyokan dan Perampokan',
            'Penadahan',
            'Pencucian Uang',
            'Pencurian',
            'Penganiayaan',
            'Pengelolaan Wilayah Pesisir',
            'Penempatan dan Perlindungan TKI',
            'Penggelapan',
            'Penghinaan',
            'Penipuan',
            'Penyiaran',
            'Peradilan Anak / ABH',
            'Perbankan',
            'Perdagangan Orang',
            'Perikanan',
            'Perjudian',
            'Perlindungan Anak',
            'Perpajakan',
            'Pertambangan',
            'Perumahan dan Pemukiman',
            'Perusakan dan Pembakaran',
            'Pidsus Cukai',
            'Pidsus Kehutanan',
            'Pidsus Pendidikan',
            'Pidsus Pra Peradilan',
            'Pidsus Terorisme',
            'Pornografi',
            'Pra Peradilan',
            'Rahasia Dagang',
            'Senjata Api, Senjata Tajam dan Bahan Peledak',
            'Sumpah Palsu dan Keterangan Palsu',
            'Tindak Pidana Ekonomi',
            'Uang Palsu dan Penggandaan',
            'Rahasia Dagang',
            'Perusakan dan Pembakaran',
            'Pidsus Cukai',
        ];

        foreach ($crymeTypes as $type) {
            CrymeType::create(['nama' => $type]);
        }

        $this->command->info('Crime Types seeded successfully!');
    }
}
