<?php

namespace App\Http\Controllers\AdminPanel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\barangayDocument;
use App\Models\document_requirement;
use App\Models\requirement_listing;
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

    public function store(Request $request) {
        
        try {
               // Retrieve the JSON data from the request
        $jsonData = $request->input('documentArray');
      
        // Decode JSON data to PHP array
        $arrayData = json_decode($jsonData, true);
        $documentData = $arrayData['documentDetails'];
        $requirementData = $arrayData['requirements'];
        $current_time = now();

        barangayDocument::create([
            'name' => $documentData['name'],
        'description' =>$documentData['desc'],
        'created_at' => $current_time
        ]);

        $newDocument  = DB::table("barangay_documents")->where('created_at', $current_time)->first(); 
        foreach ($requirementData as $key => $value) {
                
            requirement_listing::create([
                'for_document_id' => $newDocument->id,
                'from_requirement_id' =>$value
            ]);

            }
 
            
            return response()->json(['status' => 'success'], 200);

        } catch (\Throwable $th) {
        Log::info("JSON not received: ". $th);

            return response()->json(['status' => 'failed'], 200);
        }
     
    }


    public function check(Request $request) {
        
        $documentId = $request->input('document_id');

        $documentData = barangayDocument::find($documentId);

        if ($documentData !=null) {
         
            //get the full list of requirements
            $allRequirements = DB::table("document_requirements")->get(); 
            

            //get the list of the listed requirements
            $query = 
            "SELECT doc_req.id 
            FROM requirement_listings as req_list
            INNER JOIN barangay_documents as b_docs
            ON req_list.for_document_id = b_docs.id
            INNER JOIN document_requirements as doc_req
            ON req_list.from_requirement_id = doc_req.id
            WHERE b_docs.id =".$documentId;

            $resultSet = DB::select($query);
            $docJsonData = json_encode($documentData);
            $selected_reqJsonData = json_encode($resultSet);
            $all_reqJsonData = json_encode($allRequirements);

            
 
             

            Log::info("Document Data sent: ".$docJsonData);
            Log::info("Requirement Data sent: ".$selected_reqJsonData);
            return view('administrator.document_operations.view_document', ['document_data' => json_decode($docJsonData, true), 
            'assigned_requirement_IDs' =>json_decode($selected_reqJsonData), 'all_requirements_data' =>json_decode($all_reqJsonData)]);

        } else {
            Log::info("No Data received for document ID: ".$documentId);
        }

    }


    function update(Request $request){
        

        try {

            $jsonData = $request->input('documentArray');
      
            // Decode JSON data to PHP array
            $arrayData = json_decode($jsonData, true);

            Log::info($arrayData);

            $documentData = $arrayData['documentDetails'];
            $requirementData = $arrayData['requirements'];


            
            $newDocument=  barangayDocument::find($documentData['id']);

            if ($newDocument) {
                $newDocument -> update([
                    'name' => $documentData['name'],
                'description' =>$documentData['desc']
                ]);
    
    
    
                DB::table("requirement_listings")->where('for_document_id', $documentData['id'])->delete(); 
    
                foreach ($requirementData as $key => $value) {
                    
                    requirement_listing::create([
                        'for_document_id' =>  $newDocument ->id,
                        'from_requirement_id' =>$value
                    ]);
        
                    }

                 return response()->json(['status' => 'success'], 200);
                
            }
           

        } catch (\Throwable $th) {

            Log::info("Error: ".$th);
            return response()->json(['status' => 'failed'], 200);

        }


        


    }
}

    
