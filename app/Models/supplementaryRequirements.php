<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplementaryRequirements extends Model
{
    use HasFactory;
    
    protected $fillable =[
        'id',
        'from_request_id',
        'for_document_id',
        'requirement_type'
    ];

    public function from_request_id()
    {
        return $this->belongsTo(requestRecord::class, 'id');
    }

    public function for_document_id()
    {
        return $this->belongsTo(barangayDocument::class, 'id');
    }

}
