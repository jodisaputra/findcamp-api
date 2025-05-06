<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'region_id', 'flag_path', 'rating', 'description'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function requirements()
    {
        return $this->belongsToMany(\App\Models\Requirement::class)
            ->withPivot('is_required')
            ->withTimestamps();
    }
}
