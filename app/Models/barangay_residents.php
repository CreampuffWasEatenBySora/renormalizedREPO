<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class barangay_residents extends Model
{
    use HasFactory;

    protected $fillable =[
        'id',
        'UUID',
        'fullName',
        'email', 
        'password',
        'birthday',
        'status',
        'access_level',
        'address_id',
        'registration_id'
    ];

    public function address()
    {
        return $this->belongsTo(addresses::class, 'address_id');
    }

    public function registration()
    {
        return $this->belongsTo(registration::class, 'registration_id');
    }

}
