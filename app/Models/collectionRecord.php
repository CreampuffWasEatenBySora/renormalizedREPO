<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class collectionRecord extends Model
{
    use HasFactory;

    
    protected $fillable =[
        'id',
        'request_id',
        'barangay_officer_id',
        'date_granted',
        'date_scheduled',
        'date_collected',
        'status',
        'remarks'
    ];

    public function request_id()
    {
        return $this->belongsTo(requestRecord::class, 'id');
    }


}
