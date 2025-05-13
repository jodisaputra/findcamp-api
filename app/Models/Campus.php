<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'banner',
        'rating',
        'description',
        'phone',
        'email',
        'website',
        'country_id'
    ];

    protected $casts = [
        'rating' => 'float'
    ];

    protected $appends = ['logo_url', 'banner_url'];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? url('storage/' . $this->logo) : null;
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner ? url('storage/' . $this->banner) : null;
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
} 