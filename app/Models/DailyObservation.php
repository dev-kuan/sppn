<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyObservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'observation_item_id',
        'hari',
        'is_checked',
        'catatan',
    ];

    protected $casts = [
        'hari' => 'integer',
        'is_checked' => 'boolean',
    ];

    // Relationships
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function observationItem()
    {
        return $this->belongsTo(ObservationItem::class);
    }

    // Scopes
    public function scopeByDay($query, $day)
    {
        return $query->where('hari', $day);
    }

    public function scopeChecked($query)
    {
        return $query->where('is_checked', true);
    }

    public function scopeUnchecked($query)
    {
        return $query->where('is_checked', false);
    }
}
