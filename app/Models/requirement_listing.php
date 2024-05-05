<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class requirement_listing extends Model
{
    use HasFactory;

    
    protected $fillable =[
        'id',
        'for_document_id',
        'from_requirement_id' 
    ];

 
    public function for_document_id()
    {
        return $this->belongsTo(barangayDocument::class, 'id');
    }

    public function from_requirement_id()
    {
        return $this->belongsTo(document_requirement::class, 'id');
    }

}
