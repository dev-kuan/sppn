<?php

namespace App\Exports;

use App\Models\Assessment;
use App\Models\AssessmentVariabel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class AssessmentTemplateExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $assessment;
    protected $daysInMonth;
    protected $variabels;

    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
        $this->daysInMonth = $assessment->tanggal_penilaian->daysInMonth;
        $this->variabels = AssessmentVariabel::with(['aspect.observationItems' => function ($q) {
            $q->aktif()->ordered();
        }])->get();
    }

    public function array(): array
    {
        $data = [];

        // Row 1: Title
        $titleRow = ['TEMPLATE PENILAIAN NARAPIDANA'];
        for ($i = 1; $i < 5 + $this->daysInMonth; $i++) {
            $titleRow[] = '';
        }
        $data[] = $titleRow;

        // Row 2: Empty
        $data[] = [];

        // Row 3-6: Info Narapidana
        $data[] = ['No. Registrasi', $this->assessment->inmate->no_registrasi];
        $data[] = ['Nama Narapidana', $this->assessment->inmate->nama];
        $data[] = ['Periode Penilaian', $this->assessment->tanggal_penilaian->format('F Y')];
        $data[] = ['Jumlah Hari', $this->daysInMonth . ' hari'];

        // Row 7: Empty
        $data[] = [];

        // Row 8: Table Header
        $headerRow = ['Variabel', 'Aspek', 'Item Observasi', 'Bobot', 'Frekuensi'];

        // Add day numbers to header
        for ($day = 1; $day <= $this->daysInMonth; $day++) {
            $headerRow[] = $day;
        }
        $data[] = $headerRow;

        // Row 9+: Data rows for each observation item
        foreach ($this->variabels as $variabel) {
            foreach ($variabel->aspect as $aspek) {
                foreach ($aspek->observationItems as $item) {
                    $row = [
                        $variabel->nama,
                        $aspek->nama,
                        $item->nama_item,
                        $item->bobot,
                        $item->calculateFrequency($this->daysInMonth)
                    ];

                    // Add empty cells for each day
                    for ($day = 1; $day <= $this->daysInMonth; $day++) {
                        $row[] = '';
                    }

                    $data[] = $row;
                }
            }
        }

        // Add instruction rows at bottom
        $lastDataRow = count($data);
        $data[] = []; // Empty row
        $data[] = ['PETUNJUK PENGISIAN:'];
        $data[] = ['1. Isi kolom hari (angka 1-' . $this->daysInMonth . ') dengan angka 1 jika observasi terpenuhi, kosongkan jika tidak terpenuhi'];
        $data[] = ['2. Jangan mengubah data pada kolom: Variabel, Aspek, Item Observasi, Bobot, dan Frekuensi'];
        $data[] = ['3. Hanya isi kolom-kolom hari (kolom dengan header angka)'];
        $data[] = ['4. Setelah selesai mengisi, simpan file dan upload kembali ke sistem'];

        return $data;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 25, // Variabel
            'B' => 25, // Aspek
            'C' => 40, // Item Observasi
            'D' => 10, // Bobot
            'E' => 12, // Frekuensi
        ];

        // Day columns - narrow
        $column = 'F';
        for ($day = 1; $day <= $this->daysInMonth; $day++) {
            $widths[$column] = 4.5;
            $column++;
        }

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $this->getColumnLetter(5 + $this->daysInMonth);

        return [
            // Title (Row 1) - Bold, large, centered, colored
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'] // Indigo
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],

            // Info labels (Column A, rows 3-6) - Bold with background
            'A3:A6' => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'] // Light gray
                ]
            ],

            // Table header (Row 8) - Bold, white text, colored background
            6 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '6366F1'] // Indigo
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $this->getColumnLetter(5 + $this->daysInMonth);
                $lastDataRow = 8 + $this->getTotalObservationItems();
                $instructionStartRow = $lastDataRow + 2;

                // Merge title cells (Row 1)
                $sheet->mergeCells("A1:{$lastColumn}1");

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getRowDimension(8)->setRowHeight(30);

                // Apply borders to entire table (including header)
                $tableRange = "A8:{$lastColumn}{$lastDataRow}";
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB']
                        ]
                    ]
                ]);

                // Center align for specific columns in data area
                // Bobot (D) and Frekuensi (E)
                $sheet->getStyle("D9:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E9:E{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // All day columns (F onwards) - center aligned
                $sheet->getStyle("F8:{$lastColumn}{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Vertical center for all data rows
                $sheet->getStyle("A8:{$lastColumn}{$lastDataRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Wrap text for Item Observasi column
                $sheet->getStyle("C9:C{$lastDataRow}")->getAlignment()->setWrapText(true);

                // Set row height for data rows
                for ($row = 9; $row <= $lastDataRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }

                // Freeze panes (freeze first 5 columns and row 8 header)
                $sheet->freezePane('F7');

                // Style instruction section
                $sheet->getStyle("A{$instructionStartRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => '1F2937']
                    ]
                ]);

                $instructionRange = "A" . ($instructionStartRow + 1) . ":A" . ($instructionStartRow + 4);
                $sheet->getStyle($instructionRange)->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'color' => ['rgb' => '6B7280']
                    ]
                ]);

                // Add alternating row colors for data (zebra striping)
                for ($row = 9; $row <= $lastDataRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F9FAFB'] // Very light gray
                            ]
                        ]);
                    }
                }

                // Add data validation to day columns (only allow 1 or empty)
                $dayColumnStart = 'F';
                $dayColumnEnd = $this->getColumnLetter(5 + $this->daysInMonth);

                for ($row = 9; $row <= $lastDataRow; $row++) {
                    $validation = $sheet->getCell("{$dayColumnStart}{$row}")->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_WHOLE);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input tidak valid');
                    $validation->setError('Hanya boleh diisi angka 1 atau dikosongkan');
                    $validation->setPromptTitle('Petunjuk');
                    $validation->setPrompt('Isi dengan angka 1 jika observasi terpenuhi, kosongkan jika tidak');
                    $validation->setFormula1('1');
                    $validation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_EQUAL);

                    // Copy validation to all day columns in this row
                    for ($col = $dayColumnStart; $col <= $dayColumnEnd; $col++) {
                        $cellCoordinate = "{$col}{$row}";
                        $cellValidation = $sheet->getCell($cellCoordinate)->getDataValidation();
                        $cellValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_WHOLE);
                        $cellValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                        $cellValidation->setAllowBlank(true);
                        $cellValidation->setShowInputMessage(true);
                        $cellValidation->setShowErrorMessage(true);
                        $cellValidation->setShowDropDown(true);
                        $cellValidation->setErrorTitle('Input tidak valid');
                        $cellValidation->setError('Hanya boleh diisi angka 1 atau dikosongkan');
                        $cellValidation->setPromptTitle('Petunjuk');
                        $cellValidation->setPrompt('Isi dengan angka 1 jika observasi terpenuhi, kosongkan jika tidak');
                        $cellValidation->setFormula1('1');
                        $cellValidation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_EQUAL);
                    }
                }

                // Protect columns that shouldn't be edited (A-E)
                $sheet->getProtection()->setSheet(true);
                $sheet->getStyle("A1:E{$lastDataRow}")->getProtection()->setLocked(true);
                $sheet->getStyle("F1:{$lastColumn}{$lastDataRow}")->getProtection()->setLocked(false);
            }
        ];
    }

    private function getColumnLetter($columnNumber)
    {
        $letter = '';
        while ($columnNumber > 0) {
            $temp = ($columnNumber - 1) % 26;
            $letter = chr($temp + 65) . $letter;
            $columnNumber = ($columnNumber - $temp - 1) / 26;
        }
        return $letter;
    }

    private function getTotalObservationItems()
    {
        $count = 0;
        foreach ($this->variabels as $variabel) {
            foreach ($variabel->aspect as $aspek) {
                $count += $aspek->observationItems->count();
            }
        }
        return $count;
    }
}
