<?php

namespace Database\Seeders;

use App\Models\Inmate;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InmateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Inmate::insert([
            [
                'no_registrasi' => 'REG-001-2026',
                'nama' => 'Andi Saputra',
                'tempat_lahir' => 'Kupang',
                'tanggal_lahir' => '1990-05-12',
                'jenis_kelamin' => 'laki-laki',
                'agama' => 'Kristen',
                'tingkat_pendidikan' => 'SMA',
                'pekerjaan_terakhir' => 'Sopir',
                'lama_pidana_bulan' => 60,
                'sisa_pidana_bulan' => 24,
                'jumlah_residivisme' => 1,
                'catatan_kesehatan' => 'Sehat',
                'pelatihan' => 'Pelatihan Las',
                'program_kerja' => 'Workshop Bengkel',
                'crime_type_id' => 1,
                'status' => 'aktif',
                'tanggal_masuk' => '2022-01-10',
                'tanggal_bebas' => '2027-01-10',
            ],
            [
                'no_registrasi' => 'REG-002-2026',
                'nama' => 'Budi Hartono',
                'tempat_lahir' => 'Atambua',
                'tanggal_lahir' => '1985-08-20',
                'jenis_kelamin' => 'laki-laki',
                'agama' => 'Katolik',
                'tingkat_pendidikan' => 'SMP',
                'pekerjaan_terakhir' => 'Petani',
                'lama_pidana_bulan' => 48,
                'sisa_pidana_bulan' => 12,
                'jumlah_residivisme' => 0,
                'catatan_kesehatan' => 'Hipertensi ringan',
                'pelatihan' => 'Pelatihan Pertanian',
                'program_kerja' => 'Kebun Lapas',
                'crime_type_id' => 2,
                'status' => 'aktif',
                'tanggal_masuk' => '2023-02-15',
                'tanggal_bebas' => '2027-02-15',
            ],
            [
                'no_registrasi' => 'REG-003-2026',
                'nama' => 'Carlos Neno',
                'tempat_lahir' => 'Soe',
                'tanggal_lahir' => '1995-11-03',
                'jenis_kelamin' => 'laki-laki',
                'agama' => 'Kristen',
                'tingkat_pendidikan' => 'D3',
                'pekerjaan_terakhir' => 'Teknisi',
                'lama_pidana_bulan' => 36,
                'sisa_pidana_bulan' => 6,
                'jumlah_residivisme' => 2,
                'catatan_kesehatan' => 'Asma',
                'pelatihan' => 'Pelatihan IT',
                'program_kerja' => 'Administrasi',
                'crime_type_id' => 1,
                'status' => 'aktif',
                'tanggal_masuk' => '2023-08-01',
                'tanggal_bebas' => '2026-08-01',
            ],
            [
                'no_registrasi' => 'REG-004-2026',
                'nama' => 'Doni Kolo',
                'tempat_lahir' => 'Maumere',
                'tanggal_lahir' => '1988-03-17',
                'jenis_kelamin' => 'laki-laki',
                'agama' => 'Islam',
                'tingkat_pendidikan' => 'SMA',
                'pekerjaan_terakhir' => 'Karyawan Swasta',
                'lama_pidana_bulan' => 72,
                'sisa_pidana_bulan' => 48,
                'jumlah_residivisme' => 0,
                'catatan_kesehatan' => 'Sehat',
                'pelatihan' => 'Pelatihan Menjahit',
                'program_kerja' => 'Konveksi',
                'crime_type_id' => 3,
                'status' => 'aktif',
                'tanggal_masuk' => '2021-06-20',
                'tanggal_bebas' => '2027-06-20',
            ],
            [
                'no_registrasi' => 'REG-005-2026',
                'nama' => 'Eko Pratama',
                'tempat_lahir' => 'Waingapu',
                'tanggal_lahir' => '1992-09-09',
                'jenis_kelamin' => 'laki-laki',
                'agama' => 'Hindu',
                'tingkat_pendidikan' => 'S1',
                'pekerjaan_terakhir' => 'Wiraswasta',
                'lama_pidana_bulan' => 24,
                'sisa_pidana_bulan' => 3,
                'jumlah_residivisme' => 1,
                'catatan_kesehatan' => 'Diabetes',
                'pelatihan' => 'Pelatihan Kewirausahaan',
                'program_kerja' => 'Produksi Roti',
                'crime_type_id' => 2,
                'status' => 'aktif',
                'tanggal_masuk' => '2024-03-01',
                'tanggal_bebas' => '2026-03-01',
            ],
        ]);
    }
}
