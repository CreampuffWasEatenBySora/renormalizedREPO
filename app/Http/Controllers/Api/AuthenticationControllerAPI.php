<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\barangay_residents;
use App\Models\personalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\isEmpty;

class AuthenticationControllerAPI extends Controller
{


    public function validateLogin($email, $password) : Request{
        
        $request = Request();


        try {
           
            $residentLogin = User::where('email', $email)->first();

            if ($residentLogin && Hash::check($password,  $residentLogin ->password)) {
            
                $request['userData'] = $residentLogin;  

            } 
 
        } catch (\Throwable $th) {

            Log::info("Error in retrieving JSON data from android client: ".$th);  // Debug statement

        }

        return $request;

    }

    public static  function validateAccessKey($UUID, $accesskey) : Bool{
        
        $token = personalAccessToken::where(['token'=> $accesskey, 'tokenable_type' => $UUID])->first();


        if ($token != null) {
            Log::info("token found:".$token);  // Debug statement

            if ($token->expires_at < now()) {
            Log::info("expired...");  // Debug statement
            return false;

            } else{
                Log::info("fine token");  // Debug statement
                return true;
            }

        } else {
            Log::info("no token found");  // Debug statement
            return false;
        }


    }


    public function getUserToken($apiKey, $UUID, $id) : Request{
        
        $request = Request();

        try {
           
            $user_token = personalAccessToken::where('tokenable_id', $id)
            ->where('tokenable_type',$UUID)->first(); 

            // Log::info("User token: ".$user_token);  // Debug statement



            if ( $user_token != null) {
                
                    // Log::info("DETECTED User token: ".$user_token);  // Debug statement

                    if (date($user_token['expires_at']) > now()) {

                        Log::info("Expiry: ".$user_token['expires_at']. " vs " . now());  // Debug statement
                    

                        $user_token ->update([
                            'last_used_at' => now() 
                        ]);

                            $request['tokenData'] = $user_token;  

                        }   

            } else {


                $newUserToken = personalAccessToken::create([
                    'token' => $apiKey,
                    'tokenable_type' =>$UUID,
                    'tokenable_id' => $id,
                    'last_used_at' => now(),
                    'expires_at' => now()->addWeek(2) //Token Expires in two weeks. 
    
                ])->get();
    
                if ($newUserToken !=null) {
        $request['tokenData'] = $user_token;   
                } 
    
            }
 
        } catch (\Throwable $th) {

            Log::info("Error in retrieving token data from server: ".$th);  // Debug statement

        }

        return  $request;

    }


    public function login(Request $request){

        $email = $request->input('email');
        $password = $request->input('password');
        $apiKey = bin2hex(random_bytes(28));    
        $resident  = AuthenticationControllerAPI::validateLogin($email, $password); 

        
        if ( $resident['userData'] != null) {
           
        $user_token = AuthenticationControllerAPI::getUserToken($apiKey, $resident['userData']->UUID, $resident['userData']->id); 



            if ( $user_token['tokenData']!= null ) {
        
                $resident = barangay_residents::where('UUID', $resident['userData']->UUID )->first();
                Log::info($resident);  // Debug statement    


                $sentUserData =[
                    'Email' => $resident->email,
                    'UUID' => $resident->UUID,
                    'FullName' => $resident->fullName,
                    'Birthday' => $resident->birthday,
                    'access_token' => $user_token['tokenData']->token,
                    'Status' => $resident->status,
                    'Address_id' => $resident->address_id,
                    'Registration_id'=> $resident->registration_id
                ];

                Log::info($sentUserData);  // Debug statement    
                Log::info("Account logged in successfully!");  // Debug statement     
                return response()->json(['status' => 'success', 'message' => 'Logged in successfully!', 'userdata' => $sentUserData], 200);
                


            } else {
               
                return response()->json(['status' => 'failure', 'message' => 'Expired token. Verify Account again.'], 200);
                
            }
            
        } else {

            return response()->json(['status' => 'failure', 'message' => 'Invalid login credentials. Try again.'], 200);

        }
        
    }


    public function findPhoneMatch(Request $request){
        
        $phoneNumber = $request->input('phoneNum');
        $findMatch = DB::table('addresses')->where('phone_number','=', $phoneNumber)->get();

        if ( count($findMatch) == 0 ) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'number is already used']);
        }
    }

    
    public function findEmailMatch(Request $request){
        
        $email = $request->input('email');
        $findMatch = DB::table('barangay_residents')->where('email','=', $email)->get();

        if ( count($findMatch) == 0 ) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'email is already used']);
        }
    }
}
