<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Assessment;
use App\Models\ObservationItem;
use App\Models\DailyObservation;
use App\Imports\AssessmentImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\CommitmentStatement;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Assessment\ImportResult;

class AssessmentService {

    public function storeAssessment(array $data): Assessment {
        return DB::transaction(function () use ($data) {
            // check duplicate
            $this->ensureNotDuplicate($data);

            // Create assessment
            $assessment = Assessment::create([
                'inmate_id' => $data['inmate_id'],
                'tanggal_penilaian' => $data['tanggal_penilaian'],
                'status' => 'draf',
                'created_by' => auth()->id(),
            ]);

            // initialize Daily Observation
            $this->initializeDailyObservations($assessment);

            // create commitment statement
            $this->createCommitmentStatements($assessment);

            // log
            $this->logAssessmentActivity($assessment, 'create');

            return $assessment;
        });
    }

    public function updateObservation(Assessment $assessment, array $data) {

        return DB::transaction(function () use ($assessment, $data) {
            $observation = DailyObservation::updateOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'observation_item_id' => $data['observation_item_id'],
                    'hari' => $data['hari'],
                ],
                [
                    'is_checked' => $data['is_checked'],
                    'catatan' => $data['catatan'],
                ]
            );

            // Recalculate scores
            $assessment->calculateScores();

            return $observation;
        });
    }

    public function submitAssessment(Assessment $assessment) {

        // only draft can be submited
        if ($assessment->status !== 'draf') {
            throw new \DomainException('Hanya penilaian dengan status draf yang dapat disubmit.');
        }

        DB::transaction(function () use ($assessment) {
            $assessment->update([
                'status' => 'disubmit',
                'submitted_at' => now(),
            ]);

            $this->logAssessmentActivity($assessment, 'submited');
        });
    }

    public function approveAssessment(Assessment $assessment) {

        // only submited can be approved
        if ($assessment->status !== 'disubmit') {
            throw new \DomainException('Hanya penilaian yang sudah disubmit yang dapat disetujui.');
        }

        DB::transaction(function () use ($assessment) {
            $assessment->update([
                'status' => 'diterima',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->logAssessmentActivity($assessment, 'approved');
        });
    }
    public function rejectAssessment(Assessment $assessment, array $data) {
        DB::transaction(function () use ($assessment, $data) {
            $assessment->update([
                'status' => 'ditolak',
                'catatan' => $data['catatan'],
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->logAssessmentActivity($assessment, 'rejected');
        });
    }

    public function importAssessment(Assessment $assessment, UploadedFile $file) {
         return DB::transaction(function () use ($assessment, $file) {

            $import = new AssessmentImport($assessment);
            Excel::import($import, $file);

            if ($import->hasErrors()) {
                return ImportResult::withErrors(
                    $import->getErrors()
                );
            }

            $this->logAssessmentActivity($assessment, 'imported');

            return ImportResult::success(
                $import->getSuccessCount()
            );
        });
    }

    private function ensureNotDuplicate(array $data) {
        $exists = Assessment::where('inmate_id', $data['inmate_id'])
                ->whereMonth('tanggal_penilaian', Carbon::parse($data['tanggal_penilaian'])->month)
                ->whereYear('tanggal_penilaian', Carbon::parse($data['tanggal_penilaian'])->year)
                ->exists();

        if ($exists) {
            throw new \DomainException('Penilaian untuk bulan tersebut sudah ada.');
        }
    }

    private function createCommitmentStatements(Assessment $assessment) {
            // Initialize commitment statements
        CommitmentStatement::create([
            'assessment_id' => $assessment->id,
            'jenis' => 'nkri',
            'is_signed' => false,
        ]);

        CommitmentStatement::create([
            'assessment_id' => $assessment->id,
            'jenis' => 'narkoba',
            'is_signed' => false,
        ]);
    }

    private function initializeDailyObservations(Assessment $assessment)
    {
        $daysInMonth = $assessment->tanggal_penilaian->daysInMonth;
        $observationItems = ObservationItem::aktif()->get();

        foreach ($observationItems as $item) {
            for ($day = 1; $day <= $daysInMonth; $day++) {
                DailyObservation::create([
                    'assessment_id' => $assessment->id,
                    'observation_item_id' => $item->id,
                    'hari' => $day,
                    'is_checked' => false,
                ]);
            }
        }
    }

    protected function logAssessmentActivity(Assessment $assessment, string $action): void
    {
        $messages = [
            'created' => 'Penilaian baru dibuat untuk: ' . $assessment->inmate->nama,
            'submited' => 'Penilaian disubmit untuk: ' . $assessment->inmate->nama,
            'rejected' => 'Penilaian ditolak untuk: ' . $assessment->inmate->nama,
            'approved' => 'Penilaian disetujui untuk: ' . $assessment->inmate->nama,
            'imported' => 'Import data penilaian untuk: ' . $assessment->inmate->nama,
        ];

        activity()
            ->performedOn($assessment)
            ->causedBy(auth()->user())
            ->log($messages[$action] ?? 'Aktivitas Penilaian: ' . $action);
    }


}
