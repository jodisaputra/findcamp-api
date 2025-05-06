<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'requirement_name',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function countries()
    {
        return $this->belongsToMany(Country::class)
            ->withPivot('is_required')
            ->withTimestamps();
    }
}
