<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class accountSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('users')->insert([[
            'UUID' =>'ABCDE',
            'role' => 'A',
            'name' => 'Juan Dela Cruz',
            'email' => 'admin@net.com',
            'password' =>Hash::make('admin'),
        ],
        [
            'UUID' => 'FGHIJ',
            'role' => 'R',
            'name' => 'Pedro Penduko',
            'email' => 'pedro@penduko.com',
            'password' =>Hash::make('resident'),
        ],
        [
            'UUID' => 'LMNOP',
            'role' => 'R',
            'name' => 'John Doe',
            'email' => 'Juan@dose.com',
            'password' =>Hash::make('resident'),
        ]]
    );

    

    
    DB::table('barangay_residents')
    ->insert([[
        'UUID' =>'ABCDE',
        'access_level' => 'A',
        'status' => 'V',
        'fullName' => 'Juan Dela Cruz',
        'email' => 'admin',
        'password' =>Hash::make('admin'),
    ],
    [
        'UUID' => 'FGHIJ',
        'access_level' => 'R',
        'status' => 'V',  
        'fullName'  => 'Pedro Penduko',
        'email' => 'pedro@penduko.com',
        'password' =>Hash::make('resident'),
    ],
    [
        'UUID' => 'LMNOP',
        'access_level' => 'R',
        'status' => 'V',  
        'fullName'  => 'John Doe',
        'email' => 'Juan@dose.com',
        'password' =>Hash::make('resident'),
    ]]
    );

    
    DB::table('addresses')
    ->insert([[
        'id' => 200001,
        'resident_id' => 'ABCDE',
        'municipality' => 'LeProvincia',
        'barangay' => 'HOHUM',
        'subdivision_district' => 'Kambba',
        'house_number' => 'BLK 13 DT 22',
        'phone_number' => '09123356321', 
    ],
    [
        'id' => 200002,   
        'resident_id' => 'FGHIJ',
        'municipality' => 'LeProvincia',
        'barangay' => 'HOHUM',
        'subdivision_district' => 'Kambba',
        'house_number' => 'BLK 23 ART 21',
        'phone_number' => '09123456321', 
    ],
    [
        'id' => 200003,
        'resident_id' => 'FGHIJ',
        'municipality' => 'LeProvincia',
        'barangay' => 'HOHEE',
        'subdivision_district' => 'Lambda',
        'house_number' => 'BLK 23 HRT 2A',
        'phone_number' => '09123556321', 
    ]]
    );


    

    
    DB::table('registrations')
    ->insert([[
        'id' => 300001,
        'requirement_type' => 'passport',
        'selfie_filename' => 'SJuan.jpg',
        'document_filename' => 'DJuan.jpg',
        'resident_id' => 'ABCDE'
        
    ],
    [
        'id' => 300002,   
        'requirement_type' => 'passport',
        'selfie_filename' => 'SPedro.jpg',
        'document_filename' => 'DPedro.jpg',
        'resident_id' => 'FGHIJ',

    ],
    [
        'id' => 300003,
        'requirement_type' => 'passport',
        'selfie_filename' => 'SJohn.jpg',
        'document_filename' => 'DJohn.jpg',
        'resident_id' => 'LMNOP',

    ]]
    );

    
    DB::table('barangay_residents')
    ->where('UUID','=','ABCDE')
    ->update(['registration_id' => 300001 , 'address_id' => 200001]);

    
    DB::table('barangay_residents')
    ->where('UUID','=','FGHIJ')
    ->update(['registration_id' => 300002 , 'address_id' => 200002]);

    
    DB::table('barangay_residents')
    ->where('UUID','=','LMNOP')
    ->update(['registration_id' => 300003 , 'address_id' => 200003]);
    
    





 

        


    }
}
