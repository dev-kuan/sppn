<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

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
        return $this->belongsTo(Inmate::class);
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
    public function calculateScores()
    {
        $daysInMonth = $this->tanggal_penilaian->daysInMonth;

        // Get all observation items grouped by variabel and aspek
        $observationItems = ObservationItem::aktif()->ordered()->get();

        $variabelScores = [];

        foreach (['pembinaan_kepribadian', 'pembinaan_kemandirian', 'sikap', 'kondisi_mental'] as $variabel) {
            $items = $observationItems->where('variabel', $variabel);
            $totalSkor = 0;

            foreach ($items as $item) {
                $checkedDays = $this->dailyObservations()
                    ->where('observation_item_id', $item->id)
                    ->where('is_checked', true)
                    ->count();

                $frequency = $item->calculateFrequency($daysInMonth);

                // Calculate item score
                if ($frequency > 0) {
                    $skorItem = ($checkedDays / $frequency) * $item->bobot;
                    $totalSkor += $skorItem;
                }
            }

            $variabelScores[$variabel] = $totalSkor;
        }

        // Update assessment scores
        $this->skor_kepribadian = $variabelScores['pembinaan_kepribadian'] ?? 0;
        $this->skor_kemandirian = $variabelScores['pembinaan_kemandirian'] ?? 0;
        $this->skor_sikap = $variabelScores['sikap'] ?? 0;
        $this->skor_mental = $variabelScores['kondisi_mental'] ?? 0;
        $this->skor_total = array_sum($variabelScores);

        $this->save();

        return $this;
    }
}
