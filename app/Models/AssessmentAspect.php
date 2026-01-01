<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AssessmentAspect extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'assessment_aspect';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'assessment_variabel_id',
    ];

    protected $casts = [
        'assessment_variabel_id' => 'integer',
    ];

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'assessment_variabel_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function variabel()
    {
        return $this->belongsTo(AssessmentVariabel::class, 'assessment_variabel_id');
    }

    public function observationItems()
    {
        return $this->hasMany(ObservationItem::class, 'aspek_id');
    }

    public function assessmentScores()
    {
        return $this->hasMany(AssessmentScore::class, 'aspek_id');
    }

    // Scopes
    public function scopeByVariabel($query, $variabelId)
    {
        return $query->where('assessment_variabel_id', $variabelId);
    }

    public function scopeWithItems($query)
    {
        return $query->with(['observationItems' => function ($q) {
            $q->aktif()->ordered();
        }]);
    }
}
