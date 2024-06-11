<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\Controller;
use App\Http\Controllers\notificationController;
use App\Models\collectionRecord;
use App\Models\personalAccessToken;
use App\Models\requestedDocument;
use App\Models\requestRecord;
use App\Models\submittedRequirements; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class CollectionControllerAPI extends Controller
{
    

    static function store(Request $request){
        
        $UUID =  $request->input('userID');
        $accesskey = $request->input('accessKey');
        $collectionID = $request->input('requestID');
        $requestSchedDate = $request->input('schedDate');

        Log::info($request);  // Debug statement
        

        if (AuthenticationControllerAPI::validateAccessKey($UUID, $accesskey)) {
         

        $newCollection = collectionRecord::create([
            'request_id' => $collectionID,
            'date_scheduled' => $requestSchedDate,
            'status' => "TBC",
            'remarks' => "Scheduled for collection"
        ]);


        }

        try {
                

            $resident = DB::table('users')->where('UUID','=', $UUID)->first();
            notificationController::notifyBarangayOfficers($resident->id, $newCollection->id, "Collection", "Scheduled");

            } catch (\Throwable $th) {
                return response()->json(['status' => 'error', 'message' => 'Collection date scheduled unsuccessfully...'  ], 500);
            }

        
        return response()->json(['status' => 'success', 'message' => 'Collection date scheduled successfully!'  ], 200);


      }

      
    public function fetch(Request $request){


        try {
     
            $UUID = $request->input('userID');
            $collectionID = $request->input('collectID');
            Log::info($request);  // Debug statement
            $requestData = [];
            $requirementImages = [];


            
            $query = DB::table('request_records as reqs')
            -> select(

              'collection.id as collectID', 
              'reqs.id as requestID', 
              'reqs.request_code as requestCode', 
              'reqs.date_requested as dateRequested', 
              'resident.name as requestee', 
              'apprOfficer.name as reqApproveOfficerName', 
              'collectOfficer.name as reqCollectOfficerName', 
              'reqs.date_responded as dateResponded', 
              'collection.remarks as remarks', 
              'collection.date_scheduled as dateScheduled', 
              'collection.date_collected as dateCollected', 
              'collection.status as status'

            )     
            ->join('users as resident', 'resident.UUID', '=', 'reqs.resident_id')
            ->Join('collection_records as collection', 'collection.request_id', '=', 'reqs.id')
            ->leftJoin('users as apprOfficer', 'apprOfficer.UUID', '=', 'reqs.barangay_officer_id')
            ->leftJoin('users as collectOfficer', 'collectOfficer.UUID', '=', 'collection.barangay_officer_id')
            ;
            if ($collectionID!= null) {
                
                $query = $query ->where('collection.id', $collectionID);
    
            } else {

                $query = $query ->where('resident.UUID', $UUID);
            
            }
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

              

            // Log::info($requestData);  // Debug statement
            return response()->json(['status' => 'success', 'message' => 'Logged in successfully!', 'collection_data' => json_decode($jsonData) ], 200);
     
        } catch (\Throwable $th) {

            Log::info("Error in retrieving document data from database: ".$th);  // Debug statement
            return response()->json(['status' => 'failure', 'message' => 'Invalid login credentials. Try again.'], 200);
     
        }

     

    }

    
    public function cancel(Request $request){
    
        $id = $request->input('id');
        // Log::info($request);
  
        try {
  
            $collection = collectionRecord::find($id);
            $collection -> update([
                    'status' => "CAN",
                    'remarks' => "Cancelled by resident",
                    'date_collected' => now()
                ]);
             
                return response()->json(['status' => 'success', 'message' => 'Cancelled successfully'], 200);
  
        } catch (\Throwable $th) {
  
            Log::info("Error: ".$th);
            return response()->json(['status' => 'failed'], 200);
  
        }
  
      }
}
