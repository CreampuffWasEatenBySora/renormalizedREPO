<?php

namespace App\Http\Controllers\AdminPanel;

use App\Models\requestRecord;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class requestRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $sort = $request->input('sort');
        $filter = $request->input('filter');
        $filterText = $request->input('searchbox');
        $requestData = [];
        
        try {        
              
              $query = DB::table('request_records as reqs')
              -> select(

                'reqs.id as id', 
                'reqs.request_code as Request_code', 
                'reqs.date_requested as Requested_on', 
                'resident.fullName as Requestee', 
                'officer.fullName as Responded_by', 
                'reqs.date_responded as Responded_on', 
                'reqs.status as Status'

              )     
              ->join('barangay_residents as resident', 'resident.UUID', '=', 'reqs.resident_id')
              ->leftJoin('barangay_residents as officer', 'officer.UUID', '=', 'reqs.barangay_officer_id');

              $requests = $query->get();
                
                foreach ($requests as $request) {

                    $array = json_decode(json_encode($request), true);
                    $request_entry['request'] = $array;
                    $requestedDocuments_query = DB::table('requested_documents as doc_reqs')
                    ->select(
                        'doc_reqs.*',
                        'document.name as docName'
                    )
                    ->join('barangay_documents as document', 'document.id', '=', 'doc_reqs.for_document_id')
                    ->where('for_request_id', $request->id);
                    $requestedDocuments = $requestedDocuments_query->get()->toArray();  
                    
                    $i = 0;
                    foreach ($requestedDocuments as $requestedDocument) {

                        $array = json_decode(json_encode($requestedDocument), true);
                        $request_entry['request']['requested_doc'][$i] = $array;
                        
                        $i++;
                    }
                    
                    array_push($requestData, $request_entry['request']);
                }

            $jsonData = json_encode($requestData); 
            Log::info($requestData);  // Debug statement
            
            return view('administrator.requests_operations.list_requests',  ['requests_jsonData' =>  $jsonData  ]);

        } catch (\Throwable $th) {
            Log::error("Request set returned unsuccessfully : ".$th->getMessage() );  // Debug statement
            return response()->json(['status' => 'failed'], 200);
        }


        return view('requests.create');

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('requests.create');
    }

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
    public function show(requestRecord $requestRecord)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(requestRecord $requestRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, requestRecord $requestRecord)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequestRecord $requestRecord)
    {
        //
    }
}
