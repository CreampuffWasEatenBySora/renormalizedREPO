<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class pdfController extends Controller
{
   public function generate_RequestRecords(){
    
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
        ->join('barangay_residents as officer', 'officer.UUID', '=', 'reqs.barangay_officer_id')
        ->where('reqs.status','=','APR');

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

        } catch(\Throwable $th){
            Log::error("Request set returned unsuccessfully : ".$th->getMessage() );  // Debug statement
        }

    
    $data = [
        'title' => 'Barangay Request Records History Report',
        'date' => now(),
        'requests' => $requestData
    ];
    $pdf = Pdf::loadView('administrator.pdf_operations.generate_request_recordsPDF',$data);
    return $pdf->download(today().'approved_request_records.pdf');

   }


   public function generateSummary(){

    $summaryData = [];
    // Data included in the summary: 
    // Admins registered up to the Report's creation
    // Users registered up the reports'creation
    // Total Registrations for the month, rejections included. 
    // Quick stats i.e requests submitted, approved, rejected, and collected in the month.
    // List of the documents available for request up to the report's creation. Along with the quantity of them collected.  
    $registrationBaseQuery = DB::table('barangay_residents as br')
    -> select(
      'br.fullName as fullName', 
      'officer.fullName as Responded_by', 
      'reqs.date_responded as Responded_on', 
      'reqs.status as Status'

    )     
    ->leftjoin('barangay_residents as resident', 'resident.UUID', '=', 'reqs.resident_id')
    ->join('barangay_residents as officer', 'officer.UUID', '=', 'reqs.barangay_officer_id')
    ->where('reqs.status','=','APR');

    ; 
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
    ->join('barangay_residents as officer', 'officer.UUID', '=', 'reqs.barangay_officer_id')
    ->where('reqs.status','=','APR');


    $data = [
        'title' => 'Barangay Request Records History Report',
        'date' => now(),
        'requests' => $summaryData
    ];
    $pdf = Pdf::loadView('administrator.pdf_operations.generate_request_recordsPDF',$data);
    return $pdf->download(today().'approved_request_records.pdf');

    
   }
}
