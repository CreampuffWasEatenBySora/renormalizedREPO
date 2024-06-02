@extends('administrator\layout')
 
@section('content')
 


<div class="resident-container">
   <div class="content-header">

      <h3>{{ $request_data[0]['requestDetails']['requestee']}}'s Request</h3>
    
    </div>
<div class="resident-detail-container">

   <div class="resident-detail-box">
   <div class="detail-section">
      <p> Request Status: 
         @php
            
            switch (  $request_data[0]['requestDetails']['status'] ) {
            
            case 'PEN':
               echo 'Pending for Approval';
               break;

            case 'APR':
               echo 'Approved for Collection';
               break;

            case 'REJ':
               echo 'Rejected Request';
               break;

            default:
               echo 'Pending for Approval';
               break;
         }
         @endphp
      
         
           </p>
      <p> Requested on: {{$request_data[0]['requestDetails']['dateRequested'] }}</p>
      <p> Documents Requested: 
         
         @php
            {  
               
               echo count($request_data[0]['requested_doc']);
            }
         @endphp

        
      
   
   
   </p></p>
   

</div>

   <div class="detail-section">
      
      <label for="remarksInput"> Remarks for this request: </label>
      <textarea  name="remarksInput" id="remarksInput"   rows="4"></textarea>

      <button class="approvaButton" id="approveButton" onclick="updateRequest('APR',{{$request_data[0]['requestDetails']['requestID'] }})">
         Approve
      </button>
     
      <button class="approvaButton" id="rejectButton" onclick="updateRequest('REJ',{{$request_data[0]['requestDetails']['requestID'] }})">
         Reject
      </button>
     


   </div>


   <div class="detail-section">
       
      {{-- <p> Registered on: {{$request_data[0]['date_registered'] }}</p>
      <p> Responded by: 
      @php
         if ( $request_data[0]['Barangay Officer'] == null) {
            echo "Waiting for approval";
         } else {
            echo $request_data[0]['Barangay Officer'];
         }
      @endphp   
      
      </p>
      <p> Remarks: {{ $request_data[0]['remarks'] }}</p> 
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

         @foreach ($request_data[0]['requirements'] as $requirement ) 
            <tr>
               <td> {{ $requirement['name']; }} </td>
         
         <td> <a href="{{  asset(Storage::url($requirement['requirement_filepath'])); }}" target="_blank"> View file</a> 
                        
            
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
   
   var requestData =  @json($request_data[0]['requestDetails']);
   var requestedDocumentList =  @json($request_data[0]['requested_doc']);
   
   if (requestData.status != 'PEN') {
      modifyButtons.forEach(function(button) {
         button.disabled = true; 
      });

      console.log(requestData['status']);
   }

   var grantedRequestedDocs = [];
    

   
     console.log(requestedDocumentList);
   
   // const edit_button = document.getElementById("enable-edit-button");
   // const save_button = document.getElementById("save-edit-button");
   // const cancel_button = document.getElementById("cancel-edit-button");
   // const edit_buttons_box = document.getElementById("edit-buttons-box");
   // const requirement_menu = document.getElementById('requirement_menu');
   // const add_requirement_button = document.getElementById('add-requirement-button');
   // const document_details = document.getElementById("detail-form");
   // const document_edit_details = document.getElementById("edit-form");
   // const document_id= document.getElementById('document_id');
   // const document_name = document.getElementById('document_name');
   // const document_desc = document.getElementById('document_desc');
   
   // function assignRequirements() {
     
   
   //   for (let i = 0; i < requirementsMasterList.length; i++) {
       
   //     var selected = false;
   
   //     for (let j = 0; j < selectedRequirementsList.length; j++) {
   
   //         if (selectedRequirementsList[j]['id'] == requirementsMasterList[i]['id']) {
           
   //           selected = true;
   
   //         }
   //     }
   
   //     if (selected) {
   //       fillTable(requirementsMasterList[i], i, false)
   //     } else {
   //       fillRequirementes(requirementsMasterList[i]['name'], i);
   
   //     }
   //   }
   
     
             
   
   
      
   // }
   
   // assignRequirements();
   
   
   
   // function fillRequirementes(name, index) {
      
   //       const requirementOption = document.createElement("option");      
   //       requirementOption.value = index;
   //       requirementOption.textContent = name;
   //       requirement_menu.add(requirementOption);
       
   // }
    
   // // fillRequirementes(unselected_requirements_array);
         


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

                    if (requestData.status == 'PEN') {
                     
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
   
     
   //   function fillRemoveRequirement(name, row, index) {
                     
   //                   // Handle row click event here
   //                   table.deleteRow(row.rowIndex);
   //                   var requirementRemoval = selectedReqsArr.indexOf(index);
   //                   selectedReqsArr.splice(requirementRemoval, 1);
   //                   fillRequirementes(name, index);            
   
   //                   console.log("Selected documents:" + selectedReqsArr);
   
   
     
   //             }
   
   
   
   
   //         edit_button.addEventListener('click', function() {
               
   //             var remove_requirment_cells = document.querySelectorAll('.remove-requirement');
   //             edit_buttons_box.style.display = 'block';
   //             document_edit_details.style.display ='block';
   //             edit_button.style.display = 'none';
   //             document_details.style.display ='none';
               
   //             remove_requirment_cells.forEach(function(element) {
                 
   //               element.style.display ='block';
   
   //             });
   
   //         });
   
   
   //         cancel_button.addEventListener('click', function() {
    
   //             // Refresh the page
   //             location.reload();
   //         });
   
   
   //         add_requirement_button.addEventListener('click', function () {
             
   //           var index = requirement_menu.options[requirement_menu.selectedIndex].value;
             
   //           fillTable(requirementsMasterList[index], index, true);
             
   //           requirement_menu.remove(requirement_menu.selectedIndex);
              
   //         });
   
           function updateRequest(Status, requestId) {
   
                  var remarks = 'No remarks';

                  if ( remarksText.value.length > 0) {
                     
                  var remarks = remarksText.value;

                  }
            
                 
                 requestJson = {requestDetails: {
                             id:requestId,
                             status:Status, 
                             remarks:remarks}, 
                             documents:grantedRequestedDocs};
   
                    
   
   
                 var jsonData = JSON.stringify(requestJson);
   
   
                   $.ajax({
                       url: "{{ route('admin.modify_request') }}",
                       type: "GET",
                       data: { requestArray: jsonData },
                       success: function(response) {
                           console.log("Data sent successfully");
                           window.location.replace("{{  route('admin.view_request')}}?request_id="+requestId);
                       },
                       error: function(xhr, status, error) {
                           console.error("Error sending data to server:", error);
                       }
                   });
   
   
             
           } 
   
           
   
   
   
   
      </script>

@endsection
