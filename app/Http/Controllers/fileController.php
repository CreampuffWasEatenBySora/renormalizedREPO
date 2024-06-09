<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class fileController extends Controller
{

    public function __construct()
    {
      $this->middleware('auth');  
    } 

    /**
     * Display a listing of the resource.
     */
    public function get($category, $categoryCode, $filename)
    {
        

        
        switch ($category) {
            case 'REG':
                $filePath = "registrationImages\\reg-".$categoryCode."\\".$filename;
                break;
                        
            case 'REQ':
                $filePath = "requirementImages\\".$categoryCode."\\".$filename;
                break;
           
            default:
                $filePath = "requirementImages\\".$categoryCode."\\".$filename;
                break;
        }
        
        
        if (!Storage::disk('private')->exists($filePath)) {

            return "haha";

        }

        // Get the file's content
        $file = Storage::disk('private')->get($filePath);
 
        $path = Storage::path('private\\'.$filePath);
        // Get the file's mime type
        $mimeType = Storage::mimeType($path);
 
        // Return the file as a response    
        return response()->file($path);



    }

    /**
     * return the path of the file after authenticating the request 
     */
        

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
