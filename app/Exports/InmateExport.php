<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InmateExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $inmates;

    public function __construct($inmates)
    {
        $this->inmates = $inmates;
    }

    public function collection()
    {
        return $this->inmates;
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Registrasi',
            'Nama',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Umur',
            'Jenis Kelamin',
            'Agama',
            'Pendidikan',
            'Pekerjaan Terakhir',
            'Jenis Tindak Pidana',
            'Lama Pidana (Bulan)',
            'Sisa Pidana (Bulan)',
            'Jumlah Residivisme',
            'Status',
            'Tanggal Masuk',
            'Tanggal Bebas',
            'Pelatihan',
            'Program Kerja',
        ];
    }

    public function map($inmate): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $inmate->no_registrasi,
            $inmate->nama,
            $inmate->tempat_lahir,
            $inmate->tanggal_lahir->format('d/m/Y'),
            $inmate->umur . ' tahun',
            ucfirst($inmate->jenis_kelamin),
            $inmate->agama,
            $inmate->tingkat_pendidikan ?? '-',
            $inmate->pekerjaan_terakhir ?? '-',
            $inmate->crimeType->nama,
            $inmate->lama_pidana_bulan,
            $inmate->sisa_pidana_bulan,
            $inmate->jumlah_residivisme,
            ucfirst($inmate->status),
            $inmate->tanggal_masuk->format('d/m/Y'),
            $inmate->tanggal_bebas ? $inmate->tanggal_bebas->format('d/m/Y') : '-',
            $inmate->pelatihan ?? '-',
            $inmate->program_kerja ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
