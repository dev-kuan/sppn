<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AssessmentVariabel extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'assessment_variabels';

    public $timestamps = false;

    protected $fillable = [
        'nama',
    ];

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function aspect()
    {
        return $this->hasMany(AssessmentAspect::class, 'assessment_variabel_id');
    }

    public function observationItems()
    {
        return $this->hasMany(ObservationItem::class, 'variabel_id');
    }

    public function assessmentScores()
    {
        return $this->hasMany(AssessmentScore::class, 'variabel_id');
    }

    // Scopes
    public function scopeWithAspect($query)
    {
        return $query->with('aspect');
    }

    public function scopeWithItems($query)
    {
        return $query->with(['aspects.observationItems' => function ($q) {
            $q->aktif()->ordered();
        }]);
    }
}
