<?php

namespace App\Models;

use App\Domain\Frequency\FrequencyResolver;
use App\Enums\JenisFrekuensi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

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
        'bobot_default',
        'bobot_last_set_month',
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
        'bobot_default' => 'decimal:2',
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

    public function scopeConditional($query)
    {
        return $query->where('jenis_frekuensi', JenisFrekuensi::KONDISIONAL);
    }

    public function getFrekuensiAttribute()
{
    return FrequencyResolver::resolve(
        $this,
        now()
    );
}
public function getFrequencyForMonth(Carbon $tanggalPenilaian): int
    {
        return FrequencyResolver::resolve(
            $this,
            $tanggalPenilaian
        );
    }

/**
     * Check if this item is conditional and bobot can be modified
     */
    public function isConditional(): bool
    {
        return $this->jenis_frekuensi === JenisFrekuensi::KONDISIONAL;
    }

    /**
     * Check if bobot has been set for current month
     */
    public function isBobotSetForMonth(string $monthKey): bool
    {
        return $this->bobot_last_set_month === $monthKey;
    }

    /**
     * Reset bobot to default value
     */
    public function resetBobotToDefault(): void
    {
        $this->update([
            'bobot' => $this->bobot_default,
            'bobot_last_set_month' => null,
        ]);
    }

}
