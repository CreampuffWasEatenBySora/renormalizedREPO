<?php

namespace App\Http\Controllers\ResidentPanel;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\notificationController;
use App\Models\registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\select;

class ResidentController extends Controller
{
    
    public function index(Request $request){
     
        $sort = $request->input('sort');
        $filter = $request->input('resident_filter');
        $filterText = $request->input('resident_searchbox');

        

        try {
            

            
            $query =DB::table('addresses as ad')
            ->select(
                 'br.id as id', 
                 'br.name', 
                 'ad.municipality', 
                 'ad.subdivision_district', 
                 'ad.barangay', 
                 'ad.house_number', 
                 'ad.created_at AS Reg_Date') 
            ->join('users as br', 'ad.resident_id', '=', 'br.UUID');

            if ($filter && $filterText) {
                
                if ($filter == 'any') {
                
                $query = $query
                ->whereAny([
                    'br.name',
                    'ad.municipality',
                    'ad.subdivision_district',
                    'ad.barangay',
                    'ad.house_number',
                    'br.created_at'], 'LIKE', '%'.$filterText.'%');
                $resultSet =$query->get();
            } 

            else {
                    $query = $query
                    ->where($filter, 'LIKE', '%'.$filterText.'%')
                    ->orderBy($filter);
                    $resultSet =$query->get();

            }

            } 
            else{
                $resultSet =$query->get();

            }

            // Log::info("Query Submitted: ". $query);
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
            $query = DB::table('registrations as reg')
            ->select(
                'br.UUID',
                'br.name as resident_name',
                'br.status',
                'br.birthday',
                'br.email',
                'ad.municipality',
                'ad.barangay',
                'ad.subdivision_district',
                'ad.house_number',
                'ad.phone_number',
                'reg.id',
                'reg.requirement_type',
                'reg.date_registered',
                'officer.name as Barangay_Officer',
                'reg.date_responded',
                'reg.remarks',
                'reg.selfie_filename',
                'reg.document_filename'
            )
            ->join('users as br', 'br.UUID', '=', 'reg.resident_id')
            ->leftJoin('users as officer', 'officer.UUID', '=', 'reg.barangay_officer_id')
            ->join('addresses as ad', 'ad.resident_id', '=', 'reg.resident_id')
            ->where(function($query) use ($resident_id) {
                $query->where('br.id', $resident_id)
                      ->orWhere('reg.id', $resident_id);
            });
            $resultSet = $query->get();       
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
            
        $resident =  DB::table("users")->where('UUID', $request->input('resident_uuid'))->first(); 
        $registration =  DB::table("registrations")->where('resident_id', $request->input('resident_uuid'))->first(); 
       
        
            $resident = User::find($resident->id);
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
                $officer= DB::table('users')->where('UUID','=', Auth::user()->UUID)->first();

                $eventDesc = $registration->status == "V" ? "Verified" : "Rejected";                

                notificationController::notifySpecific($officer->id, $resident->id, $registration->id, "Registration",$eventDesc);

            } catch (\Throwable $th) {
                //throw $th;
            }

            Log::info("Resident access level updated successfully.");  // Debug statement
            return redirect()->route('admin.view_resident',['resident_id'=>  $resident->id]);
            

        } catch (\Exception $e) {
            Log::error("Resident access level updated unsuccessful. {$e->getMessage()}");  // Debug statement
         return response()->json(['status' => 'error', 'message' => 'resident acess level update failed'], 500);
        
     }
    }
}
