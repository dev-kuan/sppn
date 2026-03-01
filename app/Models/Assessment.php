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
        'skor_komitmen',
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
        'skor_komitmen' => 'decimal:4',
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
            Log::info('=== START SCORE CALCULATION ===', [
                'assessment_id' => $this->id,
            ]);

            $observationItems = ObservationItem::with('aspect.variabel')
                ->aktif()
                ->get();

            $variabelScores = [
                'kepribadian' => 0,
                'kemandirian' => 0,
                'sikap' => 0,
                'mental' => 0,
                'komitmen' => 0,
            ];

            // Group: variabel_id → aspek_id → items
            $grouped = $observationItems->groupBy([
                fn($item) => $item->aspect->assessment_variabel_id,
                fn($item) => $item->aspect_id,
            ]);

            foreach ($grouped as $variabelId => $aspects) {
                $firstItem = $aspects->first()->first();
                $variabelNama = strtolower($firstItem->aspect->variabel->nama ?? '');
                $variabelKey = $this->mapVariabelName($variabelNama);
                $aspectCount = $aspects->count();

                Log::info('--- Processing Variabel ---', [
                    'variabel_id' => $variabelId,
                    'variabel_nama' => $variabelNama,
                    'variabel_key' => $variabelKey,
                    'aspect_count' => $aspectCount,
                ]);

                $variabelTotal = 0;

                foreach ($aspects as $aspectId => $items) {
                    $itemsCountAspect = $items->count();
                    $aspectScore = 0;

                    foreach ($items as $item) {
                        $checkedCount = $this->dailyObservations()
                            ->where('observation_item_id', $item->id)
                            ->where('is_checked', true)
                            ->count();

                        $frequency = $item->getFrequencyForMonth($this->tanggal_penilaian);

                        if ($frequency > 0 && $itemsCountAspect > 0) {
                            $itemScore = (($checkedCount / $frequency) * $item->bobot)
                                * (100 / $itemsCountAspect);
                            $aspectScore += $itemScore;
                        }

                        Log::debug('Item calculation', [
                            'item_id' => $item->id,
                            'checked' => $checkedCount,
                            'frequency' => $frequency,
                            'bobot' => $item->bobot,
                            'items_in_aspek' => $itemsCountAspect,
                            'item_score' => $itemScore ?? 0,
                        ]);
                    }

                    // Save or update AssessmentScore for this aspect
                    $kategori = $this->getKategoriFromScore($aspectScore, $variabelKey);

                    AssessmentScore::updateOrCreate(
                        [
                            'assessment_id' => $this->id,
                            'aspect_id' => $aspectId,
                        ],
                        [
                            'skor' => $aspectScore,
                            'kategori' => $kategori,
                        ]
                    );

                    $variabelTotal += $aspectScore;

                    Log::info('Aspect calculated', [
                        'aspect_id' => $aspectId,
                        'score' => $aspectScore,
                        'kategori' => $kategori,
                    ]);
                }

                $variabelScore = $variabelTotal / $aspectCount;

                if (isset($variabelScores[$variabelKey])) {
                    $variabelScores[$variabelKey] = $variabelScore;
                } else {
                    Log::warning('Variabel key tidak dikenali', [
                        'variabel_nama' => $variabelNama,
                        'variabel_key' => $variabelKey,
                    ]);
                }
            }

            $this->skor_kepribadian = $variabelScores['kepribadian'];
            $this->skor_kemandirian = $variabelScores['kemandirian'];
            $this->skor_sikap = $variabelScores['sikap'];
            $this->skor_mental = $variabelScores['mental'];
            $this->skor_total = array_sum($variabelScores);

            Log::info('FINAL SCORE RESULT', [
                'kepribadian' => $this->skor_kepribadian,
                'kemandirian' => $this->skor_kemandirian,
                'sikap' => $this->skor_sikap,
                'mental' => $this->skor_mental,
                'total' => $this->skor_total,
            ]);

            $this->save();

            Log::info('=== END SCORE CALCULATION ===');

            return $this;
        } catch (\Exception $e) {
            Log::error('Error calculating scores: ' . $e->getMessage(), [
                'assessment_id' => $this->id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

private function mapVariabelName(string $variabelNama): string
{
    return match (true) {
        str_contains($variabelNama, 'kepribadian') => 'kepribadian',
        str_contains($variabelNama, 'kemandirian') => 'kemandirian',
        str_contains($variabelNama, 'sikap')       => 'sikap',
        str_contains($variabelNama, 'mental')      => 'mental',
        str_contains($variabelNama, 'komitmen')    => 'komitmen',
        default                                    => 'unknown',
    };
}
private function getKategoriFromScore(float $skor, string $variabelKey): string
    {
        if ($variabelKey === 'kepribadian' || $variabelKey === 'kemandirian') {
            if ($skor > 83.35) return 'Sangat Baik';
            if ($skor > 66.67) return 'Baik';
            if ($skor > 33.33) return 'Cukup Baik';
            if ($skor > 16.66) return 'Tidak Baik';
            return 'Sangat Tidak Baik';
        }
        if ($variabelKey === 'sikap'){
            if ($skor > 83.35) return 'Sangat Patuh';
            if ($skor > 66.67) return 'Patuh';
            if ($skor > 33.33) return 'Cukup Patuh';
            if ($skor > 16.66) return 'Tidak Patuh';
            return 'Sangat Tidak Patuh';
        }
        if ($variabelKey === 'mental'){
            if ($skor > 83.35) return 'Sangat Sehat Mental';
            if ($skor > 66.67) return 'Sehat Mental';
            if ($skor > 33.33) return 'Cukup Sehat Mental';
            if ($skor > 16.66) return 'Tidak Sehat Mental';
            return 'Sangat Tidak Sehat Mental';
        }

        if ($skor > 83.35) return 'Sangat Baik';
        if ($skor > 66.67) return 'Baik';
        if ($skor > 33.33) return 'Cukup Baik';
        if ($skor > 16.66) return 'Tidak Baik';
        return 'Sangat Tidak Baik';
    }

    /**
     * Check if all aspect scores have been reviewed (have catatan)
     */
    public function hasAllAspectScoresReviewed(): bool
    {
        $totalAspects = AssessmentAspect::count();
        $reviewedAspects = $this->assessmentScores()->whereNotNull('catatan')->count();

        return $reviewedAspects === $totalAspects;
    }
}
