<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrymeType extends Model
{
    use HasFactory;

    protected $table = 'cryme_type';

    public $timestamps = false;

    protected $fillable = [
        'nama',
    ];

    // Relationships
    public function inmates()
    {
        return $this->hasMany(Inmate::class, 'cryme_type_id');
    }
}
