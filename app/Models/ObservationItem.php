<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ObservationItem extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'kode',
        'variabel_id',
        'aspect_id',
        'nama_item',
        'bobot_item',
        'bobot',
        'is_conditional_weight',
        'frekuensi_bulan',
        'frequency_rule_id',
        'use_dynamic_frequency',
        'sort_order',
        'aktif',
    ];

    protected $casts = [
        'variabel_id' => 'integer',
        'aspect_id' => 'integer',
        'bobot_item' => 'decimal:2',
        'bobot' => 'decimal:2',
        'is_conditional_weight' => 'boolean',
        'frekuensi_bulan' => 'integer',
        'sort_order' => 'integer',
        'aktif' => 'boolean',
        'use_dynamic_frequency' => 'boolean',
    ];

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['kode', 'nama_item', 'bobot', 'aktif'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function variabel()
    {
        return $this->belongsTo(AssessmentVariabel::class, 'variabel_id');
    }

    public function aspect()
    {
        return $this->belongsTo(AssessmentAspect::class, 'aspect_id');
    }

    public function frequencyRule()
    {
        return $this->belongsTo(FrequencyRule::class);
    }

    public function dailyObservations()
    {
        return $this->hasMany(DailyObservation::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeByVariabel($query, $variabelId)
    {
        return $query->where('variabel_id', $variabelId);
    }

    public function scopeByAspect($query, $aspekId)
    {
        return $query->where('aspect_id', $aspekId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('kode');
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['variabel', 'aspect', 'frequencyRule']);
    }

    // Methods
    public function calculateFrequency($daysInMonth)
    {
        if (!$this->use_dynamic_frequency || !$this->frequencyRule) {
            return $this->frekuensi_bulan;
        }

        $formula = $this->frequencyRule->formula;

        // Evaluate formula based on days in month
        foreach ($formula as $rule) {
            if ($daysInMonth <= $rule['max_days']) {
                return $rule['frequency'];
            }
        }

        return $this->frekuensi_bulan;
    }
}
