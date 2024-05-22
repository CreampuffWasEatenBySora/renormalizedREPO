<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\barangayDocument;
use App\Models\document_requirement;
use App\Models\requirement_listing;
use App\Http\Controllers\Controller;
use App\Models\personalAccessToken;
use App\Models\requestedDocument;
use App\Models\requestRecord;
use App\Models\submittedRequirements;
use Illuminate\Http\Request;
use Nette\Utils\Arrays;

class requestControllerAPI extends Controller
{
    

    static function validateAccessKey($accesskey) : Bool{
        
        $token = personalAccessToken::where('token', $accesskey)->first();


        if ($token != null) {
            Log::info("token found:".$token);  // Debug statement

            if ($token->expires_at < now()) {
            Log::info("expired...");  // Debug statement
            return false;

            } else{
                Log::info("fine token");  // Debug statement
                return true;
            }

        } else {
            Log::info("no token found");  // Debug statement
            return false;
        }


    }

    static function storeFiles(Request $request, $requestID) : array {
        $fileArray = [];
        
        foreach ($request->all() as $key => $value) {
            if ($request->hasFile($key)) {
          
                
                try {
                  
                    Log::info($requestID);  // Debug statement
            
                // Get the original filename
                $originalFilename = $value->getClientOriginalName();
                $parts = explode(".", $originalFilename); // The requirement ID comes from the name of the file.

                       $filename =  $request->input('requestCode')."-".uniqid()."-".$originalFilename;
                       $value->move(public_path('storage/requirement_images'), $filename);
                       $path =  "requirement_images/".$filename;
                        Log::info($filename. " Uploaded to: ". $path);  // Debug statement

                        
                        submittedRequirements::create([
                                                
                            'for_request_id' =>  $requestID,
                            'for_requirement_id' =>   $parts[0], 
                            'requirement_filepath' => $path

                        ]);

                                       
                array_push($fileArray, $path);
                    
                } catch (\Throwable $th) {
                    Log::info("Requirement upload error: " . $th);  // Debug statement

                }
           
                }
            
            }


        return $fileArray;
    }


    public function fetch(Request $request){


           

        try {
     
            $UUID = $request->input('userID');
            Log::info($request);  // Debug statement
            $requestData = [];
            $requirementImages = [];

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
          ->where('reqs.resident_id', $UUID)
            
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

            // Log::info($requestData);  // Debug statement
            return response()->json(['status' => 'success', 'message' => 'Logged in successfully!', 'request_data' => json_decode($jsonData) ], 200);
     
        } catch (\Throwable $th) {

            Log::info("Error in retrieving document data from database: ".$th);  // Debug statement
            return response()->json(['status' => 'failure', 'message' => 'Invalid login credentials. Try again.'], 200);
     
        }

     

    }

    public function store(Request $request) {
        

        
        Log::info($request);  // Debug statement




        if (requestControllerApi::validateAccessKey($request->input('accessKey'))) {
          
           $newRequest =  requestRecord::create([

                'request_code' => $request->input('requestCode'),
                'resident_id' => $request->input('requesterID')

            ]);
            //":"4","reason":"Scholarship","quantity":0}}',
            
            $requestedDocuments = json_decode($request->input('requested_documents'));
            
            requestControllerAPI::storeFiles($request, $newRequest->id);
            
            foreach ($requestedDocuments as $document) {
                
            
                    requestedDocument::create([

                        'for_request_id' => $newRequest->id,
                        'for_document_id' => $document->id,
                        'request_reason' => $document->reason,
                        'request_quantity'=> $document->quantity

                    ]);

                    // if ($newrequestedDocument != null) {

                    //     Log::info("Requested Document Uploaded ". $newrequestedDocument->id);  // Debug statement
    
                    //         }
                        
                        
                        }
 
 

        if ($request != null) {
            return response()->json(['status' => 'success', 'message' => 'Logged in successfully!'  ], 200);

        } else {
            return response()->json(['status' => 'error', 'message' => 'Logged in successfully!'  ], 500);
        }

    } else {
        return response()->json(['status' => 'error', 'message' => 'Expired or invalid token.'  ], 400);

    }
    
}
}
