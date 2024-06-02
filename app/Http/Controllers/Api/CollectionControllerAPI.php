<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\Controller;
use App\Models\collectionRecord;
use App\Models\personalAccessToken;
use App\Models\requestedDocument;
use App\Models\requestRecord;
use App\Models\submittedRequirements; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class CollectionControllerAPI extends Controller
{
    

    static function store(Request $request){
        
        $UUID =  $request->input('userID');
        $accesskey = $request->input('accessKey');
        $requestID = $request->input('requestID');
        $requestSchedDate = $request->input('schedDate');

        Log::info($request);  // Debug statement
        

        if (AuthenticationControllerAPI::validateAccessKey($UUID, $accesskey)) {
         

        collectionRecord::create([
            'request_id' => $requestID,
            'date_scheduled' => $requestSchedDate,
            'status' => "TBC",
            'remarks' => "Scheduled for collection"
        ]);


        }

        
        return response()->json(['status' => 'success', 'message' => 'Collection date scheduled successfully!'  ], 200);


      }
}
