<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{

    
    protected $fillable =[
        'id',
        'for_user_id',
        'from_user_id',
        'for_event_id',
        'event_type',
        'event_description',
        'read_status' 
    ];


    
    public function for_user_id()
    {
        return $this->belongsTo(User::class, 'id');
    }

    
    public function from_user_id()
    {
        return $this->belongsTo(User::class, 'id');
    }
    use HasFactory;

}
