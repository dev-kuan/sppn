<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommitmentSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'user_id',
        'role',
        'nama',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    // Relationships
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeWaliPemasyarakatan($query)
    {
        return $query->where('role', 'wali_pemasyarakatan');
    }

    public function scopeKasubsi($query)
    {
        return $query->where('role', 'kasubsi');
    }
}
