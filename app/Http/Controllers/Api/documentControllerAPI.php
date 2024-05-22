<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use App\Models\barangayDocument;
use App\Models\document_requirement;
use App\Models\requirement_listing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class documentControllerAPI extends Controller
{
    

    public function fetch(Request $request){


        $sentDocumentData = [];
        
        try {
     
            $documents = barangayDocument::all();
            foreach ($documents as $document) {
                
                $sentDocumentData['documents'][$document->id] = [
                    'id' => $document->id,
                    'name' => $document->name,
                    'description' => $document->description,
                    'requirements' => []
                ];
                
                    $requirements = requirement_listing::where('for_document_id', $document->id )->get();
            
                    foreach ($requirements as $listing) {
                    $requirement = document_requirement::where('id', $listing->from_requirement_id )->first();
                     $sentDocumentData['documents'][$document->id]['requirements'][$requirement->id] =
                     
                     [
                        'id' => $requirement->id,
                        'name' => $requirement->name,
                        'description' => $requirement->description
                     ];
                     
                        
                    }
            }
    
            // Log::info($sentDocumentData['documents']);  // Debug statement
            return response()->json(['status' => 'success', 'message' => 'Logged in successfully!', 'document_data' => $sentDocumentData], 200);
     
        } catch (\Throwable $th) {

            Log::info("Error in retrieving document data from database: ".$th);  // Debug statement
            return response()->json(['status' => 'failure', 'message' => 'Invalid login credentials. Try again.'], 200);
     
        }

     

    }

    
}
