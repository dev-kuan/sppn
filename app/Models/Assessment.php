<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assessment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'inmate_id',
        'tanggal_penilaian',
        'skor_kepribadian',
        'skor_kemandirian',
        'skor_sikap',
        'skor_mental',
        'skor_total',
        'status',
        'catatan',
        'created_by',
        'approved_by',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_penilaian' => 'date',
        'skor_kepribadian' => 'decimal:4',
        'skor_kemandirian' => 'decimal:4',
        'skor_sikap' => 'decimal:4',
        'skor_mental' => 'decimal:4',
        'skor_total' => 'decimal:4',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'skor_total'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function inmate()
    {
        return $this->belongsTo(Inmate::class, 'inmate_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dailyObservations()
    {
        return $this->hasMany(DailyObservation::class);
    }

    public function assessmentScores()
    {
        return $this->hasMany(AssessmentScore::class);
    }

    public function commitmentStatements()
    {
        return $this->hasMany(CommitmentStatement::class);
    }

    public function commitmentRecommendations()
    {
        return $this->hasMany(CommitmentRecommendation::class);
    }

    public function commitmentSignatures()
    {
        return $this->hasMany(CommitmentSignature::class);
    }

    // Accessors
    public function getBulanTahunAttribute()
    {
        return $this->tanggal_penilaian->format('F Y');
    }

    public function getKategoriTotalAttribute()
    {
        if (!$this->skor_total) return null;

        $skor = $this->skor_total;

        if ($skor >= 81) return 'Sangat Baik';
        if ($skor >= 61) return 'Baik';
        if ($skor >= 41) return 'Cukup Baik';
        if ($skor >= 21) return 'Tidak Baik';
        return 'Sangat Tidak Baik';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draf' => 'bg-gray-100 text-gray-800',
            'disubmit' => 'bg-blue-100 text-blue-800',
            'diterima' => 'bg-green-100 text-green-800',
            'ditolak' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    // Scopes
    public function scopeDraf($query)
    {
        return $query->where('status', 'draf');
    }

    public function scopeDisubmit($query)
    {
        return $query->where('status', 'disubmit');
    }

    public function scopeDiterima($query)
    {
        return $query->where('status', 'diterima');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('tanggal_penilaian', $month)
                     ->whereYear('tanggal_penilaian', $year);
    }

    public function scopeByInmate($query, $inmateId)
    {
        return $query->where('inmate_id', $inmateId);
    }

    // Methods
// Methods
    public function calculateScores()
    {
        try {
            Log::info('=== Starting Score Calculation ===', [
                'assessment_id' => $this->id,
                'tanggal_penilaian' => $this->tanggal_penilaian
            ]);

            $daysInMonth = $this->tanggal_penilaian->daysInMonth;

            Log::info('Days in month: ' . $daysInMonth);

            // Get all active observation items with their relationships
            $observationItems = ObservationItem::with(['aspect.variabel'])
                ->aktif()
                ->get();

            Log::info('Total observation items: ' . $observationItems->count());

            // Initialize scores array
            $variabelScores = [
                'Pembinaan Kepribadian' => 0,
                'Pembinaan Kemandirian' => 0,
                'Penilaian Sikap' => 0,
                'Penilaian Kondisi Mental' => 0,
            ];

            // Group items by variabel
            $groupedByVariabel = $observationItems->groupBy(function($item) {
                return $item->aspect->variabel->nama;
            });

            Log::info('Grouped variabels: ' . $groupedByVariabel->keys()->implode(', '));

            // Calculate score for each variabel
            foreach ($groupedByVariabel as $variabelNama => $items) {
                $variabelTotalScore = 0;

                Log::info("Processing variabel: {$variabelNama} with {$items->count()} items");

                foreach ($items as $item) {
                    // Count checked days for this item
                    $checkedCount = $this->dailyObservations()
                        ->where('observation_item_id', $item->id)
                        ->where('is_checked', true)
                        ->count();

                    // Calculate frequency for this item
                    $frequency = $item->calculateFrequency($daysInMonth);

                    Log::debug("Item: {$item->nama_item}", [
                        'checked' => $checkedCount,
                        'frequency' => $frequency,
                        'bobot' => $item->bobot
                    ]);

                    // Calculate item score
                    if ($frequency > 0) {
                        $itemScore = ($checkedCount / $frequency) * $item->bobot;
                        $variabelTotalScore += $itemScore;

                        Log::debug("Item score: {$itemScore}");
                    }
                }

                // Store in array with exact variabel name
                if (isset($variabelScores[$variabelNama])) {
                    $variabelScores[$variabelNama] = $variabelTotalScore;
                    Log::info("Variabel '{$variabelNama}' total score: {$variabelTotalScore}");
                } else {
                    Log::warning("Variabel '{$variabelNama}' not found in predefined list");
                }
            }

            // Map to assessment fields
            $this->skor_kepribadian = $variabelScores['Pembinaan Kepribadian'] ?? 0;
            $this->skor_kemandirian = $variabelScores['Pembinaan Kemandirian'] ?? 0;
            $this->skor_sikap = $variabelScores['Penilaian Sikap'] ?? 0;
            $this->skor_mental = $variabelScores['Penilaian Kondisi Mental'] ?? 0;
            $this->skor_total = $this->skor_kepribadian + $this->skor_kemandirian +
                               $this->skor_sikap + $this->skor_mental;

            Log::info('Final scores:', [
                'kepribadian' => $this->skor_kepribadian,
                'kemandirian' => $this->skor_kemandirian,
                'sikap' => $this->skor_sikap,
                'mental' => $this->skor_mental,
                'total' => $this->skor_total
            ]);

            $this->save();

            Log::info('=== Score Calculation Complete ===');

            return $this;

        } catch (\Exception $e) {
            Log::error('Error calculating scores: ' . $e->getMessage(), [
                'assessment_id' => $this->id,
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
