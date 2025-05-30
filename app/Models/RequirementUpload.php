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
        'payment_path',
        'payment_status',
        'payment_note',
        'admin_document_path',
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

    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getPaymentUrlAttribute()
    {
        return $this->payment_path ? asset('storage/' . $this->payment_path) : null;
    }
}
