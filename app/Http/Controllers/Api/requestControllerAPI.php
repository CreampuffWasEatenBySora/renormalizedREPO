<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\notificationController;
use App\Models\notifications;
use App\Models\requestedDocument;
use App\Models\requestRecord;
use App\Models\submittedRequirements; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class requestControllerAPI extends Controller
{
    
    static function storeFiles(Request $request, $requestID){
        
        foreach ($request->all() as $key => $value) {
            if ($request->hasFile($key)) {
          
                
                try {
                  
            
                // Get the original filename
                $originalFilename = $value->getClientOriginalName();
                $parts = explode(".", $originalFilename); // The requirement ID comes from the name of the file.

                       $filename = uniqid().".jpg" ;
 
                        $path = $value->storeAs(
                            'requirementImages/'.$request->input('requestCode'), $filename, 'private'
                        );
                        // Log::info($filename. " Uploaded to: ". $path);  // Debug statement

                        
                        submittedRequirements::create([
                                                
                            'for_request_id' =>  $requestID,
                            'for_requirement_id' =>   $parts[0], 
                            'requirement_filename' => $filename 

                        ]);

                                       
                    
                } catch (\Throwable $th) {
                    Log::info("Requirement upload error: " . $th);  // Debug statement

                }
           
                }
            
            }

    }
    
    static function fetchFileURLs(Request $request)  {
        $fileArray = [];
        $requestID = $request->input('requestID');
        $requestCode = $request->input('requestCode');
        $UUID = $request->input('userID');
        $accesskey = $request->input('accessKey');
        
            if (AuthenticationControllerAPI::validateAccessKey($UUID,$accesskey)) {
              
                $submuittedRequirements = DB::table('submitted_requirements as sub_req')
                ->select('sub_req.*','req.id','req.name','req.description'  )
                ->join('document_requirements as req', 'sub_req.for_requirement_id', '=', 'req.id')
                ->where('for_request_id',$requestID)->get();
    
                foreach ($submuittedRequirements as $requirement) {
                    $fileDetails = [];
                    $filePath =  "requirementImages/".$requestCode."/".$requirement->requirement_filename;
                    $fileDetails['requirementId'] = $requirement->id ;
                    $fileDetails['requirementName'] = $requirement->name ;
                    $fileDetails['requirementDesc'] = $requirement->description ;

                    
                    if (Storage::disk('private')->exists($filePath)) {
                        // Generate a URL for the file
                        $filePath =   "http://192.168.0.109/barangay_eConnect/renormalizedREPO/storage".'/app/private/'.$filePath;
                        $fileDetails['filePath'] = $filePath;
                        // Log::info( $filePath);  // Debug statement

                        array_push($fileArray,$fileDetails);
    
                    } 
                }

            }
            $jsonData = json_encode($fileArray);
           
            return response()->json(['status' => 'success', 'message' => 'URLs retrieved successfully!', 'requirement_URLs' => json_decode($jsonData) ], 200);
        
    }

    public function fetch(Request $request){


        try {
     
            $UUID = $request->input('userID');
            $requestID = $request->input('requestID');
            // Log::info($request);  // Debug statement
            $requestData = [];
            $requirementImages = [];


            $query = DB::table('request_records as reqs')
            -> select(

              'reqs.id as requestID', 
              'reqs.request_code as requestCode', 
              'reqs.date_requested as dateRequested', 
              'officer.name as officerName', 
              'reqs.date_responded as dateResponded', 
              'reqs.remarks as remarks',
              'reqs.status as status',
              'cols.date_scheduled as colSched',
              'cols.date_collected as colDate',
              'cols.id as colID',
              'cols.status as colStat'

            )     
            ->join('users as resident', 'resident.UUID', '=', 'reqs.resident_id')
            ->leftJoin('users as officer', 'officer.UUID', '=', 'reqs.barangay_officer_id')
            ->leftJoin('collection_records as cols', 'cols.request_id', '=', 'reqs.id')
            ;
            if ($requestID!= null) {
                
                $query = $query ->where('reqs.id', $requestID);
    
            } else {

                $query = $query ->where('reqs.resident_id', $UUID);
            
            }

            
                $requests = $query->get();
                Log::info( $requests);  // Debug statement

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
            //   Log::info($requestData);  // Debug statement

            // Log::info($requestData);  // Debug statement
            return response()->json(['status' => 'success', 'message' => 'Logged in successfully!', 'request_data' => json_decode($jsonData) ], 200);
     
        } catch (\Throwable $th) {

            Log::info("Error in retrieving document data from database: ".$th);  // Debug statement
            return response()->json(['status' => 'failure', 'message' => 'Invalid login credentials. Try again.'], 200);
     
        }

     

    }

    public function store(Request $request) {
        

        
        // Log::info($request);  // Debug statement
 
        $UUID =  $request->input('requesterID');
        $accesskey = $request->input('accessKey');

        if (AuthenticationControllerAPI::validateAccessKey($UUID, $accesskey)) {
          
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

            try {
                

            $resident = DB::table('users')->where('UUID','=', $UUID)->first();
            notificationController::notifyBarangayOfficers($resident->id, $newRequest->id, "Request", "New");

            } catch (\Throwable $th) {
                return response()->json(['status' => 'error', 'message' => 'Request submitted unsuccessfully...'  ], 500);
            }


            return response()->json(['status' => 'success', 'message' => 'Request submitted successfully!'  ], 200);

        } else {
            return response()->json(['status' => 'error', 'message' => 'Request submitted unsuccessfully...'  ], 500);
        }

    } else {
        return response()->json(['status' => 'error', 'message' => 'Expired or invalid token.'  ], 400);
    }
    }

    public function cancel(Request $request){
    
        $id = $request->input('id');
        // Log::info($request);
  
        try {
  
            $collection = requestRecord::find($id);
            $collection -> update([
                    'status' => "CAN",
                    'remarks' => "Cancelled by resident",
                    'date_responded' => now()
                ]);

                return response()->json(['status' => 'success', 'message' => 'Cancelled successfully'], 200);
             
  
        } catch (\Throwable $th) {
  
            Log::info("Error: ".$th);
            return response()->json(['status' => 'failed'], 200);
  
        }
  
      }
}
