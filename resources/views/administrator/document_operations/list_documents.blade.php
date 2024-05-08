@extends('administrator\layout')
 
@section('content')

<div class="content-container">

  <div class="content-header">

  <h3>Manage DOcuments</h3>

  <div class="searchbar">
    <form action="{{ route('admin.list_residents') }}" method="get">
    <button>search</button>
    <input type="text" name="document_searchbox" id="document_searchbox" :value="old('document_searchbox')">
   
    <select name="document_filter" id="document_filter">

      <option value="any"> No filter </option>
      <option value="id"> ID </option>
      <option value="name"> Name</option>
      <option value="description"> Description </option>
      <option value="created_at"> Created on </option>
      <option value="updated_at"> Updated on </option>

    </select>

    <select name="document_sort" id="document_sort">
      <option value="any"> No filter </option>
      <option value="id"> ID </option>
      <option value="name"> Name</option>
      <option value="description"> Description </option>
      <option value="created_at"> Created on </option>
      <option value="updated_at"> Updated on </option>
    </select>
   
    </form>

    <button id="sort-button">⬆ Ascending</button>


  </div>

</div>

<div class="resident-container">

  <div class="resident-header">

    <div class="document-detail">
      <div class="details-box">
       <p id="document_regdate" class="detail-regdate">Created on:</p>  
       <p id="document_name" class="detail-name">Name</p>
       <p id="document_description" class="detail-address">Description</p>
      </div>
      <div id="close_button" style="display: none">
        <p>X</p>
      </div>
    </div>

    <div class="approval-container">
 
      <form action= "{{ route('admin.create_document') }}" method="get">
        <input  style="display: none"  type="text">
        <button  id="add_document_button" type="submit">
          Add a new document
        </button>
      </form>

      <form action= "{{ route('admin.view_document') }}" method="get"  style="display: none" id="document_edit_form">
        <input  style="display: none" id="document_id" name="document_id" type="text">
        <button id="edit_document_button" type="submit">
          Edit This Document
        </button>
      </form>

    </div>

  </div>

  <div class="resident-table-container">
    <table class="document-table" id="document-table">
      <thead>
        <th>Created on:</th>
        <th>Name</th>
        <th>Description</th>
        <th>Updated</th>
      </thead>
       <tbody>

       </tbody>
    </table>
  </div>

</div>
</div>

<script>


  // Assume jsonData contains your JSON data
var jsonData = {!! $document_jsonData !!};

// Get a reference to the table body
var tableBody = document.getElementById('document-table').getElementsByTagName('tbody')[0];
let document_id = document.getElementById('document_id');
let document_regdate = document.getElementById('document_regdate');
let document_name = document.getElementById('document_name');
let document_description = document.getElementById('document_description');

let  close_button = document.getElementById('close_button');
let  add_document_button = document.getElementById('add_document_button');
let  edit_document_button =document.getElementById('document_edit_form');

let  reg = document_regdate.textContent;
let  name = document_name.textContent;
let  description = document_description.textContent;


close_button.addEventListener('click', function name() {
  
  close_button.style.display = 'none';
  add_document_button.style.display = 'block';
  edit_document_button.style.display = 'none';

})


function fillTable(data) {
      
      
    // Iterate over each entry in the JSON data
    data.forEach(function(docs) {


    // Create a new table row
    var row = tableBody.insertRow();

          row.classList.add('resident-clickable-row');
          //assign an onclick function to the row:
          row.addEventListener('click', function() {
            
            // Handle row click event here
            document_regdate.textContent = reg.concat(": ", docs.created_at);
            document_name.textContent = name.concat(": ",docs.name) ;
            document_description.textContent  = description.concat(": ", docs.description);
            document_id.value = docs.id;
            close_button.style.display = 'block';
            add_document_button.style.display = 'none';
            edit_document_button.style.display = 'block';


          });

          // Insert cells into the row and populate them with data
          var cell1 = row.insertCell(0);
          cell1.innerHTML = docs.created_at;

          var cell2 = row.insertCell(1);
          cell2.innerHTML =  docs.name;

          var cell3 = row.insertCell(2);
          cell3.innerHTML =  docs.description;

          var cell4 = row.insertCell(3);
          cell4.innerHTML =  docs.updated_at;

          });
          console.log(jsonData);

    }

    fillTable(jsonData);


let sort_order_button = document.getElementById('sort-button');

sort_order_button.addEventListener('click', function sorted() {
  
  tableBody.innerHTML = "";
  
  const reversedArray = jsonData.reverse();
  
    fillTable(reversedArray);
  
  if (sort_order_button.textContent == "⬆ Ascending") {
    sort_order_button.textContent = "⬇ Descending";
  } else {
    sort_order_button.textContent = "⬆ Ascending";
  }

});

let resident_sort = document.getElementById('document_sort');
resident_sort.addEventListener("change", function () {
  
  tableBody.innerHTML = "";
  
  const resortedArray = jsonData;
  var filter = resident_sort.value;

  switch (filter) {
    case "name":
      resortedArray.sort((a, b) => 
        {
      if(a.name < b.name){
          return -1;
        }});
      break;

      case "description":
      resortedArray.sort((a, b) => 
        {
      if(a.description < b.description){
          return -1;
        }});
      break;


      case "created_at":
      resortedArray.sort(function(a, b){return new Date(a.created_at) - new Date(b.created_at)});;
      break;
  
      
      case "updated_at":
      resortedArray.sort(function(a, b){return new Date(a.updated_at) - new Date(b.updated_at)});;
      break;

    default:
    resortedArray.sort((a, b) => 
        {
      if(a.name < b.name ){
          return -1;
        }});
      break;
  }

 

    console.log("Filter :" + filter +" " +resortedArray);
    fillTable(resortedArray);

 
    sort_order_button.textContent = "⬆ Ascending";
  

});




</script>

@endsection
