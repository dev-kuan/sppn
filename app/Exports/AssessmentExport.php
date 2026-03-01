<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// ============================================
// AssessmentExport Class
// ============================================
class AssessmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $assessments;

    public function __construct($assessments)
    {
        $this->assessments = $assessments;
    }

    public function collection()
    {
        return $this->assessments;
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Registrasi',
            'Nama Narapidana',
            'Bulan Penilaian',
            'Skor Kepribadian',
            'Skor Kemandirian',
            'Skor Sikap',
            'Skor Mental',
            'Skor Komitmen',
            'Skor Total',
            'Kategori',
            'Status',
            'Dibuat Oleh',
            'Disetujui Oleh',
            'Tanggal Dibuat',
            'Tanggal Disetujui',
        ];
    }

    public function map($assessment): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $assessment->inmate->no_registrasi,
            $assessment->inmate->nama,
            $assessment->tanggal_penilaian->format('F Y'),
            round($assessment->skor_kepribadian, 2),
            round($assessment->skor_kemandirian, 2),
            round($assessment->skor_sikap, 2),
            round($assessment->skor_mental, 2),
            round($assessment->skor_komitmen, 2),
            round($assessment->skor_total, 2),
            $assessment->kategori_total,
            ucfirst($assessment->status),
            $assessment->creator->name ?? '-',
            $assessment->approver->name ?? '-',
            $assessment->created_at->format('d/m/Y H:i'),
            $assessment->approved_at ? $assessment->approved_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
