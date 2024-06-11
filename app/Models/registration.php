<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class registration extends Model
{
    use HasFactory;

    protected $fillable =[
        'id',
        'date_registered',
        'date_responded',
        'remarks',
        'requirement_type',
        'selfie_filename',
        'document_filename',
        'resident_id',
        'barangay_officer_id'
        
    ];

    public function resident()
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    public function barangay_Officer()
    {
        return $this->belongsTo(User::class, 'barangay_officer_id');
    }


}
