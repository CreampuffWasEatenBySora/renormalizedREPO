<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\barangay_residents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\addresses;
use Illuminate\Auth\Events\Registered;
use App\Models\registration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\notificationController;

class ApiController extends Controller
{

    public $regId;

    public function register(Request $request)
    {
        Log::info("requestStarted");  // Debug statement

        try {
            // Ready the data to be uploaded
            $fullName = $request->input('temp_resident_nameData');
            $email = $request->input('temp_resident_loginData.email');
            $password = $request->input('temp_resident_loginData.password');
            $uuid = Str::uuid()->toString();
            $IDtype = $request->input('temp_resident_IDType');

            // Store the user data first
            barangay_residents::create([
                'UUID' => $uuid,
                'fullName' => $fullName,
                'email' => $email,
                'password' => Hash::make($password)

            ]);

            User::create([
                'name' => $fullName,
                'UUID' => $uuid,
                'email' => $email,
                'password' => Hash::make($password)
            ]);
    

            $municipality = $request->input('temp_resident_addressData.municipality');
            $barangay = $request->input('temp_resident_addressData.barangay');
            $subdivision = $request->input('temp_resident_addressData.subdivision');
            $houseNumber = $request->input('temp_resident_addressData.houseNumber');
            $phoneNumber = $request->input('temp_resident_addressData.phoneNumber');
       

            addresses::create([
                'resident_id' => $uuid,
                'municipality' => $municipality,
                'barangay' => $barangay,
                'subdivision_district' => $subdivision,
                'house_number' => $houseNumber,
                'phone_number' => $phoneNumber
            ]);

            registration::create([
                'resident_id' => $uuid,
                'requirement_type'=>$IDtype
            ]);

        $newResidentID = DB::table("barangay_residents")->where('UUID', $uuid)->first(); 
        $newAddress = DB::table("addresses")->where('resident_id', $uuid)->first(); 
        $newRegistration = DB::table("registrations")->where('resident_id', $uuid)->first(); 

        if($newResidentID){

            $newResident =  barangay_residents::find($newResidentID->id);

            $newResident ->update([
                'address_id' => $newAddress->id,
                'registration_id' => $newRegistration->id
            ]);

        }

            return response()->json(['status' => 'success', 'message' => 'Account is added','registrationID' => $newRegistration->id, 'residentID' => $newResidentID->UUID], 200);
        } catch (\Exception $e) {
            Log::error("Error registering user: {$e->getMessage()}");  // Debug statement
            return response()->json(['status' => 'error', 'message' => 'Registration failed'], 500);
        }
    }

    public function uploadImages(Request $request){

        $regId = $request->input('registrationID');
        Log::info('regID: '.$regId);
      
        $newRegistration = registration::find($regId);
                                
        
         try {

            foreach ($request->all() as $key => $value) {
                if ($request->hasFile($key)) {
                   
                    // Get the identifier from the form field name
                    $id = str_replace('file', '', $key);
                
                    // Get the original filename
                    $originalFilename = $value->getClientOriginalName();


                        if ( Str::contains($originalFilename, 'selfie')) {
    
                            $filename = "SEL".uniqid().".jpg" ;
 
                            $value->storeAs(
                                'registrationImages/reg-'.$newRegistration->id, $filename, 'private'
                            );
     
                            $newRegistration->update([
                                'selfie_filename' => $filename,
                                'remarks' => 'For verification'
                            ]);
                            
                        } else {
     
                            $filename = "DOC".uniqid().".jpg" ;
 
                            $value->storeAs(
                                'registrationImages/reg-'.$newRegistration->id, $filename, 'private'
                            );
          

                            $newRegistration->update([
                                'document_filename' => $filename
                            ]);
                        }
                        

                } 
            }


            try {
                
                
                $resident = DB::table('barangay_residents')->where('UUID','=', $newRegistration->resident_id)->first();

                notificationController::notifyBarangayOfficers($resident->id, $newRegistration->id, "Registration", "New Registration Submitted");


            } catch (\Throwable $th) {
                //throw $th;
            }
            
            Log::info("Image records updated successfully.");  // Debug statement
            return response()->json(['status' => 'success', 'message' => 'Images are uploaded'], 200);

           } catch (\Throwable $th) {
            Log::error("Error uploading image user: ".$th);  // Debug statement
            return response()->json(['status' => 'error', 'message' => 'Image upload failed'], 500);
           
        }
    }


     





}
