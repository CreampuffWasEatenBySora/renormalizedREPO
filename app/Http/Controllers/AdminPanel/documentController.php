<?php

namespace App\Http\Controllers\AdminPanel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\barangayDocument;
use Illuminate\Support\Facades\DB;

class documentController extends Controller
{
    public function index(Request $request){
     
        $sort = $request->input('sort');
        $filter = $request->input('document_filter');
        $filterText = $request->input('document_searchbox');

        try {        
            $query = "SELECT id, `name`, `description`, created_at, updated_at
            FROM documents";

            if ($filter && $filterText) {
                
                if ($filter == 'any') {
                $query .= 
                "WHERE id LIKE '%". $filterText ."%' OR
                       name LIKE '%". $filterText ."%' OR
                       description LIKE '%". $filterText ."%' OR
                       created_at LIKE '%". $filterText ."%' OR
                       updated_at LIKE '%". $filterText ."%' ORDER BY br.fullName ASC 
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
}
