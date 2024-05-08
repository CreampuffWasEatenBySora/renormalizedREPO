<?php

namespace App\Http\Controllers\AdminPanel;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\document_requirement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class requirementController extends Controller
{
    public function index(Request $request){
     
        $sort = $request->input('sort');
        $filter = $request->input('document_filter');
        $filterText = $request->input('document_searchbox');

        try {        
            $query = "SELECT id, `name`, `description`, created_at, updated_at
            FROM document_requirements";

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
          
            return view('administrator.requirements_operations.list_requirements',  ['requirement_jsonData' => $jsonData]);

        } catch (\Throwable $th) {
            Log::error("Request set returned unsuccessfully : ".$th->getMessage() );  // Debug statement
            return response()->json(['status' => 'failed'], 200);
        }


    }

    public function create(Request $request){
        
        return view('administrator.requirements_operations.create_requirement');

    }

    public function store(Request $request){
        $name = $request->input('name');
        $desc = $request->input('description');

        try {        
            
            document_requirement::create([

                'name' => $name,
                'description'=> $desc

            ]);

            Log::info("Requirement Added:" . $name );
            
            return redirect()->route('admin.list_requirements');


        } catch (\Throwable $th) {
            Log::error("Request set returned unsuccessfully : ".$th->getMessage() );  // Debug statement
            return response()->json(['status' => 'failed'], 200);
        }

    }
}
