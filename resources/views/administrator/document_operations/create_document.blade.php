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
    <form class="create-form" action="{{ route('admin.create_document') }}" method="get">
    
        
        <label for="document_name"> Document Title: </label>
        <input type="text" name="document_name" id="document_name">
        
        <label for="document_name"> Description </label>
        <input type="text" name="document_name" id="document_desc">
    
        <div class="requirement-box">
        <label for="requirement_menu"> Add Requirements: </label>
        <select name="requirement_menu" id="requirement_menu">
        </select>
         <button type="button" id="add-requirement-button">Add Requirement</button>  
      </div>
        <a href="">View Requirements List</a>
        <button type="submit">Create document</button>
    </form>

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
const requirement_menu = document.getElementById('requirement_menu');
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
   
   var selecteditemID = requirement_menu.options[requirement_menu.selectedIndex].text;
   var selecteditemText = requirement_menu.options[requirement_menu.selectedIndex].text;
      
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

         const requirementOption = document.createElement("option");
         requirementOption.value = selecteditemID;
         requirementOption.textContent = selecteditemText;
         requirement_menu.add(requirementOption);

         });

   

})

</script>
</div>
@endsection
