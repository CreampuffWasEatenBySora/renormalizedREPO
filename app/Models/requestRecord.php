<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class requestRecord extends Model
{
    use HasFactory;

    protected $fillable =[
        'id',
        'resident_id',
        'barangay_officer_id',
        'date_requested',
        'date_responded',
        'status',
        'remarks'
    ];

    public function resident_id()
    {
        return $this->belongsTo(barangay_residents::class, 'id');
    }

    public function barangay_officer_id()
    {
        return $this->belongsTo(barangay_residents::class, 'id');
    }



}
