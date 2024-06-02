<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class submittedRequirements extends Model
{
    use HasFactory;
    
    protected $fillable =[
        'id',
        'for_request_id',
        'for_requirement_id',
        'requirement_filename' 
    ];

    public function for_request_id()
    {
        return $this->belongsTo(requestRecord::class, 'id');
    }
 

    public function for_requirement_id()
    {
        return $this->belongsTo(document_requirement::class, 'id');
    }

}
