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
               echo 'Active Resident';
               break;

            default:
               echo 'New Account';
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
      <p> Responded on: 
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
      
      <div class="verification-window">
        
         <div class="verification-header">
            <p>Submitted document: {{ $data[0]['requirement_type']}}</p>
         </div>
         
         <div class="verification-images-container">
            <div class="selfie">
               <img src="" alt="" class="verification-image" alt="selfie">
            </div>
            <div class="document">
               <img src="" alt="" class="verification-image" alt="document">
            </div>
         </div>

         <div class="verification-buttons">
            <form action= "{{ route('admin.view_resident') }}" method="get">
               <input  style="display: none" id="resident_uuid" name="resident_uuid" type="text">
               <button  type="submit">
                 Approve
               </button>
             </form>

             <form action= "{{ route('admin.view_resident') }}" method="get">
               <input  style="display: none" id="resident_uuid" name="resident_uuid" type="text">
               <button  type="submit">
                  Reject
               </button>
             </form>
         </div>

      </div>


   </div>
   
</div>
</div>
@endsection
