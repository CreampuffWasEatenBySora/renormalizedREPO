<?php

namespace App\Http\Controllers\AdminPanel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class collectionController extends Controller
{
    public function index(Request $request)
    {

        $sort = $request->input('sort');
        $filter = $request->input('filter');
        $filterText = $request->input('searchbox');
        $collectionData = [];
        
        try {        
              
              $query = DB::table('collection_records as cols')
              -> select(

                'cols.id as id', 
                'cols.request_id as reqId', 
                'reqs.request_code as Request_code', 
                'resident.fullName as Requestee', 
                'officer.fullName as Issued_by', 
                'cols.date_confirmed as confirmed_on', 
                'cols.date_scheduled as scheduled_on', 
                'cols.date_collected as collected_on', 
                'cols.status as Status'

              )     
              ->join('request_records as reqs', 'cols.request_id', '=', 'reqs.id')
              ->join('barangay_residents as resident', 'resident.UUID', '=', 'reqs.resident_id')
              ->join('barangay_residents as officer', 'officer.UUID', '=', 'reqs.barangay_officer_id');

              $collections = $query->get();
                
                foreach ($collections as $collection) {

                    $array = json_decode(json_encode($collection), true);
                    $collection_entry['collection'] = $array;
                    $requestedDocuments_query = DB::table('requested_documents as doc_reqs')
                    ->select(
                        'doc_reqs.*',
                        'document.name as docName'
                    )
                    ->join('barangay_documents as document', 'document.id', '=', 'doc_reqs.for_document_id')
                    ->where('for_request_id',$collection->reqId);
                    $requestedDocuments = $requestedDocuments_query->get()->toArray();  
                    
                    $i = 0;
                    foreach ($requestedDocuments as $requestedDocument) {

                        $array = json_decode(json_encode($requestedDocument), true);
                        $collection_entry['collection']['requested_doc'][$i] = $array;
                        
                        $i++;
                    }
                    
                    array_push($collectionData, $collection_entry['collection'] );
                }

            $jsonData = json_encode($collectionData); 
            Log::info($collectionData);  // Debug statement
            
            return view('administrator.collections_operations.list_collections',  ['collections_jsonData' =>  $jsonData  ]);

        } catch (\Throwable $th) {
            Log::error("Request set returned unsuccessfully : ".$th->getMessage() );  // Debug statement
            return response()->json(['status' => 'failed'], 200);
        }


        return view('requests.create');

    }
 

    public function check(Request $request) {
        
        try {
     
            $collectionData = [];
            $requirementImages = [];

            $query = DB::table('request_records as reqs')
            -> select(

              'collection.id as collectID', 
              'reqs.id as requestID', 
              'reqs.request_code as requestCode', 
              'reqs.date_requested as dateRequested', 
              'resident.fullName as requestee', 
              'apprOfficer.fullName as reqAproveOfficerName', 
              'collectOfficer.fullName as reqAproveOfficerName', 
              'reqs.date_responded as dateResponded', 
              'collection.remarks as remarks', 
              'collection.date_scheduled as dateScheduled', 
              'collection.date_collected as dateCollected', 
              'collection.status as status'

            )     
            ->join('barangay_residents as resident', 'resident.UUID', '=', 'reqs.resident_id')
            ->Join('collection_records as collection', 'collection.request_id', '=', 'reqs.id')
            ->leftJoin('barangay_residents as apprOfficer', 'apprOfficer.UUID', '=', 'reqs.barangay_officer_id')
            ->leftJoin('barangay_residents as collectOfficer', 'collectOfficer.UUID', '=', 'collection.barangay_officer_id')
            ->where('collection.id', $request->input('collection_id'))
            ;

            $collection = $query->first();
            $array = json_decode(json_encode($collection), true);
              
            $collection_entry['collection']['collectionDetails']= $array;
 
                  $requestedDocuments_query = DB::table('requested_documents as doc_reqs')
                  ->select(
                      'doc_reqs.*',
                      'document.name as docName'
                  )
                  ->join('barangay_documents as document', 'document.id', '=', 'doc_reqs.for_document_id')
                  ->where('for_request_id', $collection->requestID);
                  $requestedDocuments = $requestedDocuments_query->get()->toArray();  
                  
                  $i = 0;
                  foreach ($requestedDocuments as $requestedDocument) {

                      $array = json_decode(json_encode($requestedDocument), true);
                      $collection_entry['collection']['requested_doc'][$i] = $array;
                      $i++;
                  }


                  $requirements_query = DB::table('submitted_requirements as requirement')
                  ->select(
                      'requirement.*',
                      'doc_reqs.name'
                  )
                  ->join('document_requirements as doc_reqs', 'doc_reqs.id', '=', 'requirement.for_requirement_id')
                  ->where('for_request_id', $collection->requestID);
                  $requirements = $requirements_query->get()->toArray();  

                  $b = 0;
                  foreach ($requirements as $requirement) {
                 
                    $array = json_decode(json_encode($requirement), true);
                      $collection_entry['collection']['requirements'][$b] = $array;
                      $b++;
                  }

                  
                  array_push($collectionData, $collection_entry['collection']);

              $jsonData = json_encode($collectionData);

            Log::info( $jsonData);  // Debug statement
            return view('administrator.collections_operations.view_collection')->with('collection_data', json_decode($jsonData, true));
     
        } catch (\Throwable $th) {

            Log::info("Error in retrieving document data from database: ".$th);  // Debug statement
            return response()->json(['status' => 'failure', 'message' => 'Error.:'.$th.' Please Try again.'], 200);
     
        }

     


    }


    function update(Request $request){
        

       

            $jsonData = $request->input('collectionArray');
            Log::info($jsonData);
      
            // Decode JSON data to PHP array
            $arrayData = json_decode($jsonData, true);



            $collectionDetails= $arrayData['collectiontDetails'];
            $collectionId = $collectionDetails['collectionID'];
            $requestId = $collectionDetails['requestID'];
            $collectionStatus =$collectionDetails['status'];
            $collectionRemarks =$collectionDetails['remarks'];
            $requestDocuments_granted = $arrayData['documents'];
            
            Log::info($requestDocuments_granted );
    
    
            try {
            
                DB::table('collection_records')
                ->where('id', $collectionId)
                ->update(['barangay_officer_id' => Auth::user()->UUID,
                  'date_collected' => now(), 'remarks' =>$collectionRemarks
                  ,'status' =>  $collectionStatus ]);
    
                $requestedDocs = DB::table('requested_documents')
                  ->where('for_request_id', $requestId)->get();
                
                  foreach ($requestedDocs as $document) {
    
                        if (in_array($document->id, $requestDocuments_granted) && $collectionStatus != 'CAN') {
    
                            DB::table('requested_documents')
                            ->where('id', $document->id)
                            ->update(['remarks' => 'COLLECTED', 
                            'status' => 'COL']);
        
                        } else {
                          
                            DB::table('requested_documents')
                            ->where('id', $document->id)
                            ->update(['remarks' => 'Collection Cancelled',  'status' => 'CAN']);
        
                        }
                     
                
                    
                  }
    
    
                return response()->json(['status' => 'success' ], 200);
                
                } catch (\Throwable $th) {
                Log::info("Error in updating request: ".$th);  // Debug statement
                return response()->json(['status' => 'failed'], 200);
                 
            }
              


        


    }
}

    
