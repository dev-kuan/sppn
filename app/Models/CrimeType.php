<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrimeType extends Model
{
    use HasFactory;

    protected $table = 'crime_types';

    public $timestamps = false;

    protected $fillable = [
        'nama',
    ];

    // Relationships
    public function inmates()
    {
        return $this->hasMany(Inmate::class, 'crime_type_id');
    }
}
