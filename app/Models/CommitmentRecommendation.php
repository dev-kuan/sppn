<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommitmentRecommendation extends Model
{
    use HasFactory;

    protected $table = 'commitment_recommendations';

    protected $fillable = [
        'assessment_id',
        'deskripsi',
        'layak_dapat_hak',
        'recommended_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'layak_dapat_hak' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeLayak($query)
    {
        return $query->where('layak_dapat_hak', true);
    }

    public function scopeTidakLayak($query)
    {
        return $query->where('layak_dapat_hak', false);
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }
}
