<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class barangayDocument extends Model
{
    use HasFactory;

    
    protected $fillable =[
        'id',
        'name',
        'description',
        'supplementary requirements' 
    ];

    

}
