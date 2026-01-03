<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentScore extends Model
{
    use HasFactory;

    protected $table = 'assessment_scores';

    protected $fillable = [
        'assessment_id',
        'variabel_id',
        'aspect_id',
        'skor',
        'bobot',
        'skor_terbobot',
        'kategori',
        'catatan',
    ];

    protected $casts = [
        'variabel_id' => 'integer',
        'aspect_id' => 'integer',
        'skor' => 'decimal:4',
        'bobot' => 'decimal:4',
        'skor_terbobot' => 'decimal:4',
    ];

    // Relationships
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function variabel()
    {
        return $this->belongsTo(AssessmentVariabel::class, 'variabel_id');
    }

    public function aspect()
    {
        return $this->belongsTo(AssessmentAspect::class, 'aspect_id');
    }

    // Accessors
    public function getKategoriAttribute($value)
    {
        if ($value) return $value;

        $skor = $this->skor;

        // Check if variabel is sikap or kondisi_mental based on variabel relationship
        if ($this->variabel) {
            $variabelNama = strtolower($this->variabel->nama);

            if (str_contains($variabelNama, 'sikap') || str_contains($variabelNama, 'mental')) {
                if ($skor >= 81) return 'Sangat Patuh / Sangat Sehat Mental';
                if ($skor >= 61) return 'Patuh / Sehat Mental';
                if ($skor >= 41) return 'Cukup Patuh / Cukup Sehat Mental';
                if ($skor >= 21) return 'Tidak Patuh / Tidak Sehat Mental';
                return 'Sangat Tidak Patuh / Sangat Tidak Sehat Mental';
            }
        }

        if ($skor >= 81) return 'Sangat Baik';
        if ($skor >= 61) return 'Baik';
        if ($skor >= 41) return 'Cukup Baik';
        if ($skor >= 21) return 'Tidak Baik';
        return 'Sangat Tidak Baik';
    }

    // Scopes
    public function scopeByVariabel($query, $variabelId)
    {
        return $query->where('variabel_id', $variabelId);
    }

    public function scopeByAspect($query, $aspekId)
    {
        return $query->where('aspect_id', $aspekId);
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['variabel', 'aspect']);
    }
}
