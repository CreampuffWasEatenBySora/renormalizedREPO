<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class addresses extends Model
{
    use HasFactory;

    protected $fillable =[
        'id',
        'resident_id',
        'municipality',
        'barangay',
        'subdivision_district',
        'house_number',
        'phone_number'
    ];

    public function resident()
    {
        return $this->belongsTo(barangay_residents::class, 'resident_id');
    }

}
