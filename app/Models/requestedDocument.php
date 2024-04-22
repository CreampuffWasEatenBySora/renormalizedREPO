<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class requestedDocument extends Model
{
    use HasFactory;

    
    protected $fillable =[
        'id',
        'for_request_id',
        'for_document_id',
        'request_reason',
        'request_quantity'
    ];

    public function for_request_id()
    {
        return $this->belongsTo(requestRecord::class, 'id');
    }

    public function for_document_id()
    {
        return $this->belongsTo(barangayDocument::class, 'id');
    }



}
