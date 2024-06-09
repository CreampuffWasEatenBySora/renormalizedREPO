<?php

namespace App\Http\Controllers\ResidentPanel;

use Illuminate\Support\Facades\Log;
use App\Models\barangay_residents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\notificationController;
use App\Models\registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            "SELECT br.UUID, br.fullName as `resident_name`, br.status, br.birthday, br.email,  ad.municipality,  ad.barangay, ad.subdivision_district, ad.house_number, ad.phone_number, reg.requirement_type,  reg.date_registered, officer.fullName as `Barangay Officer`, reg.date_responded, reg.remarks, reg.selfie_filepath, reg.document_filepath
            FROM registrations as reg
            INNER JOIN barangay_residents as br
            ON br.UUID = reg.resident_id 
            LEFT JOIN barangay_residents as officer
            ON officer.UUID = reg.barangay_officer_id
            INNER JOIN addresses as ad
            ON ad.resident_id = reg.resident_id           
            WHERE br.id = '".$resident_id."'";
            
            $resultSet = DB::select($query);       
            Log::info("Query Submitted: ". $query);
            $jsonData = json_encode($resultSet);

            // $dataArray = json_decode($jsonData, true);
            // $document_filepath =  $dataArray[0]['document_filepath'];
            // $document_filepath =  Storage::url($document_filepath) ;
            // Log::info("Document filepath: ". $document_filepath); // Debug statement

            Log::info("Resident set returned successfully: ". $jsonData);  // Debug statement
            
            return view('administrator.resident_operations.view_resident')->with('data', json_decode($jsonData, true));
        
        } catch (\Throwable $th) {
            Log::info("Error in viewing resident detail: ". $th);
            return response()->json(['status' => 'failed'], 200);

        }

    }

    public function sortResidents(Request $request){

    }

    public function updateRegistration(Request $request){
        try {
            Log::error("Resident UUID: ".$request->input('resident_uuid'));  // Debug statement
            
        $resident =  DB::table("barangay_residents")->where('UUID', $request->input('resident_uuid'))->first(); 
        $registration =  DB::table("registrations")->where('resident_id', $request->input('resident_uuid'))->first(); 
       
        
            $resident = barangay_residents::find($resident->id);
            $resident->update([
                'status' => $request->input('approval_status')
            ]);
            
            $registration = registration::find($registration->id);
            $registration->update([
                'date_responded' => now(),
                'remarks' => $request->input('approval_remarks'),
                'barangay_officer_id'=> Auth::user()->UUID
            ]);


            try {
                $officer= DB::table('barangay_residents')->where('UUID','=', Auth::user()->UUID)->first();

                $eventDesc = $registration->status == "V" ? "Registration Verified" : "Registration Rejected";                

                notificationController::notifySpecific($officer->id, $resident->id, $registration->id, "Registration",$eventDesc);

            } catch (\Throwable $th) {
                //throw $th;
            }

            Log::info("Resident access level updated successfully.");  // Debug statement
            return route('admin.view_resident',['resident_uuid'=>  $resident->UUID]);

        } catch (\Exception $e) {
            Log::error("Resident access level updated unsuccessful. {$e->getMessage()}");  // Debug statement
         return response()->json(['status' => 'error', 'message' => 'resident acess level update failed'], 500);
        
     }
    }
}
