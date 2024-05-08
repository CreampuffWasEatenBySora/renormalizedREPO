@extends('administrator\layout')
 
@section('content')

@php
   
@endphp



<div class="resident-container">
   <div class="content-header">

      <h3>Create a new document</h3>
    
    </div>
<div class="resident-detail-container">
 <div class="document-detail-box">
    <div class="create-form">
    
        
        <label for="document_name"> Document Title: </label>
        <input type="text" name="document_name" id="document_name">
        
        <label for="document_name"> Description </label>
        <input type="text" name="document_desc" id="document_desc">
    
        <div class="requirement-box">
        <label for="requirement_menu"> Add Requirements: </label>
        <select name="requirement_menu" id="requirement_menu">
        </select>
         <button type="button" id="add-requirement-button">Add Requirement</button>  
      </div>
        <a href="{{ route('admin.list_requirements') }}">View Requirements List</a>
        <button id="store-document-button" type="submit">Create document</button>
    </div >

    <div class="resident-table-container">

      <h3>Applied Requirements:</h3>

      <table class="document-table" id="document-table">
        <thead>
          <th>Requirement</th>
        </thead>
         <tbody>
  
         </tbody>
      </table>
    </div>

 </div>
</div>

<script>

var requirements_list = {!! $requirement_jsonData !!};

var requirements_to_be_added = [];

const document_name = document.getElementById('document_name');
const document_desc = document.getElementById('document_desc');

const requirement_menu = document.getElementById('requirement_menu');
const store_document_button = document.getElementById('store-document-button');
const add_requirement_button = document.getElementById('add-requirement-button');
const table = document.getElementById('document-table');
var tableBody = document.getElementById('document-table').getElementsByTagName('tbody')[0];


function fillRequirementes(data) {
   data.forEach(function(reqs) {
      const requirementOption = document.createElement("option");
      
      requirementOption.value = reqs.id;
      requirementOption.textContent = reqs.name;
      console.log(reqs.name);
      requirement_menu.add(requirementOption);


   });
}

fillRequirementes(requirements_list);

add_requirement_button.addEventListener('click', function() {
   
   var selecteditemID = requirement_menu.options[requirement_menu.selectedIndex].value;
   var selecteditemText = requirement_menu.options[requirement_menu.selectedIndex].text;
   requirements_to_be_added.push(selecteditemID);
   console.log('Array: '  + requirements_to_be_added);

   requirement_menu.remove(requirement_menu.selectedIndex);
   
      // Create a new table row
      var row = tableBody.insertRow();
         // Insert cells into the row and populate them with data
         var cell1 = row.insertCell(0);
         cell1.innerHTML = selecteditemText;

         var cell2 = row.insertCell(1);
         cell2.innerHTML =  "X";

         cell2.classList.add('resident-clickable-row');
         //assign an onclick function to the row:
         cell2.addEventListener('click', function() {

         // Handle row click event here
         table.deleteRow(row.rowIndex);
         const index = requirements_to_be_added.indexOf(selecteditemID);
         requirements_to_be_added.splice(index, 1);
         console.log('Array:' + requirements_to_be_added);

         const requirementOption = document.createElement("option");
         requirementOption.value = selecteditemID;
         requirementOption.textContent = selecteditemText;
         requirement_menu.add(requirementOption);

         });
      });

store_document_button.addEventListener('click', function() {

    

   documentJson = {documentDetails: {
               name:document_name.value,
               desc:document_desc.value}, 
               requirements: {}};

    for (var k = 0; k < requirements_to_be_added.length; k++) {
    var objName = 'req' + k;
    var objValue = requirements_to_be_added[k];
    documentJson.requirements[objName] = objValue;
    }


   var jsonData = JSON.stringify(documentJson);


    $.ajax({
        url: "{{ route('admin.store_document') }}",
        type: "GET",
        data: { documentArray: jsonData },
        success: function(response) {
            console.log("Data sent successfully");
            window.location.replace("{{  route('admin.list_documents')  }}");
        },
        error: function(xhr, status, error) {
            console.error("Error sending data to server:", error);
        }
    });


})








</script>
</div>
@endsection
