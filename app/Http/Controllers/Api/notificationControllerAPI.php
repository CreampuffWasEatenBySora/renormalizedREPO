<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\barangayDocument;
use App\Models\document_requirement;
use App\Models\requirement_listing;
use App\Http\Controllers\Controller;
use App\Models\notifications;
use Illuminate\Http\Request;

class notificationControllerAPI extends Controller
{
    

    public function fetch(Request $request){


        $notifData = [];
        
        
        $UUID = $request->input('userUUID');
        $accessKey = $request->input('accessKey');
        $resident = DB::table('users')->where('UUID','=', $UUID)->first();
        Log::info( $request);  // Debug statement
        

        if (AuthenticationControllerAPI::validateAccessKey($UUID, $accessKey)) {
        try {        
              
            $query = DB::table('notifications as notif')
            -> select(

              'notif.id as id', 
              'sender.name as senderName', 
              'receiver.name as receiverName', 
              'notif.created_at as notifDate', 
              'notif.updated_at as upDate', 
              'notif.for_event_id as eventID', 
              'notif.event_type as eventType', 
              'notif.event_description as eventDesc', 
              'notif.read_status as readStatus'
            )     
            ->join('users as sender', 'sender.id', '=', 'notif.from_user_id')
            ->join('users as receiver', 'receiver.id', '=', 'notif.for_user_id')
          //   ->orWhere(function (Builder $query) {
          //     $query
              
              ->where('notif.for_user_id', '=',$resident->id)
          //     ->orWhere('notif.from_user_id', '=',Auth::user()->id);
          // })
          ;
            $query = $query 
            ->orderBy('notif.read_status')
            ->orderByDesc('notif.updated_at');
            $notifications = $query->get();

            $i = 0;
            foreach ($notifications as $notification) {

              $notifData[$i]=[
                "id" =>$notification->id ,
                "notifDate" => $notification->notifDate,
                "upDate" => $notification->upDate,
                "senderName" => $notification->senderName ,
                "eventID" => $notification->eventID,
                "eventType" => $notification->eventType,
                "eventDesc" => $notification->eventDesc,
                "readStatus" => $notification->readStatus 
              ];
              $i++;
            }
            $data= json_encode($notifData, true);

            
            $jsonData= json_decode($data, true);
           Log::info($jsonData);  // Debug statement
          
           return response()->json(['status' => 'success', 'message' => 'Data senr successfully!', 'notif_data' => $jsonData], 200);

      } catch (\Throwable $th) {
          Log::error("notification set returned unsuccessfully : ".$th->getMessage() );  // Debug statement
          return response()->json(['status' => 'failed'], 200);
      }


      return view('requests.create');




        } 

    }

    public function update(Request $request){

      $id = $request->input('id');
      // Log::info($request);

      try {

          $collection = notifications::find($id);
          $collection -> update([
                  'read_status' => 1,
              ]);
           

      } catch (\Throwable $th) {

          Log::info("Error: ".$th);
          return response()->json(['status' => 'failed'], 200);

      }

    }
    
}
