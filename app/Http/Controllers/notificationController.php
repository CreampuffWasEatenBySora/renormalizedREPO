<?php

namespace App\Http\Controllers;

use App\Models\collectionRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Query\Builder;
use App\Models\notifications;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class notificationController extends Controller
{

    public function index(Request $request) {
        
        $eventType = request()->input('eventType');
        $dateRange = request()->input('requestDay');
        $officer = DB::table('barangay_residents')->where('UUID','=', Auth::user()->UUID)->first();
        
        $notifData = [];
        
        try {        
              
              $query = DB::table('notifications as notif')
              -> select(

                'notif.id as id', 
                'sender.fullName as senderName', 
                'receiver.fullName as receiverName', 
                'notif.for_event_id as eventID', 
                'notif.event_type as eventType', 
                'notif.event_description as eventDesc', 
                'notif.read_status as readStatus'
              )     
              ->join('barangay_residents as sender', 'sender.id', '=', 'notif.from_user_id')
              ->join('barangay_residents as receiver', 'receiver.id', '=', 'notif.for_user_id')
            //   ->orWhere(function (Builder $query) {
            //     $query
                
                ->where('notif.for_user_id', '=',$officer->id)
            //     ->orWhere('notif.from_user_id', '=',Auth::user()->id);
            // })
            ;
             
                if ($eventType != null) {
                $query = $query->where('notif.event_type', '=', $eventType);
               };
            
            if ($dateRange != null) {
                switch ($dateRange) {
                    
                    case 'last hour':
                    $until = now()->subHour();
                    break;
                    
                    case 'last 12 hours':
                    $until = now()->subHours(12);
                    break;
                    
                    case 'last 24 hours':
                    $until = now()->subHours(24);
                    break;

                    case 'last 3 days':
                    $until = now()->subDays(3);
                    break;

                    case 'last week':
                    $until = now()->subWeek();
                    break;
                    
                    case 'last month':
                    $until = now()->subDays(3);
                    break;

                    default:
                    $until = now()->subDay();
                    break;
                }
                $query = $query->whereBetween('notif.created_at', [$until, now()], $eventType);
               }
 

              $query = $query
              ->orderBy('notif.for_user_id')
              ->orderBy('notif.read_status')
              ->orderBy('notif.created_at');
              $notifications = $query->get();
                

            $jsonData = json_encode($notifications); 
             Log::info($jsonData);  // Debug statement
            
            return view('administrator.notifications_operations.list_notifications',  ['notifs_jsonData' =>  $jsonData  ]);

        } catch (\Throwable $th) {
            Log::error("notification set returned unsuccessfully : ".$th->getMessage() );  // Debug statement
            return response()->json(['status' => 'failed'], 200);
        }


        return view('requests.create');



    }

    public function check(Request $request){
            

        $id = $request->input('id');
        $eventId = $request->input('eventId');
        $eventType = $request->input('eventType');
        $readStatus = $request->input('readStatus');
        Log::info($request);


        try {

            $collection = notifications::find($id);

            if (!$readStatus) {
                $collection -> update([
                    'read_status' => 1,
                ]);
            }
            
                        
                switch ($eventType) {
                    case "Request":
                        $route = route('admin.view_request', ['request_id' => $eventId]);
                        break;
                    case "Collection":
                        $route = route('admin.view_collection', ['collection_id' => $eventId]);
                        break;
                    case "Document":
                        $route = route('admin.view_document', ['document_id' => $eventId]);
                        break;
                    case "Registration":
                        $route = route('admin.view_resident', ['resident_id' => $eventId]);
                        break;
                    default:
                        $route = route('admin.list_notifications');
                        break;
                }
                return response()->json(['route' => $route]);
           

        } catch (\Throwable $th) {

            Log::info("Error: ".$th);
            return response()->json(['status' => 'failed'], 200);

        }


    }

    public static function notifyBarangayOfficers($fromUserId, $eventId, $eventType, $eventDescription){

        
        $barangayOfficers = DB::table('barangay_residents')->where('access_level','=', 'A')->get();

        foreach ($barangayOfficers as $barangayOfficer) {

        notifications::create([
        
            'for_user_id' => $barangayOfficer->id,
            'from_user_id' => $fromUserId,
            'for_event_id' => $eventId,
            'event_type' => $eventType,
            'event_description' => $eventDescription
        ]);

        }
        
    }
    
    public static function notifyBarangayresidents($fromUserId, $eventId, $eventType, $eventDescription){

        
        $barangayResidents = DB::table('barangay_residents')->where('access_level','=', 'R')->get();

        foreach ($barangayResidents as $barangayResident) {

        notifications::create([
        
            'for_user_id' => $barangayResident -> id,
            'from_user_id' => $fromUserId,
            'for_event_id' => $eventId,
            'event_type' => $eventType,
            'event_description' => $eventDescription
        ]);

        }
    }

    
    public static function notifySpecific($fromUserId, $toUserID, $eventId, $eventType, $eventDescription){

        notifications::create([
        
            'for_user_id' => $toUserID,
            'from_user_id' => $fromUserId,
            'for_event_id' => $eventId,
            'event_type' => $eventType,
            'event_description' => $eventDescription
        ]);

    }

    
}
