@extends('administrator\layout')
 
@section('content')
 


<div class="resident-container">
   <div class="content-header">

      <h3>{{ $collection_data[0]['collectionDetails']['requestee']}}'s Approved Request</h3>
    
    </div>
<div class="resident-detail-container">

   <div class="resident-detail-box">
   <div class="detail-section">
      <p> Request Status: 
         <br>
         @php
            switch (  $collection_data[0]['collectionDetails']['status'] ) {
            
            case 'TBC':
               echo 'To Be Collected by Requestee';
               break;

            case 'COL':
               echo 'Collected by Requestee';
               break;

            case 'CAN':
               echo 'Collection Schedule Cancelled';
               break;

            default:
               echo 'To Be Collected by Requestee';
               break;
         }
         @endphp
      
         
           </p>
      <p> Requested on: <br> {{$collection_data[0]['collectionDetails']['dateRequested'] }}</p>
      <p> Scheduled to be collected on: <br> {{$collection_data[0]['collectionDetails']['dateScheduled'] }}</p> 
      <p> Collected On: <br> {{$collection_data[0]['collectionDetails']['dateCollected'] }}</p> 

        
      
   
   
   </p></p>
   

</div>

   <div class="detail-section">
      
      <label for="remarksInput"> Remarks for this Collection: </label>
      <textarea  name="remarksInput" id="remarksInput"   rows="4"></textarea>

      <button class="approvaButton" id="approveButton" onclick="confirmCollectionDate()">
         Confirm Collection
      </button>
      


   </div>


   <div class="detail-section">
       
      {{-- <p> Registered on: {{$collection_data[0]['date_registered'] }}</p>
      <p> Responded by: 
      @php
         if ( $collection_data[0]['Barangay Officer'] == null) {
            echo "Waiting for approval";
         } else {
            echo $collection_data[0]['Barangay Officer'];
         }
      @endphp   
      
      </p>
      <p> Remarks: {{ $collection_data[0]['remarks'] }}</p> 
    --}}
   </div>


</div>

   <div class="resident-side-box">
      
<div class="request-table-container">
     
   <h3>Submitted Requirements:</h3>

   <table class="document-table" id="requirement-table">
     <thead>
       <th>Requirement</th>
       <th>Requirement file</th>
     </thead>
      <tbody>

         @foreach ($collection_data[0]['requirements'] as $requirement ) 
            <tr>
               <td> {{ $requirement['name']; }} </td>
         
                          
               <td> <a  href="{{    
               route('files.get', ['category' => 'REQ', 'categoryCode'=>$collection_data[0]['collectionDetails']['requestCode'],'fileName' => $requirement['requirement_filename'] ]) }}" target="_blank">View file</a>
            
            </td>
           
         
         
         </tr>
         @endforeach


      </tbody>
   </table>

 </div>


      
<div class="request-table-container">
     
   <h3>Requested Documents:</h3>

   <table class="document-table" id="document-table">
     <thead>
       <th>Document</th>
       <th>Purpose</th>
       <th>Requested Quantity</th>
       <th>Grant Status</th>
     </thead>
      <tbody>
      </tbody>
   </table>
 </div>


   </div>
   
</div>
</div>

<script>


   var requirementTableBody = document.getElementById('requirement-table').getElementsByTagName('tbody')[0];
   var requestedDocTableBody = document.getElementById('document-table').getElementsByTagName('tbody')[0];
   const requiremenTable = document.getElementById('document-table');
   const requestedDocumentTable = document.getElementById('document-table');
   const remarksText = document.getElementById('remarksInput');
   var modifyButtons = document.querySelectorAll('.approvaButton');
   
   var collectionData =  @json($collection_data[0]['collectionDetails']);
   var requestedDocumentList =  @json($collection_data[0]['requested_doc']);
   
   if (collectionData.status != 'TBC') {
      modifyButtons.forEach(function(button) {
         button.disabled = true; 
         remarksText.disabled = true;
      });

      console.log(collectionData['status']);
   }

   remarksText.textContent = collectionData['remarks'];
   var grantedRequestedDocs = [];
    

   
     console.log(requestedDocumentList);
    
   function fillDocumentTable(documents) {
                 
                documents.forEach(req_document => {
           
                  grantedRequestedDocs.push(req_document['id']);                   
   
                    var row =  requestedDocTableBody.insertRow();
                    
                    // Insert cells into the row and populate them with data
                    var cell1 = row.insertCell(0);
                    cell1.innerHTML = req_document['docName'];
           
                    var cell2 = row.insertCell(1);
                    cell2.innerHTML =   req_document['request_reason']; 
                          
                    var cell3= row.insertCell(2);
                    cell3.innerHTML =  req_document['request_quantity']; 

                    var cell4= row.insertCell(3);

                    if (collectionData.status == 'PEN') {
                     
                        var checkbox = document.createElement('input');

                        // Step 2: Set its attributes
                        checkbox.type = 'checkbox';
                        checkbox.checked = 'true';

                        //assign an onclick function to the checkbox:
                        checkbox.addEventListener('click', function ( ) {
                           
                           if (!checkbox.checked) {

                              let index = grantedRequestedDocs.indexOf(req_document['id'])
                              if (index !== -1) {
                                 grantedRequestedDocs.splice(index, 1);

                              }
                              } else {
                                 console.log('Checkbox is checked.');       
                                 grantedRequestedDocs.push(req_document['id']);              
                              }


                        });   
                        cell4.appendChild(checkbox);

                    } else{

                       cell4.innerHTML =  req_document['remarks']; 
                          

                    }

                 });

                 


              
              }
   
   fillDocumentTable(requestedDocumentList);
   
      
           function updateRequest(Status) {
   
                  var remarks = 'No remarks.';

                  if ( remarksText.value.length > 0) {
                  var remarks = remarksText.value;
                  }
                  
                                    
                  
                 
                 requestJson = {collectiontDetails: {
                             collectionID:collectionData.collectID,
                             requestID:collectionData.requestID,
                             remarks:remarks,
                             status:Status}, 
                             documents:grantedRequestedDocs};
   
                    
   
   
                 var jsonData = JSON.stringify(requestJson);
   
   
                   $.ajax({
                       url: "{{ route('admin.modify_collection') }}",
                       type: "GET",
                       data: { collectionArray: jsonData },
                       success: function(response) {
                           console.log("Data sent successfully");
                           window.location.replace("{{  route('admin.view_collection')}}?collection_id="+collectionData.collectID);
                       },
                       error: function(xhr, status, error) {
                           console.error("Error sending data to server:", error);
                       }
                   });
   
   
             
           } 
   
           function confirmCollectionDate() {
            const d = new Date();
            const schedDate = new Date(collectionData.dateScheduled);
            
            if(d.getDate() < schedDate.getDate()) {
                     
                  if (confirm("It isn't the request's collection date yet. Confirm?")) {
                     updateRequest("COL");
                  }  
               
               } else if (d.getDate() > schedDate.getDate()) {
 
                  if (confirm("This is an overdue request. Confirm?")) {
                     updateRequest("COL");
                  }  
               
               } else {

                  if (confirm("Has the resident received their requested documents already? Confirm?")) {
                     updateRequest("COL");
                  }  
               
               }

            }
           
   
   
   
   
      </script>

@endsection
