<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Inmate extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'no_registrasi',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'tingkat_pendidikan',
        'pekerjaan_terakhir',
        'lama_pidana_bulan',
        'sisa_pidana_bulan',
        'jumlah_residivisme',
        'catatan_kesehatan',
        'pelatihan',
        'program_kerja',
        'cryme_type_id',
        'status',
        'tanggal_masuk',
        'tanggal_bebas',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_bebas' => 'date',
        'lama_pidana_bulan' => 'integer',
        'sisa_pidana_bulan' => 'integer',
        'jumlah_residivisme' => 'integer',
    ];

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'no_registrasi', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function crymeType()
    {
        return $this->belongsTo(CrymeType::class, 'cryme_type_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    // Accessors
    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'aktif' => 'bg-green-100 text-green-800',
            'dirilis' => 'bg-blue-100 text-blue-800',
            'dipindahkan' => 'bg-yellow-100 text-yellow-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeDirilis($query)
    {
        return $query->where('status', 'dirilis');
    }

    public function scopeDipindahkan($query)
    {
        return $query->where('status', 'dipindahkan');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('no_registrasi', 'like', "%{$search}%");
        });
    }
}
