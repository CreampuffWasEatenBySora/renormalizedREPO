<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\collectionRecord;
use App\Models\requestedDocument;
use App\Models\requestRecord;
use App\Models\requirements;
use App\Models\submittedRequirements;
use App\Models\supplementaryRequirements;
use DateTime;

use function PHPUnit\Framework\isNull;

class requestApiController extends Controller
{
    
    public function enterRequestRecord(Request $request){

        try {
            $reqID = $request->input('request_id');
            $resident_id = $request->input('request_resident_id');
            $requestedDocumentJSON = $request->input('requested_document_JSON');
            $requirementsJSON = $request->input('requirements_JSON');


            requestRecord::create([
                'id' => $reqID,
                'resident_id' => $resident_id
            ]);
            
            foreach ($requestedDocumentJSON as $key => $value) {
               requestedDocument::create([
                'for_request_id' => $reqID,
                'for_document_id'=> $value['docID']
               ]);
            }
            
            foreach ($requirementsJSON as $key => $value) {
                 submittedRequirements::create([
                 'from_request_id' => $reqID,
                 'for_document_id'=> $value['docID'],
                 'requirement_type'=> $value['type']
                ]);
             }

            
    
            Log::info("Request submitted successfully.");  // Debug statement
            return response()->json(['status' => 'success', 'message' => 'request is added','request_id' => $reqID ], 200);
        
        } catch (\Throwable $th) {
            Log::info("Error in submitting request: ".$th->getMessage());  // Debug statement
            return response()->json(['status' => 'failed.'], 200);
        
        }
    }

    public function uploadRequirements(Request $request){


         try {

            foreach ($request->all() as $key => $value) {
                if ($request->hasFile($key)) {
                   
                    // Get the identifier from the form field name
                    $id = str_replace('file', '', $key);
                
                    // Get the original filename
                    $originalFilename = $value->getClientOriginalName();
                   
                    // Log::info("Imagefound:" . $key . " - " .$value);  // Debug statement
                   
                    $path = $value->storeAs('requirement_images', $originalFilename);
                
                   
                } 
            }
            
            Log::info("Requirement image records updated successfully.");  // Debug statement
            return response()->json(['status' => 'success', 'message' => 'Images are uploaded'], 200);

           } catch (\Exception $e) {
            Log::error("Error uploading Requirement image : {$e->getMessage()}");  // Debug statement
            return response()->json(['status' => 'error', 'message' => 'Image upload failed'], 500);
           
        }
            

    }
    
    public function fetchRequestSet(Request $request){

        try {
            
            $id = $request->input('request_id');
            $filter = $request->input('filter');
            $filterText = $request->input('filterText');
            
            $query = "SELECT * FROM request_records WHERE id = ?";

            if ($filter && $filterText) {
                $query .= " AND " . $filter . " = ?";
            }

            $resultSet = requestRecord::select($query, [$id, $filterText])->get();

            Log::info("Request set returned successfully.");  // Debug statement
            return response()->json($resultSet);

        } catch (\Throwable $th) {
            Log::error("Request set returned unsuccessfully : ".$th->getMessage() );  // Debug statement
            return response()->json(['status' => 'failed'], 200);
        }


    }

    public function updateRequestRecord(Request $request){
        

        
        try {
            
            $reqID = $request->input('request_id');
            $barangay_officer_id = $request->input('barangay_officer_id');
            $status = $request->input('status');
            $remarks = $request->input('remarks');
            $currentTimestamp = now();

            $targetRequest =  requestRecord::find($reqID);
    
            if ($targetRequest) {
                $targetRequest ->update([
                    'barangay_officer_id' => $barangay_officer_id,
                    'date_responded' => $currentTimestamp,
                    'status' => $status,
                    'remarks' => $remarks
                ]);

                if ($status.equalTo("TBC")) {
                    try {
                        collectionRecord::create([
                            'request_id' => $reqID,
                            'status' => $status,
                            'remarks' => $remarks
                        ]);

                        Log::info("Collection record entered successfully.");  // Debug statement
                        
                    } catch (\Throwable $th) {
                        Log::error("Collection record entered unsuccessfully: ".$th->getMessage() );  // Debug statement
                    }
                
                }
            }
    

            Log::info("Request record updated successfully.");  // Debug statement
            return response()->json(['status' => 'success', 'message' => 'request is updated','request_id' => $reqID ], 200);


        } catch (\Throwable $th) {
            Log::error("Request record updated  unsuccessfully: ".$th->getMessage() );  // Debug statement
            return response()->json(['status' => 'failed'], 200);
        }

    }
}
