<?php

namespace App\Http\Controllers\AdminPanel;

use App\Models\requestRecord;
use App\Http\Controllers\Controller;
use App\Http\Controllers\notificationController;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class requestRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $sort = $request->input('sort');
        $filter = $request->input('filter');
        $filterText = $request->input('searchbox');
        
        $requestData = [];
        
        try {        
              
              $query = DB::table('request_records as reqs')
              -> select(

                'reqs.id as id', 
                'reqs.request_code as Request_code', 
                'reqs.date_requested as Requested_on', 
                'resident.name as Requestee', 
                'officer.name as Responded_by', 
                'reqs.date_responded as Responded_on', 
                'reqs.status as Status'

              )     
              ->join('users as resident', 'resident.UUID', '=', 'reqs.resident_id')
              ->leftJoin('users as officer', 'officer.UUID', '=', 'reqs.barangay_officer_id');

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
                    ->where('for_request_id', $request->id);
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
            
            return view('administrator.requests_operations.list_requests',  ['requests_jsonData' =>  $jsonData  ]);

        } catch (\Throwable $th) {
            Log::error("Request set returned unsuccessfully : ".$th->getMessage() );  // Debug statement
            return response()->json(['status' => 'failed'], 200);
        }


        return view('requests.create');

    }





    public function check(Request $request){
           
        try {
     
            $requestData = [];
            $requirementImages = [];

            $query = DB::table('request_records as reqs')
            -> select(

              'reqs.id as requestID', 
              'reqs.request_code as requestCode', 
              'reqs.date_requested as dateRequested', 
              'resident.name as requestee', 
              'officer.name as officerName', 
              'reqs.date_responded as dateResponded', 
              'reqs.remarks as remarks',
              'reqs.status as status'

            )     
            ->join('users as resident', 'resident.UUID', '=', 'reqs.resident_id')
            ->leftJoin('users as officer', 'officer.UUID', '=', 'reqs.barangay_officer_id')
            ->where('reqs.id', $request->input('request_id'))
            
            ;

            $request = $query->first();
            $array = json_decode(json_encode($request ), true);
              
            $request_entry['request']['requestDetails']= $array;
 
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


                  $requirements_query = DB::table('submitted_requirements as requirement')
                  ->select(
                      'requirement.*',
                      'doc_reqs.name'
                  )
                  ->join('document_requirements as doc_reqs', 'doc_reqs.id', '=', 'requirement.for_requirement_id')
                  ->where('for_request_id', $request->requestID);
                  $requirements = $requirements_query->get()->toArray();  

                  $b = 0;
                  foreach ($requirements as $requirement) {
                 
                    $array = json_decode(json_encode($requirement), true);
                      $request_entry['request']['requirements'][$b] = $array;
                      $b++;
                  }

                  
                  array_push($requestData, $request_entry['request']);

              $jsonData = json_encode($requestData);

       //     Log::info($requestData);  // Debug statement
            return view('administrator.requests_operations.view_request')->with('request_data', json_decode($jsonData, true));
     
        } catch (\Throwable $th) {

            Log::info("Error in retrieving document data from database: ".$th);  // Debug statement
            return response()->json(['status' => 'failure', 'message' => 'Error.:'.$th.' Please Try again.'], 200);
     
        }

     

    }
 


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(requestRecord $requestRecord)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(requestRecord $requestRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request )
    {
        
        
        $jsonData = $request->input('requestArray');
      
        // Decode JSON data to PHP array
        $arrayData = json_decode($jsonData, true);
 

        $requestDetails= $arrayData['requestDetails'];
        $requestId = $requestDetails['id'];
        $requestStatus =$requestDetails['status'];
        $requestRemarks =$requestDetails['remarks'];
        $requestDocuments_granted = $arrayData['documents'];
        
        // Log::info($requestDocuments_granted );


        try {
        
            DB::table('request_records')
            ->where('id', $requestId)
            ->update(['barangay_officer_id' => Auth::user()->UUID,
              'date_responded' => now(), 'status' =>  $requestStatus,
              'remarks' => $requestRemarks]);

            $requestedDocs = DB::table('requested_documents')
              ->where('for_request_id', $requestId)->get();
            
              foreach ($requestedDocs as $document) {
 

                    if (in_array($document->id, $requestDocuments_granted) && $requestStatus != 'REJ') {

                        DB::table('requested_documents')
                        ->where('id', $document->id)
                        ->update(['remarks' => 'Granted', 
                        'status' => 'APR']);
    
                    } else {
                      
                        DB::table('requested_documents')
                        ->where('id', $document->id)
                        ->update(['remarks' => 'Not Granted',  'status' => 'REJ']);
    
                    }
                 
            
                
              }



            try {
                
                $request = DB::table('request_records')
                ->where('id', $requestId)->first();           
                
                $officer= DB::table('users')->where('UUID','=', Auth::user()->UUID)->first();
                $resident = DB::table('users')->where('UUID','=', $request->resident_id)->first();
                $eventDesc = $requestStatus == "APR" ? "Approved" : "Rejected";                

                notificationController::notifySpecific($officer->id, $resident->id, $request->id, "Request",$eventDesc);

            } catch (\Throwable $th) {
                Log::error("Error in notifying: ".$th);  // Debug statement
            }

            return response()->json(['status' => 'success' ], 200);
            
            } catch (\Throwable $th) {
            Log::error("Error in updating request: ".$th);  // Debug statement
            return response()->json(['status' => 'failed'], 200);
             
        }
          




    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequestRecord $requestRecord)
    {
        //
    }
}
