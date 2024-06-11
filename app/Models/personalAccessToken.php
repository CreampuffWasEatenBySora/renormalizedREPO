<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class personalAccessToken extends Model
{
    use HasFactory;


    protected $fillable =[
        'id',
        'token_holder_id',
        'tokenable_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
        'created_at'
    ];

}
