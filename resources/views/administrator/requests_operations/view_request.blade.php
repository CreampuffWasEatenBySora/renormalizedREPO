@extends('administrator\layout')
 
@section('content')

@php
  $requirement_data = [];

 foreach ( $request_data[0]['requirements'] as $requirement) {
   
   $requirement['requirement_filepath'] =  Storage::url($requirement['requirement_filepath']); 
   
   array_push($requirement_data, $requirement);

  }

@endphp



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
   
   
   var requirementList =    @json($requirement_data);
   var requestedDocumentList =  @json($request_data[0]['requested_doc']);
   
 

   var selectedReqsArr = [];
    

   
     console.log(requirementList );
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
          
   function fillRequirementTable(requirements) {
                 
      requirements.forEach(requirement => {

         var row = requirementTableBody.insertRow();
         
         // Insert cells into the row and populate them with data
         var cell1 = row.insertCell(0);
         cell1.innerHTML = requirement['name'];

         var cell2 = row.insertCell(1);

         var link = document.createElement('a');
                link.href = "http://localhost/example-app/public"+requirement['requirement_filepath'];
                link.innerHTML = "View File";
                cell2.appendChild(link);
         
         // var cell3= row.insertCell(2);
         // cell3.innerHTML = "X";

         // if (editing) {
         // cell3.style.display ="block";
         // } else {
         // cell3.style.display ="none";
         // }


         // //assign an onclick function to the row:
         // cell3.classList.add('resident-clickable-row');
         // cell3.classList.add('remove-requirement');
         // cell3.addEventListener('click', function () {
         //    fillRemoveRequirement( requirement['name'], row, index)});   
         // selectedReqsArr.push(index);
         // console.log("Selected documents:" + selectedReqsArr);
         
      });
   
   }


   function fillDocumentTable(documents) {
                 
                documents.forEach(document => {
           
                    var row =  requestedDocTableBody.insertRow();
                    
                    // Insert cells into the row and populate them with data
                    var cell1 = row.insertCell(0);
                    cell1.innerHTML = document['docName'];
           
                    var cell2 = row.insertCell(1);
                    cell2.innerHTML =   document['request_reason']; 
                          
                    var cell3= row.insertCell(2);
                    cell3.innerHTML =  document['request_quantity']; 

                    var cell4= row.insertCell(3);
                    cell4.innerHTML = "X";
           
                    // if (editing) {
                    // cell3.style.display ="block";
                    // } else {
                    // cell3.style.display ="none";
                    // }
           
           
                    // //assign an onclick function to the row:
                    // cell3.classList.add('resident-clickable-row');
                    // cell3.classList.add('remove-requirement');
                    // cell3.addEventListener('click', function () {
                    //    fillRemoveRequirement( requirement['name'], row, index)});   
                    // selectedReqsArr.push(index);
                    // console.log("Selected documents:" + selectedReqsArr);
                    
                 });
              
              }
   
   fillRequirementTable(requirementList);
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
   
   //         save_button.addEventListener('click', function name(params) {
   
   
             
   //               documentJson = {documentDetails: {
   //                           id:document_id.value,
   //                           name:document_name.value,
   //                           desc:document_desc.value}, 
   //                           requirements: {}};
   
   //                 for (var k = 0; k < selectedReqsArr.length; k++) {
   //                 var objName = 'req' + k;
   //                 var objValue = requirementsMasterList[k]['id'];
   //                 documentJson.requirements[objName] = objValue;
   //                 }
   
   
   //               var jsonData = JSON.stringify(documentJson);
   
   
   //                 $.ajax({
   //                     url: "{{ route('admin.modify_document') }}",
   //                     type: "GET",
   //                     data: { documentArray: jsonData },
   //                     success: function(response) {
   //                         console.log("Data sent successfully");
   //                         window.location.replace("{{  route('admin.view_document')}}?document_id="+document_id.value);
   //                     },
   //                     error: function(xhr, status, error) {
   //                         console.error("Error sending data to server:", error);
   //                     }
   //                 });
   
   
             
   //         })
   
           
   
   
   
   
      </script>

@endsection
