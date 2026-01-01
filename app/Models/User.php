<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function createdAssessments()
    {
        return $this->hasMany(Assessment::class, 'created_by');
    }

    public function approvedAssessments()
    {
        return $this->hasMany(Assessment::class, 'approved_by');
    }

    public function recommendedCommitments()
    {
        return $this->hasMany(CommitmentRecommendation::class, 'recommended_by');
    }

    public function approvedCommitments()
    {
        return $this->hasMany(CommitmentRecommendation::class, 'approved_by');
    }

    public function commitmentSignatures()
    {
        return $this->hasMany(CommitmentSignature::class);
    }

    // Accessors
    public function getRoleNameAttribute()
    {
        return $this->roles->first()?->name ?? 'No Role';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active
            ? 'bg-green-100 text-green-800'
            : 'bg-red-100 text-red-800';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByRole($query, $roleName)
    {
        return $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Helper Methods
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isKepalaLapas()
    {
        return $this->hasRole('kepala_lapas');
    }

    public function isWaliPemasyarakatan()
    {
        return $this->hasRole('wali_pemasyarakatan');
    }

    public function isPetugasInput()
    {
        return $this->hasRole('petugas_input');
    }
}
