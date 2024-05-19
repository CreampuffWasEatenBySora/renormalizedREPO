<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\barangayDocument;
use App\Models\document_requirement;
use App\Models\requirement_listing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class requestControllerAPI extends Controller
{
    

    public function fetch(Request $request){


           

        try {
     
            $UUID = $request->input('userID');
            Log::info($request);  // Debug statement
            $requestData = [];

            $query = DB::table('request_records as reqs')
            -> select(

              'reqs.id as requestID', 
              'reqs.request_code as requestCode', 
              'reqs.date_requested as dateRequested', 
              'officer.fullName as officerName', 
              'reqs.date_responded as dateResponded', 
              'reqs.remarks as remarks',
              'reqs.status as status'

            )     
            ->join('barangay_residents as resident', 'resident.UUID', '=', 'reqs.resident_id')
            ->leftJoin('barangay_residents as officer', 'officer.UUID', '=', 'reqs.barangay_officer_id')
        //   ->where('reqs.resident_id', $UUID)
            
            ;

            $requests = $query->get();
              
              foreach ($requests as $request) {

                  $array = json_decode(json_encode($request), true);
                  $request_entry['request'] = $array;
                  $requestedDocuments_query = DB::table('requested_documents as doc_reqs')
                  ->select(
                      'doc_reqs.*',
                      'document.name as docName'
                  )
                  ->join('barangay_documents as document', 'document.id', '=', 'doc_reqs.for_document_id')
                  ->where('for_request_id', $request->requestID);
                  $requestedDocuments = $requestedDocuments_query->get()->toArray();  
                  
                  $i = 0;
                  foreach ($requestedDocuments as $requestedDocument) {

                      $array = json_decode(json_encode($requestedDocument), true);
                      $request_entry['request']['requested_doc'][$i] = $array;
                      
                      $i++;
                  }
                  
                  array_push($requestData, $request_entry['request']);
              }

              $jsonData = json_encode($requestData);

            Log::info($requestData);  // Debug statement
            return response()->json(['status' => 'success', 'message' => 'Logged in successfully!', 'request_data' => json_decode($jsonData) ], 200);
     
        } catch (\Throwable $th) {

            Log::info("Error in retrieving document data from database: ".$th);  // Debug statement
            return response()->json(['status' => 'failure', 'message' => 'Invalid login credentials. Try again.'], 200);
     
        }

     

    }
    
}
