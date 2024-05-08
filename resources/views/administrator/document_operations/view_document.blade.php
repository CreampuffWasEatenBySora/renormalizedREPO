@extends('administrator\layout')
 
@section('content')

@php




@endphp


<div class="resident-container">
   <div class="content-header">

      <h3>{{  $document_data['name'];}}'s Details</h3>
       <div class="button-box">
        <button id ="enable-edit-button">Edit details ✍</button>

        <div id="edit-buttons-box" style="display: none"> 
        <button id = "save-edit-button">Save Changes ✅</button>
        <button id = "cancel-edit-button">Cancel Edit❌</button>
      </div>
      </div>
      
    
    </div>
      <div class="document-detail-box">

        <div class="detail-form" id="detail-form">

          <div class="document-title">
          <h3> {{ $document_data['name'];}}  </h3>
          </div>  

          <div class="document-description">
            <p>{{ $document_data['description'];}}</p>
          </div>

        </div>

         <div class="edit-form" id="edit-form" style="display: none">
         
          <input type="text" name="document_id" id="document_id" value="{{ $document_data['id'];}}" style="display: none">
             
             <label for="document_name"> Document Title: </label>
             <input type="text" name="document_name" id="document_name" value="{{ $document_data['name'];}}">
             <br>
             <label for="document_desc"> Description </label>
             <br>

            <textarea  name="document_desc" id="document_desc"  cols="30" rows="10"> {{ $document_data['description'];}}</textarea>
             <div class="requirement-box">
             <label for="requirement_menu"> Add Requirements: </label>
             <select name="requirement_menu" id="requirement_menu">
             </select>
              <button type="button" id="add-requirement-button">Add Requirement</button>  
           </div>
             <a href="{{ route('admin.list_requirements') }}">View Requirements List</a>
         </div >
     
         <div class="resident-table-container">
     
           <h3>Applied Requirements:</h3>
     
           <table class="document-table" id="document-table">
             <thead>
               <th>Requirement</th>
               <th>Description</th>
             </thead>
              <tbody>
              </tbody>
           </table>
         </div>
</div>
</div>

<script>


var tableBody = document.getElementById('document-table').getElementsByTagName('tbody')[0];
const table = document.getElementById('document-table');


var requirementsMasterList =    @json($all_requirements_data);
var selectedRequirementsList =  @json($assigned_requirement_IDs);

var selectedReqsArr = [];
 

console.log(requirementsMasterList);
console.log(selectedRequirementsList);

const edit_button = document.getElementById("enable-edit-button");
const save_button = document.getElementById("save-edit-button");
const cancel_button = document.getElementById("cancel-edit-button");
const edit_buttons_box = document.getElementById("edit-buttons-box");
const requirement_menu = document.getElementById('requirement_menu');
const add_requirement_button = document.getElementById('add-requirement-button');
const document_details = document.getElementById("detail-form");
const document_edit_details = document.getElementById("edit-form");
const document_id= document.getElementById('document_id');
const document_name = document.getElementById('document_name');
const document_desc = document.getElementById('document_desc');

function assignRequirements() {
  

  for (let i = 0; i < requirementsMasterList.length; i++) {
    
    var selected = false;

    for (let j = 0; j < selectedRequirementsList.length; j++) {

        if (selectedRequirementsList[j]['id'] == requirementsMasterList[i]['id']) {
        
          selected = true;

        }
    }

    if (selected) {
      fillTable(requirementsMasterList[i], i, false)
    } else {
      fillRequirementes(requirementsMasterList[i]['name'], i);

    }
  }

  
          


   
}

assignRequirements();



function fillRequirementes(name, index) {
   
      const requirementOption = document.createElement("option");      
      requirementOption.value = index;
      requirementOption.textContent = name;
      requirement_menu.add(requirementOption);
    
}
 
// fillRequirementes(unselected_requirements_array);
       
function fillTable(requirement, index, editing) {
              
  
      // Create a new table row
      var row = tableBody.insertRow();
  
            // Insert cells into the row and populate them with data
            var cell1 = row.insertCell(0);
            cell1.innerHTML = requirement['name'];
  
            var cell2 = row.insertCell(1);
            cell2.innerHTML = requirement['description'];
  
            var cell3= row.insertCell(2);
            cell3.innerHTML = "X";

            if (editing) {
            cell3.style.display ="block";
            } else {
            cell3.style.display ="none";
            }


            //assign an onclick function to the row:
            cell3.classList.add('resident-clickable-row');
            cell3.classList.add('remove-requirement');
            cell3.addEventListener('click', function () {
              fillRemoveRequirement( requirement['name'], row, index)});   
            selectedReqsArr.push(index);
            console.log("Selected documents:" + selectedReqsArr);
         

  }


  
  function fillRemoveRequirement(name, row, index) {
                  
                  // Handle row click event here
                  table.deleteRow(row.rowIndex);
                  var requirementRemoval = selectedReqsArr.indexOf(index);
                  selectedReqsArr.splice(requirementRemoval, 1);
                  fillRequirementes(name, index);            

                  console.log("Selected documents:" + selectedReqsArr);


  
            }




        edit_button.addEventListener('click', function() {
            
            var remove_requirment_cells = document.querySelectorAll('.remove-requirement');
            edit_buttons_box.style.display = 'block';
            document_edit_details.style.display ='block';
            edit_button.style.display = 'none';
            document_details.style.display ='none';
            
            remove_requirment_cells.forEach(function(element) {
              
              element.style.display ='block';

            });

        });


        cancel_button.addEventListener('click', function() {
 
            // Refresh the page
            location.reload();
        });


        add_requirement_button.addEventListener('click', function () {
          
          var index = requirement_menu.options[requirement_menu.selectedIndex].value;
          
          fillTable(requirementsMasterList[index], index, true);
          
          requirement_menu.remove(requirement_menu.selectedIndex);
           
        });

        save_button.addEventListener('click', function name(params) {


          
              documentJson = {documentDetails: {
                          id:document_id.value,
                          name:document_name.value,
                          desc:document_desc.value}, 
                          requirements: {}};

                for (var k = 0; k < selectedReqsArr.length; k++) {
                var objName = 'req' + k;
                var objValue = requirementsMasterList[k]['id'];
                documentJson.requirements[objName] = objValue;
                }


              var jsonData = JSON.stringify(documentJson);


                $.ajax({
                    url: "{{ route('admin.modify_document') }}",
                    type: "GET",
                    data: { documentArray: jsonData },
                    success: function(response) {
                        console.log("Data sent successfully");
                        window.location.replace("{{  route('admin.view_document')}}?document_id="+document_id.value);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error sending data to server:", error);
                    }
                });


          
        })

        




   </script>

@endsection
