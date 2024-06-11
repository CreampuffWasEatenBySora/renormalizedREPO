<?php

namespace App\Http\Controllers\AdminPanel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\notificationController;
use Illuminate\Support\Facades\Log;
use App\Models\barangayDocument;
use Illuminate\Support\Facades\Auth;
use App\Models\requirement_listing;
use Illuminate\Support\Facades\DB;

class documentController extends Controller
{
    public function index(Request $request){
     
        $sort = $request->input('sort');
        $filter = $request->input('document_filter');
        $filterText = $request->input('document_searchbox');

        try {        
            $query = "SELECT id, `name`, `description`, created_at, updated_at
            FROM barangay_documents";

            if ($filter && $filterText) {
                
                if ($filter == 'any') {
                $query .= 
                "WHERE id LIKE '%". $filterText ."%' OR
                       name LIKE '%". $filterText ."%' OR
                       description LIKE '%". $filterText ."%' OR
                       created_at LIKE '%". $filterText ."%' OR
                       updated_at LIKE '%". $filterText ."%' ORDER BY name ASC 
                ";
                $resultSet = DB::select($query);
                } else {
                $query .= "WHERE ".$filter." LIKE '%".$filterText."%' ORDER BY  ".$filter."  ASC";
                $resultSet = DB::select($query );
                }
            } else{
                $resultSet = DB::select($query);
            }
            Log::info("Query Submitted: ". $query);
            $jsonData = json_encode($resultSet);
          
            return view('administrator.document_operations.list_documents',  ['document_jsonData' => $jsonData]);

        } catch (\Throwable $th) {
            Log::error("Request set returned unsuccessfully : ".$th->getMessage() );  // Debug statement
            return response()->json(['status' => 'failed'], 200);
        }


    }

    public function create(Request $request){

        $query = "SELECT * FROM document_requirements";
        $resultSet = DB::select($query);
        // Log::info("Query Submitted: ". $query);
        $jsonData = json_encode($resultSet);

        return view('administrator.document_operations.create_document', ['requirement_jsonData' => $jsonData]);
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
 
            try {
                $officer= DB::table('users')->where('UUID','=', Auth::user()->UUID)->first();
                notificationController::notifyBarangayresidents($officer->id, $newDocument->id, "Document", "New");
            } catch (\Throwable $th) {
                Log::info("JSON not received: ". $th);
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

            $query = DB::table('requirement_listings as req_list')
            ->select('doc_req.id')
            ->join('barangay_documents as b_docs', 'req_list.for_document_id', '=','b_docs.id')
            ->join('document_requirements as doc_req', 'req_list.from_requirement_id', '=','doc_req.id')
            ->where('b_docs.id',$documentId);

            $resultSet = $query->get();
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

    
