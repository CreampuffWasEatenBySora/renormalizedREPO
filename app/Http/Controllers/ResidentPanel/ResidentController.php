<?php

namespace App\Http\Controllers\ResidentPanel;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    
    public function index(Request $request){
     
        $sort = $request->input('sort');
        $filter = $request->input('resident_filter');
        $filterText = $request->input('resident_searchbox');

        

        try {
            
        
            $query = "SELECT br.UUID as `id`, br.fullName, ad.municipality, ad.subdivision_district, 
            ad.barangay, ad.house_number, ad.created_at AS `Reg_Date`
            FROM addresses AS ad
            INNER JOIN barangay_residents AS br 
            ON ad.resident_id = br.UUID ";

            if ($filter && $filterText) {
                
                if ($filter == 'any') {
                $query .= 
                "WHERE br.fullname LIKE '%". $filterText ."%' OR
                       ad.municipality LIKE '%". $filterText ."%' OR
                       ad.subdivision_district LIKE '%". $filterText ."%' OR
                       ad.barangay LIKE '%". $filterText ."%' OR
                       ad.house_number LIKE '%". $filterText ."%' OR
                       br.created_at LIKE '%". $filterText ."%' ORDER BY br.fullName ASC 
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
            Log::info("Resident set returned successfully: ". $jsonData);  // Debug statement
            return view('administrator.resident_operations.list_residents',  ['resident_jsonData' => $jsonData]);

        } catch (\Throwable $th) {
            Log::error("Request set returned unsuccessfully : ".$th->getMessage() );  // Debug statement
            return response()->json(['status' => 'failed'], 200);
        }

 
        
    }
    
    public function investigate(Request $request){
        $resident_id = $request->input('resident_id');
        try {
            $query = 
            "SELECT  br.fullName as `Resident`, br.status, br.birthday, br.email,  ad.municipality,  ad.barangay, ad.subdivision_district, ad.house_number, ad.phone_number, reg.requirement_type,  reg.date_registered, officer.fullName as `Barangay Officer`, reg.date_responded, reg.remarks, reg.selfie_filepath, reg.document_filepath
            FROM registrations as reg
            INNER JOIN barangay_residents as br
            ON br.UUID = reg.resident_id 
            INNER JOIN barangay_residents as officer
            ON officer.UUID = reg.barangay_officer_id
            INNER JOIN addresses as ad
            ON ad.resident_id = reg.resident_id;            
            WHERE br.UUID = ".$resident_id;

            
        } catch (\Throwable $th) {
            //throw $th;
        }

        return view('administrator.resident_operations.view_resident', $request);




    }

    public function sortResidents(Request $request){

    }

    public function updateRegistration(Request $request){
        
    }
}
