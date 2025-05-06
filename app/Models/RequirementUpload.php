<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country_id',
        'requirement_id',
        'file_path',
        'status',
        'admin_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }
}
