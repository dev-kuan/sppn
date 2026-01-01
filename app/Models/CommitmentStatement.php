<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ============================================
// CommitmentStatement Model
// ============================================
class CommitmentStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'jenis',
        'is_signed',
        'signed_at',
        'catatan',
    ];

    protected $casts = [
        'is_signed' => 'boolean',
        'signed_at' => 'datetime',
    ];

    // Relationships
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    // Scopes
    public function scopeSigned($query)
    {
        return $query->where('is_signed', true);
    }

    public function scopeUnsigned($query)
    {
        return $query->where('is_signed', false);
    }

    public function scopeNkri($query)
    {
        return $query->where('jenis', 'nkri');
    }

    public function scopeNarkoba($query)
    {
        return $query->where('jenis', 'narkoba');
    }
}
