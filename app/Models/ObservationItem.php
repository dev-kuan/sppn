<?php

namespace App\Models;

use App\Enums\JenisFrekuensi;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'jenis_frekuensi',
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
        'jenis_frekuensi' => JenisFrekuensi::class,
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

    // public function frequencyRule()
    // {
    //     return $this->belongsTo(FrequencyRule::class);
    // }

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
        return $query->with(['variabel', 'aspect']);
    }


    public function getFrekuensiAttribute()
{
    return \App\Domain\Frequency\FrequencyResolver::resolve(
        $this,
        now()
    );
}


    // Methods
// public function calculateFrequency($daysInMonth)
// {
//     // If using dynamic frequency with rules
//     if ($this->use_dynamic_frequency && $this->frequency_rule) {
//         $rules = is_string($this->frequency_rule)
//             ? json_decode($this->frequency_rule, true)
//             : $this->frequency_rule;

//         if (isset($rules['formula']) && is_array($rules['formula'])) {
//             foreach ($rules['formula'] as $rule) {
//                 if ($daysInMonth <= ($rule['max_days'] ?? 31)) {
//                     return $rule['frequency'] ?? $this->jenis_frekuensi;
//                 }
//             }
//         }
//     }

//     // Default: use static frequency
//     return $this->jenis_frekuensi ?? 0;
// }

}
