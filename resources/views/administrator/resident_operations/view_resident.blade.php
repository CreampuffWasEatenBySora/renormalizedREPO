@extends('administrator\layout')
 
@section('content')

@php
   
@endphp



<div class="resident-container">
   <div class="content-header">

      <h3>{{ $data[0]['resident_name']}}'s Details</h3>
    
    </div>
<div class="resident-detail-container">

   <div class="resident-detail-box">
   <div class="detail-section">
      <p> Account Status: 
         @php
            
            switch (  $data[0]['status'] ) {
            case 'N':
               echo 'New Account';
               break;
            
            case 'P':
               echo 'Pending for Approval';
               break;

            case 'R':
               echo 'Rejected';
               break;

            default:
               echo 'Active resident';
               break;
         }
         @endphp
      
         
           </p>
      <p> Name: {{$data[0]['resident_name'] }}</p>
      <p> Birthday: {{$data[0]['birthday'] }}</p></p>
   </div>

   <div class="detail-section">
       
      <p> Email: {{$data[0]['email'] }}</p>
      <p> Address: {{ $data[0]['municipality'].", ".$data[0]['subdivision_district'].", ". $data[0]['barangay'].", ". $data[0]['house_number']}}</p>
      <p> PhoneNumber: {{$data[0]['phone_number'] }}</p></p>
   </div>


   <div class="detail-section">
       
      <p> Registered on: {{$data[0]['date_registered'] }}</p>
      <p> Responded by: 
      @php
         if ( $data[0]['Barangay Officer'] == null) {
            echo "Waiting for approval";
         } else {
            echo $data[0]['Barangay Officer'];
         }
      @endphp   
      
      </p>
      <p> Remarks: {{ $data[0]['remarks'] }}</p> 
   </div>


</div>

   <div class="resident-side-box">
      
   
    @if ( $data[0]['status'] == 'V' )
         
      <div class="resident-stats-window">
         <div class="stat-header">
            <h3>Resident's stats</h3>
         </div>
         <div class="stat-box">
            <div class="box-header">
            <h3>Requests</h3>   
            </div>      
            <div class="stat-container">
               <div class="stat">
                  <p>Rejected:</p>
                  <p id="collectedRequests"></p>
               </div>
               <div class="stat">
                  <p>Current:</p>
                  <p id="collectedRequests"></p>
               </div>
               <div class="stat">
                  <p>Approved:</p>
                  <p id="collectedRequests"></p>
               </div>
            </div>
         </div>

         <div class="stat-box">
            <div class="box-header">
            <h3>Collections</h3>   
            </div>      
            <div class="stat-container">
               <div class="stat">
                  <p>Uncollected:</p>
                  <p id="collectedRequests"></p>
               </div>
               <div class="stat">
                  <p>Collected:</p>
                  <p id="collectedRequests"></p>
               </div>
            </div>
         </div>

         <div class="stat-box">
            <div class="box-header">
            <h3>Most recent transactions</h3>   
            </div>      
            <div class="stat-container">
               <table>
                  <thead>
                     <tr>transaction</tr>
                  </thead>
                  <tbody>
                     <tr> <td>some transaction</td></tr>
                  </tbody>
               </table>
            </div>
         </div>


      </div>
         
      @elseif ($data[0]['status'] == 'N')
      <div class="verification-window">
        
         <div class="verification-header">
            <p>Submitted document: {{ $data[0]['requirement_type']}}</p>
            
         <div class="verification-buttons">
            <form action= "{{ route('admin.verify_resident') }}" method="get">
               <input  style="display: none" id="resident_uuid" name="resident_uuid" value="{{ $data[0]['UUID'] }}" type="text">
               <input  style="display: none" name="approval_status" value="V" type="text">
               <input  style="display: none" name="approval_remarks" value="Verified" type="text">
               <button  type="submit">
                 Approve
               </button>
             </form>

             <form action= "{{ route('admin.verify_resident') }}" method="get">
               <input  style="display: none" id="resident_uuid" name="resident_uuid"  value="{{ $data[0]['UUID'] }}" type="text">
               <input  style="display: none" name="approval_status" value="R" type="text">
               <input  style="display: none" name="approval_remarks" value="Rejected" type="text">
               <button  type="submit">
                  Reject
               </button>
             </form>
         </div>

         </div>
         
         <div class="verification-images-container">
            <div class="selfie">

               <a  href="{{    
               route('files.get', ['category' => 'REG', 'categoryCode'=>$data[0]['id'],'fileName' => $data[0]['selfie_filename'] ]) }}" target="_blank">

               <img src="{{    
               route('files.get', ['category' => 'REG', 'categoryCode'=>$data[0]['id'],'fileName' => $data[0]['selfie_filename'] ]) }}
               " alt="" class="verification-image" alt="selfie"> 
             </a>
            </a>
            </div>
            <div class="document">
               <a href="{{    
               route('files.get', ['category' => 'REG', 'categoryCode'=>$data[0]['id'],'fileName' => $data[0]['document_filename'] ]) }}"target="_blank" rel="noopener noreferrer">
               <img src="{{    
               route('files.get', ['category' => 'REG', 'categoryCode'=>$data[0]['id'],'fileName' => $data[0]['document_filename'] ]) }}"  alt="" class="verification-image" alt="document">
            </a>
            </div>
         </div>

      </div>
      @else
      <h1>User is rejected</h1>
      @endif
   



   </div>
   
</div>
</div>
@endsection
