<?php

namespace App\Imports;

use App\Models\Assessment;
use App\Models\DailyObservation;
use App\Models\ObservationItem;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentImport implements ToArray
{
    protected $assessment;
    protected $errors = [];
    protected $successCount = 0;
    protected $daysInMonth;

    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
        $this->daysInMonth = $assessment->tanggal_penilaian->daysInMonth;
    }

    public function array(array $array)
    {
        DB::beginTransaction();

        try {
            // Row 8 (index 7) is the header row
            // Row 9+ (index 8+) are data rows

            // Verify header row
            if (!isset($array[5])) {
                throw new \Exception("Format file tidak sesuai. Header tidak ditemukan.");
            }

            $headerRow = $array[5];

            // Verify header columns
            if (!$this->verifyHeader($headerRow)) {
                throw new \Exception("Format header tidak sesuai. Pastikan menggunakan template yang benar.");
            }

            // Process data rows (starting from index 7)
            $rowNumber = 7; // Excel row number for error messages

            for ($i = 6; $i < count($array); $i++) {
                $row = $array[$i];

                // Skip empty rows
                if ($this->isEmptyRow($row)) {
                    $rowNumber++;
                    continue;
                }

                // Stop if we hit instruction section
                if (isset($row[0]) && stripos($row[0], 'PETUNJUK') !== false) {
                    break;
                }

                try {
                    $this->processRow($row, $rowNumber);
                } catch (\Exception $e) {
                    $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    Log::error("Import error on row {$rowNumber}", [
                        'error' => $e->getMessage(),
                        'row' => $row
                    ]);
                }

                $rowNumber++;
            }

            // Recalculate assessment scores after import
            $this->assessment->calculateScores();

            DB::commit();

            Log::info('Assessment import completed', [
                'assessment_id' => $this->assessment->id,
                'success_count' => $this->successCount,
                'error_count' => count($this->errors)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assessment import failed', [
                'assessment_id' => $this->assessment->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function verifyHeader(array $headerRow)
    {
        // Check if required columns exist
        $requiredColumns = ['Variabel', 'Aspek', 'Item Observasi', 'Bobot', 'Frekuensi'];

        for ($i = 0; $i < 5; $i++) {
            if (!isset($headerRow[$i]) ||
                stripos($headerRow[$i], $requiredColumns[$i]) === false) {
                return false;
            }
        }

        // Check if day columns exist (should have numbers 1 to daysInMonth)
        for ($day = 1; $day <= $this->daysInMonth; $day++) {
            if (!isset($headerRow[4 + $day]) || $headerRow[4 + $day] != $day) {
                return false;
            }
        }

        return true;
    }

    protected function isEmptyRow(array $row)
    {
        // Check first 3 columns (Variabel, Aspek, Item Observasi)
        for ($i = 0; $i < 3; $i++) {
            if (isset($row[$i]) && !empty(trim($row[$i]))) {
                return false;
            }
        }
        return true;
    }

    protected function processRow(array $row, int $rowNumber)
    {
        // Get data from fixed column positions
        $variabelNama = isset($row[0]) ? trim($row[0]) : null;
        $aspekNama = isset($row[1]) ? trim($row[1]) : null;
        $itemNama = isset($row[2]) ? trim($row[2]) : null;

        if (!$itemNama || !$variabelNama || !$aspekNama) {
            throw new \Exception("Data tidak lengkap (Variabel, Aspek, atau Item Observasi kosong)");
        }

        // Find observation item by matching all three
        $observationItem = ObservationItem::where('nama_item', $itemNama)
            ->whereHas('aspect', function ($q) use ($aspekNama) {
                $q->where('nama', $aspekNama);
            })
            ->whereHas('aspect.variabel', function ($q) use ($variabelNama) {
                $q->where('nama', $variabelNama);
            })
            ->first();

        if (!$observationItem) {
            // Try to find by item name only for better error message
            $itemExists = ObservationItem::where('nama_item', 'LIKE', "%{$itemNama}%")->first();

            if ($itemExists) {
                throw new \Exception("Item observasi '{$itemNama}' ditemukan tetapi Variabel/Aspek tidak cocok");
            } else {
                throw new \Exception("Item observasi '{$itemNama}' tidak ditemukan di sistem");
            }
        }

        // Process day columns (starting from column index 5)
        // Column F (index 5) = day 1, G (index 6) = day 2, etc.
        $processedDays = 0;

        for ($day = 1; $day <= $this->daysInMonth; $day++) {
            $columnIndex = 4 + $day; // Column E is index 4, so day 1 is at index 5

            if (!isset($row[$columnIndex])) {
                // Column doesn't exist, treat as unchecked
                $isChecked = false;
            } else {
                $value = $row[$columnIndex];

                // Parse value: 1, "1", "x", "X" means checked
                // Empty, 0, null, or anything else means unchecked
                $isChecked = false;

                if (!is_null($value) && $value !== '') {
                    $valueStr = strtolower(trim((string)$value));
                    $isChecked = in_array($valueStr, ['1', 'x', 'yes', 'y', 'true', 'v', '✓']);
                }
            }

            // Update or create observation
            DailyObservation::updateOrCreate(
                [
                    'assessment_id' => $this->assessment->id,
                    'observation_item_id' => $observationItem->id,
                    'hari' => $day,
                ],
                [
                    'is_checked' => $isChecked,
                    'catatan' => null,
                ]
            );

            $processedDays++;
        }

        $this->successCount += $processedDays;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }
}
