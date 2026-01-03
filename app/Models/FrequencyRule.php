<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrequencyRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_aturan',
        'deskripsi',
        'formula',
        'aktif',
    ];

    protected $casts = [
        'formula' => 'array',
        'aktif' => 'boolean',
    ];

    // Relationships
    public function observationItems()
    {
        return $this->hasMany(ObservationItem::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

}
