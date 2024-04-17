<?php

namespace App\Http\Controllers;

use App\Models\barangay_residents;
use App\Models\address;
use App\Models\addresses;
use App\Models\registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class signupController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    public function stepOne(Request $request)    
    {


  
        return view('signup.1_enterName');

    }

    public function stepTwo(Request $request)    
    {
        

        $stepOneData = $request->validate([
            'firstName'=> 'required|regex:/^[a-zA-Z\s]+$/u|string|max:30',
            'middleName'=> 'required|regex:/^[a-zA-Z\s]+$/u|string|max:30',
            'lastName'=> 'required|regex:/^[a-zA-Z\s]+$/u|string|max:30'
        ]);
        
        $fullName = $stepOneData['firstName']." ".$stepOneData['middleName']." ".$stepOneData['lastName'];

        $request->session()->put('temp_resident_nameData',$fullName);
        
        return view('signup.2_enterAddress');

    }

    public function stepThree(Request $request)    
    {

        // Collect all input data
    $municipality = $request->input('municipality');
    $barangays = $request->input('barangays');
   

    // Validate the input data
    $validatedData = $request->validate([
        'barangays' => 'required',
        'subdivision' => 'required|string|max:30',
        'houseNumber' => 'required|string|max:30',
        'phoneNumber' => 'required|string|size:11'
    ]);

    // Combine the input data and validated data
    $stepTwoData = [
        'municipality' => $municipality,
        'barangays' => $barangays,
        'subdivision' => $validatedData['subdivision'],
        'houseNumber' => $validatedData['houseNumber'],
        'phoneNumber' => $validatedData['phoneNumber']
    ];

        $request->session()->put('temp_resident_addressData', $stepTwoData);
        
        return view('signup.3_enterLogIn');

    }

    public function stepFive(Request $request)    
    {
        
            return view('signup.5_confirmAccount');

    }


    public function stepFour(Request $request)    
    {

        $data = $request->validate([
            'email' => 'required|email|unique:barangay_residents,email',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised()],
            'password_confirmation' => 'required|same:password'
        ]);

    
        $request->session()->put('temp_resident_loginData', $data);
        
        return $this->store($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        //Ready the data to be uploaded
        $fullName= $request->session()->get('temp_resident_nameData');
        $email = $request->session()->get('temp_resident_loginData')['email'];
        $password = $request->session()->get('temp_resident_loginData')['password'];
        $uuid = Str::uuid()->toString();
        //Store the user data first
        
        barangay_residents::create([
            'UUID' => $uuid,
            'fullName' => $fullName,
            'email' => $email,
            'password' => $password
        ]);


        $municipality = $request->session()->get('temp_resident_addressData')['municipality'];
        $barangay = $request->session()->get('temp_resident_addressData')['barangays'];
        $subdivision = $request->session()->get('temp_resident_addressData')['subdivision'];
        $houseNumber = $request->session()->get('temp_resident_addressData')['houseNumber'];
        $phoneNumber = $request->session()->get('temp_resident_addressData')['phoneNumber'];


        addresses::create([
            'resident_id' => $uuid,
            'municipality' => $municipality,
            'barangay' => $barangay,
            'subdivision/district' => $subdivision ,
            'house_number' => $houseNumber,
            'phone_number' =>$phoneNumber
        ]);

        registration::create([
            'resident_id' => $uuid
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
       




        return redirect()->route('signup.5_confirmAccount')->with('success','account is added');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(barangay_residents $resident)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(barangay_residents $resident)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, barangay_residents $resident)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(barangay_residents $resident)
    {
        //
    }
}
